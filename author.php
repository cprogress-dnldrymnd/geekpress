<?php
// Redirect non-logged-in users to the dynamic custom login page
if ( ! is_user_logged_in() ) {
    $login_url = site_url('/login/');
    $redirect_url = get_permalink();
    wp_redirect( add_query_arg('redirect_to', urlencode($redirect_url), $login_url) );
    exit;
}
?>

<?php get_header()?>

	<style>
		.release__author__info img {
			object-fit:cover;
		}
		.displayOnMobile {
			display:none;
		}
		@media screen and (max-width:992px){
			.hideOnMobile{
				display:none;
			}
			.displayOnMobile {
				display:block;
			}
			.release__wrapper{
				grid-template-columns: 1fr;
			}
		}
		body {
			display:inline !important;
		}
	</style>


   <section class="author__banner" style="background-image:url(<?php echo wp_get_attachment_image_url(get_user_meta(get_the_author_meta('ID'), 'page_banner', true), 'full');?>)">
	   <div class="container">
		  <div class="author__banner__content">
			<div class="hideOnMobile">
				<h1><?php echo get_the_author_meta('display_name'); ?></h1>
				<ul class="breadcrumb">
				  <li><a href="<?php echo esc_url(site_url('/press-release'))?>">Home</a></li>
				  <li>></li>
				  <li><?php echo get_the_author_meta('display_name'); ?></li>
				</ul>
			</div>
			<div class="release__author displayOnMobile">
				<div class="release__author__info blur">
				  <img src="<?php echo wp_get_attachment_image_url(get_user_meta(get_the_author_meta('ID'), 'profile_image', true), 'full');?>" alt="" />
				  <small><img src="./images/hk-logo.svg" alt="" /> <?php echo get_field('location')?></small>
				  <h3><?php echo get_the_author_meta('display_name'); ?></h3>
				  <a href="mailto:<?php echo get_the_author_meta('email'); ?>" class="" style="color:#f3ff49"><?php echo get_the_author_meta('email'); ?></a>
				  <p>
				   <?php echo get_the_author_meta('author_bio'); ?>
				  </p>
				  <ul style="margin-bottom:0; list-style:none;display:flex;justify-content:center;gap:10px">
					<?php
					$author_id = get_the_author_meta('ID');

					// ACF fields for user
					$social_links = [
						'x'         => get_field('x', 'user_' . $author_id),
						'linkedin'  => get_field('linkedin', 'user_' . $author_id),
						'instagram' => get_field('instagram', 'user_' . $author_id),
						'bluesky'   => get_field('bluesky', 'user_' . $author_id),
					];

					// Loop through only non-empty links
					foreach ($social_links as $platform => $url) {
						if (!empty($url)) {
							echo '<li>
								<a href="' . esc_url($url) . '" target="_blank" rel="noopener">
									<img src="' . get_theme_file_uri() . '/images/' . $platform . '.svg" alt="' . esc_attr(ucfirst($platform)) . '" />
								</a>
							</li>';
						}
					}
					?>
				</ul>
				</div>
          	</div>
		   </div>
       </div>
    </section>



      <section class="release">
      <div class="container">
        <div class="release__wrapper">
          <main class="release__main">
            <h2 class="block__header">Press Release</h2>
            <?php $article = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => -1,
                'author' => get_the_author_meta('ID')
            ));


            if($article ->have_posts()) : while($article ->have_posts()) : $article ->the_post() ?>

            <div class="release__card">
              <a href="<?php the_permalink()?>">
              <?php if (has_post_thumbnail()) : ?>
					<img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'large'); ?>" alt="<?php the_title(); ?>" />
				<?php endif; ?>

              </a>
              <div class="release__card__content">
                <span class="tag"><?php $categories = get_the_category();
                    if ( ! empty( $categories ) ) {
                        echo esc_html( $categories[0]->name );
                    } ?></span>
                <small>
                  <img src="<?php echo get_theme_file_uri()?>/images/clock.svg" alt="" />
                  <span><?php echo get_the_date('M j, Y')?></span>
                </small>

                <h3>
					<a href="<?php the_permalink()?>" style="color:white;">					
                    <?php echo get_the_title() ?>
					</a>
                </h3>
                <p>
                 <?php echo wp_trim_words( get_the_excerpt(), 15 )?>

                </p>
                <a href="<?php echo get_the_permalink()?>" class="btn-custom">Read More</a>
              </div>
            </div>
            <?php endwhile;
                else:
                    echo "No Post by this author";
                endif;
            ?>
     
 
          </main>

          <aside class="release__author hideOnMobile">
            <h2 class="block__header">About Author</h2>
            <div class="release__author__info">
              <img src="<?php echo wp_get_attachment_image_url(get_user_meta(get_the_author_meta('ID'), 'profile_image', true), 'full');?>" alt="" />
              <small><img src="./images/hk-logo.svg" alt="" /> <?php echo get_field('location')?></small>
              <h3><?php echo get_the_author_meta('display_name'); ?></h3>
			  <a href="mailto:<?php echo get_the_author_meta('email'); ?>" class="" style="color:#f3ff49"><?php echo get_the_author_meta('email'); ?></a>
              <p>
               <?php echo get_the_author_meta('author_bio'); ?>
              </p>
			  <ul style="margin-bottom:0; list-style:none;display:flex;justify-content:center;gap:10px;align-items:center;">
					<?php
					$author_id = get_the_author_meta('ID');

					// ACF fields for user
					$social_links = [
						'x'         => get_field('x', 'user_' . $author_id),
						'linkedin'  => get_field('linkedin', 'user_' . $author_id),
						'instagram' => get_field('instagram', 'user_' . $author_id),
						'bluesky'   => get_field('bluesky', 'user_' . $author_id),
					];

					// Loop through only non-empty links
					foreach ($social_links as $platform => $url) {
						if (!empty($url)) {
							echo '<li>
								<a href="' . esc_url($url) . '" target="_blank" rel="noopener">
									<img src="' . get_theme_file_uri() . '/images/' . $platform . '.svg" alt="' . esc_attr(ucfirst($platform)) . '" />
								</a>
							</li>';
						}
					}
					?>
				</ul>
            </div>
          </aside>


        </div>
      </div>
    </section>

    
<?php  require_once( get_theme_file_path(  ) . '/templates/template-subscribe.php');  ?>


<?php get_footer()?>