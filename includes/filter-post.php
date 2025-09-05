<?php


function ajax_filter_posts() {
    // Default args to match your initial load
    $args = [
        'post_type'      => 'post',
        'posts_per_page' => 5,  // Match original
        'orderby'        => 'date',
        'order'          => 'DESC',
        'paged'          => 1,  // Optional if you use pagination later
    ];

    // Apply filters only if set
    if (!empty($_POST['category'])) {
        $args['category__in'] = array_map('intval', $_POST['category']);
    }

    if (!empty($_POST['date_filter'])) {
        $date_filters = $_POST['date_filter'];
        $date_query   = ['relation' => 'OR'];

		foreach ($date_filters as $filter) {
			switch ($filter) {

				case 'today':
					$date_query[] = [
						'after'     => date('Y-m-d 00:00:00'),
						'before'    => date('Y-m-d 23:59:59'),
						'inclusive' => true,
						'column'    => 'post_date',
					];
					break;

				case 'yesterday':
					$date_query[] = [
						'after'     => date('Y-m-d 00:00:00', strtotime('yesterday')),
						'before'    => date('Y-m-d 23:59:59', strtotime('yesterday')),
						'inclusive' => true,
						'column'    => 'post_date',
					];
					break;

				case 'monday':
					$date_query[] = [
						'after'     => date('Y-m-d 00:00:00', strtotime('monday this week')),
						'before'    => date('Y-m-d 23:59:59', strtotime('monday this week')),
						'inclusive' => true,
						'column'    => 'post_date',
					];
					break;

				case 'this_week':
					$date_query[] = [
						'after'     => date('Y-m-d 00:00:00', strtotime('monday this week')),
						'before'    => date('Y-m-d 23:59:59', strtotime('sunday this week')),
						'inclusive' => true,
						'column'    => 'post_date',
					];
					break;

				case 'last_week':
					$date_query[] = [
						'after'     => date('Y-m-d 00:00:00', strtotime('monday last week')),
						'before'    => date('Y-m-d 23:59:59', strtotime('sunday last week')),
						'inclusive' => true,
						'column'    => 'post_date',
					];
					break;

				case 'this_month':
					$date_query[] = [
						'after'     => date('Y-m-01 00:00:00'),
						'before'    => date('Y-m-t 23:59:59'),
						'inclusive' => true,
						'column'    => 'post_date',
					];
					break;

				case 'last_month':
					$date_query[] = [
						'after'     => date('Y-m-01 00:00:00', strtotime('first day of last month')),
						'before'    => date('Y-m-t 23:59:59', strtotime('last day of last month')),
						'inclusive' => true,
						'column'    => 'post_date',
					];
					break;

				case 'this_year':
					$date_query[] = [
						'after'     => date('Y-01-01 00:00:00'),
						'before'    => date('Y-12-31 23:59:59'),
						'inclusive' => true,
						'column'    => 'post_date',
					];
					break;

				case 'last_year':
					$date_query[] = [
						'after'     => date('Y-01-01 00:00:00', strtotime('last year')),
						'before'    => date('Y-12-31 23:59:59', strtotime('last year')),
						'inclusive' => true,
						'column'    => 'post_date',
					];
					break;

				case '2023':
					$date_query[] = [
						'after'     => '2023-01-01 00:00:00',
						'before'    => '2023-12-31 23:59:59',
						'inclusive' => true,
						'column'    => 'post_date',
					];
					break;

				case '2022':
					$date_query[] = [
						'after'     => '2022-01-01 00:00:00',
						'before'    => '2022-12-31 23:59:59',
						'inclusive' => true,
						'column'    => 'post_date',
					];
					break;

				case '2021':
					$date_query[] = [
						'after'     => '2021-01-01 00:00:00',
						'before'    => '2021-12-31 23:59:59',
						'inclusive' => true,
						'column'    => 'post_date',
					];
					break;
			}
		}

        $args['date_query'] = $date_query;
    }

    $query = new WP_Query($args);

    if ($query->have_posts()) :
        $last_date = '';

        while ($query->have_posts()) : $query->the_post();
            $current_date = get_the_date('l j F Y');

            if ($current_date !== $last_date) {
                echo '<h3 class="post-date-heading">' . esc_html($current_date) . '</h3>';
                $last_date = $current_date;
            }
            ?>
            <div class="press__item">
                <div class="press__item__image">
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
                    <?php endif; ?>
                    <div class="tag"><?php echo esc_html(get_the_category()[0]->name); ?></div>
                </div>
                <div class="press__item__content">
                    <div class="meta">
                        <div>
                            <img src="<?php echo esc_url(get_theme_file_uri('/images/clock.svg')); ?>" alt="" />
                            <span><?php echo esc_html(get_the_date('F j, Y')); ?> at <?php echo esc_html(get_the_time('g:i A')); ?></span>
                        </div>
                        <div>
                            <img src="<?php echo esc_url(get_theme_file_uri('/images/user.svg')); ?>" alt="" />
                            <span>by <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>" style="color:#fff"><?php echo get_the_author_meta('display_name'); ?></a></span>
                        </div>
                    </div>
                    <h5><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h5>
                    <p><?php echo esc_html(wp_trim_words(get_the_excerpt(), 15)); ?></p>
                </div>
            </div>
            <?php
        endwhile;
    else :
        echo "<p>No posts found.</p>";
    endif;

    wp_reset_postdata();
    wp_die();
}


add_action('wp_ajax_filter_posts', 'ajax_filter_posts');
add_action('wp_ajax_nopriv_filter_posts', 'ajax_filter_posts');