<?php get_header()?>


   <section class="author__banner">
      <div class="author__banner__content">
        <h1><?php the_title()?></h1>
        <ul class="breadcrumb">
          <li><a href="<?php echo esc_url(site_url('/press-release'))?>">Home</a></li>
          <li>></li>
          <li><?php the_title()?></li>
        </ul>
      </div>
    </section>



      <section class="release">
      <div class="container">
        <div class="release__wrapper">
          <main class="release__main">
            <h2 class="block__header">Press Release</h2>

            <?php $authorArticles = get_field('articles'); 
                if($authorArticles) : 
            
         foreach( $authorArticles as $authorArticle ): 
            $image = get_field('feature_image', $authorArticle->ID);
            $title = get_the_title($authorArticle->ID);
            $excerpt = get_the_excerpt($authorArticle->ID);
            $permalink = get_the_permalink($authorArticle->ID);
        
         
         ?>
            <div class="release__card">
              <a href="<?php the_permalink()?>">
              <img src="<?php echo $image;?>" alt="" />
              </a>
              <div class="release__card__content">
                <span class="tag"><?php print_r(get_categories()[0]->name)?></span>
                <small>
                  <img src="<?php echo get_theme_file_uri()?>/images/clock.svg" alt="" />
                  <span><?php echo get_the_date('M j, Y')?></span>
                </small>

                <h3>
                    <?php echo $title; ?>
                </h3>
                <p>
                  <?php echo wp_trim_words( $excerpt, 10 )?>
                </p>
                <a href="<?php echo $permalink?>" class="btn-custom">Read More</a>
              </div>
              
            </div>

            <?php endforeach; 
            endif;
            ?>

 
          </main>

          <aside class="release__author">
            <h2 class="block__header">About Author</h2>
            <div class="release__author__info">
              <img src="<?php echo get_field('author_logo')?>" alt="" />
              <small><img src="./images/hk-logo.svg" alt="" /> <?php echo get_field('location')?></small>
              <h3><?php the_title()?></h3>
              <p>
                <?php the_content()?>
              </p>
              <a href="#" class="btn-custom">Contact</a>
            </div>
          </aside>


        </div>
      </div>
    </section>

    
<?php  require_once( get_theme_file_path(  ) . '/templates/template-subscribe.php');  ?>


<?php get_footer()?>