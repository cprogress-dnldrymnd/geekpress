<?php 
//Deprecated
function custom_user_extra_fields($user) { 
    
        $current_job = get_user_meta($user->ID, 'job', true);
        $current_country = get_user_meta($user->ID, 'country', true);
        
        $job_list = ['Developer', 'Designer', 'Manager', 'Writer', 'Marketer'];
        $country_list = ['Country A', 'Country B', 'Country C', 'Country D', 'Country E'];

        $current_day = get_user_meta($user->ID, 'dobday', true);
        $current_month = get_user_meta($user->ID, 'dobmonth', true);
        $current_year = get_user_meta($user->ID, 'dobyear', true);
        $show_name = get_user_meta($user->ID, 'show_name', true);
    ?>

    

    <h3>Custom User Info</h3>
    <table class="form-table">
 
        <tr>
            <th><label>Outlet</label></th>
            <td><input type="text" name="outlet" value="<?php echo esc_attr(get_user_meta($user->ID, 'outlet', true)); ?>"></td>
        </tr> 
        <tr>
            <th><label>Company</label></th>
            <td><input type="text" name="company" value="<?php echo esc_attr(get_user_meta($user->ID, 'company', true)); ?>"></td>
        </tr>
        <tr>
            <th><label>Website</label></th>
            <td><input type="text" name="website" value="<?php echo esc_attr(get_user_meta($user->ID, 'website', true)); ?>"></td>
        </tr>

        <tr>
            <th><label>Job</label></th>
        <td>
                <select name="job" id="job">
                    <option value="">Select Job</option>
                    <?php foreach ($job_list as $job): ?>
                        <option value="<?php echo esc_attr($job); ?>" <?php selected($current_job, $job); ?>>
                            <?php echo esc_html($job); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description">The user's job title.</p>
            </td>
        </tr>

        <tr>
            <th><label>Day</label></th>
            <td>
                <select name="dobday" >
                        <option value="" hidden="">Day</option>
                        <option value="01" <?php selected($current_day, '01'); ?> >01</option>
                        <option value="02" <?php selected($current_day, '02'); ?> >02</option>
                        <option value="03" <?php selected($current_day, '03'); ?> >03</option>
                        <option value="04" <?php selected($current_day, '04'); ?> >04</option>
                        <option value="05" <?php selected($current_day, '05'); ?> >05</option>
                        <option value="06" <?php selected($current_day, '06'); ?> >06</option>
                        <option value="07" <?php selected($current_day, '07'); ?> >07</option>
                        <option value="08" <?php selected($current_day, '08'); ?> >08</option>
                        <option value="09" <?php selected($current_day, '09'); ?> >09</option>
                        <option value="10" <?php selected($current_day, '10'); ?> >10</option>
                        <option value="11" <?php selected($current_day, '11'); ?> >11</option>
                        <option value="12" <?php selected($current_day, '12'); ?> >12</option>
                        <option value="13" <?php selected($current_day, '13'); ?> >13</option>
                        <option value="14" <?php selected($current_day, '14'); ?> >14</option>
                        <option value="15" <?php selected($current_day, '15'); ?> >15</option>
                        <option value="16" <?php selected($current_day, '16'); ?> >16</option>
                        <option value="17" <?php selected($current_day, '17'); ?> >17</option>
                        <option value="18" <?php selected($current_day, '18'); ?> >18</option>
                        <option value="19" <?php selected($current_day, '19'); ?> >19</option>
                        <option value="20" <?php selected($current_day, '20'); ?> >20</option>
                        <option value="21" <?php selected($current_day, '21'); ?> >21</option>
                        <option value="22" <?php selected($current_day, '22'); ?> >22</option>
                        <option value="23" <?php selected($current_day, '23'); ?> >23</option>
                        <option value="24" <?php selected($current_day, '24'); ?> >24</option>
                        <option value="25" <?php selected($current_day, '25'); ?> >25</option>
                        <option value="26" <?php selected($current_day, '26'); ?> >26</option>
                        <option value="27" <?php selected($current_day, '27'); ?> >27</option>
                        <option value="28" <?php selected($current_day, '28'); ?> >28</option>
                        <option value="29" <?php selected($current_day, '29'); ?> >29</option>
                        <option value="30" <?php selected($current_day, '30'); ?> >30</option>
                        <option value="31" <?php selected($current_day, '31'); ?> >31</option>
                </select>
            </td>
        </tr>

        <tr>
            <th><label>Month</label></th>
            <td>
               <select name="dobmonth" >
                 <option value="" hidden="">Month</option>
                        <option value="01" <?php selected($current_month, '01'); ?> >01</option>
                        <option value="02" <?php selected($current_month, '02'); ?> >02</option>
                        <option value="03" <?php selected($current_month, '03'); ?> >03</option>
                        <option value="04" <?php selected($current_month, '04'); ?> >04</option>
                        <option value="05" <?php selected($current_month, '05'); ?> >05</option>
                        <option value="06" <?php selected($current_month, '06'); ?> >06</option>
                        <option value="07" <?php selected($current_month, '07'); ?> >07</option>
                        <option value="08" <?php selected($current_month, '08'); ?> >08</option>
                        <option value="09" <?php selected($current_month, '09'); ?> >09</option>
                        <option value="10" <?php selected($current_month, '10'); ?> >10</option>
                        <option value="11" <?php selected($current_month, '11'); ?> >11</option>
                        <option value="12" <?php selected($current_month, '12'); ?> >12</option>
                </select>  
            </td>
        </tr>

                <tr>
            <th><label>Year</label></th>
            <td>
                <select name="dobyear" >
                    <option value="" hidden>Year</option>
                    <?php for ($y = date('Y'); $y >= 1900; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php selected($current_year, $y); ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>  
            </td>
        </tr>


    <tr>
            <th><label>Country</label></th>
        <td> 
            <select name="country" id="country">
                    <option value="">Select Country</option>
                    <?php foreach ($country_list as $country): ?>
                        <option value="<?php echo esc_attr($country); ?>" <?php selected($current_country, $country); ?>>
                            <?php echo esc_html($country); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p class="description">The user's country title.</p></td>
        </tr>

        
        <tr>
            <th><label>Terms And Condition</label></th>
            <td><input type="checkbox" name="toc" value="" <?php echo get_user_meta($user->ID)['toc'][0] == 1 ? "checked" : "" ?> ></td>
        </tr>
        <tr>
           <th><label>Opt Out</label></th>
            <td>
                <input type="checkbox" name="optin" value="" <?php echo get_user_meta($user->ID)['optin'][0] == 1 ? "checked" : "" ?> > 
               
         </td>
        </tr>
        <tr>
            <th><label>Display Name</label></th>
            <td><input type="text" name="show_name" value="<?php echo esc_attr(get_user_meta($user->ID, 'show_name', true)); ?>"></td>
        </tr>


        <tr>
           <th><label for="page_banner">Upload Banner</label></th>
            <td>
                <?php 
                    $banner_id = get_user_meta($user->ID, 'page_banner', true);
                    $image_url= wp_get_attachment_image_url($banner_id, 'full'); // or 'large'
                ?>

                <?php if ($image_url): ?>
                <img src="<?php echo esc_url($image_url); ?>" style="max-width:300px; height: 300px; object-fit: cover; display:block; margin-bottom:10px;">
                <?php endif; ?>
                <input type="file" name="page_banner" id="page_banner" accept="image/*"><br>
                <span class="description">Upload a page banner image for this user.</span>
              
            </td>
        </tr>


        <tr>
           <th><label for="profile_image">Profile Image</label></th>
            <td>
                <?php 
                 
                    $profile_image_id = get_user_meta($user->ID, 'profile_image', true);
                    $profile_image_url= wp_get_attachment_image_url($profile_image_id, 'full'); 
                ?>

                <?php if ($profile_image_url): ?>
                <img src="<?php echo esc_url($profile_image_url); ?>" style="max-width:300px; display:block; margin-bottom:10px;">
                <?php endif; ?>
                <input type="file" name="profile_image" id="profile_image" accept="image/*"><br>
                <span class="description">Upload a profile image for this user.</span>
              
            </td>
        </tr>

    </table>
<?php }
add_action('show_user_profile', 'custom_user_extra_fields');
add_action('edit_user_profile', 'custom_user_extra_fields');

function save_custom_user_extra_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) return;
    update_user_meta($user_id, 'first_name', sanitize_text_field($_POST['first_name']));
    update_user_meta($user_id, 'last_name', sanitize_text_field($_POST['last_name']));

    update_user_meta($user_id, 'outlet', sanitize_text_field($_POST['outlet']));

    update_user_meta($user_id, 'company', sanitize_text_field($_POST['company']));
    update_user_meta($user_id, 'website', sanitize_text_field($_POST['website']));

    update_user_meta($user_id, 'job', sanitize_text_field($_POST['job']));
    update_user_meta($user_id, 'country', sanitize_text_field($_POST['country']));

    update_user_meta($user_id, 'dobday', sanitize_text_field($_POST['dobday']));
    update_user_meta($user_id, 'dobmonth', sanitize_text_field($_POST['dobmonth']));
    update_user_meta($user_id, 'dobyear', sanitize_text_field($_POST['dobyear']));
    update_user_meta($user_id, 'show_name', sanitize_text_field($_POST['show_name']));



    $toc_val = isset( $_POST['toc'] ) ? 1 : 0;
    update_user_meta($user_id, 'toc', $toc_val);

    $optin_val = isset( $_POST['optin'] ) ? 1 : 0;
    update_user_meta($user_id, 'optin', $optin_val);

    
    $optout_val = isset( $_POST['optout'] ) ? 1 : 0;
    update_user_meta($user_id, 'optout', $optout_val);

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

}
add_action('personal_options_update', 'save_custom_user_extra_fields');
add_action('edit_user_profile_update', 'save_custom_user_extra_fields');




add_action('admin_footer-user-edit.php', 'fix_user_edit_enctype');
add_action('admin_footer-profile.php', 'fix_user_edit_enctype');

function fix_user_edit_enctype() {
    echo '<script>document.getElementById("your-profile").setAttribute("enctype", "multipart/form-data");</script>';
}