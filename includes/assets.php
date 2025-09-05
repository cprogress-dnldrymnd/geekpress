<?php 

add_action('wp_enqueue_scripts', 'geekpress_assets');

function geekpress_assets()
{
    wp_enqueue_style('geekpress-style', get_stylesheet_directory_uri() . '/css/main.min.css', microtime());
    

    if(is_page('create-announcement')) {
        wp_enqueue_style('geekpress-style-select', 'https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css', '1.0');
        wp_enqueue_script('geekpress-script-select', 'https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js', '1.0', [], true);
        wp_enqueue_script('geekpress-choice', get_stylesheet_directory_uri() . "/js/choice.js", microtime(), [], true);
         wp_enqueue_media();
    }

    wp_enqueue_script('geekpress-jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js', '1', [], true);

    // JSZIP
    wp_enqueue_script('geekpress-jszip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js', '1', [], true);
    wp_enqueue_script( 'geekpress-jszip-script', get_stylesheet_directory_uri() . '/js/jszip.js', microtime(), [], true );

   
    if(is_home()) {
        wp_enqueue_script('geekpress-loadmore', get_stylesheet_directory_uri() . "/js/loadmore.js", microtime(), [], true);
    }


    


    wp_enqueue_script('geekpress-script', get_stylesheet_directory_uri() . "/js/app.js", microtime(), [], true);
    wp_enqueue_script( 'my_loadmore', get_stylesheet_directory_uri() . '/js/loadmore.js', ['jquery'], null, true );
    wp_localize_script( 'my_loadmore', 'my_loadmore_params', [
        'ajaxurl' => admin_url('admin-ajax.php'),
    ]);

} 


function load_tinymce_frontend() {
    if (!is_admin()) {
        wp_enqueue_script('jquery');
        wp_enqueue_script('editor');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('media-upload');
        wp_enqueue_style('editor-buttons');
        wp_enqueue_style('thickbox');
    }
}
add_action('wp_enqueue_scripts', 'load_tinymce_frontend');