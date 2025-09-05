<?php get_header() ?>

	<style>
		p span {
			color:white !important;
		}
	</style>

	<section class="page__banner">
		<div class="container">
			<div class="page__banner__wrapper">
				<h2>FAQ's</h2>
				<ul>
					<li><a href=""#>Home</a></li>
					<li> ></li>
					<li>Faqs</li>
				</ul>
			</div>
		</div>
	</section>


	<section class="faq">
		<div class="container">
			<?php echo the_content()?>
		</div>
	</section>


<?php  require_once( get_theme_file_path(  ) . '/templates/template-subscribe.php');  ?>

<?php get_footer() ?>