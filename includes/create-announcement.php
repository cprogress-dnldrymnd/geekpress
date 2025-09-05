<?php
add_filter('login_redirect', "geek_role_redirection", 10, 3);

function geek_role_redirection($redirect_to, $request, $user) {
    if (is_wp_error($user)) return $redirect_to;
         if ( in_array( 'administrator', $user->roles ) ) {
            return admin_url(); 
         } else {
             return home_url('/create-announcement/');
         }
}
/*

function handle_save_external_links() {
    check_ajax_referer('save_external_links_nonce', 'security');

    if (!is_user_logged_in()) {
        wp_send_json_error('Login required.');
        wp_die();
    }

    $links = isset($_POST['external_links']) ? $_POST['external_links'] : [];

    // Sanitize and validate URLs
    $cleaned_links = array();
    foreach ((array) $links as $link) {
        $link = esc_url_raw(trim($link));
        if (!empty($link)) {
            $cleaned_links[] = $link;
        }
    }

    // Save to user meta (or change to post meta if needed)
    update_user_meta(get_current_user_id(), 'external_links', $cleaned_links);

    wp_send_json_success('Links saved successfully.');
    wp_die();
}




add_action('wp_ajax_submit_custom_post', 'handle_custom_post_submission');

function handle_custom_post_submission() {
    if (!is_user_logged_in()) {
        wp_send_json_error(['general' => 'You must be logged in.']);
    }

    $errors = [];

    $title   = sanitize_text_field($_POST['post_title'] ?? '');
    $content = wp_kses_post($_POST['post_content'] ?? '');
    $image   = $_FILES['featured_image'] ?? null;

    if (empty($title))   $errors['title']   = 'Title is required.';
    if (empty($content)) $errors['content'] = 'Content is required.';
    if (!$image || $image['error'] !== 0) $errors['image'] = 'Featured image is required.';

    if (!empty($errors)) {
        wp_send_json_error(['errors' => $errors]);
    }

    $post_id = wp_insert_post([
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => 'pending',
        'post_author'  => get_current_user_id(),
    ]);

    if (is_wp_error($post_id)) {
        wp_send_json_error(['general' => 'Failed to create post.']);
    }

    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $attach_id = media_handle_upload('featured_image', $post_id);

    if (is_wp_error($attach_id)) {
        wp_send_json_error(['image' => 'Image upload failed.']);
    }

    set_post_thumbnail($post_id, $attach_id);
    wp_send_json_success([  
        'message' => 'Post submitted successfully!',
        'redirect_url' =>  get_permalink($post_id) 
    ]);    
 
}*/