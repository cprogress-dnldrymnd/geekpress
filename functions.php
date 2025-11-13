<?php
require_once get_stylesheet_directory() . '/includes/assets.php';
require_once get_stylesheet_directory() . '/includes/cpt.php';
require_once get_stylesheet_directory() . '/includes/theme-support.php';
//require_once get_stylesheet_directory() . '/includes/theme-options.php';
//require_once get_stylesheet_directory() . '/includes/custom-registration.php';
require_once get_stylesheet_directory() . '/includes/create-announcement.php';
require_once get_stylesheet_directory() . '/includes/filter-post.php';
require_once get_stylesheet_directory() . '/includes/loadmore.php';



if (!function_exists('media_handle_upload')) {
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
}


function mytheme_register_menus()
{
    register_nav_menus(array(
        'primary_menu' => __('Primary Menu', 'mytheme'),
        'left_side_menu' => __('Left Side Menu', 'mytheme'),
        'footer_menu'  => __('Footer Menu', 'mytheme')
    ));
}
add_action('init', 'mytheme_register_menus');


/*
add_action('add_meta_boxes', function () {
    add_meta_box(
        'custom_gallery_meta',
        'Post Gallery',
        'custom_gallery_meta_box_callback',
        'post', // Change to your CPT slug if needed
        'normal',
        'default'
    );
});

function custom_gallery_meta_box_callback($post)
{
    wp_nonce_field('custom_gallery_nonce_action', 'custom_gallery_nonce');

    $gallery_ids = get_post_meta($post->ID, 'gallery_images', true);
    $gallery_ids = is_array($gallery_ids) ? $gallery_ids : [];

    echo '<div id="custom-gallery-container">';
    echo '<ul id="custom-gallery-list" style="display:flex;flex-wrap:wrap;gap:10px;">';

    foreach ($gallery_ids as $id) {
        $image = wp_get_attachment_image($id, 'thumbnail');
        echo '<li data-id="' . esc_attr($id) . '" style="position:relative;">';
        echo $image;
        echo '<button type="button" class="remove-image" style="position:absolute;top:0;right:0;background:red;color:white;">×</button>';
        echo '</li>';
    }

    echo '</ul>';
    echo '<input type="hidden" id="custom_gallery_ids" name="custom_gallery_ids" value="' . esc_attr(implode(',', $gallery_ids)) . '">';
    echo '<br><button type="button" class="button" id="add-gallery-images">Add Images</button>';
    echo '</div>';
}

add_action('save_post', function ($post_id) {
    if (!isset($_POST['custom_gallery_nonce']) || !wp_verify_nonce($_POST['custom_gallery_nonce'], 'custom_gallery_nonce_action')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['custom_gallery_ids'])) {
        $ids = array_filter(array_map('intval', explode(',', $_POST['custom_gallery_ids'])));
        update_post_meta($post_id, 'gallery_images', $ids);
    }
});


add_action('admin_footer-post.php', 'custom_gallery_admin_scripts');
add_action('admin_footer-post-new.php', 'custom_gallery_admin_scripts');

function custom_gallery_admin_scripts()
{
?>
    <script>
        jQuery(document).ready(function($) {
            let frame;
            const galleryInput = $('#custom_gallery_ids');
            const galleryList = $('#custom-gallery-list');

            $('#add-gallery-images').on('click', function(e) {
                e.preventDefault();
                if (frame) {
                    frame.open();
                    return;
                }

                frame = wp.media({
                    title: 'Select Images',
                    button: {
                        text: 'Add to Gallery'
                    },
                    multiple: true
                });

                frame.on('select', function() {
                    const attachments = frame.state().get('selection').toJSON();
                    attachments.forEach(att => {
                        const id = att.id;
                        const thumb = att.sizes?.thumbnail?.url || att.url;
                        galleryList.append(`
                        <li data-id="${id}" style="position:relative;">
                            <img src="${thumb}" style="max-width:100px;">
                            <button type="button" class="remove-image" style="position:absolute;top:0;right:0;background:red;color:white;">×</button>
                        </li>
                    `);
                    });
                    updateInput();
                });

                frame.open();
            });

            galleryList.on('click', '.remove-image', function() {
                $(this).closest('li').remove();
                updateInput();
            });

            function updateInput() {
                const ids = galleryList.children('li').map(function() {
                    return $(this).data('id');
                }).get();
                galleryInput.val(ids.join(','));
            }
        });
    </script>
<?php
}





// ADD post subtitle

add_action('add_meta_boxes', 'add_subheading_meta_box');

function add_subheading_meta_box()
{
    add_meta_box(
        'subheading_meta_box',            // Meta box ID
        'Subheading',                     // Meta box Title
        'render_subheading_meta_box',     // Callback function
        'post',                           // Post type ('page' or custom post type can also be used)
        'normal',                         // Context (normal, side, advanced)
        'high'                            // Priority
    );
}

function render_subheading_meta_box($post)
{
    // Add a nonce field for security
    wp_nonce_field('save_subheading_meta_box', 'subheading_meta_box_nonce');

    // Retrieve existing value if available
    $subheading = get_post_meta($post->ID, 'post_subtitle', true);
?>

    <label for="subheading_field">Subheading:</label>
    <input type="text" id="subheading_field" name="subheading_field" value="<?php echo esc_attr($subheading); ?>" style="width: 100%;" />

<?php
}


add_action('save_post', 'save_subheading_meta_box_data');

function save_subheading_meta_box_data($post_id)
{
    if (
        !isset($_POST['subheading_meta_box_nonce']) ||
        !wp_verify_nonce($_POST['subheading_meta_box_nonce'], 'save_subheading_meta_box')
    ) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['subheading_field'])) {
        $subheading = sanitize_text_field($_POST['subheading_field']);
        update_post_meta($post_id, '_subheading', $subheading);
    }
}








// EXTERNAL LINKS
// Add meta box
function custom_post_external_links_meta_box()
{
    add_meta_box(
        'external_links_meta_box',
        'External Links',
        'custom_external_links_meta_box_callback',
        'post',
        'normal',
        'default'
    );
}
add_action('add_meta_boxes', 'custom_post_external_links_meta_box');

function custom_external_links_meta_box_callback($post)
{
    $external_links = get_post_meta($post->ID, 'external_links', true);
    if (!is_array($external_links)) $external_links = [];

    echo '<div id="external-links-container">';
    foreach ($external_links as $index => $link) {
        echo '<input type="url" name="external_links[]" value="' . esc_attr($link) . '" style="width: 100%; margin-bottom: 5px;">';
    }
    echo '</div>';
    echo '<button type="button" onclick="addExternalLinkInput()" class="button" id="add-gallery-images">Add Link</button>';

    echo '<script>
        function addExternalLinkInput() {
            var container = document.getElementById("external-links-container");
            var input = document.createElement("input");
            input.type = "url";
            input.name = "external_links[]";
            input.style = "width:100%; margin-bottom:5px;";
            container.appendChild(input);
        }
    </script>';
}

// Save the external links
function save_external_links_meta_box($post_id)
{
    if (isset($_POST['external_links'])) {
        $links = array_map('esc_url_raw', $_POST['external_links']);
        update_post_meta($post_id, 'external_links', $links);
    }
}
add_action('save_post', 'save_external_links_meta_box');
*/

// -------------Restrict Frontend User to WP--------------------

add_filter('login_redirect', function ($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles)) {
        if (!in_array('administrator', $user->roles)) {
            return home_url('/edit-profile'); // Change to your page slug
        }
    }
    return $redirect_to;
}, 10, 3);

add_action('admin_init', function () {
    // If user is not an admin and not doing AJAX
    if (!current_user_can('manage_options') && !wp_doing_ajax()) {
        wp_redirect(home_url()); // Redirect to homepage or any page
        exit;
    }
});

add_action('after_setup_theme', function () {
    if (!current_user_can('manage_options')) {
        show_admin_bar(false);
    }
});

// ---------------Account Approval------------------------

add_action('wp_authenticate_user', 'check_user_approval_status', 10, 2);
function check_user_approval_status($user, $password)
{
    $status = get_user_meta($user->ID, 'account_status', true);

    if ($status === 'pending') {
        return new WP_Error('pending_approval', 'Your account is pending approval. Please wait for admin confirmation.');
    }
    return $user;
}

add_action('show_user_profile', 'approval_user_meta_field');
add_action('edit_user_profile', 'approval_user_meta_field');
function approval_user_meta_field($user)
{
    $status = get_user_meta($user->ID, 'account_status', true);
?>
    <h3>Account Approval</h3>
    <table class="form-table">
        <tr>
            <th><label for="account_status">Status</label></th>
            <td>
                <select name="account_status" id="account_status">
                    <option value="pending" <?php selected($status, 'pending'); ?>>Pending</option>
                    <option value="approved" <?php selected($status, 'approved'); ?>>Approved</option>
                    <option value="rejected" <?php selected($status, 'rejected'); ?>>Rejected</option>
                </select>
            </td>
        </tr>
    </table>
<?php
}

add_action('personal_options_update', 'save_approval_user_meta');
add_action('edit_user_profile_update', 'save_approval_user_meta');
function save_approval_user_meta($user_id)
{
    // Ensure the current user can edit this user
    if (!current_user_can('edit_user', $user_id)) {
        return;
    }

    $old_status = get_user_meta($user_id, 'account_status', true);
    $new_status = isset($_POST['account_status']) ? sanitize_text_field($_POST['account_status']) : '';

    update_user_meta($user_id, 'account_status', $new_status);

    $user = get_userdata($user_id);
    $headers = ['Content-Type: text/plain; charset=UTF-8'];

    // Approved email
    if ($old_status !== 'approved' && $new_status === 'approved') {
        $subject = 'Your Account Has Been Approved';
        $message = "
			<p>Hi " . esc_html($user->display_name) . ",</p>
			<p>Good news! We’ve approved your application to <strong>GeekPress</strong>.</p>
			<p>You can now log in to your account using the link below:</p>
			<p>
				<a href='https://geekpress.theprogressteam.com/login/' target='_blank'>
					https://geekpress.theprogressteam.com/login/
				</a>
			</p>
			<p>Thank you,<br>" . get_bloginfo('name') . " Team</p>
		";
        wp_mail($user->user_email, $subject, $message, $headers);
    }

    // Rejected email
    if ($old_status !== 'rejected' && $new_status === 'rejected') {
        $subject = 'Your Account Has Been Rejected';
        $message = "
			<p>Hi " . esc_html($user->display_name) . ",</p>
			<p>Sorry, your application for <strong>GeekPress</strong> has not been approved as you did not meet our criteria at this time.</p>
			<p>Thank you,<br>" . get_bloginfo('name') . " Team</p>
		";
        wp_mail($user->user_email, $subject, $message, $headers);
    }
}

// --------------NOTIFY ADMIN ON NEW REGISTRATION-----------------------
add_action('user_register', 'notify_admin_new_user', 10, 1);
function notify_admin_new_user($user_id)
{
    $user = get_userdata($user_id);
    $admin_email = 'hello@geek.press';
    $subject = 'New User Registration on ' . get_bloginfo('name');
    // Use HTML formatting instead of \n
    $message = "
        <p>Hello Admin,</p>
        <p>A new user has just registered on your website:</p>
        <hr>
        <p>
			<strong>Name: </strong>{$user->display_name}<br>
			<strong>Email: </strong>{$user->user_email}
        </p>
        <hr>
        <p>
            You can review or approve this user here:<br>
            <a href='" . admin_url('user-edit.php?user_id=' . $user_id) . "'>" . admin_url('user-edit.php?user_id=' . $user_id) . "</a>
        </p>
        <p>Best regards,<br>" . get_bloginfo('name') . " Team</p>
    ";

    $headers = ['Content-Type: text/plain; charset=UTF-8'];

    wp_mail($admin_email, $subject, $message, $headers);
}


// --------------ADDING the status in the userlist-----------------------

// Add custom column
function add_user_status_column($columns)
{
    $columns['account_status'] = __('Account Status', 'textdomain');
    return $columns;
}
add_filter('manage_users_columns', 'add_user_status_column');

// Show real status in the column
function show_user_status_column($value, $column_name, $user_id)
{
    if ('account_status' === $column_name) {
        $status = get_user_meta($user_id, 'account_status', true); // Fetch meta
        if ($status === 'pending') {
            return '<span style="color:#d63638;font-weight:bold;">Pending Approval</span>';
        } elseif ($status === 'approved') {
            return '<span style="color:#2c9f45;font-weight:bold;">Approved</span>';
        } else {
            return '<span style="color:#aaa;">Not Set</span>'; // Fallback
        }
    }
    return $value;
}
add_filter('manage_users_custom_column', 'show_user_status_column', 10, 3);

// ------------Restrict login for users whose account is not approved.--------------
function restrict_login_based_on_account_status($user, $username, $password)
{
    if (is_wp_error($user)) {
        return $user; // Already has an error
    }

    if ($user instanceof WP_User) {
        $status = get_user_meta($user->ID, 'account_status', true);

        if ($status !== 'approved') {
            // Log out the user if somehow logged in
            wp_logout();

            // Redirect to the custom page
            wp_redirect(home_url('/account-pending/')); // <-- Replace with your page slug
            exit;
        }
    }

    return $user;
}
add_filter('authenticate', 'restrict_login_based_on_account_status', 30, 3);

// ----------------------PHP MAILER-----------------

// add_action('phpmailer_init', function ($phpmailer) {
//     // Force SMTP
//     $phpmailer->isSMTP();

//     // SMTP server details
//     $phpmailer->Host       = 'smtp.gmail.com'; // e.g., smtp.gmail.com
//     $phpmailer->SMTPAuth   = true;
//     $phpmailer->Port       = 587; // Usually 587 for TLS or 465 for SSL
//     $phpmailer->Username   = 'info.geekpress@gmail.com';  // Your SMTP username
//     $phpmailer->Password   = 'vsbvqrbdcwrfbwlb';     // Your SMTP password
//     $phpmailer->SMTPSecure = 'tls'; // Options: 'ssl' or 'tls'

//     // Default sender info
//     $phpmailer->From       = 'bradley@digitallydisruptive.co.uk';
//     $phpmailer->FromName   = 'GEEKPRESS';
// });

/*
add_action('admin_menu', function () {
    add_options_page(
        'Email Settings',
        'Email Settings',
        'manage_options',
        'custom-email-settings',
        'render_email_settings_page'
    );
});

// Register settings
add_action('admin_init', function () {
    register_setting('custom_email_settings', 'smtp_host');
    register_setting('custom_email_settings', 'smtp_port');
    register_setting('custom_email_settings', 'smtp_secure');
    register_setting('custom_email_settings', 'smtp_username');
    register_setting('custom_email_settings', 'smtp_password');
    register_setting('custom_email_settings', 'smtp_from_email');
    register_setting('custom_email_settings', 'smtp_from_name');
    register_setting('custom_email_settings', 'smtp_extra_recipients');
});

// Render the settings page
function render_email_settings_page()
{
?>
    <div class="wrap">
        <h1>Email Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('custom_email_settings'); ?>
            <?php do_settings_sections('custom_email_settings'); ?>

            <table class="form-table">
                <tr>
                    <th scope="row">SMTP Host</th>
                    <td><input type="text" name="smtp_host" value="<?php echo esc_attr(get_option('smtp_host', 'smtp.gmail.com')); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">SMTP Port</th>
                    <td><input type="number" name="smtp_port" value="<?php echo esc_attr(get_option('smtp_port', 587)); ?>"></td>
                </tr>
                <tr>
                    <th scope="row">SMTP Secure</th>
                    <td>
                        <select name="smtp_secure">
                            <option value="tls" <?php selected(get_option('smtp_secure'), 'tls'); ?>>TLS</option>
                            <option value="ssl" <?php selected(get_option('smtp_secure'), 'ssl'); ?>>SSL</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">SMTP Username</th>
                    <td><input type="text" name="smtp_username" value="<?php echo esc_attr(get_option('smtp_username')); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">SMTP Password</th>
                    <td><input type="password" name="smtp_password" value="<?php echo esc_attr(get_option('smtp_password')); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">From Email</th>
                    <td><input type="email" name="smtp_from_email" value="<?php echo esc_attr(get_option('smtp_from_email')); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">From Name</th>
                    <td><input type="text" name="smtp_from_name" value="<?php echo esc_attr(get_option('smtp_from_name')); ?>" class="regular-text"></td>
                </tr>
                <tr>
                    <th scope="row">Additional Recipients (comma-separated)</th>
                    <td><input type="text" name="smtp_extra_recipients" value="<?php echo esc_attr(get_option('smtp_extra_recipients')); ?>" class="regular-text"></td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

add_action('phpmailer_init', function ($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = get_option('smtp_host', 'smtp.gmail.com');
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = get_option('smtp_port', 587);
    $phpmailer->Username   = get_option('smtp_username');
    $phpmailer->Password   = get_option('smtp_password');
    $phpmailer->SMTPSecure = get_option('smtp_secure', 'tls');

    $phpmailer->From       = get_option('smtp_from_email', 'wordpress@' . $_SERVER['SERVER_NAME']);
    $phpmailer->FromName   = get_option('smtp_from_name', 'WordPress');

    // Add additional recipients
    $extra_recipients = get_option('smtp_extra_recipients', '');
    if (!empty($extra_recipients)) {
        $recipients = array_map('trim', explode(',', $extra_recipients));
        foreach ($recipients as $recipient) {
            if (is_email($recipient)) {
                $phpmailer->addAddress($recipient);
            }
        }
    }
});
*/

// -----------------------------------

add_action('personal_options_update', 'save_email_pref_checkbox');
add_action('edit_user_profile_update', 'save_email_pref_checkbox');

function save_email_pref_checkbox($user_id)
{
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    if (!empty($_POST['emailpref_optin'])) {
        $email_pref = 'optin';
    } elseif (!empty($_POST['emailpref_optout'])) {
        $email_pref = 'optout';
    } else {
        $email_pref = 'optin'; // default
    }

    update_user_meta($user_id, 'emailpref', $email_pref);
}

// --------------------Search Post Types: post, company--------------

/**
 * Join taxonomies for search.
 * This allows searching within category and tag names.
 */
function custom_search_include_taxonomies($join)
{
    global $wpdb;

    if (is_search() && !is_admin()) {
        $join .= " LEFT JOIN {$wpdb->term_relationships} tr ON ({$wpdb->posts}.ID = tr.object_id)
                   LEFT JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
                   LEFT JOIN {$wpdb->terms} t ON (tt.term_id = t.term_id)";
    }

    return $join;
}
add_filter('posts_join', 'custom_search_include_taxonomies');


/**
 * Modify WHERE clause to search in post title, content, and taxonomy names.
 * This function is modified to include both 'post' and 'company' post types.
 */
function custom_search_where($where)
{
    global $wpdb;

    if (is_search() && !is_admin()) {
        $search_term = esc_sql(get_query_var('s'));

        // This is the updated line to include both post types in the direct SQL query
        $where = " AND ({$wpdb->posts}.post_type = 'post' OR {$wpdb->posts}.post_type = 'company')
                   AND {$wpdb->posts}.post_status = 'publish'
                   AND (
                        {$wpdb->posts}.post_title LIKE '%{$search_term}%'
                        OR {$wpdb->posts}.post_content LIKE '%{$search_term}%'
                        OR t.name LIKE '%{$search_term}%'
                   )";
    }

    return $where;
}
add_filter('posts_where', 'custom_search_where');


/**
 * Avoid duplicate results when a post matches in multiple taxonomies.
 */
function custom_search_distinct($distinct)
{
    if (is_search() && !is_admin()) {
        return "DISTINCT";
    }
    return $distinct;
}
add_filter('posts_distinct', 'custom_search_distinct');


/**
 * Set the post types for the main search query.
 * This function was already correct.
 */
function custom_search_only_posts($query)
{
    if ($query->is_search() && $query->is_main_query() && !is_admin()) {
        $query->set('post_type', array('post', 'company'));
    }
}
add_action('pre_get_posts', 'custom_search_only_posts');

function current_user_url()
{
    $current_user_id = get_current_user_id();
    if ($current_user_id !== 0) {
        return get_author_posts_url($current_user_id);
    }
}
add_shortcode('current_user_url', 'current_user_url');


/**
 * Registers a shortcode to display a logout link for the current user.
 *
 * This function is designed to be placed in your theme's functions.php file or a custom plugin.
 * It uses the built-in WordPress function wp_logout_url() to securely generate the logout link.
 * The shortcode will display a link that, when clicked, logs the user out.
 */

// Define the function that will handle the shortcode.
function logout_url()
{
    // Check if the user is logged in. This shortcode should only show for logged-in users.
    if (is_user_logged_in()) {
        // Use wp_logout_url() to get the correct logout URL.
        // home_url() is used to redirect the user back to the homepage after logging out.
        return wp_logout_url(home_url());
    }

    // If the user is not logged in, return an empty string.
    return '';
}

// Register the shortcode with WordPress.
// The first parameter is the shortcode tag (e.g., [logout]), and the second is the function name.
add_shortcode('logout_url', 'logout_url');

function user_icon()
{
    ob_start(); ?>
    <div class="signup header__signup">
        <div class="dropdown">

            <?php if (is_user_logged_in()) {
                $current_user = wp_get_current_user(); ?>
                <img src="<?php echo get_theme_file_uri() ?>/images/user.svg" alt="" />
                <span class="current__user"> <?php echo  get_user_meta($current_user->ID, 'first_name', true)  ?></span>

            <?php } else { ?>
                <ul>
                    <li><a href="<?php echo esc_url(site_url('/login')) ?>" class="header-btn">Login</a></li>
                    <li><a href="<?php echo esc_url(site_url('/registration')) ?>" class="header-btn">Register</a></li>
                </ul>
            <?php } ?>

            <?php if (is_user_logged_in()) { ?>
                <?php
                $user_id = get_current_user_id();
                $companies = get_user_companies();
                ?>
                <div class="dropdown__menu">
                    <ul>
                        <?php if ($companies) { ?>
                            <li><a href="<?php echo esc_url(get_the_permalink(1687)); ?>">Manage Companies</a></li>
                        <?php } ?>
                        <li><a href="<?php echo esc_url(get_the_permalink(436)); ?>">Edit Profile</a></li>
                        <li><a href="<?php echo wp_logout_url(home_url()); ?>">Logout</a></li>
                    </ul>
                </div>
            <?php } ?>

        </div>
    </div>
<?php
    return ob_get_clean();
}
add_shortcode('user_icon', 'user_icon');

function login_page_redirect()
{
    if (is_user_logged_in() && !current_user_can('administrator') && (is_page(179) || is_page(126))) {
        wp_redirect(home_url());
        exit;
    }
}
add_action('wp_head', 'login_page_redirect');

function custom_registration()
{
    ob_start();
    include get_stylesheet_directory() . '/includes/custom-registration.php';
    return ob_get_clean();
}
add_shortcode('custom_registration', 'custom_registration');


/**
 * Post View Counter
 *
 * This PHP code snippet provides a function to count post views in WordPress.
 * It checks if the current page is a single post and then increments a custom
 * post meta field named 'post_views_count'. The view count is stored for each post.
 *
 * The code also includes a helper function to retrieve and display the view count.
 *
 * Usage:
 * 1. Add this code to your theme's functions.php file or a custom plugin.
 * 2. The `my_post_views_count` function will automatically run on single post pages.
 * 3. Use the `get_post_views` function in your theme's template files (e.g., single.php)
 * to display the view count.
 */

// Function to increment the post view count
function my_post_views_count($postID)
{
    $count_key = 'post_views_count';
    // Retrieve the current view count from post meta
    $count = get_post_meta($postID, 'post_views_count', true);

    // If no view count exists, set it to 1
    if ($count == '') {
        $count = 1;
        delete_post_meta($postID, $count_key); // Clear any old empty meta
        add_post_meta($postID, $count_key, $count);
    } else {
        // If a view count exists, increment it
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

// Add an action hook to fire the function on single post pages
// This ensures the count is updated every time a post is viewed.
function my_post_views_tracker()
{
    if (is_single()) {

        my_post_views_count(get_the_ID());
    }
}
add_action('wp_head', 'my_post_views_tracker');

function get_custom_post_id_by_title($post_title, $post_type)
{
    // get_page_by_title() is a versatile function that can be used
    // for any post type, not just 'page'. We just need to specify
    // the correct post type argument.
    $post = get_page_by_title($post_title, OBJECT, $post_type);

    // Check if a post object was returned.
    if ($post) {
        // Return the post ID.
        return $post->ID;
    }

    // If no post was found, return null.
    return false;
}


/*custom functions*/
function get__company($company_id, $link = true)
{
    if ($company_id) {
        $html = '';
        if ($link == true && is_user_logged_in()) {
            $html .= '<a href="' . get_the_permalink($company_id) . '">';
        }
        $html .= '<span class="company--name">';
        $html .= get_the_title($company_id);
        $html .= '</span>';
        if ($link == true && is_user_logged_in()) {
            $html .= '</a>';
        }
        return $html;
    }
}



function get__company_contacts($ids_only = true)
{
    if (get_post_type() == 'company') {
        $company_id = get_the_ID();
    } else  if (get_post_type() == 'post') {
        $company_id = get_field('company', get_the_ID());
    }

    $journalist = get_field('journalist', $company_id);
    if (!is_array($journalist)) {
        $journalist = [];
    }
    $company_manager = get_field('company_manager', $company_id);
    if (!is_array($company_manager)) {
        $company_manager = [];
    }

    $user_ids = array_merge($company_manager, $journalist);

    echo count($user_ids);

    $user_query = new WP_User_Query(array(
        'include' => array_unique($user_ids)
    ));

    if (! empty($user_query->get_results())) {
        if ($ids_only == true) {
            $user_arr = [];
            // Loop through the results
            foreach ($user_query->get_results() as $user) {
                $user_arr[] = $user->ID;
            }
        } else {
            $html = '<div class="company-contacts">';
            foreach ($user_query->get_results() as $user) {
                $html .= '<div class="company-contact">';

                $first_name = get_user_meta($user->ID, 'first_name', true);
                $last_name  = get_user_meta($user->ID, 'last_name', true);
                $email      = $user->user_email;
                $html .= '<div class="name">' . $first_name . ' ' . $last_name . '</div>';
                $html .= '<div class="email"><a href="mailto:' . $email . '">' . $email . '</a></div>';
                $html .= '</div>';
            }
            $html .= '</div>';
            return $html;
        }

        return $user_arr;
    } else {
        return false;
    }
}
function get__company_flag($company_id, $country_name = false)
{
    $country = get_field('country', $company_id);
    if ($country) {

        $country_code = get_country_code_by_name($country);
        return '<div class="flag">' . get__svg($country_code) . ' ' . ($country_name == true ? $country : '') . ' </div>';
    }
}

function get__company_flag_sc()
{
    return get__company_flag(get_the_ID(), true);
}
add_shortcode('get__company_flag_sc', 'get__company_flag_sc');

function get__company_posts()
{
    ob_start();
?>

    <main class="release__main">
        <?php $article = new WP_Query(array(
            'post_type' => 'post',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key'     => 'company', // **REQUIRED**
                    'value'   => get_the_ID(), // Value needs to be wrapped in quotes
                    'compare' => '=',
                ),
            ),
        ));


        if ($article->have_posts()) : while ($article->have_posts()) : $article->the_post() ?>

                <div class="release__card">
                    <a href="<?php the_permalink() ?>">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>" alt="<?php the_title(); ?>" />
                        <?php endif; ?>

                    </a>
                    <div class="release__card__content">
                        <span class="tag">
                            <?php $categories = get_the_category();
                            if (! empty($categories)) {
                                echo esc_html($categories[0]->name);
                            } ?>
                        </span>
                        <small>
                            <img src="<?php echo get_theme_file_uri() ?>/images/clock.svg" alt="" />
                            <span><?php echo get_the_date('M j, Y') ?></span>
                        </small>

                        <h3>
                            <a href="<?php the_permalink() ?>" style="color:white;">
                                <?php echo preview__title() ?>
                            </a>
                        </h3>
                        <p>
                            <?php echo wp_trim_words(get_the_excerpt(), 15) ?>

                        </p>
                        <a href="<?php echo get_the_permalink() ?>" class="btn-custom">Read More</a>
                    </div>
                </div>
        <?php endwhile;
            wp_reset_postdata();
        else:
            echo "No Post by this company";
        endif;
        ?>
    </main>
<?php
    return ob_get_clean();
}
add_shortcode('get__company_posts', 'get__company_posts');


function get_user_companies()
{
    $company = get_posts(array(
        'post_type' => 'company',
        'numberposts' => -1,
        'fields' => 'ids',
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key'     => 'journalist', // **REQUIRED**
                'value'   => '"' . get_current_user_id() . '"', // Value needs to be wrapped in quotes
                'compare' => 'LIKE',
            ),
            array(
                'key'     => 'company_manager', // **REQUIRED**
                'value'   => '"' . get_current_user_id() . '"', // Value needs to be wrapped in quotes
                'compare' => 'LIKE',
            ),
        ),
    ));

    return $company;
}



function display__company_contacts()
{
    return get__company_contacts(false);
}
add_shortcode('display__company_contacts', 'display__company_contacts');
/*end of custom functions*/

add_filter('wp_prepare_themes_for_js', function ($themes) {

    $themes['gamxo']['screenshot'][0] = get_stylesheet_directory_uri() . '/screenshot.png';
    $themes['gamxo']['name'] = 'Geekpress';
    $themes['gamxo']['authorAndUri'] = 'Digitally Disruptive';


    $themes['gamxo-child']['name'] = 'Geekpress';
    $themes['gamxo-child']['authorAndUri'] = 'Digitally Disruptive';


    return $themes;
});

/**
 * Detects if a given URL is a YouTube video, an image, or something else.
 *
 * This function checks the URL against common YouTube URL patterns and
 * common image file extensions.
 *
 * @param string $url The URL to check.
 * @return string Returns 'YouTube', 'Image', or 'Unknown'.
 */
function getLinkType($url)
{
    // --- YouTube URL Detection ---
    // This regex looks for common YouTube URL patterns, including:
    // - youtube.com/watch?v=...
    // - youtu.be/...
    // - youtube.com/embed/...
    // - youtube.com/v/...
    $youtubeRegex = '/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$/';
    if (preg_match($youtubeRegex, $url)) {
        return 'YouTube';
    }

    // --- Image URL Detection ---
    // We parse the URL and check the file extension of the path.
    $path = parse_url($url, PHP_URL_PATH);
    if ($path) {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico'];

        if (in_array($extension, $imageExtensions)) {
            return 'Image';
        }
    }


    // --- Default Case ---
    // If it's neither a YouTube link nor a recognized image format.
    return 'Unknown';
}


function getYoutubeEmbedUrl($url)
{
    $videoId = null;
    $patterns = [
        '#(?:https?://)?(?:www\.)?(?:youtube\.com/watch\?v=|youtu\.be/)([\w-]{11})#',
        '#(?:https?://)?(?:www\.)?(?:youtube\.com/embed/)([\w-]{11})#',
        '#(?:https?://)?(?:www\.)?(?:youtube\.com/v/)([\w-]{11})#',
        '#([\w-]{11})$#'
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            $videoId = $matches[1];
            break;
        }
    }

    if ($videoId) {
        return "https://www.youtube.com/embed/{$videoId}";
    }

    return false;
}

function file_type($attachment_id)
{
    // Get the MIME type of the attachment.
    $mime_type = get_post_mime_type($attachment_id);

    // Check if the MIME type indicates an image.
    if (strpos($mime_type, 'image/') === 0) {
        return 'image';
    }

    // Check if the MIME type indicates a PDF.
    if ($mime_type === 'application/pdf') {
        return 'pdf';
    }

    // If it's neither, return 'other'.
    return 'other';
}

/*posts listing */

function admin_head_css()
{
?>
    <style>
        .readonly.readonly {
            pointer-events: none;
            opacity: 0.6;
        }

        .listing-status {
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
        }

        .status-Pending {
            background-color: #f0ad4e;
            color: white;
        }

        .status-Approve {
            background-color: #5cb85c;
            color: white;
        }

        .status-Reject {
            background-color: #d9534f;
            color: white;
        }

        .listing-status-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .approve--reject {
            margin-top: 10px;
            border-top: 1px solid lightgrey;
            padding-top: 10px;
            line-height: 1;
        }

        .approve-listing {
            color: #5cb85c;
        }

        .reject-listing {
            color: #d9534f
        }
    </style>
<?php
}
add_action('admin_head', 'admin_head_css');
/**
 * Adds a 'Listing Status' column to the admin post list.
 * This can be adapted for any post type by changing the filter name.
 * For a custom post type 'listing', the filter would be 'manage_listing_posts_columns'.
 *
 * @param array $columns The existing columns.
 * @return array The modified columns.
 */
function add_listing_status_column($columns)
{
    // Add the new column after the 'title' column
    $new_columns = [];
    foreach ($columns as $key => $title) {
        $new_columns[$key] = $title;
        if ($key === 'title') {
            $new_columns['listing_status'] = __('Listing Status', 'your-text-domain');
        }
    }
    return $new_columns;
}
add_filter('manage_post_posts_columns', 'add_listing_status_column');


/**
 * Displays the content for the custom 'listing_status' column.
 * Assumes the status is stored in a post meta field with the key 'listing_status'.
 *
 * @param string $column_name The name of the column to display.
 * @param int    $post_id     The ID of the current post.
 */
function display_listing_status_column_content($column_name, $post_id)
{
?>

    <?php
    if ($column_name === 'listing_status') {
        // Get the value of the custom field
        $status = get_post_meta($post_id, 'listing_status', true);

        if (!empty($status)) {
            // Sanitize and display the value
            $status = esc_html(ucwords(str_replace('_', ' ', $status)));
        } else {
            // Display a default value if not set
            $status = 'Pending';
        }

        echo "<div class='listing-status-wrapper'>";

        echo '<span class="listing-status status-' . $status . '">' . $status . '</span>';

        if ($status === 'Pending') {
            echo '<a href="' . admin_url('post.php?post=' . $post_id . '&action=edit') . '">Review</a>';
        }
        echo "</div>";
        if ($status === 'Pending') {
            echo "<div class='listing-status-wrapper approve--reject'>";
            echo '<a class="approve-listing" href="' . admin_url('post.php?post=' . $post_id . '&action=edit&listing_status=approve') . '">Approve</a>';
            echo '<a class="reject-listing" href="' . admin_url('post.php?post=' . $post_id . '&action=edit&listing_status=reject') . '">Reject</a>';
            echo "</div>";
        }
    }
}
add_action('manage_posts_custom_column', 'display_listing_status_column_content', 10, 2);


/**
 * Makes the 'listing_status' column sortable.
 *
 * @param array $columns The existing sortable columns.
 * @return array The modified sortable columns.
 */
function make_listing_status_column_sortable($columns)
{
    $columns['listing_status'] = 'listing_status';
    return $columns;
}
add_filter('manage_edit-post_sortable_columns', 'make_listing_status_column_sortable');


/**
 * Update an ACF field on post save if a specific URL parameter is present.
 *
 * This function hooks into the 'save_post' action, which is triggered
 * whenever a post or page is created or updated. It checks for the
 * presence of a 'listing_status' URL parameter. If the parameter
 * exists, its value is sanitized and used to update a specified
 * ACF field for the post being saved.
 *
 * @param int $post_id The ID of the post being saved.
 */
function update_acf_on_post_edit_with_url_param()
{

    // 1. Set the name of the URL parameter to check for.
    $url_parameter = 'listing_status';

    // 2. Set the name of the ACF field you want to update.
    $acf_field_name = 'listing_status'; // 🚨 Replace with your actual ACF field name.


    // ---  Execution ---

    // Check if the URL parameter is set in the current request.
    if (isset($_GET[$url_parameter])) {
        $post_id = $_GET['post'];
        // Sanitize the input from the URL parameter to ensure security.
        // 'sanitize_text_field' is a good general-purpose function.
        // For other data types, consider using functions like 'absint' for integers
        // or 'sanitize_email' for emails.
        $current_value = get_field($acf_field_name, $post_id);
        $status_value = sanitize_text_field($_GET[$url_parameter]);

        if ($current_value != $status_value) {

            // Update the ACF field with the sanitized value.
            // The `update_field()` function is the recommended way to update ACF fields.
            // It requires the field name (or key), the new value, and the post ID.
            update_field($acf_field_name, $status_value, $post_id);

            if ($status_value === 'approve') {
                // Set the post status to 'publish' if approved
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_status' => 'publish'
                ));
            } elseif ($status_value === 'reject') {
                // Set the post status to 'draft' if rejected
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_status' => 'pending'
                ));
            }
    ?>
            <script>
                jQuery(document).ready(function() {
                    jQuery('.acf-field[data-name="listing_status"] select').val('<?php echo esc_js($status_value); ?>').trigger('change');
                    <?php if ($status_value === 'approve') { ?>
                        jQuery('.editor-post-publish-button__button').click();
                    <?php } ?>
                });
            </script>
        <?php
        }
    }


    //Admin Application
    // 1. Set the name of the URL parameter to check for.
    $url_parameter = 'application_status';

    // 2. Set the name of the ACF field you want to update.
    $acf_field_name = 'application_status'; // 🚨 Replace with your actual ACF field name.


    // ---  Execution ---

    // Check if the URL parameter is set in the current request.
    if (isset($_GET[$url_parameter])) {
        $post_id = $_GET['post'];
        // Sanitize the input from the URL parameter to ensure security.
        // 'sanitize_text_field' is a good general-purpose function.
        // For other data types, consider using functions like 'absint' for integers
        // or 'sanitize_email' for emails.
        $current_value = get_field($acf_field_name, $post_id);
        $status_value = sanitize_text_field($_GET[$url_parameter]);




        if ($current_value != $status_value) {

            // Update the ACF field with the sanitized value.
            // The `update_field()` function is the recommended way to update ACF fields.
            // It requires the field name (or key), the new value, and the post ID.
            update_field($acf_field_name, $status_value, $post_id);

            if ($status_value === 'approve') {
                // Set the post status to 'publish' if approved
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_status' => 'publish'
                ));
            } elseif ($status_value === 'reject') {
                // Set the post status to 'draft' if rejected
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_status' => 'pending'
                ));
            }
        ?>
            <script>
                jQuery(document).ready(function() {
                    jQuery('.acf-field[data-name="application_status"] select').val('<?php echo esc_js($status_value); ?>').trigger('change');
                    <?php if ($status_value === 'approve') { ?>
                        jQuery('.editor-post-publish-button__button').click();
                    <?php } ?>
                });
            </script>
    <?php
        }
    }
}

add_action('admin_footer', 'update_acf_on_post_edit_with_url_param');

function reject__email($post_id)
{
    /*

    $to = get_post_author_email_by_id($post_id);
    $subject = 'Geekpress rejected your submission';
    $message = email__template('Submission Rejected for ' . get_the_title($post_id), '<p>We’re really sorry, but GeekPress has rejected your submission. We appreciate that this can be frustrating, so please check our <a href="https://geekpress.theprogressteam.com/submission-guidelines/">submission guidelines</a> to understand why this has happened. If you would like more detailed reasons, then <a href="mailto:contact@geekpress.co.uk">email us</a> and we can look into it for you.<p>');
    wp_mail($to, $subject, $message);
	*/

    $to = get_post_author_email_by_id($post_id);
    $subject = 'GeekPress rejected your submission';

    // Get rejection reason field (optional field)
    $reason = get_field('rejection_reason', $post_id);

    // Default message
    $message_body = '<p>We’re really sorry, but GeekPress has rejected your submission.</p>
    <p>Please check our <a href="https://geekpress.theprogressteam.com/submission-guidelines/">submission guidelines</a> to understand why this has happened.</p>
    <p>If you would like more detailed reasons, then <a href="mailto:contact@geekpress.co.uk">email us</a> and we can look into it for you.</p>';

    // Append reason if provided
    if (!empty($reason)) {
        $message_body = '<p><strong>Reason for rejection:</strong><br>' . nl2br(esc_html($reason)) . '</p>' . $message_body;
    }

    $message = email__template('Submission Rejected for ' . get_the_title($post_id), $message_body);

    wp_mail($to, $subject, $message);
}
/**
 * Sends an email to the post author when a specific ACF field value changes.
 *
 * This function is hooked into 'acf/update_value' which runs before a field
 * value is saved to the database. This allows us to compare the new value
 * with the old one.
 *
 * Field Name: listing_status
 * Trigger Value: reject
 *
 * @param mixed $value   The new field value.
 * @param int   $post_id The ID of the post being updated.
 * @param array $field   The ACF field object.
 * @return mixed The original value to allow the update to proceed.
 */
function send_email_on_listing_rejection($value, $post_id, $field)
{

    // --- Configuration ---
    // The post type you want this to run for. Use 'any' for all post types.
    $target_post_type = 'post'; // e.g., 'post', 'page', 'your_custom_post_type'

    // --- Validation ---
    // 1. Check if the post type matches our target.
    //    If you want this to run for ANY post type, you can remove this block.
    if ($target_post_type !== 'any' && get_post_type($post_id) !== $target_post_type) {
        return $value;
    }

    // 2. Get the value of the field *before* this update.
    $old_value = get_field('listing_status', $post_id);


    // 3. Check if the new value is 'reject' and the old value was NOT 'reject'.
    //    This ensures the email is sent only when the status changes *to* reject.
    if ($value === 'reject' && $old_value !== 'reject') {
        reject__email($post_id);
    }

    // IMPORTANT: Always return the original value to allow ACF to save the field.
    return $value;
}

/**
 * We use a targeted hook 'acf/update_value/name={$field_name}' for better performance.
 * This ensures our function only runs when the 'listing_status' field is updated.
 */
add_filter('acf/update_value/name=listing_status', 'send_email_on_listing_rejection', 10, 3);

function wpse27856_set_content_type()
{
    return "text/html";
}
add_filter('wp_mail_content_type', 'wpse27856_set_content_type');



/**
 * Sync ACF 'listing_status' with WordPress post status changes.
 * If a rejected or approved post is edited and saved as draft/pending,
 * reset the ACF field to 'pending'.
 */
function sync_listing_status_with_post_status($post_id, $post, $update)
{
    // Prevent running on autosave, revisions, or direct status-change URLs
    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id) || isset($_GET['listing_status'])) {
        return;
    }

    if ($post->post_type !== 'post') {
        return;
    }

    $listing_status = get_field('listing_status', $post_id);

    // Only reset if the admin manually reverts post to Draft or Pending from the editor
    if (in_array($post->post_status, ['draft', 'pending'], true)) {
        if (in_array($listing_status, ['reject', 'approve'], true)) {
            update_field('listing_status', 'pending', $post_id);
        }
    }
}
//add_action('save_post', 'sync_listing_status_with_post_status', 10, 3);

function email__template($headline, $content)
{
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>GeekPress Email Template</title>
        <style>
            /* Basic Reset */
            body,
            table,
            td,
            a {
                -webkit-text-size-adjust: 100%;
                -ms-text-size-adjust: 100%;
            }

            table,
            td {
                mso-table-lspace: 0pt;
                mso-table-rspace: 0pt;
            }

            img {
                -ms-interpolation-mode: bicubic;
                border: 0;
                height: auto;
                line-height: 100%;
                outline: none;
                text-decoration: none;
            }

            table {
                border-collapse: collapse !important;
            }

            body {
                height: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            /* General Styles */
            body {
                background-color: #f4f4f4;
                font-family: Arial, sans-serif;
            }

            /* Responsive Styles */
            @media screen and (max-width: 600px) {
                .email-container {
                    width: 100% !important;
                    margin: auto !important;
                }

                .fluid {
                    max-width: 100% !important;
                    height: auto !important;
                    margin-left: auto !important;
                    margin-right: auto !important;
                }

                .stack-column,
                .stack-column-center {
                    display: block !important;
                    width: 100% !important;
                    max-width: 100% !important;
                    direction: ltr !important;
                }

                .stack-column-center {
                    text-align: center !important;
                }
            }
        </style>
    </head>

    <body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #f4f4f4;">
        <center style="width: 100%; background-color: #f4f4f4;">
            <!--[if (gte mso 9)|(IE)]>
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" align="center" style="background-color: #222222;">
        <tr>
        <td>
        <![endif]-->

            <!-- Main Email Wrapper -->
            <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="margin: auto; background-color: #0D0629;" class="email-container">

                <!-- BEGIN HEADER -->
                <tr>
                    <td style="padding: 20px 20px; text-align: center;">
                        <!-- LOGO: Replace with your logo URL -->
                        <img src="https://geekpress.theprogressteam.com/wp-content/uploads/2025/09/GEEK_PRESS_PNG.png" width="200" alt="GeekPress Logo" border="0" style="font-family: sans-serif; font-size: 15px; line-height: 15px; color: #ffffff;">
                    </td>
                </tr>
                <!-- END HEADER -->

                <!-- BEGIN BODY -->
                <tr>
                    <td style="background-color: #ffffff; padding: 40px 30px;">
                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                            <tr>
                                <td style="font-family: Arial, sans-serif; font-size: 24px; color: #333333; font-weight: bold; text-align: center; padding-bottom: 20px;">
                                    <!-- HEADLINE: Edit your main message here -->
                                    <?= $headline ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-family: Arial, sans-serif; font-size: 16px; line-height: 24px; color: #555555; text-align: left; padding-bottom: 30px;">
                                    <?= wpautop($content) ?>
                                </td>
                            </tr>
                            <!-- BEGIN CTA BUTTON -->
                            <tr>
                                <td align="center">
                                    <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                        <tr>
                                            <td class="button-td" style="border-radius: 5px; background: #F3FF49; text-align: center;">
                                                <!-- BUTTON: Edit link and text -->
                                                <a href="https://geekpress.theprogressteam.com/" style="background: #F3FF49; border: 15px solid #F3FF49; font-family: Arial, sans-serif; font-size: 16px; font-weight: bold; line-height: 1.1; text-align: center; text-decoration: none; display: block; border-radius: 5px; color: #0D0629;">
                                                    Visit Website
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <!-- END CTA BUTTON -->
                        </table>
                    </td>
                </tr>
                <!-- END BODY -->

                <!-- BEGIN FOOTER -->
                <tr>
                    <td style="text-align: center; padding: 30px 20px; font-family: Arial, sans-serif; font-size: 12px; line-height: 18px; color: #888888;">
                        <!-- COMPANY INFO: Edit your company details -->
                        <p style="margin: 0;">GeekPress &copy; 2025. All Rights Reserved.</p>
                        <p style="margin: 0;">International House, 64 Nile Street, London N1 7SR</p>
                        <br>
                    </td>
                </tr>
                <!-- END FOOTER -->

            </table>
            <!-- Main Email Wrapper -->

            <!--[if (gte mso 9)|(IE)]>
        </td>
        </tr>
        </table>
        <![endif]-->
        </center>
    </body>

    </html>

<?php
    return ob_get_clean();
}


/**
 * Retrieves the email address of a post's author based on the post ID.
 *
 * This function first retrieves the post data to find the author's ID.
 * It then uses that ID to fetch the author's user email from the user metadata.
 *
 * @param int $post_id The ID of the post for which to find the author's email.
 * @return string|false The author's email address on success, or false if the post is not found.
 */
function get_post_author_email_by_id($post_id)
{
    // Get the post object using the provided post ID.
    $post = get_post($post_id);

    // Check if a valid post was found. If not, return false.
    if (! $post) {
        return false;
    }

    // The 'post_author' property of the post object contains the author's user ID.
    $author_id = $post->post_author;

    // Use the author's ID to retrieve the 'user_email' metadata.
    $author_email = get_the_author_meta('user_email', $author_id);

    // Return the retrieved email address.
    return $author_email;
}

/**
 * Get the ISO 3166-1 alpha-2 country code from a country name.
 *
 * @param string $country_name The full name of the country (e.g., "United States").
 * @return string|false The two-letter country code (e.g., "US") or false if not found.
 */
function get_country_code_by_name($country_name)
{
    // A comprehensive list of ISO 3166-1 alpha-2 country codes mapped to names.
    // This array is inverted from the standard, making it efficient for name-to-code lookups.
    $countries = array(
        'Afghanistan' => 'AF',
        'Åland Islands' => 'AX',
        'Albania' => 'AL',
        'Algeria' => 'DZ',
        'American Samoa' => 'AS',
        'Andorra' => 'AD',
        'Angola' => 'AO',
        'Anguilla' => 'AI',
        'Antarctica' => 'AQ',
        'Antigua and Barbuda' => 'AG',
        'Argentina' => 'AR',
        'Armenia' => 'AM',
        'Aruba' => 'AW',
        'Australia' => 'AU',
        'Austria' => 'AT',
        'Azerbaijan' => 'AZ',
        'Bahamas' => 'BS',
        'Bahrain' => 'BH',
        'Bangladesh' => 'BD',
        'Barbados' => 'BB',
        'Belarus' => 'BY',
        'Belgium' => 'BE',
        'Belize' => 'BZ',
        'Benin' => 'BJ',
        'Bermuda' => 'BM',
        'Bhutan' => 'BT',
        'Bolivia (Plurinational State of)' => 'BO',
        'Bonaire, Sint Eustatius and Saba' => 'BQ',
        'Bosnia and Herzegovina' => 'BA',
        'Botswana' => 'BW',
        'Bouvet Island' => 'BV',
        'Brazil' => 'BR',
        'British Indian Ocean Territory' => 'IO',
        'Brunei Darussalam' => 'BN',
        'Bulgaria' => 'BG',
        'Burkina Faso' => 'BF',
        'Burundi' => 'BI',
        'Cabo Verde' => 'CV',
        'Cambodia' => 'KH',
        'Cameroon' => 'CM',
        'Canada' => 'CA',
        'Cayman Islands' => 'KY',
        'Central African Republic' => 'CF',
        'Chad' => 'TD',
        'Chile' => 'CL',
        'China' => 'CN',
        'Christmas Island' => 'CX',
        'Cocos (Keeling) Islands' => 'CC',
        'Colombia' => 'CO',
        'Comoros' => 'KM',
        'Congo' => 'CG',
        'Congo, Democratic Republic of the' => 'CD',
        'Cook Islands' => 'CK',
        'Costa Rica' => 'CR',
        'Côte d\'Ivoire' => 'CI',
        'Croatia' => 'HR',
        'Cuba' => 'CU',
        'Curaçao' => 'CW',
        'Cyprus' => 'CY',
        'Czechia' => 'CZ',
        'Denmark' => 'DK',
        'Djibouti' => 'DJ',
        'Dominica' => 'DM',
        'Dominican Republic' => 'DO',
        'Ecuador' => 'EC',
        'Egypt' => 'EG',
        'El Salvador' => 'SV',
        'Equatorial Guinea' => 'GQ',
        'Eritrea' => 'ER',
        'Estonia' => 'EE',
        'Eswatini' => 'SZ',
        'Ethiopia' => 'ET',
        'Falkland Islands (Malvinas)' => 'FK',
        'Faroe Islands' => 'FO',
        'Fiji' => 'FJ',
        'Finland' => 'FI',
        'France' => 'FR',
        'French Guiana' => 'GF',
        'French Polynesia' => 'PF',
        'French Southern Territories' => 'TF',
        'Gabon' => 'GA',
        'Gambia' => 'GM',
        'Georgia' => 'GE',
        'Germany' => 'DE',
        'Ghana' => 'GH',
        'Gibraltar' => 'GI',
        'Greece' => 'GR',
        'Greenland' => 'GL',
        'Grenada' => 'GD',
        'Guadeloupe' => 'GP',
        'Guam' => 'GU',
        'Guatemala' => 'GT',
        'Guernsey' => 'GG',
        'Guinea' => 'GN',
        'Guinea-Bissau' => 'GW',
        'Guyana' => 'GY',
        'Haiti' => 'HT',
        'Heard Island and McDonald Islands' => 'HM',
        'Holy See' => 'VA',
        'Honduras' => 'HN',
        'Hong Kong' => 'HK',
        'Hungary' => 'HU',
        'Iceland' => 'IS',
        'India' => 'IN',
        'Indonesia' => 'ID',
        'Iran (Islamic Republic of)' => 'IR',
        'Iraq' => 'IQ',
        'Ireland' => 'IE',
        'Isle of Man' => 'IM',
        'Israel' => 'IL',
        'Italy' => 'IT',
        'Jamaica' => 'JM',
        'Japan' => 'JP',
        'Jersey' => 'JE',
        'Jordan' => 'JO',
        'Kazakhstan' => 'KZ',
        'Kenya' => 'KE',
        'Kiribati' => 'KI',
        'Korea (Democratic People\'s Republic of)' => 'KP',
        'Korea, Republic of' => 'KR',
        'Kuwait' => 'KW',
        'Kyrgyzstan' => 'KG',
        'Lao People\'s Democratic Republic' => 'LA',
        'Latvia' => 'LV',
        'Lebanon' => 'LB',
        'Lesotho' => 'LS',
        'Liberia' => 'LR',
        'Libya' => 'LY',
        'Liechtenstein' => 'LI',
        'Lithuania' => 'LT',
        'Luxembourg' => 'LU',
        'Macao' => 'MO',
        'Madagascar' => 'MG',
        'Malawi' => 'MW',
        'Malaysia' => 'MY',
        'Maldives' => 'MV',
        'Mali' => 'ML',
        'Malta' => 'MT',
        'Marshall Islands' => 'MH',
        'Martinique' => 'MQ',
        'Mauritania' => 'MR',
        'Mauritius' => 'MU',
        'Mayotte' => 'YT',
        'Mexico' => 'MX',
        'Micronesia (Federated States of)' => 'FM',
        'Moldova, Republic of' => 'MD',
        'Monaco' => 'MC',
        'Mongolia' => 'MN',
        'Montenegro' => 'ME',
        'Montserrat' => 'MS',
        'Morocco' => 'MA',
        'Mozambique' => 'MZ',
        'Myanmar' => 'MM',
        'Namibia' => 'NA',
        'Nauru' => 'NR',
        'Nepal' => 'NP',
        'Netherlands' => 'NL',
        'New Caledonia' => 'NC',
        'New Zealand' => 'NZ',
        'Nicaragua' => 'NI',
        'Niger' => 'NE',
        'Nigeria' => 'NG',
        'Niue' => 'NU',
        'Norfolk Island' => 'NF',
        'Northern Mariana Islands' => 'MP',
        'North Macedonia' => 'MK',
        'Norway' => 'NO',
        'Oman' => 'OM',
        'Pakistan' => 'PK',
        'Palau' => 'PW',
        'Palestine, State of' => 'PS',
        'Panama' => 'PA',
        'Papua New Guinea' => 'PG',
        'Paraguay' => 'PY',
        'Peru' => 'PE',
        'Philippines' => 'PH',
        'Pitcairn' => 'PN',
        'Poland' => 'PL',
        'Portugal' => 'PT',
        'Puerto Rico' => 'PR',
        'Qatar' => 'QA',
        'Réunion' => 'RE',
        'Romania' => 'RO',
        'Russian Federation' => 'RU',
        'Rwanda' => 'RW',
        'Saint Barthélemy' => 'BL',
        'Saint Helena, Ascension and Tristan da Cunha' => 'SH',
        'Saint Kitts and Nevis' => 'KN',
        'Saint Lucia' => 'LC',
        'Saint Martin (French part)' => 'MF',
        'Saint Pierre and Miquelon' => 'PM',
        'Saint Vincent and the Grenadines' => 'VC',
        'Samoa' => 'WS',
        'San Marino' => 'SM',
        'Sao Tome and Principe' => 'ST',
        'Saudi Arabia' => 'SA',
        'Senegal' => 'SN',
        'Serbia' => 'RS',
        'Seychelles' => 'SC',
        'Sierra Leone' => 'SL',
        'Singapore' => 'SG',
        'Sint Maarten (Dutch part)' => 'SX',
        'Slovakia' => 'SK',
        'Slovenia' => 'SI',
        'Solomon Islands' => 'SB',
        'Somalia' => 'SO',
        'South Africa' => 'ZA',
        'South Georgia and the South Sandwich Islands' => 'GS',
        'South Sudan' => 'SS',
        'Spain' => 'ES',
        'Sri Lanka' => 'LK',
        'Sudan' => 'SD',
        'Suriname' => 'SR',
        'Svalbard and Jan Mayen' => 'SJ',
        'Sweden' => 'SE',
        'Switzerland' => 'CH',
        'Syrian Arab Republic' => 'SY',
        'Taiwan' => 'TW',
        'Tajikistan' => 'TJ',
        'Tanzania, United Republic of' => 'TZ',
        'Thailand' => 'TH',
        'Timor-Leste' => 'TL',
        'Togo' => 'TG',
        'Tokelau' => 'TK',
        'Tonga' => 'TO',
        'Trinidad and Tobago' => 'TT',
        'Tunisia' => 'TN',
        'Turkey' => 'TR',
        'Turkmenistan' => 'TM',
        'Turks and Caicos Islands' => 'TC',
        'Tuvalu' => 'TV',
        'Uganda' => 'UG',
        'Ukraine' => 'UA',
        'United Arab Emirates' => 'AE',
        'United Kingdom' => 'GB',
        'United States of America' => 'US',
        'United States Minor Outlying Islands' => 'UM',
        'Uruguay' => 'UY',
        'Uzbekistan' => 'UZ',
        'Vanuatu' => 'VU',
        'Venezuela (Bolivarian Republic of)' => 'VE',
        'Viet Nam' => 'VN',
        'Virgin Islands (British)' => 'VG',
        'Virgin Islands (U.S.)' => 'VI',
        'Wallis and Futuna' => 'WF',
        'Western Sahara' => 'EH',
        'Yemen' => 'YE',
        'Zambia' => 'ZM',
        'Zimbabwe' => 'ZW',
    );

    $sanitized_name = trim($country_name);

    if (array_key_exists($sanitized_name, $countries)) {
        return strtolower($countries[$sanitized_name]);
    }

    return false;
}

function get__svg($name)
{
    if ($name != false) {
        $svgPath = get_stylesheet_directory() . '/images/flags/' . DIRECTORY_SEPARATOR . $name . '.svg';

        if (file_exists($svgPath)) {
            return file_get_contents($svgPath);
        } else {
            throw new Exception("SVG not found: {$name}");
        }
    }
}

/*end of post listing*/

function add_featured_image_to_rss_feed($content)
{
    global $post;

    $preview_title = get_field('preview_title', $post->ID);
    if ($preview_title) {
        $title = $preview_title;
    } else {
        $title = get_the_title($post->ID);
    }

    $date = '<div style="margin-bottom: 10px;"><em>Posted: ';
    $date .= get_the_date('', $post->ID);
    $date .= '</em></div>';



    $content_html = '<table style="margin-bottom: 30px">';
    $content_html = '<tr>';



    if (has_post_thumbnail($post->ID)) {
        $content_html .= '<td style="width: 110px; padding-top: 20px; padding-bottom: 20px; border-bottom: 1px solid lightgray">';
        $content_html .= '<a href="' . get_the_permalink($post->ID) . '">';
        $content_html .= get_the_post_thumbnail($post->ID, 'thumbnail');
        $content_html .= '</a>';
        $content_html .= '</td>';
    }

    $content_html .= '<td style="padding-left: 20px; font-family: Helvetica; padding-top: 20px; padding-bottom: 20px; border-bottom: 1px solid lightgray">';
    $content_html .= '<div>';
    $content_html .= '<h2 style="margin-bottom: 0; display: inline">';
    $content_html .= '<a href="' . get_the_permalink($post->ID) . '" style="font-size: 18px; text-decoration: none; color: #110835">';
    $content_html .= $title;
    $content_html .= '</a>';
    $content_html .= '</h2>';
    $content_html .= '<span style="margin-top: 0; font-size: 12px; margin-left: 10px; float: right; ">';
    $content_html .= 'by ' . get__company(get_the_author_meta('ID'), false);
    $content_html .= '</span>';
    $content_html .= '</div>';

    $categories = get_the_category();
    $content_html .= '<p style="margin-top: 0;  font-size: 12px;">';
    if (! empty($categories)) {
        foreach ($categories as $cat) {
            $content_html .= $cat->name . ' ';
        }
    }
    $content_html .= '</p>';




    $content_html .= $content;
    $content_html .= '</td>';


    $content_html .= '</tr>';
    $content_html .= '</table>';

    return $content_html;
}

// Add the function to the RSS EXCERPT filter.
add_filter('the_excerpt_rss', 'add_featured_image_to_rss_feed');

function remove_excerpt_more_string($more)
{
    return '...';
}
add_filter('excerpt_more', 'remove_excerpt_more_string');


function preview__title()
{
    $preview_title = get_field('preview_title');
    if ($preview_title) {
        $title = $preview_title;
    } else {
        $title =  get_the_title();
    }
    return $title;
}

add_shortcode('preview__title', 'preview__title');


/*Admin Registration

/**
 * Adds a 'Listing Status' column to the admin post list.
 * This can be adapted for any post type by changing the filter name.
 * For a custom post type 'listing', the filter would be 'manage_listing_posts_columns'.
 *
 * @param array $columns The existing columns.
 * @return array The modified columns.
 */
function add_listing_status_column_company_admin($columns)
{
    // Add the new column after the 'title' column
    $new_columns = [];
    foreach ($columns as $key => $title) {
        $new_columns[$key] = $title;
        if ($key === 'title') {
            $new_columns['application_status'] = __('Application Status', 'your-text-domain');
        }
    }
    return $new_columns;
}
add_filter('manage_admin-registration_posts_columns', 'add_listing_status_column_company_admin');


/**
 * Displays the content for the custom 'listing_status' column.
 * Assumes the status is stored in a post meta field with the key 'listing_status'.
 *
 * @param string $column_name The name of the column to display.
 * @param int    $post_id     The ID of the current post.
 */
function display_listing_status_column_content_company_admin($column_name, $post_id)
{
?>

    <?php
    if ($column_name === 'application_status') {
        // Get the value of the custom field
        $status = get_post_meta($post_id, 'application_status', true);

        if (!empty($status)) {
            // Sanitize and display the value
            $status = esc_html(ucwords(str_replace('_', ' ', $status)));
        } else {
            // Display a default value if not set
            $status = 'Pending';
        }

        echo "<div class='listing-status-wrapper'>";

        echo '<span class="listing-status status-' . $status . '">' . $status . '</span>';

        if ($status === 'Pending') {
            echo '<a href="' . admin_url('post.php?post=' . $post_id . '&action=edit') . '">Review</a>';
        }
        echo "</div>";
        if ($status === 'Pending') {
            echo "<div class='listing-status-wrapper approve--reject'>";
            echo '<a class="approve-listing" href="' . admin_url('post.php?post=' . $post_id . '&action=edit&application_status=approve') . '">Approve</a>';
            echo '<a class="reject-listing" href="' . admin_url('post.php?post=' . $post_id . '&action=edit&application_status=reject') . '">Reject</a>';
            echo "</div>";
        }
    }
}
add_action('manage_posts_custom_column', 'display_listing_status_column_content_company_admin', 10, 2);


/**
 * Sends an email to the post author when a specific ACF field value changes.
 *
 * This function is hooked into 'acf/update_value' which runs before a field
 * value is saved to the database. This allows us to compare the new value
 * with the old one.
 *
 * Field Name: listing_status
 * Trigger Value: reject
 *
 * @param mixed $value   The new field value.
 * @param int   $post_id The ID of the post being updated.
 * @param array $field   The ACF field object.
 * @return mixed The original value to allow the update to proceed.
 */
function action_application_status_change_value($value, $post_id, $field)
{

    // --- Configuration ---
    // The post type you want this to run for. Use 'any' for all post types.
    $target_post_type = 'admin-registration'; // e.g., 'post', 'page', 'your_custom_post_type'

    // --- Validation ---
    // 1. Check if the post type matches our target.
    //    If you want this to run for ANY post type, you can remove this block.
    if ($target_post_type !== 'any' && get_post_type($post_id) !== $target_post_type) {
        return $value;
    }

    // 2. Get the value of the field *before* this update.
    $old_value = get_field('application_status', $post_id);


    // 3. Check if the new value is 'reject' and the old value was NOT 'reject'.
    //    This ensures the email is sent only when the status changes *to* reject.
    $post = get_post($post_id);
    $author_id = $post->post_author;
    $company_id = get_field('company', $post_id);
    $company_manager = get_field('company_manager', $company_id);

    if (!is_array($company_manager)) {
        $company_manager = [];
    }

    if ($value === 'approve') {
        // Add author_id if 'approve'
        // Check if the author_id is not already in the array before pushing
        if (!in_array($author_id, $company_manager)) {
            array_push($company_manager, $author_id);
        }
    } else {
        // Remove author_id if not 'approve' (or 'reject' based on the comment's intent)
        // Use array_diff to remove the author_id from the array
        $company_manager = array_diff($company_manager, [$author_id]);
        // Re-index the array after removal (optional but good practice)
        $company_manager = array_values($company_manager);
    }

    update_field('company_manager', $company_manager, $company_id);
    // IMPORTANT: Always return the original value to allow ACF to save the field.
    return $value;
}

/**
 * We use a targeted hook 'acf/update_value/name={$field_name}' for better performance.
 * This ensures our function only runs when the 'listing_status' field is updated.
 */
add_filter('acf/update_value/name=application_status', 'action_application_status_change_value', 10, 3);

function company_grid_buttons()
{
    ob_start();
    ?>
    <div class="company-grid-buttons">
        <a href="<?= get_the_permalink() ?> " class="btn btn-yellow">View</a>
        <?php if (is_company_manager(get_current_user_id(), get_the_ID())) { ?>
            <a href="<?= get_the_permalink(1330) . '?id=' . get_the_ID() ?> " class="btn btn-bordered">Edit</a>
        <?php } ?>
    </div>
<?php
    return ob_get_clean();
}

add_shortcode('company_grid_buttons', 'company_grid_buttons');


function is_company_manager($user_id, $company_id)
{
    $company_manager = get_field('company_manager', get_the_ID());
    if (!is_array($company_manager)) {
        $company_manager = [];
    }
    return in_array($user_id, $company_manager);
}

/**
 * Update the query by specific post meta.
 *
 * @since 1.0.0
 * @param \WP_Query $query The WordPress query instance.
 */
function user_company_query($query)
{

    // Get current meta Query
    $meta_query = $query->get('meta_query');

    // If there is no meta query when this filter runs, it should be initialized as an empty array.
    if (! $meta_query) {
        $meta_query = [];
    }

    // Append our meta query
    $meta_query['relation'] = 'OR';

    $meta_query[] = [
        'key'     => 'journalist', // **REQUIRED**
        'value'   => '"' . get_current_user_id() . '"', // Value needs to be wrapped in quotes
        'compare' => 'LIKE',
    ];

    $meta_query[] = [
        'key'     => 'company_manager', // **REQUIRED**
        'value'   => '"' . get_current_user_id() . '"', // Value needs to be wrapped in quotes
        'compare' => 'LIKE',
    ];


    $query->set('meta_query', $meta_query);
}
add_action('elementor/query/user_company', 'user_company_query');


function handle_mailchimp_subscribe($email, $fname, $lname)
{
    // Check for security nonce
    /*
    if (! check_ajax_referer('mailchimp_subscribe_nonce', 'security', false)) {
        return;
    }*/

    $email = sanitize_email($email);
    $fname = sanitize_text_field($fname);
    $lname = sanitize_text_field($lname);

    $api_key = get_field('mailchimp_api_key', 'option');
    $list_id = get_field('mailchimp_list_id', 'option');

    $datacenter = explode('-', $api_key);;

    $member_hash = md5(strtolower($email));
    $api_url = "https://{$datacenter}.api.mailchimp.com/3.0/lists/{$list_id}/members/{$member_hash}";

    // Request body for Mailchimp
    $body = json_encode([
        'email_address' => $email,
        'status'        => 'pending',
        'merge_fields'  => [
            'FNAME' => $fname,
            'LNAME' => $lname,
        ],
    ]);

    // Headers for the Mailchimp API request
    $headers = [
        'Content-Type'  => 'application/json',
        // Basic Authentication: The username is anything (usually 'user'), and the password is the API key.
        'Authorization' => 'Basic ' . base64_encode("user:{$api_key}"),
    ];

    // Arguments for wp_remote_post
    $args = [
        'method'    => 'PUT', // Use PUT to add or update (upsert)
        'headers'   => $headers,
        'body'      => $body,
        'timeout'   => 15,
        'sslverify' => false, // Set to true in a production environment with proper SSL setup
    ];

    // Send the request using WordPress HTTP API
    $response = wp_remote_post($api_url, $args);

    if (is_wp_error($response)) {
        echo 'Error connecting to Mailchimp service.';
    } /*else {
        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = json_decode(wp_remote_retrieve_body($response), true);

        if ($response_code === 200) {
            // Successful update or add
            $status = $response_body['status'] ?? 'pending';
            $message = match ($status) {
                'subscribed' => 'You are already subscribed and active!',
                'pending' => 'Success! Please check your email to confirm your subscription (double opt-in).',
                'unsubscribed' => 'Subscription successful. Your status was updated.',
                default => 'Subscription received successfully.',
            };
            

            wp_send_json_success(['message' => $message]);
        } elseif ($response_code === 400 && ($response_body['title'] ?? '') === 'Member Exists') {
            // Member already exists, but perhaps is 'pending' or 'unsubscribed'
            wp_send_json_error(['message' => 'That email address is already on our list.']);
        } elseif ($response_code === 404 && ($response_body['title'] ?? '') === 'Resource Not Found') {
            // Invalid API key or List ID (check your constants)
            wp_send_json_error(['message' => 'Configuration Error: Invalid Mailchimp List ID or API Key.']);
        } else {
            // General Mailchimp API error
            $error_detail = $response_body['detail'] ?? 'An unknown error occurred with the Mailchimp API.';
            wp_send_json_error(['message' => "Subscription failed: {$error_detail}"]);
        }
    }*/
}
