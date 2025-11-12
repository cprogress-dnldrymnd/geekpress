<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_register'])) {
    $username = sanitize_user($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];

    $first_name = sanitize_user($_POST['first_name']);
    $last_name = sanitize_user($_POST['last_name']);


    $outlet = sanitize_text_field($_POST['outlet']);
    $company_bio = sanitize_text_field($_POST['company_bio']);


    $company_post = $_POST['company_post'];
    $company_post_admin = $_POST['company_post_admin'];



    $website = sanitize_text_field($_POST['website']);
    $country = sanitize_text_field($_POST['country']);
    $job = sanitize_text_field($_POST['job']);

    $email_pref = sanitize_text_field($_POST['email_pref'] ?? '');

    $toc  = isset($_POST['toc']);


    $dobmonth = sanitize_text_field($_POST['dobmonth']);
    $dobday = sanitize_text_field($_POST['dobday']);
    $dobyear = sanitize_text_field($_POST['dobyear']);

    //$display_name = sanitize_text_field($_POST['display_name']);

    $errors = [];

    if (empty($username) || empty($email) || empty($password)) {
        $errors[] = 'All fields are required.';
    } elseif (!is_email($email)) {
        $errors[] = 'Invalid email.';
    } elseif (username_exists($username) || email_exists($email)) {
        $errors[] = 'Username or email already exists.';
    }


    echo '<pre>';
    var_dump($_POST['company_post']);
    var_dump($_POST['company_post_admin']);
    echo '</pre>';


    // echo '<pre>'; print_r($_POST); echo '</pre>'; //check if 

    foreach ($company_post_admin as $company_admin) {
        echo $company_admin;
        $company_post_val = $company_post[$company_admin];
        $company_exists = get_custom_post_id_by_title($company_post_val, 'company');
        if ($company_exists != false) {
            $company_id = $company_exists;

            echo $company_id;
        }
    }

    if (empty($errors)) {
        $user_id = wp_create_user($username, $password, $email);
        if (!is_wp_error($user_id)) {

            $company_exists = get_custom_post_id_by_title($company_post, 'company');
            if ($company_exists != false) {
                $company_id = $company_exists;
            } else {
                $my_post = array(
                    'post_type' => 'company',
                    'post_title'    => wp_strip_all_tags($company_post),
                    'post_status'   => 'publish',
                    'post_author'   => $user_id,
                );
                // Insert the post into the database
                // $company_id = wp_insert_post($my_post);
            }


            update_user_meta($user_id, 'first_name', $first_name);
            update_user_meta($user_id, 'last_name', $last_name);
            update_user_meta($user_id, 'outlet', $outlet);
            update_user_meta($user_id, 'company', $company_id);
            //update_user_meta($user_id, 'company_post', $company_post);
            //update_user_meta($user_id, 'website', $website);
            //update_user_meta($user_id, 'country', $country);
            update_user_meta($user_id, 'job', $job);
            update_user_meta($user_id, 'toc', $toc);
            update_user_meta($user_id, 'email_pref', $email_pref);
            update_user_meta($user_id, 'birthday', $dobday . '/' . $dobmonth . '/' . $dobyear);

            // Prepare the user data to be updated.
            $user_data = array(
                'ID'           => $user_id,
            );

            // Update the user. wp_update_user() returns a WP_Error object on failure.
            //wp_update_user($user_data);

            //update_user_meta($user_id, 'account_status', 'pending');

            wp_mail(
                get_option('admin_email'),
                'New User Pending Approval',
                'A new user has registered and is pending approval.' . "\n\nUsername: " . $username
            );

            //wp_redirect(home_url('/registration-success'));
        } else {
            $errors[] = $user_id->get_error_message();
        }
    }
}
?>


<section class="register">
    <div class="container">

        <form method="POST" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">
            <?php wp_nonce_field('custom_register', 'custom_register_nonce'); ?>

            <div class="register__block">
                <h4>Your Details</h4>

                <div class="register__grid">
                    <div class="input__wrapper">
                        <label for="first_name">First Name*</label>
                        <input type="text" placeholder="Enter First Name" name="first_name" value="<?php echo esc_attr($_POST['first_name'] ?? ''); ?>" required>
                    </div>

                    <div class="input__wrapper">
                        <label for="last_name">Last Name*</label>
                        <input type="text" placeholder="Enter Last Name" name="last_name" value="<?php echo esc_attr($_POST['last_name'] ?? ''); ?>" required>
                    </div>

                    <div class="input__wrapper">
                        <label for="email">Email Address*</label>
                        <input type="email" id="email" name="email" placeholder="Enter Email Address" value="<?php echo esc_attr($_POST['email'] ?? ''); ?>" required>
                    </div>

                    <div class="input__wrapper dob">
                        <label for="">Date of Birth*</label>
                        <div class="grid">
                            <select name="dobday" required>
                                <option value="" hidden style="opacity: 0.6">Day</option>
                                <?php for ($d = 1; $d <= 31; $d++): ?>
                                    <option value="<?php printf('%02d', $d); ?>"><?php printf('%02d', $d); ?></option>
                                <?php endfor; ?>
                            </select>

                            <select name="dobmonth" required>
                                <option value="" hidden>Month</option>
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                    <option value="<?php printf('%02d', $m); ?>"><?php printf('%02d', $m); ?></option>
                                <?php endfor; ?>
                            </select>

                            <select name="dobyear" required>
                                <option value="" hidden>Year</option>
                                <?php for ($y = date('Y'); $y >= 1900; $y--): ?>
                                    <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                                <?php endfor; ?>
                            </select>
                            <p class="tip">Why do we need this? <img src="<?php echo get_theme_file_uri() ?>/images/info.svg" alt=""></p>
                        </div>

                    </div>

                </div>
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
                            <?php foreach ($job_list as $job): ?>
                                <option value="<?php echo esc_attr($job); ?>"><?php echo esc_html($job); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input__wrapper media-outlet">
                        <label for="outlet">Media Outlet</label>
                        <input type="text" placeholder="Enter Outlet" name="outlet" value="<?php echo esc_attr($_POST['outlet'] ?? ''); ?>">
                    </div>
                </div>
            </div>

            <?php
            $companies = get_posts(array(
                'post_type' => 'company',
                'numberposts' => -1,
            ));

            ?>
            <div class="register__block">
                <h4>Company Details *</h4>
                <div class="register__flex d-flex flex-column gap-3">
                    <div class="input__wrapper--company-fields d-flex flex-column gap-3">
                        <div class="input__wrapper--company-field d-flex flex-column gap-3">
                            <div class="input__wrapper">
                                <label for="company">Company Name</label>
                                <input list="company_post" placeholder="Enter Company" class="input-field" name="company_post[]" required>

                                <datalist id="company_post">
                                    <?php foreach ($companies as $company) { ?>
                                        <option value="<?= $company->post_title ?>">
                                        <?php } ?>
                                </datalist>
                            </div>

                            <div class="input__wrapper checkbox p-0">
                                <label>
                                    <input type="checkbox" class="input-checkbox" name="company_post_admin[]" value="0">
                                    <span class="checkbox-label"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M20 6 9 17l-5-5"></path>
                                        </svg></span>
                                    Request to be company admin
                                </label>
                            </div>
                        </div>

                    </div>
                    <div class="add-company-holder w-100">
                        <div class="btn-custom btn-add-company btn-outline mb-5"> Add another company </div>
                    </div>
                </div>
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
                <h4>Log In Details</h4>
                <div class="register__grid d-flex">
                    <div class="input__wrapper w-100">
                        <label for="username">Username*</label>
                        <input type="text" id="username" placeholder="Enter Username" name="username" value="<?php echo esc_attr($_POST['username'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="register__grid">

                    <div class="input__wrapper">
                        <label for="password">Password*</label>
                        <input type="password" id="password" placeholder="Enter Password" name="password" value="<?php echo esc_attr($_POST['password'] ?? ''); ?>" required>
                    </div>

                    <div class="input__wrapper">
                        <label for="confirm_password">Confirm Password*</label>
                        <input type="password" id="confirm_password" placeholder="Enter Password" name="confirm_password" value="<?php echo esc_attr($_POST['confirm_password'] ?? ''); ?>" required>
                    </div>
                </div>
            </div>

            <div class="input__wrapper toc checkbox p-0">
                <label for="toc">
                    <input type="checkbox" id="toc" name="toc" required <?php checked($_POST['toc'] ?? '', 1); ?>>
                    <span class="checkbox-label"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6 9 17l-5-5" />
                        </svg></span>
                    I have read and agree to the <a href="https://geekpress.theprogressteam.com/terms-of-service/" target="_blank">Terms and Conditions</a> and <a href="https://geekpress.theprogressteam.com/privacy-policy/" target="_blank">Privacy Policy</a>.
                </label>
            </div>

            <input type="submit" name="custom_register" value="Register" class="btn-custom btn-outline mb-5">

            <?php

            if (!empty($errors)) {
                foreach ($errors as $error) {
                    echo '<p style="color:red;">' . esc_html($error) . '</p>';
                }
            }


            ?>
        </form>
    </div>
</section>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const jobSelect = document.querySelector('select[name="job"]');
        const mediaOutletDiv = document.querySelector('.media-outlet');

        // Hide initially
        if (mediaOutletDiv) mediaOutletDiv.style.display = 'none';

        // Watch for changes
        if (jobSelect) {
            jobSelect.addEventListener('change', function() {
                if (this.value === 'Media (Journalist/Content Creator)') {
                    mediaOutletDiv.style.display = 'block';
                } else {
                    mediaOutletDiv.style.display = 'none';
                }
            });
        }
    });
</script>

<script>
    jQuery(document).ready(function() {
        jQuery('input[name="company_post"]').change(function(e) {
            company_field(jQuery(this).val());
            e.preventDefault();
        });

        jQuery('input[name="company_post"]').keyup(function(e) {
            company_field(jQuery(this).val());
            e.preventDefault();
        });

        add_company();
    });

    function add_company() {
        jQuery('.btn-add-company').click(function(e) {
            $input = jQuery('.input__wrapper--company-field:last-child').clone();
            $input.find('.input-field').val('');
            $val = $input.find('.input-checkbox').val();

            $val = parseInt($val) + 1;
            $input.find('.input-checkbox').val($val);

            $input.appendTo('.input__wrapper--company-fields');
            e.preventDefault();
        });
    }

    function company_field(val) {
        exists = jQuery('#company_post option[value="' + val + '"]').length;
        if (exists == 1) {
            jQuery('.input__wrapper--company-fields').hide().removeAttr('required');
        } else {
            jQuery('.input__wrapper--company-fields').show().attr('required');
        }
    }
</script>