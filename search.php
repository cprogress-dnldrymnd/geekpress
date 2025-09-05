<?php get_header(); ?>

	<style>
		.search__banner {
			min-height: 25rem;
			background-color: #0d0629;
			position: relative;
			background-size: cover;
			background-position: center;
			background-isolation: isolate;
		}
		.search__banner__content {
			display: flex;
			flex-direction: column;
			justify-content: center;
			min-height: 25rem;
			z-index: 50;
			position: relative;
			z-index: 2;
		}
		.search__banner__content h1, .search__banner__content .breadcrumb a  {
			color:white;
		}
		.breadcrumb{
			list-style: none;
			font-size: 1.4rem;
			display: flex;
			gap: 1rem;
			margin-top: 3rem;
		}
	</style>

<section class="search__banner" style="background-image:url('<?php echo get_theme_file_uri('/images/banner.jpg'); ?>')">
    <div class="container">
        <div class="search__banner__content">
            <div class="hideOnMobile">
                <h1>Search Results for: "<?php echo get_search_query(); ?>"</h1>
                <ul class="breadcrumb">
                    <li><a href="<?php echo home_url(); ?>">Home</a></li>
                    <li>></li>
                    <li>Search</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="release">
    <div class="container">
        <div class="">
            <main class="release__main">
                <h2 class="block__header">Search Results</h2>

                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <div class="release__card">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>" alt="<?php the_title(); ?>" />
                            <?php endif; ?>
                        </a>
                        <div class="release__card__content">
                            <span class="tag">
                                <?php
                                $categories = get_the_category();
                                if (!empty($categories)) {
                                    echo esc_html($categories[0]->name);
                                }
                                ?>
                            </span>
                            <small>
                                <img src="<?php echo get_theme_file_uri() ?>/images/clock.svg" alt="" />
                                <span><?php echo get_the_date('M j, Y'); ?></span>
                            </small>

                            <h3>
                                <a href="<?php the_permalink(); ?>" style="color:white;">
                                    <?php the_title(); ?>
                                </a>
                            </h3>
                            <p><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                            <a href="<?php the_permalink(); ?>" class="btn-custom">Read More</a>
                        </div>
                    </div>
                <?php endwhile;
                else: ?>
                    <p>No results found for your search. Try again with different keywords.</p>
                <?php endif; ?>
            </main>
        </div>
    </div>
</section>

<?php require_once(get_theme_file_path() . '/templates/template-subscribe.php'); ?>

<?php get_footer(); ?>
