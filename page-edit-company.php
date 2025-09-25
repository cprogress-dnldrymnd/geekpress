<?php

/**
 * Template Name: Edit Company
 */

if (! is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

get_header();
?>

<style>
    .preview_profile__container .preview img {
        object-fit: contain !important;
    }

    .upload_thumbnail img {
        object-fit: contain !important;
    }

    textarea::placeholder {
        text-transform: initial !important;
    }

    @media screen and (max-width:720px) {
        .asset__upload__wrapper {
            flex-direction: column;
        }
    }

    .error-field {
        border: 2px solid red !important;
    }

    .input-group {
        display: flex;
        align-items: center;
        border: 1px solid #ccc;
        border-radius: 8px;
        overflow: hidden;
        width: 100%;
        max-width: 300px;
    }

    .input-group .prefix {
        background: #f4f4f4;
        padding: 0.5rem 0.75rem;
        font-size: 1rem;
        color: #555;
        border-right: 1px solid #ccc;
    }

    .input-group input {
        flex: 1;
        border: none;
        outline: none;
        padding: 0.5rem;
        font-size: 1rem;
    }
</style>
<?php
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

$company_id = get__user_company($user_id, false, true);
$company_manager = get_field('company_manager', $company_id);
echo '<pre>';
var_dump($company_manager);
echo '</pre>';



if (!in_array($user_id, $company_manager) || !$company_id) {
?>
    <section class="edit__profile">
        <div class="container">
            <div class="edit__profile__wrapper">
                <h2>You don't have enough permission to access this page.</h2>
            </div>
        </div>
    </section>
<?php
} else {
    $errors = [];
    $success = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_profile_nonce']) && wp_verify_nonce($_POST['edit_profile_nonce'], 'edit_profile_action')) {

        $company_name = sanitize_text_field($_POST['company_name'] ?? '');
        $company_bio   = sanitize_textarea_field($_POST['company_bio'] ?? '');
        $new_email    = sanitize_email($_POST['email'] ?? '');



        // ----------------------Email Change-----------
        if (empty($new_email) || !is_email($new_email)) {
            $errors['email'] = 'Please enter a valid email address.';
        } elseif (email_exists($new_email) && $new_email !== $current_user->user_email) {
            $errors['email'] = 'This email is already in use by another account.';
        }

        if (empty($company_name)) {
            $errors[] = 'Company Name is required.';
        }

        // 	------------Passowrd Change----------
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $errors[] = 'Please fill in all password fields.';
            } else {
                if (!wp_check_password($current_password, $current_user->user_pass, $user_id)) {
                    $errors[] = 'Current password is incorrect.';
                } elseif ($new_password !== $confirm_password) {
                    $errors[] = 'New passwords do not match.';
                } elseif (strlen($new_password) < 6) {
                    $errors[] = 'New password must be at least 6 characters.';
                }
            }
        }


        if (empty($errors)) {
            // Update Company Name
            wp_update_user([
                'ID' => $user_id,
                'company_name' => $company_name,
                'user_email'   => $new_email
            ]);

            update_user_meta($user_id, 'company_bio', $company_bio);


            $social_fields = [
                'linkedin'  => 'https://www.linkedin.com/in/',
                'x'         => 'https://twitter.com/',
                'instagram' => 'https://instagram.com/',
                'bluesky'   => 'https://bsky.app/profile/',
                // Add more if needed
            ];

            foreach ($social_fields as $field => $base_url) {
                if (isset($_POST[$field])) {
                    $username = trim($_POST[$field]);

                    if (!empty($username)) {
                        // Remove @ if user accidentally added it
                        $username = ltrim($username, '@');

                        // Build full URL
                        $full_url = $base_url . $username;

                        // Save the full URL in the user meta via ACF
                        update_field($field, esc_url_raw($full_url), 'user_' . $user_id);
                    } else {
                        // If empty, clear the field
                        update_field($field, '', 'user_' . $user_id);
                    }
                }
            }

            if (!empty($new_password) && empty($errors)) {
                wp_set_password($new_password, $user_id);
                wp_set_current_user($user_id); // keep user logged in
                wp_set_auth_cookie($user_id);
            }

            // Refresh user data after update
            wp_cache_delete($user_id, 'users');
            wp_cache_delete($current_user->user_login, 'userlogins');
            $current_user = get_userdata($user_id);

            // Handle profile image & banner image upload
            if (!empty($_FILES['company_logo']['name']) || !empty($_FILES['company_banner']['name'])) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';

                // Profile Image
                if (!empty($_FILES['company_logo']['name'])) {
                    $profile_id = media_handle_upload('company_logo', 0);
                    if (!is_wp_error($profile_id)) {
                        update_user_meta($user_id, 'company_logo', $profile_id);
                    }
                }

                // Banner Image
                if (!empty($_FILES['company_banner']['name'])) {
                    $banner_id = media_handle_upload('company_banner', 0);
                    if (!is_wp_error($banner_id)) {
                        update_user_meta($user_id, 'page_banner', $banner_id);
                    }
                }
            }

            $success = 'Profile updated successfully!';
        }
    }

    $company_logo_id = get_user_meta($user_id, 'company_logo', true);
    $company_banner_id = get_user_meta($user_id, 'page_banner', true);
    $company_bio = get_user_meta($user_id, 'company_bio', true);
?>



    <section class="edit__profile">
        <div class="container">
            <div class="edit__profile__wrapper">
                <h2>Edit Company <?= $company_id ?></h2>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="error">
                    <?php foreach ($errors as $error): ?>
                        <p style="color:red;"><?php echo esc_html($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <p style="color:green;"><?php echo esc_html($success); ?></p>
            <?php endif; ?>

            <?php
            function get_username_from_url($url, $base)
            {
                if (empty($url)) return '';
                return str_replace($base, '', $url);
            }
            ?>

            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('edit_profile_action', 'edit_profile_nonce'); ?>

                <div class="register__block dark">
                    <div class="input__wrapper">
                        <label for="company_name">Company Name</label>
                        <input type="text" name="company_name" value="<?php echo esc_attr($_POST['company_name'] ?? $current_user->company_name); ?>" required>
                    </div>

                    <div class="input__wrapper">
                        <label for="company_bio">Company Bio</label>
                        <textarea name="company_bio" class="author-bio" placeholder="Write a short bio for the company"><?php echo esc_textarea($_POST['company_bio'] ?? $company_bio); ?></textarea>
                    </div>

                    <?php $company_country_list = array("Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia", "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo (Congo-Brazzaville)", "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czechia (Czech Republic)", "Democratic Republic of the Congo", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini (fmr. 'Swaziland')", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Holy See", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar (formerly Burma)", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "North Macedonia", "Norway", "Oman", "Pakistan", "Palau", "Palestine State", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"); ?>
                    <div class="input__wrapper">
                        <label for="company_country">company_country</label>
                        <select name="company_country">
                            <option value="">Select your company_country</option>
                            <?php foreach ($company_country_list as $company_country): ?>
                                <option value="<?php echo esc_attr($company_country); ?>"><?php echo esc_html($company_country); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input__wrapper">
                        <label for="company_logo">Company Logo</label>
                        <div class="flex">
                            <?php if ($company_logo_id): ?>
                                <div class="upload_thumbnail" id="profile_thumbnail"><img src="<?php echo esc_url(wp_get_attachment_url($company_logo_id)); ?>"></div>
                            <?php endif; ?>

                            <div class="asset__upload__wrapper">
                                <div id="preview_profile" class="preview_profile__container"></div>
                                <div class="preview_profile_wrapper">
                                    <div class="upload__image">
                                        <input type="file" id="company_logo_input" name="company_logo" accept="image/*">
                                        <label for="company_logo_input">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="transparent" stroke="#0d0629" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 15V3" />
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                <path d="m7 10 5 5 5-5" />
                                            </svg>
                                            <span>Upload</span>
                                        </label>
                                    </div>
                                    <span class="error" data-field="image" style="color:red;"></span>
                                </div>
                            </div>
                        </div>
                        <div id="error_company_logo" class="error__container"></div>
                    </div>

                    <div class="input__wrapper">
                        <label for="company_banner">Company Banner</label>
                        <div class="flex">
                            <?php if ($company_banner_id): ?>
                                <div class="upload_thumbnail" id="banner__thumbnail"><img src="<?php echo esc_url(wp_get_attachment_url($company_banner_id)); ?>" width="200"></div>
                            <?php endif; ?>

                            <div class="asset__upload__wrapper">
                                <div id="preview_banner" class="preview_banner__container"></div>
                                <div class="preview_banner_wrapper">
                                    <div class="upload__image">
                                        <input type="file" id="company_banner_input" name="company_banner" accept="image/*">
                                        <label for="company_banner_input">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="transparent" stroke="#0d0629" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M12 15V3" />
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                                <path d="m7 10 5 5 5-5" />
                                            </svg>
                                            <span>Upload</span>
                                        </label>
                                    </div>
                                    <span class="error" data-field="image" style="color:red;"></span>
                                </div>
                            </div>
                        </div>
                        <div id="error_company_banner" class="error__container"></div>
                    </div>
                </div>

                <input type="submit" value="Update Profile" class="btn-custom btn-inverse">
            </form>
        </div>
    </section>

    <script>
        const company_banner_input = document.querySelector('#company_banner_input');
        const preview_banner = document.querySelector('#preview_banner');
        const banner__thumbnail = document.querySelector('#banner__thumbnail');
        let bannerArray = [];

        function renderBannerPreview() {
            preview_banner.innerHTML = "";
            error_company_banner.innerHTML = "";
            bannerArray.forEach((file) => {
                if (file.size > 5 * 1024 * 1024) {
                    error_company_banner.innerHTML = `${file.name} exceeds 5MB please re-upload an image below 5MB.`;
                    return;
                }
                const fileUrlBanner = URL.createObjectURL(file);
                preview_banner.innerHTML = `
            <div class="preview">
                <img src="${fileUrlBanner}" alt="${file.name}"/>
            </div>
        `;
            });
        }

        company_banner_input.addEventListener("change", (e) => {
            bannerArray = Array.from(e.target.files);
            renderBannerPreview();
            if (banner__thumbnail) banner__thumbnail.style.display = "none";
        });

        const company_logo_input = document.querySelector('#company_logo_input');
        const preview_profile = document.querySelector('#preview_profile');
        const profile__thumbnail = document.querySelector('#profile_thumbnail');
        let profileArray = [];

        function renderProfilePreview() {
            preview_profile.innerHTML = "";
            error_company_logo.innerHTML = "";
            profileArray.forEach((file) => {
                if (file.size > 5 * 1024 * 1024) {
                    error_company_logo.innerHTML = `${file.name} exceeds 5MB please re-upload an image below 5MB.`;
                    return;
                }
                const fileUrlProfile = URL.createObjectURL(file);
                preview_profile.innerHTML = `
            <div class="preview">
                <img src="${fileUrlProfile}" alt="${file.name}"/>
            </div>
        `;
            });
        }

        company_logo_input.addEventListener("change", (e) => {
            profileArray = Array.from(e.target.files);
            renderProfilePreview();
            if (profile__thumbnail) profile__thumbnail.style.display = "none";
        });

        document.querySelectorAll('.toggle-password').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const targetInput = document.getElementById(this.dataset.target);
                const icon = this.querySelector('svg');

                if (targetInput.type === 'password') {
                    targetInput.type = 'text';
                    icon.setAttribute('fill', '#000'); // Change color when visible
                } else {
                    targetInput.type = 'password';
                    icon.setAttribute('fill', 'gray'); // Back to gray
                }
            });
        });
    </script>
<?php } ?>
<?php get_footer(); ?>