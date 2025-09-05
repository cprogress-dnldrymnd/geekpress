<?php
/*-----------------------------------------------------------------------------------*/
/* Template Name: Reg 
/*-----------------------------------------------------------------------------------*/
?>
<?php get_header() ?>

	<style>
	.input__wrapper.toc.checkbox {
		display: block;
		width: 100%; /* Fit the parent container */
		overflow-wrap: break-word; /* Break long words if needed */
		white-space: normal; /* Allow text to wrap */
		}
	.input__wrapper.toc.checkbox label {
			display: flex; /* Flexbox for checkbox and text alignment */
			align-items: flex-start;
			gap: 8px; /* Space between checkbox and text */
			flex-wrap: wrap; /* Allow text to wrap to next line */
		}
		.checkbox-label {
			display: inline-flex;
			align-items: center;
		}
		.input__upload {
			gap: 0;
		}
	</style>

<?php 


$job_list = ['Developer', 'Designer', 'Manager', 'Writer', 'Marketer'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_register'])) {

    $username = sanitize_user($_POST['username']);   
    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];

    
    $first_name = sanitize_user($_POST['first_name']);
    $last_name = sanitize_user($_POST['last_name']);



    $outlet = sanitize_text_field($_POST['outlet']);
    $company = sanitize_text_field($_POST['company']);
    $website = sanitize_text_field($_POST['website']);
    $country = sanitize_text_field($_POST['country']);
    $job = sanitize_text_field($_POST['job']);

    $email_pref = sanitize_text_field($_POST['email_pref'] ?? '');
	
    $toc  = isset($_POST['toc']);


    $dobmonth = sanitize_text_field($_POST['dobmonth']);
    $dobday = sanitize_text_field($_POST['dobday']);
    $dobyear = sanitize_text_field($_POST['dobyear']);

    $show_name = sanitize_text_field($_POST['show_name']);
	
	$author_bio = sanitize_textarea_field($_POST['author_bio'] ?? '');



    $errors = [];

    if (empty($username) || empty($email) || empty($password) ) {
        $errors[] = 'All fields are required.';
    } elseif (!is_email($email)) {
        $errors[] = 'Invalid email.';
    } elseif (username_exists($username) || email_exists($email)) {
        $errors[] = 'Username or email already exists.';
    } elseif (empty($show_name) ) {
        $errors[] = 'Display Name is required';
    }


    // echo '<pre>'; print_r($_POST); echo '</pre>'; //check if 


    if (empty($errors)) {
        $user_id = wp_create_user($username, $password, $email);
        if (!is_wp_error($user_id)) {

        update_user_meta($user_id, 'first_name', $first_name);
        update_user_meta($user_id, 'last_name', $last_name);
        update_user_meta($user_id, 'outlet', $outlet);
        update_user_meta($user_id, 'company', $company);
        update_user_meta($user_id, 'website', $website);
        update_user_meta($user_id, 'country', $country);
        update_user_meta($user_id, 'job', $job);
        update_user_meta($user_id, 'toc', $toc);
       	update_user_meta($user_id, 'email_pref', $email_pref);

        update_user_meta($user_id, 'dobmonth', $dobmonth);
        update_user_meta($user_id, 'dobday', $dobday);
        update_user_meta($user_id, 'dobyear', $dobyear);
        update_user_meta($user_id, 'show_name', $show_name);
		update_user_meta($user_id, 'author_bio', $author_bio);

         if (!empty($_FILES['page_banner']['name']) || !empty($_FILES['profile_image']['name'])) {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                require_once ABSPATH . 'wp-admin/includes/media.php';
                require_once ABSPATH . 'wp-admin/includes/image.php';

                $attachment_id = media_handle_upload('page_banner', 0);
                if (!is_wp_error($attachment_id)) {
                    update_user_meta($user_id, 'page_banner', $attachment_id);
                }

                $profile_image_id = media_handle_upload('profile_image', 0);
                if (!is_wp_error($profile_image_id)) {
                    update_user_meta($user_id, 'profile_image', $profile_image_id);
                }

            }
		
		update_user_meta($user_id, 'account_status', 'pending');

		wp_mail(
			get_option('admin_email'),
			'New User Pending Approval',
			'A new user has registered and is pending approval.' . "\n\nUsername: " . $user_login
		);

        wp_redirect( home_url('/registration-success') );    
        
        } else {
            $errors[] = $user_id->get_error_message();
        }
    }

    
}
?>


<section class="page__banner registration">
    <div class="container">
        <div class="page__banner__wrapper">
            <h2>Account Registration</h2>
            <ul>
                <li><a href="<?php echo site_url('/')?>">Home</a></li>
                <li> ></li>
                <li>Registration</li>
            </ul>
        </div>
    </div>
</section>



<section class="register">
    <div class="container">
        <div class="register__content">
            <h3>Register Your Account</h3>
            <p>GeekPress is a trade-only service for games journalists, influencers, developers and PR managers. We provide a hude archive of press releases, artwork and release dates from from all major games publishers, updated daily.</p>
            <p>To apply for a GeekPress login, please complete the form fields below.</p>
        </div>



<form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">
    <?php wp_nonce_field('custom_register', 'custom_register_nonce'); ?>

        <div class="register__block">
            <h4>Your Details</h4>

            <div class="register__grid">
                <div class="input__wrapper">
                    <label for="first_name">First Name</label>
                    <input type="text" placeholder="Enter First Name"  name="first_name" value="<?php echo esc_attr($_POST['first_name'] ?? ''); ?>">
                </div>

                <div class="input__wrapper">
                    <label for="last_name">Last Name</label>
                    <input type="text" placeholder="Enter Last Name" name="last_name" value="<?php echo esc_attr($_POST['last_name'] ?? ''); ?>">
                </div>

                <div class="input__wrapper">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter Email Address" value="<?php echo esc_attr($_POST['email'] ?? ''); ?>">
                </div>

                <div class="input__wrapper dob">
                    <label for="">Date of Birth</label>
                    <div class="grid">
                        <select name="dobday" >
                            <option value="" hidden style="opacity: 0.6">Day</option>
                            <?php for ($d = 1; $d <= 31; $d++): ?>
                                <option value="<?php printf('%02d', $d); ?>"><?php printf('%02d', $d); ?></option>
                            <?php endfor; ?>
                        </select>
                        
                        <select name="dobmonth" >
                            <option value="" hidden>Month</option>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?php printf('%02d', $m); ?>"><?php printf('%02d', $m); ?></option>
                            <?php endfor; ?>
                        </select>  
                        
                        <select name="dobyear" >
                            <option value="" hidden>Year</option>
                            <?php for ($y = date('Y'); $y >= 1900; $y--): ?>
                                <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>
                        <p class="tip">Why do we need this? <img src="<?php echo get_theme_file_uri()?>/images/info.svg" alt=""></p>
                    </div>

                </div>
                
            </div>
        </div>

        <div class="register__block">
            <h4>What Do You Do?</h4>
            <div class="register__grid">
          
                <?php $job_list = ['Developer', 'Designer', 'Manager', 'Writer', 'Marketer'];?>

                  <div class="input__wrapper">
                    <label for="job">Job</label>
                    <select name="job" >
                        <option value="" style="opacity: 0.8">Select your job</option>
                        <?php foreach ($job_list as $job): ?>
                            <option value="<?php echo esc_attr($job); ?>"><?php echo esc_html($job); ?></option>
                        <?php endforeach; ?>
                    </select>
                  </div>

                <div class="input__wrapper">
                    <label for="outlet">Media Outlet</label>
                    <input type="text" placeholder="Enter Outlet" name="outlet" value="<?php echo esc_attr($_POST['outlet'] ?? ''); ?>">
                </div>
            </div>
        </div>


       <div class="register__block">
            <h4>Company Details</h4>
            <div class="register__grid">
                 <div class="input__wrapper">
                    <label for="company">Company</label>
                   <input type="text" placeholder="Enter Company" name="company" value="<?php echo esc_attr($_POST['company'] ?? ''); ?>">
                </div>

                <div class="input__wrapper">
                    <label for="website">Website</label>
                    <input type="text" placeholder="Enter Website URL" name="website" value="<?php echo esc_attr($_POST['website'] ?? ''); ?>">
                </div>
                <?php $country_list = array( "Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia", "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo (Congo-Brazzaville)", "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czechia (Czech Republic)", "Democratic Republic of the Congo", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini (fmr. 'Swaziland')", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Holy See", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar (formerly Burma)", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "North Macedonia", "Norway", "Oman", "Pakistan", "Palau", "Palestine State", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe" );?>
                 <div class="input__wrapper">
                    <label for="country">Country</label>
                     <select name="country" >
                        <option value="">Select your country</option>
                        <?php foreach ($country_list as $country): ?>
                            <option value="<?php echo esc_attr($country); ?>"><?php echo esc_html($country); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>


        <div class="register__block">
            <h4>Email Preferences</h4>
            <p>Most users opt to receive our email newsletter which we send out to keep you informed of the latest additions to GeekPress. You can opt out again at any time.</p>

			<div class="register__grid opt">
				<div class="input__wrapper checkbox p-0">
					<label for="optin">
						<input type="checkbox" id="optin" name="email_pref" value="optin" <?php checked($_POST['email_pref'] ?? '', 'optin'); ?>>
						<span class="checkbox-label"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>
						OPT IN
					</label>
				</div>

				<div class="input__wrapper checkbox p-0">
					<label for="optout">
						<input type="checkbox" id="optout" name="email_pref" value="optout" <?php checked($_POST['email_pref'] ?? '', 'optout'); ?>>
						<span class="checkbox-label"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>
						OPT OUT
					</label>
				</div>
			</div>
        </div>


       


        <div class="register__block">
            <h4>Profile Details</h4>
            <p class="upload__label">Upload Cover Photo <span class="upload__error" id="error__banner"></span></p>
            <div class="input__upload">
                <div id="preview__banner" class=" preview__container"></div>
                <div class="input__wrapper upload__image">
                    <input type="file" id="page_banner" name="page_banner" accept="image/*">
                    <label for="page_banner" id="label__banner">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="transparent" stroke="#0d0629" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15V3"></path><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><path d="m7 10 5 5 5-5"></path></svg>
                            <span>Upload Cover</span>
                    </label>
                </div>
            </div>                
          <p class="upload__label">Upload Profile Picture <span class="upload__error" id="error__profile"></span></p>
            <div class="input__upload">
               <div id="preview__profile" class=" profile__container"></div>
                <div class="input__wrapper upload__image">
                    <input type="file" id="profile_image" name="profile_image" accept="image/*">
                    <label for="profile_image" id="label__profile">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="transparent" stroke="#0d0629" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15V3"></path><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><path d="m7 10 5 5 5-5"></path></svg>
                        <span>Upload Profile</span>
                    </label>
                </div>
            </div>

            <div class="input__wrapper" style="margin-bottom:3rem">
                <label for="show_name">Display Name</label>
                <input type="text" placeholder="Enter Name" name="show_name" value="<?php echo esc_attr($_POST['show_name'] ?? ''); ?>">
            </div>
			
			<div class="input__wrapper" >
				<label for="author_bio">Profile Bio</label>
				<textarea name="author_bio" id="author_bio" rows="4" placeholder="Write a short bio..." style="width:100%;"><?php echo esc_textarea($_POST['author_bio'] ?? ''); ?></textarea>
			</div>
        </div>

        
        <div class="register__block">
            <h4>Log In Details</h4>

            <div class="register__grid">
                <div class="input__wrapper">
                    <label for="username">Username</label>
                    <input type="text" id="username" placeholder="Enter Username" name="username" value="<?php echo esc_attr($_POST['username'] ?? ''); ?>">
                </div>

                <div class="input__wrapper">
                    <label for="password">Password</label>
                    <input type="password" id="password"  placeholder="Enter Password" name="password"  value="<?php echo esc_attr($_POST['password'] ?? ''); ?>">
                </div>

                <div class="input__wrapper">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" placeholder="Enter Password" name="confirm_password" value="<?php echo esc_attr($_POST['confirm_password'] ?? ''); ?>" >
                </div>
            </div>
        </div>

 <div class="input__wrapper toc checkbox p-0">
             <label for="toc">
             <input type="checkbox" id="toc" name="toc" <?php checked($_POST['toc'] ?? '', 1); ?>>
                <span class="checkbox-label"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><path d="M20 6 9 17l-5-5"/></svg></span>
					 I have read and agree to the <a href="#">Terms and Conditions</a> and <a href="#">Privacy Policy</a>
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

const inputProfile = document.querySelector('#profile_image');
const previewProfile = document.querySelector('#preview__profile');
const errorProfile = document.querySelector('#error__profile');
let filesFeatArray = [];

function renderProfilePreview(e) {
    errorProfile.innerHTML = "";
    previewProfile.innerHTML = "";
    filesFeatArray.forEach((file, index) => {
         if (file.size > 5 * 1024 * 1024) {
                errorProfile.innerHTML = ` - ${file.name} exceeds 5MB and will be ignored.`; 
                return;
            }
    const fileUrlFeat = URL.createObjectURL(file);
    previewProfile.innerHTML = `
        <div class="preview">
            <img src="${fileUrlFeat}" alt="${file.name}"/>
            <h5>${file.name}</h5>
            <ul>
                <li><small>${(file.size / 1024).toFixed(2)} KB</small></li>
            </ul>
        <div>
    `;
});
}

inputProfile.addEventListener("change", (e) => {
    filesFeatArray = Array.from(e.target.files);
    renderProfilePreview();
   
} );




const inputBanner = document.querySelector('#page_banner');
const previewBanner = document.querySelector('#preview__banner');
const errorBanner = document.querySelector('#error__banner');
let filesFeatArrayBanner = [];

function renderBannerPreview(e) {
    errorBanner.innerHTML = "";
    previewBanner.innerHTML = "";
    filesFeatArrayBanner.forEach((file, index) => {
         if (file.size > 5 * 1024 * 1024) {
                errorBanner.innerHTML = ` - ${file.name} exceeds 5MB and will be ignored.`; 
                return;
            }
    const fileUrlFeat = URL.createObjectURL(file);
    previewBanner.innerHTML = `
        <div class="preview">
            <img src="${fileUrlFeat}" alt="${file.name}"/>
            <h5>${file.name}</h5>
            <ul>
                <li><small>${(file.size / 1024).toFixed(2)} KB</small></li>
            </ul>
        <div>
    `;
});
}

inputBanner.addEventListener("change", (e) => {
    filesFeatArrayBanner = Array.from(e.target.files);
    renderBannerPreview();
   
} );

document.addEventListener('DOMContentLoaded', () => {
    const optIn = document.querySelector('#optin');
    const optOut = document.querySelector('#optout');

    optIn.addEventListener('change', () => {
        if (optIn.checked) optOut.checked = false;
    });
    optOut.addEventListener('change', () => {
        if (optOut.checked) optIn.checked = false;
    });
});




</script>


<?php get_footer() ?>