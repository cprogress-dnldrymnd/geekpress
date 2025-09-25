<?php

/**
 * Template Name: Edit Company
 */

if (! is_user_logged_in()) {
    wp_redirect(wp_login_url());
    exit;
}

get_header();

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_profile_nonce']) && wp_verify_nonce($_POST['edit_profile_nonce'], 'edit_profile_action')) {

    $display_name = sanitize_text_field($_POST['display_name'] ?? '');
    $author_bio   = sanitize_textarea_field($_POST['author_bio'] ?? '');
    $new_email    = sanitize_email($_POST['email'] ?? '');



    // ----------------------Email Change-----------
    if (empty($new_email) || !is_email($new_email)) {
        $errors['email'] = 'Please enter a valid email address.';
    } elseif (email_exists($new_email) && $new_email !== $current_user->user_email) {
        $errors['email'] = 'This email is already in use by another account.';
    }

    if (empty($display_name)) {
        $errors[] = 'Display Name is required.';
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
        // Update display name
        wp_update_user([
            'ID' => $user_id,
            'display_name' => $display_name,
            'user_email'   => $new_email
        ]);

        update_user_meta($user_id, 'author_bio', $author_bio);


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
        if (!empty($_FILES['profile_image']['name']) || !empty($_FILES['banner_image']['name'])) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            // Profile Image
            if (!empty($_FILES['profile_image']['name'])) {
                $profile_id = media_handle_upload('profile_image', 0);
                if (!is_wp_error($profile_id)) {
                    update_user_meta($user_id, 'profile_image', $profile_id);
                }
            }

            // Banner Image
            if (!empty($_FILES['banner_image']['name'])) {
                $banner_id = media_handle_upload('banner_image', 0);
                if (!is_wp_error($banner_id)) {
                    update_user_meta($user_id, 'page_banner', $banner_id);
                }
            }
        }

        $success = 'Profile updated successfully!';
    }
}

$profile_image_id = get_user_meta($user_id, 'profile_image', true);
$banner_image_id = get_user_meta($user_id, 'page_banner', true);
$author_bio = get_user_meta($user_id, 'author_bio', true);
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

acf_form(array(
    'field_groups' => array(1038), // Replace with your Field Group ID
    'post_id' => 'new',
    'new_post' => array(
        'post_type' => 'company', // Example post type
        'post_status' => 'pending', // Example status
    ),
    'submit_value' => 'Submit Your Post', // Button text
    'updated_message' => 'Your post has been submitted for review!', // Success message
));
?>

<section class="edit__profile">
    <div class="container">
        <div class="edit__profile__wrapper">
            <h2>Edit Company</h2>
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
                    <label for="display_name">Display Name</label>
                    <input type="text" name="display_name" value="<?php echo esc_attr($_POST['display_name'] ?? $current_user->display_name); ?>" required>
                </div>

                <div class="input__wrapper">
                    <label for="author_bio">Profile Bio</label>
                    <textarea name="author_bio" class="author-bio" placeholder="Write a short bio for your profile"><?php echo esc_textarea($_POST['author_bio'] ?? $author_bio); ?></textarea>
                </div>

                <div class="input__wrapper">
                    <label for="profile_image">Profile Image</label>
                    <div class="flex">
                        <?php if ($profile_image_id): ?>
                            <div class="upload_thumbnail" id="profile_thumbnail"><img src="<?php echo esc_url(wp_get_attachment_url($profile_image_id)); ?>"></div>
                        <?php endif; ?>

                        <div class="asset__upload__wrapper">
                            <div id="preview_profile" class="preview_profile__container"></div>
                            <div class="preview_profile_wrapper">
                                <div class="upload__image">
                                    <input type="file" id="profile_image_input" name="profile_image" accept="image/*">
                                    <label for="profile_image_input">
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
                    <div id="error_profile_image" class="error__container"></div>
                </div>

                <div class="input__wrapper">
                    <label for="banner_image">Banner Image</label>
                    <div class="flex">
                        <?php if ($banner_image_id): ?>
                            <div class="upload_thumbnail" id="banner__thumbnail"><img src="<?php echo esc_url(wp_get_attachment_url($banner_image_id)); ?>" width="200"></div>
                        <?php endif; ?>

                        <div class="asset__upload__wrapper">
                            <div id="preview_banner" class="preview_banner__container"></div>
                            <div class="preview_banner_wrapper">
                                <div class="upload__image">
                                    <input type="file" id="banner_image_input" name="banner_image" accept="image/*">
                                    <label for="banner_image_input">
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
                    <div id="error_banner_image" class="error__container"></div>
                </div>

                <div class="register__block">
                    <h4 style="margin-bottom:0">
                        Social Media
                    </h4>
                    <p style="font-size:14px; color:gray; margin-top:5px">
                        Enter your social media details below
                    </p>
                </div>

                <div class="input__wrapper">
                    <label for="linkedin">LinkedIn</label>
                    <div class="input-group">
                        <span class="prefix">@</span>
                        <input
                            type="text"
                            name="linkedin"
                            value="<?php
                                    $linkedin_full = $_POST['linkedin'] ?? get_field('linkedin', 'user_' . $user_id);
                                    echo esc_attr(get_username_from_url($linkedin_full, 'https://www.linkedin.com/in/'));
                                    ?>"
                            <?php echo isset($errors['linkedin']) ? 'class="error-field"' : ''; ?>>
                    </div>
                    <?php if (isset($errors['linkedin'])): ?>
                        <p style="color:red;"><?php echo esc_html($errors['linkedin']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="input__wrapper">
                    <label for="x">X (Twitter)</label>
                    <div class="input-group">
                        <span class="prefix">@</span>
                        <input
                            type="text"
                            name="x"
                            value="<?php
                                    $x_full = $_POST['x'] ?? get_field('x', 'user_' . $user_id);
                                    echo esc_attr(get_username_from_url($x_full, 'https://twitter.com/'));
                                    ?>"
                            <?php echo isset($errors['x']) ? 'class="error-field"' : ''; ?>>
                    </div>
                    <?php if (isset($errors['x'])): ?>
                        <p style="color:red;"><?php echo esc_html($errors['x']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="input__wrapper">
                    <label for="instagram">Instagram</label>
                    <div class="input-group">
                        <span class="prefix">@</span>
                        <input
                            type="text"
                            name="instagram"
                            value="<?php
                                    $insta_full = $_POST['instagram'] ?? get_field('instagram', 'user_' . $user_id);
                                    echo esc_attr(get_username_from_url($insta_full, 'https://instagram.com/'));
                                    ?>"
                            <?php echo isset($errors['instagram']) ? 'class="error-field"' : ''; ?>>
                    </div>
                    <?php if (isset($errors['instagram'])): ?>
                        <p style="color:red;"><?php echo esc_html($errors['instagram']); ?></p>
                    <?php endif; ?>
                </div>

                <div class="input__wrapper">
                    <label for="bluesky">Bluesky</label>
                    <div class="input-group">
                        <span class="prefix">@</span>
                        <input
                            type="text"
                            name="bluesky"
                            value="<?php
                                    $bluesky_full = $_POST['bluesky'] ?? get_field('bluesky', 'user_' . $user_id);
                                    echo esc_attr(get_username_from_url($bluesky_full, 'https://bsky.app/profile/'));
                                    ?>"
                            <?php echo isset($errors['bluesky']) ? 'class="error-field"' : ''; ?>>
                    </div>
                    <?php if (isset($errors['bluesky'])): ?>
                        <p style="color:red;"><?php echo esc_html($errors['bluesky']); ?></p>
                    <?php endif; ?>
                </div>


                <div class="register__block">
                    <h4 style="margin-bottom:0; margin-top:65px">
                        Account Management
                    </h4>
                </div>



                <div>
                    <div class="register__block dark" style="margin-bottom:0">
                        <h4 style="font-size:large">Email</h4>
                        <p style="font-size:14px; color:gray; margin-top:5px;font-style:italic">
                            If you want to change your email, simply enter the new email in the field below.
                        </p>
                    </div>
                    <div class="input__wrapper">
                        <label for="email">Current Email</label>
                        <input type="email" name="email" id="email"
                            value="<?php echo esc_attr($_POST['email'] ?? $current_user->user_email); ?>"
                            <?php echo isset($errors['email']) ? 'class="error-field"' : ''; ?>>
                    </div>

                </div>



                <div>
                    <div class="register__block dark" style="margin-bottom:0">
                        <h4 style="font-size:large">Password</h4>
                        <p style="font-size:14px; color:gray; margin-top:5px;font-style:italic">
                            To change your password, fill in the fields below.
                        </p>
                    </div>

                    <div class="input__wrapper" style="position:relative;">
                        <label for="current_password">Current Password</label>
                        <input type="password" name="current_password" id="current_password" placeholder="Enter current password">
                        <span class="toggle-password" data-target="current_password" style="position:absolute; right:10px; top:38px; cursor:pointer;">
                            <!-- Eye Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="gray" viewBox="0 0 24 24">
                                <path d="M12 5c-7.633 0-12 7-12 7s4.367 7 12 7 12-7 12-7-4.367-7-12-7zm0 12c-2.761 0-5-2.239-5-5s2.239-5 5-5 5 2.239 5 5-2.239 5-5 5zm0-8c-1.654 0-3 1.346-3 3s1.346 3 3 3 3-1.346 3-3-1.346-3-3-3z" />
                            </svg>
                        </span>
                    </div>

                    <div class="input__wrapper" style="position:relative;">
                        <label for="new_password">New Password</label>
                        <input type="password" name="new_password" id="new_password" placeholder="Enter new password">
                        <span class="toggle-password" data-target="new_password" style="position:absolute; right:10px; top:38px; cursor:pointer;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="gray" viewBox="0 0 24 24">
                                <path d="M12 5c-7.633 0-12 7-12 7s4.367 7 12 7 12-7 12-7-4.367-7-12-7zm0 12c-2.761 0-5-2.239-5-5s2.239-5 5-5 5 2.239 5 5-2.239 5-5 5zm0-8c-1.654 0-3 1.346-3 3s1.346 3 3 3 3-1.346 3-3-1.346-3-3-3z" />
                            </svg>
                        </span>
                    </div>

                    <div class="input__wrapper" style="position:relative;">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password">
                        <span class="toggle-password" data-target="confirm_password" style="position:absolute; right:10px; top:38px; cursor:pointer;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="gray" viewBox="0 0 24 24">
                                <path d="M12 5c-7.633 0-12 7-12 7s4.367 7 12 7 12-7 12-7-4.367-7-12-7zm0 12c-2.761 0-5-2.239-5-5s2.239-5 5-5 5 2.239 5 5-2.239 5-5 5zm0-8c-1.654 0-3 1.346-3 3s1.346 3 3 3 3-1.346 3-3-1.346-3-3-3z" />
                            </svg>
                        </span>
                    </div>
                </div>

            </div>

            <input type="submit" value="Update Profile" class="btn-custom btn-inverse">
        </form>
    </div>
</section>

<script>
    const banner_image_input = document.querySelector('#banner_image_input');
    const preview_banner = document.querySelector('#preview_banner');
    const banner__thumbnail = document.querySelector('#banner__thumbnail');
    let bannerArray = [];

    function renderBannerPreview() {
        preview_banner.innerHTML = "";
        error_banner_image.innerHTML = "";
        bannerArray.forEach((file) => {
            if (file.size > 5 * 1024 * 1024) {
                error_banner_image.innerHTML = `${file.name} exceeds 5MB please re-upload an image below 5MB.`;
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

    banner_image_input.addEventListener("change", (e) => {
        bannerArray = Array.from(e.target.files);
        renderBannerPreview();
        if (banner__thumbnail) banner__thumbnail.style.display = "none";
    });

    const profile_image_input = document.querySelector('#profile_image_input');
    const preview_profile = document.querySelector('#preview_profile');
    const profile__thumbnail = document.querySelector('#profile_thumbnail');
    let profileArray = [];

    function renderProfilePreview() {
        preview_profile.innerHTML = "";
        error_profile_image.innerHTML = "";
        profileArray.forEach((file) => {
            if (file.size > 5 * 1024 * 1024) {
                error_profile_image.innerHTML = `${file.name} exceeds 5MB please re-upload an image below 5MB.`;
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

    profile_image_input.addEventListener("change", (e) => {
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

<?php get_footer(); ?>