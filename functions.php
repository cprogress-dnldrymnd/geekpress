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
    if (!current_user_can('edit_user', $user_id)) return;

    $old_status = get_user_meta($user_id, 'account_status', true);
    $new_status = sanitize_text_field($_POST['account_status']);

    update_user_meta($user_id, 'account_status', $new_status);

    if ($old_status !== 'approved' && $new_status === 'approved') {
        $user = get_userdata($user_id);
        $subject = 'Your Account Has Been Approved';
        $message = "Hi " . esc_html($user->display_name) . ",\n\nYour account has been approved. You can now log in:\n\nhttps://geekpress.theprogressteam.com/login/\n\nThank you,\n" . get_bloginfo('name');
        $headers = ['Content-Type: text/plain; charset=UTF-8'];

        wp_mail($user->user_email, $subject, $message, $headers);
    }
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
            <img src="<?php echo get_theme_file_uri() ?>/images/user.svg" alt="" />

            <?php if (is_user_logged_in()) {
                $current_user = wp_get_current_user(); ?>

                <span class="current__user"> <?php echo  get_user_meta($current_user->ID, 'first_name', true)  ?></span>

            <?php } else { ?>
                <ul>
                    <li><a href="<?php echo esc_url(site_url('/login')) ?>">Login</a></li>
                    <li>/</li>
                    <li><a href="<?php echo esc_url(site_url('/registration')) ?>">Register</a></li>
                </ul>
            <?php } ?>

            <?php if (is_user_logged_in()) { ?>
                <?php
                $user_id = get_current_user_id();
                $company_id = get__user_company($user_id, false, true);
                $company_manager = get_field('company_manager', $company_id);
                ?>
                <div class="dropdown__menu">
                    <ul>
                        <?php if ($company_id) { ?>
                            <li><a href="<?php echo esc_url(get_the_permalink($company_id)); ?>">Company Profile</a></li>
                        <?php } ?>
                        <?php if (in_array($user_id, $company_manager) && $company_id) { ?>
                            <li><a href="<?php echo esc_url(get_the_permalink(1330)); ?>">Edit Company</a></li>
                        <?php } ?>
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_register'])) {
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];

        $first_name = sanitize_user($_POST['first_name']);
        $last_name = sanitize_user($_POST['last_name']);


        $outlet = sanitize_text_field($_POST['outlet']);
        $company_post = sanitize_text_field($_POST['company_post']);
        $company_bio = sanitize_text_field($_POST['company_bio']);



        $website = sanitize_text_field($_POST['website']);
        $country = sanitize_text_field($_POST['country']);
        $job = sanitize_text_field($_POST['job']);

        $email_pref = sanitize_text_field($_POST['email_pref'] ?? '');

        $toc  = isset($_POST['toc']);


        $dobmonth = sanitize_text_field($_POST['dobmonth']);
        $dobday = sanitize_text_field($_POST['dobday']);
        $dobyear = sanitize_text_field($_POST['dobyear']);

        $display_name = sanitize_text_field($_POST['display_name']);

        $author_bio = sanitize_textarea_field($_POST['author_bio'] ?? '');



        $errors = [];

        if (empty($username) || empty($email) || empty($password)) {
            $errors[] = 'All fields are required.';
        } elseif (!is_email($email)) {
            $errors[] = 'Invalid email.';
        } elseif (username_exists($username) || email_exists($email)) {
            $errors[] = 'Username or email already exists.';
        } elseif (empty($display_name)) {
            $errors[] = 'Display Name is required';
        }


        // echo '<pre>'; print_r($_POST); echo '</pre>'; //check if 


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
                        'post_content' => $company_bio,
                        'meta_input'   => array(
                            'website' => $website,
                            'country'   => $country
                        ),
                    );

                    // Insert the post into the database
                    $company_id = wp_insert_post($my_post);
                }


                update_user_meta($user_id, 'first_name', $first_name);
                update_user_meta($user_id, 'last_name', $last_name);
                update_user_meta($user_id, 'outlet', $outlet);
                update_user_meta($user_id, 'company', $company_id);
                // update_user_meta($user_id, 'company_post', $company_post);
                //update_user_meta($user_id, 'website', $website);
                //update_user_meta($user_id, 'country', $country);
                update_user_meta($user_id, 'job', $job);
                update_user_meta($user_id, 'toc', $toc);
                update_user_meta($user_id, 'email_pref', $email_pref);
                update_user_meta($user_id, 'birthday', $dobday . '/' . $dobmonth . '/' . $dobyear);

                //update_user_meta($user_id, 'dobmonth', $dobmonth);
                // update_user_meta($user_id, 'dobday', $dobday);
                //update_user_meta($user_id, 'dobyear', $dobyear);
                //update_user_meta($user_id, 'display_name', $display_name);
                //update_user_meta($user_id, 'author_bio', $author_bio);


                // Prepare the user data to be updated.
                $user_data = array(
                    'ID'           => $user_id,
                    'display_name' => sanitize_text_field($display_name),
                    'description'  => wp_kses_post($author_bio), // Use wp_kses_post for sanitizing the bio.
                );

                // Update the user. wp_update_user() returns a WP_Error object on failure.
                wp_update_user($user_data);

                if (!empty($_FILES['page_banner']['name']) || !empty($_FILES['profile_image']['name'])) {
                    require_once ABSPATH . 'wp-admin/includes/file.php';
                    require_once ABSPATH . 'wp-admin/includes/media.php';
                    require_once ABSPATH . 'wp-admin/includes/image.php';

                    $attachment_id = media_handle_upload('page_banner', 0);
                    if (!is_wp_error($attachment_id)) {
                        update_user_meta($user_id, 'profile_banner', $attachment_id);
                    }

                    $profile_image_id = media_handle_upload('profile_image', 0);
                    if (!is_wp_error($profile_image_id)) {
                        update_user_meta($user_id, 'profile_picture', $profile_image_id);
                    }
                }

                update_user_meta($user_id, 'account_status', 'pending');

                wp_mail(
                    get_option('admin_email'),
                    'New User Pending Approval',
                    'A new user has registered and is pending approval.' . "\n\nUsername: " . $user_login
                );

                wp_redirect(home_url('/registration-success'));
            } else {
                $errors[] = $user_id->get_error_message();
            }
        }
    }
?>


    <section class="register">
        <div class="container">

            <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">
                <?php wp_nonce_field('custom_register', 'custom_register_nonce'); ?>

                <div class="register__block">
                    <h4>Your Details</h4>

                    <div class="register__grid">
                        <div class="input__wrapper">
                            <label for="first_name">First Name</label>
                            <input type="text" placeholder="Enter First Name" name="first_name" value="<?php echo esc_attr($_POST['first_name'] ?? ''); ?>" required>
                        </div>

                        <div class="input__wrapper">
                            <label for="last_name">Last Name</label>
                            <input type="text" placeholder="Enter Last Name" name="last_name" value="<?php echo esc_attr($_POST['last_name'] ?? ''); ?>" required>
                        </div>

                        <div class="input__wrapper">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" placeholder="Enter Email Address" value="<?php echo esc_attr($_POST['email'] ?? ''); ?>" required>
                        </div>

                        <div class="input__wrapper dob">
                            <label for="">Date of Birth</label>
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
                            'Magazine',
                            'National newspaper',
                            'Online retailer',
                            'Outsourcing',
                            'Podcast',
                            'PR/Marketing agency',
                            'PR/Marketing in-house',
                            'Radio',
                            'Regional newspaper',
                            'Retailer (Website)',
                            'Retailer (Store)',
                            'Streamer/influencer',
                            'Television',
                            'Trade press'
                        ]; ?>

                        <div class="input__wrapper">
                            <label for="job">Job Type</label>
                            <select name="job" required>
                                <option value="" style="opacity: 0.8">Select your job type</option>
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

                <?php
                $companies = get_posts(array(
                    'post_type' => 'company',
                    'numberposts' => -1,
                ));

                ?>
                <div class="register__block">
                    <h4>Company Details</h4>
                    <div class="register__grid">
                        <div class="input__wrapper">
                            <label for="company">Company</label>

                            <input list="company_post" placeholder="Enter Company" name="company_post" value="<?php echo esc_attr($_POST['company_post'] ?? ''); ?>" required>

                            <datalist id="company_post">
                                <?php foreach ($companies as $company) { ?>
                                    <option value="<?= $company->post_title ?>">
                                    <?php } ?>
                            </datalist>
                        </div>

                        <div class="input__wrapper input__wrapper--company-fields">
                            <label for="website" required>Website</label>
                            <input type="text" placeholder="Enter Website URL" name="website" value="<?php echo esc_attr($_POST['website'] ?? ''); ?>">
                        </div>
                        <?php $country_list = array("Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Antigua and Barbuda", "Argentina", "Armenia", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cabo Verde", "Cambodia", "Cameroon", "Canada", "Central African Republic", "Chad", "Chile", "China", "Colombia", "Comoros", "Congo (Congo-Brazzaville)", "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czechia (Czech Republic)", "Democratic Republic of the Congo", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Eswatini (fmr. 'Swaziland')", "Ethiopia", "Fiji", "Finland", "France", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Grenada", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Holy See", "Honduras", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique", "Myanmar (formerly Burma)", "Namibia", "Nauru", "Nepal", "Netherlands", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "North Macedonia", "Norway", "Oman", "Pakistan", "Palau", "Palestine State", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Qatar", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "Sudan", "Suriname", "Sweden", "Switzerland", "Syria", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Yemen", "Zambia", "Zimbabwe"); ?>
                        <div class="input__wrapper input__wrapper--company-fields">
                            <label for="country" required>Country</label>
                            <select name="country">
                                <option value="">Select your country</option>
                                <?php foreach ($country_list as $country): ?>
                                    <option value="<?php echo esc_attr($country); ?>"><?php echo esc_html($country); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>
                    <div class="register__block input__wrapper--company-fields" style="margin-top: 1rem">
                        <div class="input__wrapper input__wrapper--company-fields">
                            <label for="country" required>Company Bio</label>
                            <textarea name="company_bio" id="company_bio" rows="4" class="textarea--height" placeholder="Write a short company bio..." style="width:100%;" required><?php echo esc_textarea($_POST['company_bio'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>



                <div class="register__block">
                    <h4>Email Preferences</h4>
                    <p>If you’d like to be kept up to date with the latest news in geek culture, then simply tick ‘Opt in’ below. You can unsubscribe at any time.</p>

                    <div class="register__grid opt">
                        <div class="input__wrapper checkbox p-0">
                            <label for="optin">
                                <input type="checkbox" id="optin" name="email_pref" value="optin" <?php checked($_POST['email_pref'] ?? '', 'optin'); ?>>
                                <span class="checkbox-label"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6 9 17l-5-5" />
                                    </svg></span>
                                OPT IN
                            </label>
                        </div>

                        <div class="input__wrapper checkbox p-0">
                            <label for="optout">
                                <input type="checkbox" id="optout" name="email_pref" value="optout" <?php checked($_POST['email_pref'] ?? '', 'optout'); ?>>
                                <span class="checkbox-label"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6 9 17l-5-5" />
                                    </svg></span>
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
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="transparent" stroke="#0d0629" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 15V3"></path>
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <path d="m7 10 5 5 5-5"></path>
                                </svg>
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
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="transparent" stroke="#0d0629" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 15V3"></path>
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <path d="m7 10 5 5 5-5"></path>
                                </svg>
                                <span>Upload Profile</span>
                            </label>
                        </div>
                    </div>

                    <div class="input__wrapper" style="margin-bottom:3rem">
                        <label for="display_name">Display Name</label>
                        <input type="text" placeholder="Enter Name" name="display_name" value="<?php echo esc_attr($_POST['display_name'] ?? ''); ?>" required>
                    </div>

                    <div class="input__wrapper">
                        <label for="author_bio">Profile Bio</label>
                        <textarea name="author_bio" id="author_bio" rows="4" placeholder="Write a short bio..." style="width:100%;" required><?php echo esc_textarea($_POST['author_bio'] ?? ''); ?></textarea>
                    </div>
                </div>


                <div class="register__block">
                    <h4>Log In Details</h4>

                    <div class="register__grid">
                        <div class="input__wrapper">
                            <label for="username">Username</label>
                            <input type="text" id="username" placeholder="Enter Username" name="username" value="<?php echo esc_attr($_POST['username'] ?? ''); ?>" required>
                        </div>

                        <div class="input__wrapper">
                            <label for="password">Password</label>
                            <input type="password" id="password" placeholder="Enter Password" name="password" value="<?php echo esc_attr($_POST['password'] ?? ''); ?>" required>
                        </div>

                        <div class="input__wrapper">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" placeholder="Enter Password" name="confirm_password" value="<?php echo esc_attr($_POST['confirm_password'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>

                <div class="input__wrapper toc checkbox p-0">
                    <label for="toc">
                        <input type="checkbox" id="toc" name="toc" <?php checked($_POST['toc'] ?? '', 1); ?>>
                        <span class="checkbox-label"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 6 9 17l-5-5" />
                            </svg></span>
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
        jQuery(document).ready(function() {
            jQuery('input[name="company_post"]').change(function(e) {
                company_field(jQuery(this).val());
                e.preventDefault();
            });

            jQuery('input[name="company_post"]').keyup(function(e) {
                company_field(jQuery(this).val());
                e.preventDefault();
            });
        });

        function company_field(val) {
            exists = jQuery('#company_post option[value="' + val + '"]').length;
            if (exists == 1) {
                jQuery('.input__wrapper--company-fields').hide().removeAttr('required');
            } else {
                jQuery('.input__wrapper--company-fields').show().attr('required');
            }
        }
    </script>


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

        });




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

        });

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
<?php
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
function get__user_company($user_id, $link = true, $id_only = false)
{
    $company_id = get__user_company_id($user_id);

    if ($id_only == true) {
        return $company_id;
    } else {
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

function get__user_company_id($user_id)
{
    $company = get_user_meta($user_id, 'company');
    $company_id = $company[0];

    return $company_id;
}

function get__company_contacts($ids_only = true)
{
    if (get_post_type() == 'company') {
        $id = get_the_ID();
    } else  if (get_post_type() == 'post') {
        $id = get__user_company(get_the_author_meta('ID'), false, true);
    }
    $user_query = new WP_User_Query(array(
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key'     => 'company',
                'value'   => $id,
                'compare' => '='
            ),
            array(
                'key'     => 'account_status',
                'value'   => 'approved',
                'compare' => '='
            )
        )
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
function get__user_company_flag($author_id, $country_name = false)
{
    $company_id = get__user_company_id($author_id);

    if ($company_id) {
        $country = get_field('country', $company_id);
        $country_code = get_country_code_by_name($country);
        return '<div class="flag">' . get__svg($country_code) . ' ' . ($country_name == true ? $country : '') . ' </div>';
    }
}

function get__user_company_flag_sc()
{
    return get__user_company_flag(get_the_author_meta('ID'), true);
}
add_shortcode('get__user_company_flag_sc', 'get__user_company_flag_sc');

function get__company_posts()
{
    ob_start();
?>

    <main class="release__main">
        <?php $article = new WP_Query(array(
            'post_type' => 'post',
            'posts_per_page' => -1,
            'author__in' => get__company_contacts()
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
                                <?php echo get_the_title() ?>
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
    <style>
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
}

function reject__email($post_id)
{
    $to = get_post_author_email_by_id($post_id);
    $subject = 'Geekpress rejected your submission';
    $message = email__template('Submission Rejected for ' . get_the_title($post_id), '<p>We’re really sorry, but GeekPress has rejected your submission. We appreciate that this can be frustrating, so please check our <a href="https://geekpress.theprogressteam.com/submission-guidelines/">submission guidelines</a> to understand why this has happened. If you would like more detailed reasons, then <a href="mailto:contact@geekpress.co.uk">email us</a> and we can look into it for you.<p>');
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


// Add the function to the 'save_post' action hook.
add_action('admin_footer', 'update_acf_on_post_edit_with_url_param');


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
    $svgPath = get_stylesheet_directory() . '/images/flags/' . DIRECTORY_SEPARATOR . $name . '.svg';

    if (file_exists($svgPath)) {
        return file_get_contents($svgPath);
    } else {
        throw new Exception("SVG not found: {$name}");
    }
}

/*end of post listing*/

function add_featured_image_to_rss_feed($content)
{
    global $post;


    $h2 = '<h2 style="margin-bottom: 15px;">';
    $h2 .= '<a href="' . get_the_permalink($post->ID) . '">';
    $h2 .= get_the_title($post->ID);
    $h2 .= '</a>';
    $h2 .= '</h2>';

    if (has_post_thumbnail($post->ID)) {
        // Get the featured image HTML.
        // You can change 'medium' to 'thumbnail', 'large', etc.
        $featured_image = '<div style="margin-bottom: 15px;">';
        $featured_image .= '<a href="' . get_the_permalink($post->ID) . '">';
        $featured_image .= get_the_post_thumbnail($post->ID, 'large');
        $featured_image .= '</a>';
        $featured_image .= '</div>';

        // Prepend the image to the content (which is the excerpt in this case).
        $content = $h2 . $featured_image . $content;
    } else {
        return $h2 . $content;
    }
}

// Add the function to the RSS EXCERPT filter.
add_filter('the_excerpt_rss', 'add_featured_image_to_rss_feed');
