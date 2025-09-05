<?php
/**
 * @author  RadiusTheme
 * @since   1.0
 * @version 1.0
 */

// Layout class
if ( GamxoTheme::$layout == 'full-width' ) {
	$gamxo_layout_class = 'col-12';
}
else{
	$gamxo_layout_class = GamxoTheme_Helper::has_active_widget();
}
$gamxo_has_entry_meta  = ( GamxoTheme::$options['post_date'] || GamxoTheme::$options['post_author_name'] || GamxoTheme::$options['post_comment_num'] || ( GamxoTheme::$options['post_length'] && function_exists( 'gamxo_reading_time' ) ) || GamxoTheme::$options['post_published'] && function_exists( 'gamxo_get_time' ) || ( GamxoTheme::$options['post_view'] && function_exists( 'gamxo_views' ) ) ) ? true : false;

$gamxo_comments_number = number_format_i18n( get_comments_number() );
$gamxo_comments_html = $gamxo_comments_number == 1 ? esc_html__( 'Comment' , 'gamxo' ) : esc_html__( 'Comments' , 'gamxo' );
$gamxo_comments_html = '<span class="comment-number">'. $gamxo_comments_number .'</span> '. $gamxo_comments_html;
$gamxo_author_bio = get_the_author_meta( 'description' );

$gamxo_time_html       = sprintf( '<span>%s</span><span>%s</span>', get_the_time( 'd' ), get_the_time( 'M' ), get_the_time( 'Y' ) );
$gamxo_time_html       = apply_filters( 'gamxo_single_time', $gamxo_time_html );
$youtube_link = get_post_meta( get_the_ID(), 'gamxo_youtube_link', true );

if ( empty(has_post_thumbnail() ) ) {
	$img_class ='no-image';
}else {
	$img_class ='show-image';
}

if( GamxoTheme::$options['image_blend'] == 'normal' ) {
	$blend = 'normal';
}else {
	$blend = 'blend';
}

if ( GamxoTheme::$layout == 'left-sidebar' ) {
	$sidebar_order = 'order-lg-2 order-md-1 order-1';
} else {
	$sidebar_order = 'no-order';
}

?>
<?php get_header(); ?>

<div id="primary" class="content-area <?php echo esc_attr($blend); ?>">
	<?php the_content() ?>
</div>
<?php get_footer(); ?>