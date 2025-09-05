<?php get_header() ?>

	<style>
		.contact {
			background:#f2f2f4;
		}
		input[type=text], input[type=email] {
			height: 50px;
			border-radius: 0.8rem;
			margin-bottom:10px;
			padding:1.2rem 3rem;
		}
		button {
			height: 45px !important;
		}
		textarea {
			min-height: 250px;
			margin-bottom:10px;
		}
		.elementor-message{
			color:black;
		}
	</style>

	<section class="page__banner">
		<div class="container">
			<div class="page__banner__wrapper">
				<h2>Contact Us</h2>
				<ul>
					<li><a href=""#>Home</a></li>
					<li> ></li>
					<li>Contact</li>
				</ul>
			</div>
		</div>
	</section>


	<section class="contact">
		<div class="container">
			<?php echo the_content()?>
		</div>
	</section>


<?php  require_once( get_theme_file_path(  ) . '/templates/template-subscribe.php');  ?>

<?php get_footer() ?>