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

    .upload_thumbnail:not([style="display: none;"])+.asset__upload__wrapper .preview_profile_wrapper,
    .upload_thumbnail:not([style="display: none;"])+.asset__upload__wrapper .preview_banner_wrapper {
        display: none !important;
    }
</style>
<?php
$user_id = get_current_user_id();
$company_id = $_GET['id'];


if (is_company_manager(get_current_user_id(), $company_id)  && isset($_GET['id'])) {
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

        // Handle profile image & banner image upload
        if (!empty($_FILES['company_logo']['name']) || !empty($_FILES['company_banner']['name'])) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            // Profile Image
            if (!empty($_FILES['company_logo']['name'])) {
                $company_logo = media_handle_upload('company_logo', 0);
                set_post_thumbnail($company_id, $company_logo);
            }

            // Banner Image
            if (!empty($_FILES['company_banner']['name'])) {
                $banner_id = media_handle_upload('company_banner', 0);
                update_field('banner', $banner_id, $company_id);
            }
        }

        if ($_POST['remove_company_logo'] == 'yes') {
            delete_post_thumbnail($company_id);
        }

        if ($_POST['remove_company_banner'] == 'yes') {
            update_field('banner', '', $company_id);
        }

        $company_name = sanitize_text_field($_POST['company_name'] ?? '');
        $company_bio   = sanitize_textarea_field($_POST['company_bio'] ?? '');
        $company_country   = sanitize_textarea_field($_POST['company_country'] ?? '');


        if (empty($company_name)) {
            $errors[] = 'Company Name is required.';
        }

        if (empty($company_country)) {
            $errors[] = 'Company Country is required.';
        }

        if (empty($errors)) {
            $my_post = array(
                'ID'           => $company_id,
                'post_title'   => $company_name,
                'post_content' => $company_bio,
                'meta_input' => array(
                    'country' => $company_country,
                )
            );

            // Update the post into the database
            wp_update_post($my_post);


            $success = 'Company Profile updated successfully!';
        }
    }

    $company_name = get_the_title($company_id);
    $company_logo_id = get_post_thumbnail_id($company_id);
    $company_banner_id = get_field('banner', $company_id);
    $company_country_val = get_field('country', $company_id);
    $company_bio = wp_strip_all_tags(get_the_content(NULL, FALSE, $company_id));
?>



    <section class="edit__profile">
        <div class="container">
            <div class="edit__profile__wrapper">
                <h2>Edit Company Profile</h2>
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
                        <input type="text" name="company_name" value="<?php echo esc_attr($_POST['company_name'] ?? $company_name); ?>" required>
                    </div>

                    <div class="input__wrapper">
                        <label for="company_bio">Company Bio</label>
                        <textarea name="company_bio" class="author-bio" placeholder="Write a short bio for the company"><?php echo esc_textarea($_POST['company_bio'] ?? $company_bio); ?></textarea>
                    </div>

                    <?php $company_country_list = array("Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia", "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo (Congo-Brazzaville)", "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czechia (Czech Republic)", "Democratic Republic of the Congo", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini (fmr. 'Swaziland')", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Holy See", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar (formerly Burma)", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "North Macedonia", "Norway", "Oman", "Pakistan", "Palau", "Palestine State", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"); ?>
                    <div class="input__wrapper">
                        <label for="company_country">Company Country</label>
                        <select name="company_country">
                            <option value="">Select your country</option>
                            <?php foreach ($company_country_list as $company_country): ?>
                                <option <?= $company_country == $company_country_val ? 'selected' : '' ?> value="<?php echo esc_attr($company_country); ?>"><?php echo esc_html($company_country); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <input type="hidden" name="remove_company_logo" id="remove_company_logo" value="no">
                    <input type="hidden" name="remove_company_banner" id="remove_company_banner" value="no">
                    <div class="input__wrapper">
                        <label for="company_logo">Company Logo</label>
                        <div class="flex">
                            <?php if ($company_logo_id): ?>
                                <div class="upload_thumbnail" id="company_logo"><img src="<?php echo esc_url(wp_get_attachment_url($company_logo_id)); ?>">
                                    <button class="remove-button" type="button" onclick="removeLogo()"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-icon lucide-x">
                                            <path d="M18 6 6 18" />
                                            <path d="m6 6 12 12" />
                                        </svg></button>
                                </div>
                            <?php endif; ?>

                            <div class="asset__upload__wrapper">
                                <div id="preview_profile" class="preview_profile__container"></div>
                                <div class="preview_profile_wrapper">
                                    <div class="upload__image">
                                        <input type="file" id="company_logo_input" name="company_logo" accept="image/*">

                                        <label for="company_logo_input">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="transparent" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                                <div class="upload_thumbnail" id="banner__thumbnail"><img src="<?php echo esc_url(wp_get_attachment_url($company_banner_id)); ?>" width="200">
                                    <button class="remove-button" type="button" onclick="removeBanner()"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-icon lucide-x">
                                            <path d="M18 6 6 18" />
                                            <path d="m6 6 12 12" />
                                        </svg></button>
                                </div>
                            <?php endif; ?>

                            <div class="asset__upload__wrapper">
                                <div id="preview_banner" class="preview_banner__container"></div>
                                <div class="preview_banner_wrapper">
                                    <div class="upload__image">
                                        <input type="file" id="company_banner_input" name="company_banner" accept="image/*">
                                        <label for="company_banner_input">
                                            <svg width="15" height="15" viewBox="0 0 24 24" fill="transparent" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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

                <input type="submit" value="Update Company" class="btn-custom btn-inverse">
            </form>
        </div>
    </section>
    <script>
        function removeLogo() {
            jQuery('#company_logo').remove();
            jQuery('#remove_company_logo').val('yes');
        }

        function removeLogoUpload() {
            jQuery('#preview_profile .preview').remove();
            jQuery('.preview_profile_wrapper').show();
            jQuery('#remove_company_logo').val('yes');

        }

        function removeBanner() {
            jQuery('#banner__thumbnail').remove();
            jQuery('#remove_company_banner').val('yes');
        }

        function removeBannerUpload() {
            jQuery('#preview_banner .preview').remove();
            jQuery('.preview_banner_wrapper').show();
            jQuery('#remove_company_banner').val('yes');
        }
    </script>
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
                 <button class="remove-button" type="button" onclick="removeBannerUpload()"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-icon lucide-x">
                                            <path d="M18 6 6 18" />
                                            <path d="m6 6 12 12" />
                                        </svg></button>
            </div>
        `;

                jQuery('.preview_banner_wrapper').hide();
                jQuery('#remove_company_banner').val('no');
            });
        }

        company_banner_input.addEventListener("change", (e) => {
            bannerArray = Array.from(e.target.files);
            renderBannerPreview();
            if (banner__thumbnail) banner__thumbnail.style.display = "none";
        });

        const company_logo_input = document.querySelector('#company_logo_input');
        const preview_profile = document.querySelector('#preview_profile');
        const profile__thumbnail = document.querySelector('#company_logo');
        let profileArray = [];

        function renderLogo() {
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
                 <button class="remove-button" type="button" onclick="removeLogoUpload()"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-icon lucide-x">
                                            <path d="M18 6 6 18" />
                                            <path d="m6 6 12 12" />
                                        </svg></button>
            </div>
        `;
            });

            jQuery('.preview_profile_wrapper').hide();
            jQuery('#remove_company_logo').val('no');


        }

        company_logo_input.addEventListener("change", (e) => {
            profileArray = Array.from(e.target.files);
            renderLogo();
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