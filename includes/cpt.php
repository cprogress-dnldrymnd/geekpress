<?php function bothwick_custom_post()
{
    $author_label = array(
        'name'     => __('Authors', 'textdomain'),
        'singular_name' => __('Author', 'textdomain'),
        'add_new'    => __('Add Author', 'textdomain'),
        'edit_item'   => __('Edit Author', 'textdomain'),
        'add_new_item' => __('Add New Author', 'textdomain'),
        'all_items'   => __('Authors', 'textdomain'),
    );


    $authors_args = array(
        'labels' => $author_label,
        'public' => true,
        'capability_type' => 'post',
        'show_ui' => true,
        'supports' => array('title', 'editor',)
    );

    //register_post_type('authors', $authors_args);





}
add_action('init', 'bothwick_custom_post');