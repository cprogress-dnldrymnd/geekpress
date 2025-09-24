<?php acf_form_head(); ?>
<?php get_header(); ?>

<div id="primary">
    <div id="content" role="main">

        <?php /* The loop */ ?>
        <?php while (have_posts()) : the_post(); ?>

            <h1><?php the_title(); ?></h1>

            <?php the_content(); ?>

            <p>My custom field: <?php the_field('my_custom_field'); ?></p>

            <?php acf_form(array(
                'post_id'       => 1038,
                'post_title'    => false,
                'post_content'  => false,
                'submit_value'  => __('Update meta')
            )); ?>

        <?php endwhile; ?>

    </div><!-- #content -->
</div><!-- #primary -->

<?php get_footer(); ?>