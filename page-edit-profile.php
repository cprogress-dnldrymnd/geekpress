<?php

/**
 * Template Name: Edit Profile
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
    $email_pref = sanitize_text_field($_POST['email_pref'] ?? '');



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
        update_user_meta($user_id, 'email_pref', $email_pref);

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

        $success = 'Profile updated successfully!';
    }
}

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

<section class="edit__profile">
    <div class="container">
        <div class="edit__profile__wrapper">
            <h2>Edit Profile</h2>
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


                <div class="register__block">
                    <h4>Email Preferences</h4>
                    <p>If you’d like to be kept up to date with the latest news in geek culture, then simply tick ‘Opt in’ below. You can unsubscribe at any time.</p>

                    <div class="register__grid opt">
                        <div class="input__wrapper radio p-0">
                            <label for="optin">
                                <input type="radio" id="optin" name="email_pref" value="optin" <?php checked($_POST['email_pref'] ?? '', 'optin'); ?>>
                                OPT IN
                            </label>
                        </div>

                        <div class="input__wrapper radio p-0">
                            <label for="optout">
                                <input type="radio" id="optout" name="email_pref" value="optout" <?php checked($_POST['email_pref'] ?? '', 'optout'); ?>>
                                OPT OUT
                            </label>
                        </div>
                    </div>
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
                        <p style="font-size:14px; color:gray; margin-top:5px;">
                            If you want to change your email, simply enter the new email in the field below
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
                        <p style="font-size:14px; color:gray; margin-top:5px;">
                            To change your password, fill in the fields below
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