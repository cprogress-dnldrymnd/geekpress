<?php get_header() ?>
<style>
    .btnShowInModal {
        width: 100%;
    }

    .single__aside ul li span a {
        color: white;
    }

    .single__aside ul li span a:hover {
        color: #f3ff49;
        text-decoration: underline;
    }

    .full__access p a {
        color: white;
        font-weight: bold;
    }

    .full__access p a:hover {
        color: #f3ff49;
    }
    .flag {
        display: flex;
        gap: ;
    }
</style>
<section class="single">
    <div class="container">
        <div class="single__wrapper">

            <!-- <?php while (have_posts()) : the_post() ?> -->
            <main class="single__main">
                <div class="single__main__image">

                    <?php
                        if (has_post_thumbnail()) : the_post_thumbnail();
                        endif;
                    ?>

                </div>

                <div class="single__main__content">
                    <div class="meta-info-holder">
                        <small class="meta__info"><img src="<?php echo get_theme_file_uri() ?>/images/clock.svg" alt="" />
                            <span><?php echo get_the_date('F j, Y') ?> </span></small>
                        <div>
                            <?= get__user_company_flag(get_the_author_meta('ID'), true) ?>
                            <span><?= get__user_company(get_the_author_meta('ID'), true) ?></span>
                        </div>
                    </div>
                    <h1>
                        <?php the_title() ?>
                    </h1>



                    <div class="single__main__content__article">
                        <?php echo apply_filters('the_content', get_post_field('post_content', get_the_ID())); ?>
                        <?php
                        $external_links = get_field('external_links');

                        if ($external_links) {
                            echo '<div class="external-links">';
                            foreach ($external_links as $external_link) {
                                echo '<div class="external-link">';
                                $link_type = getLinkType($external_link['external_link']);
                                if ($link_type == 'YouTube') {
                                    echo '<iframe src="' . getYoutubeEmbedUrl($external_link['external_link']) . '"></iframe>';
                                } else if ($link_type == 'Image') {
                                    echo '<img src="' . $external_link['external_link'] . '"/>';
                                }
                                echo '</div>';
                            }
                            echo '</div>';
                        }
                        ?>




                    </div>


                    <?php if (is_user_logged_in()) { ?>
                        <div class="assets">
                            <div class="assets__header">
                                <h2>Assets</h2>
                                <?php $assets = get_field('assets') ?>
                                <?php if ($assets) { ?>
                                    <a id="downloadAll" class="btn-custom download-all"> Download All</a>
                                <?php } ?>

                            </div>

                            <div class="assets__wrapper">
                                <?php


                                $size = 'full';
                                if ($assets) {
                                    foreach ($assets as $asset):
                                        $asset_id = $asset['asset'];
                                        $file_path = get_attached_file($asset_id);
                                        $filename = basename($file_path);
                                        $file_size = filesize($file_path); // in bytes
                                        $file_size_mb = round($file_size / 1024 / 1024, 2);

                                        $mime_type = file_type($asset_id);

                                        if ($mime_type == 'image') {
                                            $image_id = $asset_id;
                                        } else {
                                            $image_id = 1190;
                                        }


                                ?>
                                        <div class="assets__card">
                                            <input class="asset-checkbox" type="checkbox" value="<?= wp_get_attachment_url($asset_id) ?>" name="asset-checkbox" filename="<?= $filename ?>">

                                            <button class="btnShowInModal" data-image="<?php echo esc_url(wp_get_attachment_url($asset_id)); ?>">
                                                <?php echo wp_get_attachment_image($image_id, 'medium'); ?>
                                            </button>

                                            <p><?php echo strstr($filename, '.', true); ?></p>
                                            <ul>
                                                <li><?php echo  $file_size_mb; ?>MB</li>
                                                <li><a href="<?php echo wp_get_attachment_url($asset_id) ?>" download><img src="<?php echo get_theme_file_uri() . '/images/download.svg' ?>" alt=""></a></li>
                                            </ul>
                                        </div>

                                <?php endforeach;
                                } else {
                                    echo "No screenshots available";
                                }
                                ?>

                            </div>
                        </div>
                    <?php } ?>

                    <?php if (is_user_logged_in()) { ?>
                        <div class="press-contact">
                            <h2 style="color: var( --e-global-color-primary )">Press Contact</h2>
                            <?= do_shortcode('[display__company_contacts]') ?>
                            <div class="author" style="display: none !important">

                                <img src="<?php echo wp_get_attachment_image_url(get_user_meta(get_the_author_meta('ID'), 'profile_image', true), 'full'); ?>" alt="" />
                                <div class="author__info">
                                    <h4><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')) ?>" class="authorname">
                                            <?php
                                            $author_id = get_the_author_meta('ID');
                                            echo get_the_author_meta('display_name', $author_id);
                                            ?>
                                        </a></h4>

                                    <div class="author__header">
                                        <small>Author</small>
                                        <ul style="margin-bottom:0">
                                            <?php
                                            $author_id = get_the_author_meta('ID');

                                            // ACF fields for user
                                            $social_links = [
                                                'x'         => get_field('x', 'user_' . $author_id),
                                                'linkedin'  => get_field('linkedin', 'user_' . $author_id),
                                                'instagram' => get_field('instagram', 'user_' . $author_id),
                                                'bluesky'   => get_field('bluesky', 'user_' . $author_id),
                                            ];

                                            // Loop through only non-empty links
                                            foreach ($social_links as $platform => $url) {
                                                if (!empty($url)) {
                                                    echo '<li>
								<a href="' . esc_url($url) . '" target="_blank" rel="noopener">
									<img src="' . get_theme_file_uri() . '/images/' . $platform . '.svg" alt="' . esc_attr(ucfirst($platform)) . '" />
								</a>
							</li>';
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </div>

                                    <p>
                                        <?php echo get_the_author_meta('author_bio'); ?>
                                    </p>
                                </div>
                            </div>

                        </div>
                    <?php } ?>

                </div>
            </main>
            <!-- <?php endwhile ?> -->


            <aside class="single__aside">


                <div class="viewed">
                    <?= do_shortcode('[elementor-template id="840"]') ?>
                </div>


                <h2 class="block__header">Popular Posts</h2>

                <div class="popular">
                    <?php
                    $popular = new WP_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 1,
                        'orderby' => 'rand',
                        'post__not_in' => array(get_the_ID())

                    ));
                    if ($popular->have_posts()) : while ($popular->have_posts()) : $popular->the_post() ?>

                            <div class="popular__post">
                                <div class="popular__post__image">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php if (has_post_thumbnail()) {
                                            the_post_thumbnail();
                                        } ?>
                                    </a>
                                    <span class="tag"><?php $categories = get_the_category();
                                                        if (! empty($categories)) {
                                                            echo esc_html($categories[0]->name);
                                                        } ?></span>
                                </div>
                                <ul>
                                    <li>
                                        <img src="<?php echo get_theme_file_uri() ?>/images/clock.svg" alt="" />
                                        <span><?php echo get_the_date("M j, Y") ?></span>
                                    </li>
                                    <li>
                                        <?= get__user_company_flag(get_the_author_meta('ID')) ?>
                                        <span><?= get__user_company(get_the_author_meta('ID'), true) ?> </span>
                                    </li>
                                </ul>

                                <h4>
                                    <a href="<?php the_permalink() ?>"> <?php the_title() ?></a>
                                </h4>


                                <p>
                                    <?php echo wp_trim_words(get_the_excerpt(), 10) ?>
                                </p>
                            </div>

                    <?php endwhile;
                    else :
                        echo "no post";
                    endif;
                    wp_reset_postdata();
                    ?>


                    <?php
                    $popularCard = new WP_Query(array(
                        'post_type' => 'post',
                        'posts_per_page' => 1,
                        'orderby' => 'rand',
                        'post__not_in' => array(get_the_ID())

                    )); ?>
                    <?php if ($popularCard->have_posts()) : while ($popularCard->have_posts()) : $popularCard->the_post() ?>
                            <div class="popular__card">
                                <small><img src="<?php echo get_theme_file_uri() ?>/images/clock.svg" alt="" />
                                    <span><?php echo get_the_date('M j, Y') ?></span></small>
                                <h4><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h4>
                                <p>
                                    <?php echo wp_trim_words(get_the_excerpt(), 10) ?>
                                </p>
                                <ul>
                                    <li>
                                        <?= get__user_company_flag(get_the_author_meta('ID')) ?>
                                        <span><?= get__user_company(get_the_author_meta('ID'), true) ?> </span>
                                    </li>
                                    <li>
                                        <span class="tag"><?php $categories = get_the_category();
                                                            if (! empty($categories)) {
                                                                echo esc_html($categories[0]->name);
                                                            } ?></span>
                                    </li>
                                </ul>
                            </div>
                    <?php endwhile;
                    else :
                        echo "no more post";
                    endif;
                    wp_reset_postdata();
                    ?>
                </div>

            </aside>
        </div>
    </div>
</section>


<div class="modalAssetPreview">
    <div class="backdrop"></div>
    <div class="modal__main">
        <button class="btn__exit"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6 6 18" />
                <path d="m6 6 12 12" />
            </svg></button>
        <img src="https://placehold.co/400x300" alt="">
        <div class="text-center">
            <a download>Download</a>
        </div>
    </div>
</div>



<?php require_once(get_theme_file_path() . '/templates/template-subscribe.php');  ?>


<script>
    const btnShowInModal = document.querySelectorAll('.btnShowInModal');
    const modalAssetPreview = document.querySelector('.modalAssetPreview');
    const btnExit = document.querySelector('.btn__exit');

    btnShowInModal.forEach((btn) => {
        btn.addEventListener('click', () => {
            let imgPath = btn.getAttribute('data-image')
            modalAssetPreview.classList.add('open');
            modalAssetPreview.querySelector('img').src = imgPath
            modalAssetPreview.querySelector('a').href = imgPath
        })
    })


    btnExit.addEventListener('click', () => {
        modalAssetPreview.classList.remove('open');
    })

    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('backdrop')) {
            modalAssetPreview.classList.remove('open');
        }
    })
</script>

<script>
    jQuery(document).ready(function() {
        jQuery('input[name="asset-checkbox"]').on('change', function() {
            // Array to store the values of all checked checkboxes.
            let selectedValues = [];

            // The `:checked` pseudo-selector finds all checkboxes that are currently checked.
            jQuery('input[name="asset-checkbox"]:checked').each(function() {
                // The .val() method gets the value attribute of the current checkbox.
                selectedValues.push($(this).val());
            });

            // Update the result div with the selected values.
            // If the array is not empty, join the values with a comma and space.
            // Otherwise, display a default message.
            if (selectedValues.length > 0) {
                jQuery('#downloadAll').text('Download Selected').addClass('download-selected').removeClass('download-all');
            } else {
                jQuery('#downloadAll').text('Download All').removeClass('download-selected').addClass('download-all');
            }
        });
    });
</script>

<?php get_footer() ?>