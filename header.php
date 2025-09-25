<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Document</title>
	<?php wp_head() ?>
	<style>
		.container {
			padding: 0 2rem !important;
		}

		.current__user {
			cursor: pointer;
		}

		.hideOnDesktop {
			display: none !important;
		}

		@media screen and (max-width:1280px) {
			.hideOnDesktop {
				display: block !important;
			}
		}

		@media screen and (max-height: 900px) {
			.left__links>div {
				margin-top: 20px !important;
				margin-bottom: 20px !important;
			}
		}
	</style>
</head>

<body>
	<header class="header" id="header">
		<div class="container">
			<div class="header__logo">

				<ul class="header__social">
					<li>
						<a href="<?php the_field('linkedin', 'option'); ?>">
							<img src="<?php echo get_theme_file_uri() ?>/images/linkedin.svg" alt="" />
						</a>
					</li>
					<li>
						<a href="<?php the_field('x', 'option'); ?>">
							<img src="<?php echo get_theme_file_uri() ?>/images/x.svg" alt="" />
						</a>
					</li>
					<li>
						<a href="<?php the_field('instagram', 'option'); ?>"><img src="<?php echo get_theme_file_uri() ?>/images/instagram.svg" alt="" /></a>
					</li>
					<li>
						<a href="<?php the_field('bluesky', 'option'); ?>"><img src="<?php echo get_theme_file_uri() ?>/images/bluesky-1.svg" alt="" /></a>
					</li>
				</ul>

				<a href="<?php echo site_url('/') ?>" class="header__brand">
					<?php
					$custom_logo_id = get_theme_mod('custom_logo');
					$logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');

					if ($logo_url) {
						echo '<img src="' . esc_url($logo_url) . '" alt="' . get_bloginfo('name') . '">';
					}
					?>
				</a>

				<div class="signup header__signup">
					<div class="dropdown">
						<img src="<?php echo get_theme_file_uri() ?>/images/user.svg" alt="" />

						<?php if (is_user_logged_in()) {
							$current_user = wp_get_current_user(); ?>


							<span class="current__user"> <?php echo  get_user_meta($current_user->ID, 'first_name', true)  ?></span>

						<?php } else { ?>
							<ul>
								<li><a href="<?php echo esc_url(site_url('/login')) ?>">Login</a></li>
								<li>/</li>
								<li><a href="<?php echo esc_url(site_url('/registration')) ?>">Register</a></li>
							</ul>
						<?php } ?>

						<?php if (is_user_logged_in()) { ?>
							<?php
							$user_id = get_current_user_id();
							$company_id = get__user_company($user_id, false, true);
							$company_manager = get_field('company_manager', $company_id);
							?>
							<div class="dropdown__menu">
								<ul>
									<?php if ($company_id) { ?>
										<li><a href="<?php echo esc_url(get_the_permalink($company_id)); ?>">Company Profile</a></li>
									<?php } ?>
									<li><a href="<?php echo esc_url(site_url('/edit-profile')); ?>">Edit Profile</a></li>
									<li><a href="<?php echo wp_logout_url(home_url()); ?>">Logout</a></li>
								</ul>
							</div>
						<?php } ?>

					</div>
					<a href="<?php echo esc_url(site_url('/create-announcement')) ?>" class="btn-custom">Submit News</a>
				</div>
			</div>
		</div>
		<div class="header__divider <?php echo (is_front_page()) ? "border__bottom" :  "" ?>">
			<div class="container">
				<div class="header__main">
					<div>
						<button class="hamburger toggleDisplatLeftLinks">
							<span></span>
							<span></span>
							<span></span>
						</button>
					</div>


					<button class="hamburger mobile__hamburger">
						<span></span>
						<span></span>
						<span></span>
					</button>

					<nav>
						<?php if (is_user_logged_in()) { ?>
							<ul>
								<li>
									<form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="header__search hideOnDesktop" style="justify-self:center">
										<div class="input__wrap">
											<input type="text" name="s" placeholder="Search" value="<?php echo get_search_query(); ?>" style="width:100%" />
											<img src="<?php echo get_theme_file_uri(); ?>/images/search.svg" alt="Search Icon" />
										</div>
									</form>
								</li>
								<li class="hideOnDesktop"><a href="<?php echo esc_url(get_author_posts_url($current_user->ID)); ?>">Profile</a></li>
								<li class="hideOnDesktop"><a href="<?php echo esc_url(site_url('/edit-profile')); ?>">Edit Profile</a></li>
								<li><a href="<?php echo esc_url(site_url('/')) ?>">Home</a></li>
								<li><a href="<?php echo esc_url(site_url('/about')) ?>">About</a></li>
								<li><a href="<?php echo esc_url(site_url('/faq')) ?>">FAQ'S</a></li>
								<li><a href="<?php echo esc_url(site_url('/contact')) ?>">Contact</a></li>
								<li class="hideOnDesktop"><a href="<?php echo wp_logout_url(home_url()); ?>">Logout</a></li>
							</ul>
						<?php } else { ?>
							<ul>
								<li>
									<form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="header__search hideOnDesktop" style="justify-self:center">
										<div class="input__wrap">
											<input type="text" name="s" placeholder="Search" value="<?php echo get_search_query(); ?>" style="width:100%" />
											<img src="<?php echo get_theme_file_uri(); ?>/images/search.svg" alt="Search Icon" />
										</div>
									</form>
								</li>
								<?php
								wp_nav_menu(array(
									'theme_location' => 'primary_menu',
									'container'      => false,
									'items_wrap'     => '%3$s' // Outputs only the <li> items
								));
								?>
							</ul>
						<?php } ?>

						<ul class="mobile__social">
							<li>
								<a href="#">
									<img src="<?php echo get_theme_file_uri() ?>/images/linkedin.svg" alt="" />
								</a>
							</li>
							<li>
								<a href="#">
									<img src="<?php echo get_theme_file_uri() ?>/images/x.svg" alt="" />
								</a>
							</li>
							<li>
								<a href="#"><img src="<?php echo get_theme_file_uri() ?>/images/instagram.svg" alt="" /></a>
							</li>
							<li>
								<a href="#"><img src="<?php echo get_theme_file_uri() ?>/images/bluesky-1.svg" alt="" /></a>
							</li>
						</ul>

						<div class="signup mobile__signup">
							<img src="<?php echo get_theme_file_uri() ?>/images/user.svg" alt="" />
							<ul style="<?php echo is_user_logged_in() ? 'display:none;' : ''; ?>">
								<li><a href="<?php echo esc_url(site_url('/login')) ?>">Login</a></li>
								<li>/</li>
								<li><a href="<?php echo esc_url(site_url('/registration')) ?>">Register</a></li>
							</ul>

							<a href="<?php echo esc_url(site_url('/create-announcement')) ?>" class="btn-custom">Submit News</a>
						</div>
					</nav>

					<form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="header__search">
						<div class="input__wrap">
							<input type="text" name="s" placeholder="Search" value="<?php echo get_search_query(); ?>" />
							<img src="<?php echo get_theme_file_uri(); ?>/images/search.svg" alt="Search Icon" />
						</div>
					</form>
				</div>
			</div>
		</div>
	</header>


	<div class="overlay__black">
		<div class="left__links">
			<div class="left__links__wrapper__header">
				<a href="<?php echo site_url('/') ?>" class="header__brand">
					<?php
					$custom_logo_id = get_theme_mod('custom_logo');
					$logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');

					if ($logo_url) {
						echo '<img src="' . esc_url($logo_url) . '" alt="' . get_bloginfo('name') . '">';
					}
					?>
				</a>
				<span class="left__links__close" id="left__link__close__btn">
					<img src="<?php echo get_theme_file_uri() ?>/images/cross-svgrepo-com.png" alt="" />
				</span>
			</div>
			<div class="left__links_submit_news">
				<a href="<?php echo esc_url(site_url('/create-announcement')) ?>" class="btn-custom">Submit News</a>
			</div>
			<nav>
				<ul>
					<?php
					wp_nav_menu(array(
						'theme_location' => 'left_side_menu',
						'container'      => false,
						'items_wrap'     => '%3$s' // Outputs only the <li> items
					));
					?>
				</ul>
			</nav>
			<div class="left__links__about">
				<img src="https://geekpress.theprogressteam.com/wp-content/uploads/2025/07/geek-about.jpg" alt="" />
			</div>
			<div class="left__links__about">
				<h3>
					About Us
				</h3>
				<p>
					GeekPress comes from the Minds of Big Games Machine, a video games PR agency with a passion for all things geek culture.
				</p>
			</div>
			<div>
				<ul class="left__link__header__social">
					<li>
						<a href="<?php the_field('linkedin', 'option'); ?>">
							<img src="<?php echo get_theme_file_uri() ?>/images/linkedin.svg" alt="" />
						</a>
					</li>
					<li>
						<a href="<?php the_field('x', 'option'); ?>">
							<img src="<?php echo get_theme_file_uri() ?>/images/x.svg" alt="" />
						</a>
					</li>
					<li>
						<a href="<?php the_field('instagram', 'option'); ?>"><img src="<?php echo get_theme_file_uri() ?>/images/instagram.svg" alt="" /></a>
					</li>
					<li>
						<a href="<?php the_field('bluesky', 'option'); ?>"><img src="<?php echo get_theme_file_uri() ?>/images/bluesky-1.svg" alt="" /></a>
					</li>
				</ul>
			</div>
		</div>
	</div>

	<script>
		const leftSideClose = document.querySelector("#left__link__close__btn");
		const leftSideLink = document.querySelector(".left__links");
		const toggleDisplatLeftLinks = document.querySelector(".toggleDisplatLeftLinks");
		const overlayBlack = document.querySelector(".overlay__black");

		leftSideClose.addEventListener('click', () => {
			leftSideLink.classList.remove("open");
			overlayBlack.classList.remove("open");
		});

		toggleDisplatLeftLinks.addEventListener('click', () => {
			leftSideLink.classList.add("open");
			overlayBlack.classList.add("open");
		});
	</script>