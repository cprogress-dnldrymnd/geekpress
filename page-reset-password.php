<?php
/**
 * Template Name: Reset Password
 */
get_header();
?>

<style>
    .page-reset-password {
        background:#f5f5f5;
        padding: 8rem 0;
        text-align:center;
    }
    .reset-password-page {
        display:grid;
        place-items:center;
        text-align:center;
    }
    .login__action {
        display:flex;
        justify-content:center;
    }
    .success {
        color: green;
        font-weight: bold;
        margin-bottom: 1rem;
    }
    .error {
        color: red;
        font-weight: bold;
        margin-bottom: 1rem;
    }
</style>

<section class="page-reset-password">
    <div class="container">
        <div class="reset-password-page">
            <div class="register__block" style="margin-bottom:0;">
                <h1>Reset Your Password</h1>

                <?php
                $key    = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';
                $login  = isset($_GET['login']) ? sanitize_text_field($_GET['login']) : '';
                $password_reset_success = false;

                if ($key && $login) {
                    $user = check_password_reset_key($key, $login);

                    if (!is_wp_error($user)) {
                        if (isset($_POST['reset_password'])) {
                            $pass1 = sanitize_text_field($_POST['pass1']);
                            $pass2 = sanitize_text_field($_POST['pass2']);

                            if ($pass1 === $pass2 && !empty($pass1)) {
                                reset_password($user, $pass1);
                                echo '<p class="success">Password reset successful. <a href="https://geekpress.theprogressteam.com/login/">Login here</a>.</p>';
                                $password_reset_success = true;
                            } else {
                                echo '<p class="error">Passwords do not match or are empty.</p>';
                            }
                        }

                        // Show the form only if password reset is NOT successful
                        if (!$password_reset_success) {
                            ?>
                            <form method="post" class="reset-password-form">
                                <div class="input__wrapper" style="margin-bottom:3rem">
                                    <label for="pass1">New Password</label>
                                    <input type="password" name="pass1" id="pass1" style="text-align:center" required>
                                </div>

                                <div class="input__wrapper">
                                    <label for="pass2">Confirm Password</label>
                                    <input type="password" name="pass2" id="pass2" style="text-align:center" required>
                                </div>

                                <div class="login__action mb-5">
                                    <input type="submit" name="reset_password" value="Reset Password" class="btn-custom btn-outline">
                                </div>
                            </form>
                            <?php
                        }
                    } else {
                        echo '<p class="error">Invalid or expired reset link.</p>';
                    }
                } else {
                    echo '<p class="error">Invalid reset request.</p>';
                }
                ?>

                <a href="<?php echo home_url(); ?>">Back to Home</a>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
