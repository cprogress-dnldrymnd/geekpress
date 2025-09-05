<?php
/*
Template Name: Forgot Password
*/
get_header();
?>

<style>
	.page-forgot-password {
		background:#f5f5f5;
		padding: 8rem 0;
		text-align:center;
	}
	.forgot-password-page{
		display:grid;
		place-items:center;
		text-align:center;
	}
	.login__action{
		display:flex;
		justify-content:center;
	}
</style>

<section class="page-forgot-password">
	<div class="container">
    <div class="wrapper-forgot-password">

        <?php
        if (isset($_POST['forgot_email'])) {
            $email = sanitize_email($_POST['forgot_email']);

            if (email_exists($email)) {
                $user = get_user_by('email', $email);
                $reset_key = get_password_reset_key($user);

                if (!is_wp_error($reset_key)) {
                    $reset_url = site_url('/reset-password/?key=' . $reset_key . '&login=' . rawurlencode($user->user_login));
                    wp_mail(
                        $email,
                        'Password Reset Request',
                        'Hello, click the link below to reset your password: ' . $reset_url
                    );

                    echo '<p class="success">Check your email for the reset link.</p>';
                } else {
                    echo '<p class="error">Error generating reset link. Try again.</p>';
                }
            } else {
                echo '<p class="error">Email address not found.</p>';
            }
        }
        ?>

		<section class="forgot-password-page">
			<div class="register__block" style="margin-bottom:0;">
				<h1>Forgot Your Password?</h1>
				<p>Please enter your email address. We will send you a reset link.</p>

				<form method="post" class="forgot-password-form">
					<div class="input__wrapper">
						<label for="forgot_email">Email Address</label>
						<input type="email" name="forgot_email" id="forgot_email" style="text-align:center;" required>
					</div>

					<div class="login__action mb-5">
						<input type="submit" name="forgot_submit" value="Send Reset Link" class="btn-custom btn-outline">
					</div>
				</form>

				<a href="<?php echo home_url(); ?>">Back to Home</a>
			</div>
		</section>
    </div>
	</div>
</section>

<?php get_footer(); ?>