    <footer class="footer">
      <div class="container">
        <div class="footer__wrapper">
          <div class="footer__box information">
            <img src="<?php echo get_theme_file_uri()?>/images/logo.JPG " alt="" />
            <p>
              Lorem ipsum dolor, sit amet consectetur adipisicing elit. Earum
              veniam expedita rem quaerat
            </p>
            <ul>
              <li>Phone: 01244 556 555</li>
              <li>Email: contact@geekpress.co.uk</li>
            </ul>
          </div>
          <div class="footer__box">
            <h3>GeekPress</h3>

            <ul class="links">
              <li><a href="<?php echo esc_url(site_url('/about'))?>"><img src="<?php echo get_theme_file_uri()?>/images/chevron-right.svg" alt="" /> About Us</a></li>
              <li><a href="<?php echo esc_url(site_url('/faqs'))?>"><img src="<?php echo get_theme_file_uri()?>/images/chevron-right.svg" alt="" /> FAQ's</a></li>
              <li><a href="<?php echo esc_url(site_url('/contact'))?>"><img src="<?php echo get_theme_file_uri()?>/images/chevron-right.svg" alt="" /> Contact</a></li>
            </ul>
          </div>

          <div class="footer__box">
            <h3>Lastest Posts</h3>
<?php
			 $popularCard = new WP_Query(array(
                'post_type' => 'post',
                'posts_per_page' => 2,
//                 'orderby' => 'rand',
                
    
            )); ?>
              <?php if($popularCard->have_posts()) : while($popularCard->have_posts()) : $popularCard->the_post()?>
            <div class="item__card">
              <!-- <img src="<?php echo get_field('feature_image')?>" alt="" /> -->
   			 <?php
			$custom_image = get_field('feature_image');
			$post_url = get_permalink(); // Link target for all images

			if ($custom_image) {
			  // ACF feature_image exists
			  echo '<a href="' . esc_url($post_url) . '"><img src="' . esc_url($custom_image) . '" alt=""></a>';
			} elseif (has_post_thumbnail()) {
			  // Post has a featured image
			  echo '<a href="' . esc_url($post_url) . '">';
			  the_post_thumbnail();
			  echo '</a>';
			} else {
			  // Fallback to placeholder image
			  echo '<a href="' . esc_url($post_url) . '">
					  <img src="https://geekpress.theprogressteam.com/wp-content/uploads/2025/07/imagePlaceholder.jpg" alt="Placeholder">
					</a>';
			}
			?>
              <div>
                <small
                  ><img src="<?php echo get_theme_file_uri()?>/images/clock.svg" alt="" /> <?php echo get_the_date("M j, Y")?></small
                >
                <h5><a href="<?php the_permalink()?>"><?php the_title()?></a></h5>
              </div>
            </div>

			<?php endwhile;
                else :
                    echo "no more post";
                endif;
                wp_reset_postdata();
              ?>
          
          </div>

          <div class="footer__box">
            <h3>Follow Us</h3>

            <ul class="social__links">
              <li>
                <a href="<?php the_field('linkedin', 'option'); ?>"><img src="<?php echo get_theme_file_uri()?>/images/linkedin.svg" alt="" /></a>
              </li>
              <li>
                <a href="<?php the_field('x', 'option'); ?>"><img src="<?php echo get_theme_file_uri()?>/images/x.svg" alt="" /></a>
              </li>
              <li>
                <a href="<?php the_field('instagram', 'option'); ?>"><img src="<?php echo get_theme_file_uri()?>/images/instagram.svg" alt="" /></a>
              </li>
              <li>
                <a href="<?php the_field('bluesky', 'option'); ?>"><img src="<?php echo get_theme_file_uri()?>/images/bluesky.svg" alt="" /></a>
              </li>
            </ul>
          </div>
        </div>

        <div class="copyright">
          <small>&copy; 2025 GeekPress. All Rights Reserved</small>
          <ul>
            <li><a href="<?php echo home_url() ?>/privacy-policy">Privacy Policy</a></li>
            <li><a href="<?php echo home_url() ?>/terms-of-service-for-geekpress">Terms of Service</a></li>
          </ul>
        </div>
      </div>
    </footer>


    <div class="back__to__top" id="backToTop">
      <a href="#header"><img src="<?php echo get_theme_file_uri()?>/images/arrow-up.svg" alt=""></a>
    </div>
	<?php wp_footer()?>

  
  </body>
</html>
