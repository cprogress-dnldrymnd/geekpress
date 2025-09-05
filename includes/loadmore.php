<?php

function loadmore_ajax_handler() {
    $paged = isset($_POST['paged']) ? intval($_POST['paged']) : 1;

    $args = [
        'post_type'      => 'post',
        'posts_per_page' => 5,
        'paged'          => $paged,
    ];

    $query = new WP_Query($args);

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post(); ?>
         <div class="press__item">
            <div class="press__item__image">
                <a href="<?php the_permalink(); ?>">
                <?php if(has_post_thumbnail()) {
                    the_post_thumbnail();
                }?>
                </a>
                <div class="tag"><?php echo esc_html(get_the_category()[0]->name); ?></div>
            </div>
            <div class="press__item__content">
                <div class="meta">
                    <div>
                        <img src="<?php echo get_theme_file_uri(); ?>/images/clock.svg" alt="" />
                        <span><?php echo esc_html(get_the_date('F j, Y')); ?> at <?php echo esc_html(get_the_time('g:i A')); ?></span>
                    </div>
                    <div>
                        <img src="<?php echo get_theme_file_uri(); ?>/images/user.svg" alt="" />
                        <span>by <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>" style="color:#fff"><?php echo get_the_author_meta('display_name'); ?></a></span>
                    </div>
                </div>
                <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                <p><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
            </div>
        </div>
        <?php endwhile;
    endif;
    wp_die();
}

add_action('wp_ajax_loadmore', 'loadmore_ajax_handler');
add_action('wp_ajax_nopriv_loadmore', 'loadmore_ajax_handler');