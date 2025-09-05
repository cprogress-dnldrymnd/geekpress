<?php get_header() ?>


<section class="page__banner about-page">
    <div class="container">
        <div class="page__banner__wrapper">
            <h2>About Us</h2>
            <ul>
                <li><a href=""#>Home</a></li>
                <li> ></li>
                <li>About</li>
            </ul>
        </div>
    </div>
</section>
<!-- 
<section class="about">
    <div class="container">
        <div class="about__grid">
            <div class="about__grid__image">
                <img src="<?php /* echo get_field('about_image') */ ?> " alt="">
            </div>
            <div class="about__grid__content">
               <?php /* echo get_the_content() */?>
            </div>

        </div>
    </div>
</section>

<section class="howto">
    <div class="container">
        <div class="howto__wrapper">
            <?php /* echo get_field('about_join') */ ?>
        </div>
    </div>
</section> -->

<?php the_content() ?>


<?php  require_once( get_theme_file_path(  ) . '/templates/template-subscribe.php');  ?>

<?php get_footer() ?>