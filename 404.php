<?php get_header() ?>
	
	<style>
		h2 {
			color:white;
			font-size:clamp(30px, 5vw, 80px);
		}
		h3 {
			color:white;
		}
		.pageNotFound {
			text-align:center;
		}
		.notFoundWrapper {
			display:grid;
			place-items:center;
			height:30vh;
		}
	</style>

	<div class="pageNotFound">
		<div class="container">
			<div class="notFoundWrapper">
				<div>
					<h3>404 - PAGE NOT FOUND</h3>
					<a href="<?php echo home_url() ?>" class="btn-custom">Go to Home Page</a>
				</div>
			</div>
		</div>
	</div>


<?php get_footer() ?>