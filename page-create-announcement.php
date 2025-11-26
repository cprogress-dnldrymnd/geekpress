 <?php
    /*--------------------------------------------------*/
    /* Template Name: Submit News 
/*--------------------------------------------------*/
    ?>
 <?php get_header() ?>
 <?php
    if (! is_user_logged_in()) {
        wp_redirect(home_url('/login'));
        add_action('template_redirect', function () {
            wp_redirect(home_url('/create-announcement/'));
            exit;
        });
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = sanitize_text_field($_POST['post_title']);
        $company_id = sanitize_text_field($_POST['company_id']);
        $subheading  = sanitize_text_field($_POST['subheading']);
        $preview_title  = sanitize_text_field($_POST['preview_title']);
        $content = wp_kses_post($_POST['post_content']);
        $categories = array_map('intval', $_POST['post_categories'] ?? []);

        $links = array_map('esc_url_raw', $_POST['external_links']);


        // var_dump($_POST);
        $errors = [];

        if (empty($title)) {
            $errors[] = 'Header is required.';
        } elseif (empty($content)) {
            $errors[] = 'Body is required.';
        } elseif (empty($_FILES['featured_image']['name'])) {
            $errors[] = 'Featured Image is required.';
        }


        if (empty($errors)) {

            $post_id = wp_insert_post([
                'post_title'   => $title,
                'post_content' => $content,
                'post_status'  => 'draft',
                'post_category' => $categories,
                'post_author'  => get_current_user_id(),
                'meta_input'   => array(
                    'preview_title' => $preview_title,
                    'company' => $company_id,
                ),
            ]);

            $assets = [];

            if ($post_id && !is_wp_error($post_id)) {
                if (!empty($_FILES['featured_image']['name'])) {
                    $feat_id = media_handle_upload('featured_image', $post_id);
                    if (!is_wp_error($feat_id)) {
                        set_post_thumbnail($post_id, $feat_id);

                        $assets[] = array(
                            'asset' => $feat_id,
                        );
                    }
                }
                if ($links) {
                    $external_links = [];
                    foreach ($links as $link) {
                        $external_links[] = array(
                            'external_link' => $link
                        );
                    }
                    update_field('external_links', $external_links, $post_id);
                }


                if (!empty($_FILES['assets']['name'][0])) {

                    foreach ($_FILES['assets']['name'] as $key => $value) {

                        $file_size = $_FILES['assets']['size'][$key];

                        if ($file_size > 1 * 1024 * 1024) { // 4MB
                            echo "<p style='color: red;'>Image must be < 4mb.</p>";
                            break;
                        }

                        if ($_FILES['assets']['name'][$key]) {
                            $file = [
                                'name'     => $_FILES['assets']['name'][$key],
                                'type'     => $_FILES['assets']['type'][$key],
                                'tmp_name' => $_FILES['assets']['tmp_name'][$key],
                                'error'    => $_FILES['assets']['error'][$key],
                                'size'     => $_FILES['assets']['size'][$key]
                            ];
                            $_FILES['asset'] = $file;

                            $attach_id = media_handle_upload('asset', $post_id);
                            if (!is_wp_error($attach_id)) {
                                $assets[] = array(
                                    'asset' => $attach_id,
                                );
                            }
                        }
                    }

                    if ($assets) {
                        update_field('assets', $assets, $post_id);
                    }
                }
                update_field('subheading', $subheading, $post_id);

                wp_redirect(get_permalink(1571)); // redirect to new post
                exit;
            }
        }
    }


    // Fetch categories for the select dropdown
    $categories = get_categories([
        'hide_empty' => false,
    ]);

    ?>

 <style>
     .asset__upload__wrapper.xl {
         display: flex;
         flex-wrap: wrap;
     }

     .previewContainer {
         display: contents;
     }

     .medialink h5 {
         font-size: 24px;
     }

     .medialink ul button {
         text-transform: unset;
     }
 </style>

 <section class="page__banner announcement">
     <div class="container">
         <div class="page__banner__wrapper">
             <h2>News Submission</h2>
             <ul>
                 <li><a href="">Home</a></li>
                 <li> ></li>
                 <li>Submit News</li>
             </ul>
         </div>
     </div>
 </section>

 <section class="register announcement">
     <div class="container">
         <div class="register__content">
             <h3>Create your Announcement</h3>
         </div>



         <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // validate + save post data here
                echo '<p class="Thanks for submitting your news. We’ll review it and get it up on the site as soon as possible. If there are any issues, then we’ll let you know</p>';
            }
            ?>
         <form method="post" enctype="multipart/form-data" id="postForm">
             <?php wp_nonce_field('create_custom_post', 'custom_post_nonce'); ?>

             <div class="register__block">
                 <h4>Company Details</h4>

                 <?php $user_companies = get_user_companies() ?>
                 <div class="input__wrapper mb-4">
                     <label>Please select the company you want to submit news</label><br>
                     <select id="company_id" name="company_id" required>
                         <option value="">-- Select company --</option>
                         <?php
                            foreach ($user_companies as $company) {
                                echo '<option value="' . $company . '"> ' . get_the_title($company) . '</option>';
                            }
                            ?>
                     </select>
                 </div>
             </div>
             <div class="register__block">
                 <h4>Announcement Details</h4>


                 <div class="register__grid xl">
                     <div class="input__wrapper">
                         <label for="post_title">Headline</label>
                         <input type="text" id="post_title" name="post_title" placeholder="Enter Headline" required value="<?php echo esc_attr($_POST['post_title'] ?? ''); ?>">
                     </div>

                     <div class="input__wrapper">
                         <label for="subheading">Sub-Header</label>
                         <input type="text" id="subheading" name="subheading" placeholder="Enter Sub-Header" required value="<?php echo esc_attr($_POST['subheading'] ?? ''); ?>">
                     </div>


                     <div class="input__wrapper">
                         <label for="">Body</label>
                         <?php
                            wp_editor(
                                isset($_POST['post_content']) ? $_POST['post_content'] : '',
                                'post_content',
                                [
                                    'textarea_name' => 'post_content',
                                    'textarea_rows' => 10,
                                    'media_buttons' => false,
                                    'tinymce' => [
                                        'toolbar1' => 'undo,redo,removeformat,bold,italic,underline,bullist,numlist,link,unlink',
                                    ],
                                ]
                            );
                            ?>
                     </div>
                     <div class="input__wrapper">
                         <label for="preview_title">Site Homepage Headline (Maximum of 50 Char.)</label>
                         <input type="text" id="preview_title" name="preview_title" placeholder="Enter Site Homepage Headline" required value="<?php echo esc_attr($_POST['preview_title'] ?? ''); ?>" maxlength="50">
                         <div class="help-text">
                             This is a shorter version of your headline for us to show on the front of the site. We recommend just putting the name of your product or service here.
                         </div>
                     </div>


                     <div class="input__wrapper mb-4">
                         <label>Please select a category (max 2)</label><br>
                         <select id="category-select" multiple name="post_categories[]">
                             <option value="">-- Select a category --</option>
                             <?php
                                $categories = get_categories(['hide_empty' => 0]);
                                foreach ($categories as $cat) {
                                    echo '<option value="' . esc_attr($cat->term_id) . '"> ' . esc_html($cat->name) . '</option>';
                                }
                                ?>
                         </select>
                     </div>

                     <h4 class="mb-0">Feature Image *</h4>
                     <div class="input-desc mb-3">
                         <p>
                             Add a feature image that will primarily display for your announcement. Your image must be 5mb or below, and in the following formats: PNG, JPEG/JPG, WebP
                         </p>
                     </div>
                     <div class="asset__upload__wrapper">
                         <div id="feat__preview" class="featpreview__container"></div>
                         <div class="feat__preview__wrapper">
                             <div class="file-input">
                                 <input type="file" id="feat-file" class="file" name="featured_image" accept="image/*">
                                 <label for="feat-file"><svg width="15" height="15" viewBox="0 0 24 24" fill="transparent" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                         <path d="M12 15V3" />
                                         <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                         <path d="m7 10 5 5 5-5" />
                                     </svg><span>Upload</span></label>
                             </div>
                             <span class="error" data-field="image" style="color:red;"></span>
                         </div>
                     </div>
                     <div id="errorContainerFeature" class="error__container"></div>

                     <h4 class="mb-0">Upload Assets</h4>
                     <div class="input-desc mb-3">
                         <p>
                             Upload as many assets as you like under 5mb each. Uploads must be in the following formats: PNG, JPEG/JPG, WebP, PDF
                         </p>
                     </div>

                     <div class="asset__upload__wrapper xl">
                         <div id="previewContainer" class="preview__container"></div>

                         <div class="assets__wrapper dark">
                             <div class="file-input">
                                 <input type="file" id="file" class="file" name="assets[]" multiple accept="image/*, .pdf">
                                 <label for="file"><svg width="15" height="15" viewBox="0 0 24 24" fill="transparent" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                         <path d="M12 15V3" />
                                         <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                         <path d="m7 10 5 5 5-5" />
                                     </svg><span>Upload</span></label>
                             </div>
                         </div>
                     </div>
                     <div id="errorContainer" class="error__container"></div>


                     <div class="medialink">
                         <h4 class="mb-0">Video Links</h4>
                         <div class="input-desc mb-3">
                             <p>Add a video to your post - trailers, product description or more. We support links from YouTube and Vimeo to your post! </p>
                         </div>


                         <div id="external-links-wrapper">
                             <div class="external-link-item" style="display:flex; align-items:center; margin-bottom:5px;">
                                 <input type="url" name="external_links[]" placeholder="https://example.com" style="flex:1; margin-right:8px;">
                                 <span class="remove-link" onclick="removeLink(this)" style="cursor:pointer; display:flex; align-items:center;">
                                     <!-- Trash SVG -->
                                     <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="#888">
                                         <path d="M3 6h18v2H3V6zm2 3h14l-1.4 12.6A2 2 0 0 1 15.6 23H8.4a2 2 0 0 1-1.99-1.4L5 9zm4 2v9h2v-9H9zm4 0v9h2v-9h-2zM9 4V3h6v1h5v2H4V4h5z" />
                                     </svg>
                                 </span>
                             </div>
                         </div>

                         <ul>
                             <li>
                                 <button type="button" class="insert__link" onclick="addMoreLinks()">
                                     Add Video Link <img src="<?php echo get_theme_file_uri() ?>/images/plus.svg" alt="">
                                 </button>
                             </li>
                         </ul>


                         <div id="response"></div>


                         <?php
                            $links = get_user_meta(get_current_user_id(), 'external_links', true);
                            if (!empty($links) && is_array($links)) {
                                echo '<ul>';
                                foreach ($links as $link) {
                                    echo '<li><a href="' . esc_url($link) . '" target="_blank">' . esc_html($link) . '</a></li>';
                                }
                                echo '</ul>';
                            }
                            ?>
                     </div>
                     <div class="register-agreement">
                         <p>
                             <strong>PLEASE NOTE.</strong> All posts need to adhere to the GeekPress Terms of Service, which you can read <a href="https://geekpress.theprogressteam.com/terms-of-service/" target="_blank">here</a>... Failure to do so can result in your news being rejected. Thanks.
                         </p>
                     </div>
                     <input type="submit" id="btnSubmit" name="submit_post" value="Submit" class="btn-custom btn-outline" disabled>

                     <?php if (!empty($errors)) : ?>
                         <div class="error-messages" style="color: red; margin-top: 10px;">
                             <?php foreach ($errors as $error) : ?>
                                 <p><?php echo esc_html($error); ?></p>
                             <?php endforeach; ?>
                         </div>
                     <?php endif; ?>
                 </div>
             </div>
         </form>
     </div>
 </section>

 <script>
     // --- Gallery Image Uploader ---
     const fileInput = document.getElementById("file");
     const preview = document.getElementById("previewContainer");
     let filesArray = [];

     fileInput.addEventListener("change", (e) => {
         const newFiles = Array.from(e.target.files);

         newFiles.forEach((file) => {
             if (file.size > 5 * 1024 * 1024) {
                 // Assuming you have an element with id="errorContainer"
                 // errorContainer.innerHTML = `${file.name} exceeds 5MB and will be ignored.`;
                 alert(`${file.name} exceeds 5MB and will be ignored.`);
             } else {
                 filesArray.push(file);
             }
         });

         renderPreviews();
         syncInputFiles();
     });

     function renderPreviews() {
         preview.innerHTML = "";
         filesArray.forEach((file, index) => {
             const fileUrl = URL.createObjectURL(file);
             preview.innerHTML += `
                <div class="preview">
                    <img src="${fileUrl}" alt="${file.name}"/>
                    <button class="remove-button" type="button" onclick="removeFile(${index})"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-icon lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
                    <h5>${file.name}</h5>
                    <ul>
                        <li><small>${(file.size / 1024).toFixed(2)} KB</small></li>
                        
                    </ul>
                </div>
            `;
         });
     }

     window.removeFile = function(index) {
         filesArray.splice(index, 1);
         renderPreviews();
         syncInputFiles(); // Re-sync after removal
     }

     function syncInputFiles() {
         const dataTransfer = new DataTransfer();
         filesArray.forEach((file) => dataTransfer.items.add(file));
         fileInput.files = dataTransfer.files;
     }

     // --- Featured Image Uploader ---
     const fileFeatInput = document.querySelector('#feat-file');
     const fileFeatPreview = document.querySelector('#feat__preview');
     let filesFeatArray = [];

     fileFeatInput.addEventListener("change", (e) => {
         filesFeatArray = Array.from(e.target.files);
         renderFeaturePreview();
         syncFeatInputFiles(); // Sync on initial selection
     });

     function renderFeaturePreview() {
         fileFeatPreview.innerHTML = "";
         filesFeatArray.forEach((file, index) => {
             if (file.size > 5 * 1024 * 1024) {
                 // Assuming you have an element with id="errorContainerFeature"
                 // errorContainerFeature.innerHTML = `${file.name} exceeds 5MB and will be ignored.`;
                 alert(`${file.name} exceeds 5MB and will be ignored.`);
                 filesFeatArray.splice(index, 1); // Remove the oversized file
                 return;
             }
             const fileUrlFeat = URL.createObjectURL(file);
             // Use '=' instead of '+=' since it's a single featured image
             fileFeatPreview.innerHTML = `
                <div class="preview">
                    <img src="${fileUrlFeat}" alt="${file.name}"/>
                    <button type="button" class="remove-button" onclick="removeFeaturedFile(${index})"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-icon lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
                    <h5>${file.name}</h5>
                    <ul>
                        <li><small>${(file.size / 1024).toFixed(2)} KB</small></li>
                    </ul>
                </div>
            `;

             jQuery('.feat__preview__wrapper').hide();
             jQuery('#btnSubmit').removeAttr('disabled');
         });
     }

     // **FIXED FUNCTION**
     window.removeFeaturedFile = function(index) {
         // 1. Target the correct array: filesFeatArray
         filesFeatArray.splice(index, 1);
         // 2. Call the correct render function: renderFeaturePreview
         renderFeaturePreview();
         // 3. Call the correct sync function
         syncFeatInputFiles();
         jQuery('.feat__preview__wrapper').show();

         jQuery('#btnSubmit').attr('disabled');

     }

     // **NEW SYNC FUNCTION FOR FEATURED IMAGE**
     function syncFeatInputFiles() {
         const dataTransfer = new DataTransfer();
         filesFeatArray.forEach((file) => dataTransfer.items.add(file));
         fileFeatInput.files = dataTransfer.files;
     }

     // --- External Links ---
     function addMoreLinks() {
         const wrapper = document.getElementById("external-links-wrapper");
         const item = document.createElement("div");
         item.className = "external-link-item";
         item.style.display = "flex";
         item.style.alignItems = "center";
         item.style.marginBottom = "5px";

         const input = document.createElement("input");
         input.type = "url";
         input.name = "external_links[]";
         input.placeholder = "https://example.com";
         input.style.flex = "1";
         input.style.marginRight = "8px";

         const removeIcon = document.createElement("span");
         removeIcon.className = "remove-link";
         removeIcon.style.cursor = "pointer";
         removeIcon.style.display = "flex";
         removeIcon.style.alignItems = "center";
         removeIcon.innerHTML = `
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" fill="#888">
				<path d="M3 6h18v2H3V6zm2 3h14l-1.4 12.6A2 2 0 0 1 15.6 23H8.4a2 2 0 0 1-1.99-1.4L5 9zm4 2v9h2v-9H9zm4 0v9h2v-9h-2zM9 4V3h6v1h5v2H4V4h5z"/>
			</svg>
		`;
         removeIcon.onclick = function() {
             removeLink(removeIcon);
         };

         item.appendChild(input);
         item.appendChild(removeIcon);
         wrapper.appendChild(item);
     }

     function removeLink(el) {
         el.parentElement.remove();
     }
 </script>


 <?php get_footer() ?>