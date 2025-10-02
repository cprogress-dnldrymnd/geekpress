<?php get_header() ?>
<style>
	.hidden-category {
		display: none;
	}

	/* Hide the default checkbox */
	input[type="checkbox"] {
		display: none;
	}

	.filter__block {
		padding: 0rem 2rem 0 2rem;
	}

	/* Style the label to look like a checkbox */
	.checkbox-label {
		display: inline-block;
		width: 15px;
		height: 15px;
		border: 1px solid #F3FF49;
		background-color: transparent;
		border-radius: 4px;
		cursor: pointer;
		position: relative;
	}

	.checkbox-label svg {
		transform: translate(-2px, -1px);
		display: none;

	}

	/* Show green background when checked */
	input[type="checkbox"]:checked+.checkbox-label {
		background-color: #F3FF49;
	}

	input[type="checkbox"]:checked+.checkbox-label svg {
		display: block
	}

	/* Optional: Add checkmark using pseudo-element */
	input[type="checkbox"]:checked+.checkbox-label::after {
		content: '';
		color: white;
		font-size: 16px;
		position: absolute;
		top: -7px;
		left: 1px;
	}

	.post-date-heading {
		font-size: 18px;
		border-bottom: 0.4px solid rgba(243, 255, 73, 0.4);
		padding-bottom: 5px;
		margin-bottom: 15px;
	}

	.collapsible-content {
		max-height: 0;
		overflow: hidden;
		transition: max-height 0.3s ease, padding 0.3s ease;
		/* 		  padding-bottom: 20px; */
	}

	.collapsible-content a {
		color: rgba(255, 255, 255, 0.6);
		font-size: 15px;
		padding-bottom: 20px;
	}

	.filter__block.active .collapsible-content {
		max-height: 1000px;
		/* Adjust based on content size */
		padding-top: 10px;
	}

	.filter__header {
		cursor: pointer;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.filter__header img {
		transition: transform 0.3s ease;
	}

	.filter__block.active .filter__header img {
		transform: rotate(-90deg);
	}

	.filter__block ul {
		margin: 0;
	}

	.info small {
		opacity: 0.75;
		gap: 5px !important;
	}

	.introBox li a {
		color: white;
	}

	.introBox li a:hover {
		color: #f3ff49;
	}

	.home__baner.home__baner {
		background-color: var(--e-global-color-primary);
		min-height: 0;
		padding-top: 10px;
		padding-bottom: 10px;
		margin-bottom: 10px;
	}

	.home__baner h1 {

		color: var(--e-global-color-secondary);
		font-family: Roboto !important;
		font-size: 20px !important;
		font-weight: 400;
	}

	.home__baner h1 span {
		font-weight: bold;
		color: var(--e-global-color-secondary);


	}

	.banner__content.banner__content {
		position: static;
		transform: none;
	}
</style>

<section class="banner home__baner">
	<div class="container">
		<div class="banner__content">
			<h1>
				<span>GeekPress</span>: The only newswire dedicated to geek culture.
			</h1>
		</div>
	</div>
</section>

<main class="homepage">
	<div class="container">
		<div class="homepage__wrapper">
			<aside class="filter">
				<form id="filter-form">
					<h2 class="block__header">Filter By</h2>
					<div class="filter__block active">
						<div class="filter__header  collapsible-trigger">
							<h4>Categories</h4>
							<img src="<?php echo get_theme_file_uri() ?>/images/chevron-right.svg" alt="" />
						</div>
						<div class="collapsible-content">

							<ul class="category-list">
								<?php
								$categories = get_categories([
									'taxonomy'   => 'category',
									'hide_empty' => false,
								]);

								if (!empty($categories)) {
									foreach ($categories as $index => $category) {
										$checked = isset($_GET['category']) && in_array($category->term_id, (array) $_GET['category']) ? 'checked' : '';
										$is_hidden = ($index >= 5) ? 'style="display:none;"' : '';
										echo "<li class=\"category-item\" {$is_hidden}>";
										echo '<label>';
										echo '<input type="checkbox" name="category[]" value="' . esc_attr($category->term_id) . '" ' . $checked . '> ';
										echo '<span class="checkbox-label"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0d0629" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" ><path d="M20 6 9 17l-5-5"/></svg></span>';
										echo esc_html($category->name);
										echo '</label>';
										echo '</li>';
									}
								}
								?>
							</ul>
							<a href="#" id="load-more">+ More</a>
						</div>
					</div>

					<div class="filter__block active">
						<div class="filter__header collapsible-trigger">
							<h4>Issue Date</h4>
							<img src="<?php echo get_theme_file_uri() ?>/images/chevron-right.svg" alt="" />
						</div>

						<div class="collapsible-content">
							<ul class="date-filter-list">
								<?php
								// Define date filters dynamically
								$date_filters = [
									'today'       => 'Today',
									'yesterday'   => 'Yesterday',
									'monday'      => 'Monday',
									'this_week'   => 'This Week',
									'last_week'   => 'Last Week',
									'this_month'  => 'This Month',
									'last_month'  => 'Last Month',
									'this_year'   => 'This Year',
									'last_year'   => 'Last Year'
								];

								// Add last 3 years dynamically
								$current_year = date('Y');
								for ($i = 1; $i <= 3; $i++) {
									$year = $current_year - $i;
									$date_filters[$year] = $year; // key and label are the same
								}

								if (!empty($date_filters)) {
									$selected_filters = isset($_GET['date_filter']) ? (array) $_GET['date_filter'] : [];

									foreach ($date_filters as $key => $label) {
										static $index = 0;
										$checked = in_array($key, $selected_filters) ? 'checked' : '';
										$is_hidden = ($index >= 5) ? 'style="display:none;"' : '';
										echo "<li class=\"date-filter-item\" {$is_hidden}>";
										echo '<label>';
										echo '<input type="checkbox" name="date_filter[]" value="' . esc_attr($key) . '" ' . $checked . '> ';
										echo '<span class="checkbox-label"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0d0629" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg></span>';
										echo esc_html($label);
										echo '</label>';
										echo '</li>';
										$index++;
									}
								}
								?>
							</ul>
							<a href="#" id="load-more-dates">+ More</a>
						</div>
				</form>
		</div>
		</aside>
		<div class="press">
			<h2 class="block__header">Latest Releases</h2>

			<div id="post-results" class="post-container">
				<?php
				$press = new WP_Query([
					'post_type' => 'post',
					'posts_per_page' => 5,
					'orderby' => 'date',
					'order' => 'DESC',
					'paged' => 1,
				]);

				if ($press->have_posts()) :
					$last_date = ''; // Track the previous post date

					while ($press->have_posts()) : $press->the_post();
						$current_date = get_the_date('l j F Y'); // Format: June 20, 2025

						// If the date is different, display the date heading
						if ($current_date !== $last_date) {
							echo '<h3 class="post-date-heading">' . esc_html($current_date) . '</h3>';
							$last_date = $current_date;
						}
				?>
						<div class="press__item">
							<div class="press__item__image">
								<a href="<?php the_permalink(); ?>">
									<?php if (has_post_thumbnail()) {
										the_post_thumbnail();
									} ?>
								</a>
								<div class="tag"><?php echo esc_html(get_the_category()[0]->name); ?></div>
							</div>
							<div class="press__item__content">
								<div class="meta">
									<div>
										<img src="<?php echo get_theme_file_uri(); ?>/images/clock.svg" alt="" />
										<span><?php echo esc_html(get_the_date('F j, Y')); ?> at <?php echo esc_html(get_the_time('g:i A')); ?></span>
									</div>
									<div>
										<?= get__user_company_flag(get_the_author_meta('ID')) ?>
										<span> <?= get__user_company(get_the_author_meta('ID'), true) ?> </span>
									</div>
								</div>
								<?php
								echo preview__title();
								?>
								<h5><a href="<?php the_permalink(); ?>"><?= $title ?></a></h5>
								<p><?php echo wp_trim_words(get_the_excerpt(), 25); ?></p>
							</div>
						</div>
				<?php endwhile;
				else :
					echo "No posts found.";
				endif;
				wp_reset_postdata();
				?>

			</div>
			<button id="loadmore-post" data-page="1" class="btn-loadmore">Load More</button>
		</div>

		<aside class="viewed">

			<?= do_shortcode('[elementor-template id="840"]') ?>


			<div class="block__header">Most Viewed</div>


			<?php
			$viewed = new WP_Query(array(
				'post_type' => 'post',
				'posts_per_page' => 4,
				'meta_key' => 'post_views_count',
				'orderby' => 'meta_value_num',
				'order' => 'DESC'

			));
			if ($viewed->have_posts()) : while ($viewed->have_posts()) : $viewed->the_post() ?>
					<div class="viewed__item__content">
						<small>
							<img src="<?php echo get_theme_file_uri() ?>/images/clock.svg" alt="" /><span>
								<?php echo get_the_date('F j, Y') ?> at <?php echo esc_html(get_the_time('g:i A')); ?> </span></small>

						<h4><a href="<?php the_permalink(); ?>"> <?php echo get_the_title() ?></a></h4>
						<p>
							<?php echo wp_trim_words(get_the_excerpt(), 15) ?>
						</p>

						<div class="info">
							<small>
								<?= get__user_company_flag(get_the_author_meta('ID')) ?>
								<?= get__user_company(get_the_author_meta('ID'), true) ?>
							</small>
							<span><?php print_r(get_the_category(get_the_ID())[0]->name) ?></span>
						</div>
					</div>
			<?php endwhile;
			else :
				echo "no post";
			endif;
			wp_reset_postdata();
			?>

		</aside>
	</div>
	</div>
</main>

<script>
	document.addEventListener("DOMContentLoaded", function() {
		const form = document.getElementById("filter-form");

		form.addEventListener("change", function() {
			const formData = new FormData(form);
			formData.append('action', 'filter_posts'); // Add action properly

			fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
					method: 'POST',
					body: formData
				})
				.then(response => response.text())
				.then(html => {
					document.getElementById("post-results").innerHTML = html;
				});
		});
	});

	document.addEventListener("DOMContentLoaded", function() {
		/*
    function setupLoadMore(buttonId, itemClass) {
        const loadMoreBtn = document.getElementById(buttonId);
        if (!loadMoreBtn) return;

        const items = document.querySelectorAll("." + itemClass);
        let visibleCount = 5;
        const increment = 5;

        loadMoreBtn.addEventListener("click", function (e) {
            e.preventDefault();

            let revealed = 0;
            for (let i = visibleCount; i < items.length && revealed < increment; i++) {
                items[i].style.display = 'list-item';
                revealed++;
            }

            visibleCount += increment;

            if (visibleCount >= items.length) {
                loadMoreBtn.style.display = 'none'; // hide the button when all are shown
            }
        });
    }*/

		function setupLoadMore(buttonId, itemClass) {
			jQuery(buttonId).click(function(e) {
				jQuery(itemClass).show();
				jQuery(buttonId).hide();
				e.preventDefault();
			});
		}

		// Setup for categories
		setupLoadMore("#load-more", ".category-item");

		// Setup for date filters
		setupLoadMore("#load-more-dates", ".date-filter-item");
	});

	document.addEventListener("DOMContentLoaded", function() {
		const triggers = document.querySelectorAll('.collapsible-trigger');

		triggers.forEach(trigger => {
			trigger.addEventListener('click', () => {
				const block = trigger.closest('.filter__block');
				block.classList.toggle('active');
			});
		});
	});

	function removeActiveOnMobile() {
		if (window.innerWidth <= 1280) { // mobile breakpoint
			document.querySelectorAll('.filter__block.active').forEach(el => {
				el.classList.remove('active');
			});
		}
	}

	// Run on load
	removeActiveOnMobile();

	// Optional: run on resize
	window.addEventListener('resize', removeActiveOnMobile);
</script>


<?php require_once(get_theme_file_path() . '/templates/template-subscribe.php');  ?>

<?php get_footer() ?>