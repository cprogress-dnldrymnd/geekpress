<?php 
if ( is_user_logged_in() ) {
    wp_redirect( home_url() );
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize_user($_POST['username']);
    $password = $_POST['password'];
    $remember = !empty($_POST['remember']);

    $creds = [
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => $remember
    ];

    $user = wp_signon($creds, false);

    if ( is_wp_error($user) ) {
        $error = 'Invalid username or password';
    } else {
         wp_redirect(home_url());
        exit;


        if ($remember) {
            setcookie('remembered_username', $username, time() + 30 * DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN);
        } else {
            setcookie('remembered_username', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
        }
       
    }
}

$saved_username = isset($_COOKIE['remembered_username']) ? sanitize_user($_COOKIE['remembered_username']) : '';

?>

<?php get_header() ?>


<section class="page__banner ">
    <div class="container">
        <div class="page__banner__wrapper">
            <h2>Login</h2>
            <ul>
                <li><a href=""#>Home</a></li>
                <li> ></li>
                <li>Login</li>
            </ul>
        </div>
    </div>
</section>

<section class="login">
    <div class="container">
			<div class="message--box">
					<div class="message--box-inner">
					Please log in to GeekPress to submit news
				</div>
			</div>
        <div class="login__wrapper">
    <form method="post">

                <div class="register__block">
                <div class="input__wrapper">
                    <label for="username">Username or Email Address</label>
            <input type="text" name="username" id="username" value="<?php echo esc_attr($saved_username); ?>" required>
                        
                </div>

                 <div class="input__wrapper">
                    <label for="username">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>

                <div class="login__action mb-5">
                    <input type="submit" name="custom_login" value="Log In" class="btn-custom btn-outline" >


                    <div class="input__wrapper checkbox">
                        <label for="remember">
                            <input type="checkbox" id="remember" name="remember" value="1" <?php checked($saved_username != ''); ?>>
                            <span class="checkbox-label"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><path d="M20 6 9 17l-5-5"/></svg></span>
                            Remember Me
                        </label>
                    </div>
                </div>
                <a href="<?php echo site_url('/forgot-password/'); ?>">Forgot your password?</a>

               

                <?php if (!empty($error)) : ?>
                    <div style="color: red;"><?php echo esc_html($error); ?></div>
                <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</section>

<?php get_footer()?>