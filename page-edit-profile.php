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
    $new_email    = sanitize_email($_POST['email'] ?? '');
    $email_pref = sanitize_text_field($_POST['email_pref'] ?? '');

    $first_name = sanitize_user($_POST['first_name']);
    $last_name = sanitize_user($_POST['last_name']);
    $job = sanitize_text_field($_POST['job']);
    $outlet = sanitize_text_field($_POST['outlet']);

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

        update_user_meta($user_id, 'first_name', $first_name);
        update_user_meta($user_id, 'last_name', $last_name);
        update_user_meta($user_id, 'email_pref', $email_pref);
        update_user_meta($user_id, 'outlet', $outlet);
        update_user_meta($user_id, 'job', $job);


        if (!empty($new_password) && empty($errors)) {
            wp_set_password($new_password, $user_id);
            wp_set_current_user($user_id); // keep user logged in
            wp_set_auth_cookie($user_id);
        }

        // Refresh user data after update
        wp_cache_delete($user_id, 'users');
        wp_cache_delete($current_user->user_login, 'userlogins');
        $current_user = get_userdata($user_id);
        $outlet = get_user_meta($user_id, 'outlet', true);

        $success = 'Profile updated successfully!';
    }
}

$email_pref = get_user_meta($user_id, 'email_pref', true);
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


        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('edit_profile_action', 'edit_profile_nonce'); ?>
            <?php wp_nonce_field('mailchimp_subscribe_action', 'mailchimp_subscribe_nonce'); ?>

            <div class="register__block dark">
                <div class="input__wrapper">
                    <label for="display_name">Display Name</label>
                    <input type="text" name="display_name" value="<?php echo esc_attr($_POST['display_name'] ?? $current_user->display_name); ?>" required>
                </div>

                <div class="input__wrapper">
                    <label for="first_name">First Name*</label>
                    <input type="text" placeholder="Enter First Name" name="first_name" value="<?php echo esc_attr($_POST['first_name'] ?? $current_user->first_name); ?>" required>
                </div>

                <div class="input__wrapper">
                    <label for="last_name">Last Name*</label>
                    <input type="text" placeholder="Enter Last Name" name="last_name" value="<?php echo esc_attr($_POST['last_name'] ?? $current_user->last_name); ?>" required>
                </div>



                <div class="register__block">
                    <h4>What Do You Do?</h4>
                    <div class="register__grid">
                        <?php $job_list = [
                            'Analyst',
                            'Distributor',
                            'Developer/Designer',
                            'Lifestyle/news website',
                            'Media (Journalist/Content Creator)',
                            'National newspaper',
                            'Online retailer',
                            'Outsourcing',
                            'PR/Marketing agency',
                            'PR/Marketing in-house',
                            'Regional newspaper',
                            'Retailer (Website)',
                            'Retailer (Store)',
                            'Television',
                        ]; ?>

                        <div class="input__wrapper">
                            <label for="job">Job Type*</label>
                            <select name="job" required>
                                <option value="" style="opacity: 0.8">Select your job type</option>
                                <?php foreach ($job_list as $joblist): ?>
                                    <option <?= selected($joblist, $job) ?> value="<?php echo esc_attr($joblist); ?>"><?php echo esc_html($joblist); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="input__wrapper media-outlet">
                            <label for="outlet">Media Outlet</label>
                            <input type="text" placeholder="Enter Outlet" name="outlet" value="<?php echo esc_attr($_POST['last_name'] ?? $outlet); ?>">
                        </div>
                    </div>
                </div>

                <div class="register__block">
                    <h4>Email Preferences</h4>
                    <p>If you’d like to be kept up to date with the latest news in geek culture, then simply tick ‘Opt in’ below. You can unsubscribe at any time.</p>

                    <div class="register__grid opt">
                        <div class="input__wrapper radio p-0">
                            <label for="optin">
                                <input type="radio" id="optin" name="email_pref" value="optin" <?php checked($email_pref ?? '', 'optin'); ?>>
                                OPT IN
                            </label>
                        </div>

                        <div class="input__wrapper radio p-0">
                            <label for="optout">
                                <input type="radio" id="optout" name="email_pref" value="optout" <?php checked($email_pref ?? '', 'optout'); ?>>
                                OPT OUT
                            </label>
                        </div>
                    </div>
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