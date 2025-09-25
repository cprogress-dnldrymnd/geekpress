<?php 
if ( ! is_user_logged_in() ) {
    wp_redirect(home_url('/login') );
    add_action('template_redirect', function() {
        wp_redirect(home_url(''));
        exit;
});

} 
get_header() ?>

<section class="page__banner announcement">
    <div class="container">
        <div class="page__banner__wrapper">
            <h2>News Submission</h2>
            <ul>
                <li><a href=""#>Home</a></li>
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
    




   

    <?php $messages = [];
    wp_nonce_field('frontend_post_nonce'); 

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['custom_post_nonce']) && wp_verify_nonce($_POST['custom_post_nonce'], 'create_custom_post')) {

        wp_send_json(['success' => false, 'message' => 'Security check failed.']);


        $title = sanitize_text_field($_POST['post_title']);
        $subtitle  = sanitize_text_field($_POST['post_subtitle']);
        $content = wp_kses_post($_POST['post_content']);
        $categories = array_map('intval', $_POST['post_categories'] ?? []);

        // var_dump($_POST);

    

        if (!empty($title) && !empty($content)) {

            $post_id = wp_insert_post([
                'post_title'   => $title,
                'post_content' => $content,
                'post_status'  => 'publish',
                'post_category'=> $categories,
                'post_author'  => get_current_user_id(),
            ]);

            

            if ($post_id && !is_wp_error($post_id)) {
                if (!empty($_FILES['featured_image']['name'])) {
                    $feat_id = media_handle_upload('featured_image', $post_id);
                    if (!is_wp_error($feat_id)) {
                        set_post_thumbnail($post_id, $feat_id);
                    }
                }    
                update_post_meta($post_id, 'post_subtitle', $subtitle);
                $attachment_id = media_handle_upload('feature_image', $post_id);
                if (!is_wp_error($attachment_id)) {
                    update_post_meta($post_id, 'feature_image', $attachment_id);
                }

                if (!empty($_FILES['gallery_images']['name'][0])) {
                    $gallery_ids = [];
                    foreach ($_FILES['gallery_images']['name'] as $key => $value) {

                        $file_size = $_FILES['gallery_images']['size'][$key];
    
                        if ($file_size > 1 * 1024 * 1024) { // 4MB
                            echo "<p style='color: red;'>Image must be < 4mb.</p>";
                            break;
                        }
                        if ($_FILES['gallery_images']['name'][$key]) {
                            $file = [
                                'name'     => $_FILES['gallery_images']['name'][$key],
                                'type'     => $_FILES['gallery_images']['type'][$key],
                                'tmp_name' => $_FILES['gallery_images']['tmp_name'][$key],
                                'error'    => $_FILES['gallery_images']['error'][$key],
                                'size'     => $_FILES['gallery_images']['size'][$key]
                            ];
                            $_FILES['gallery_single'] = $file;

                            $attach_id = media_handle_upload('gallery_single', $post_id);
                            if (!is_wp_error($attach_id)) {
                                $gallery_ids[] = $attach_id;
                            }
                        }
                    }

                    if ($gallery_ids) {
                        update_post_meta($post_id, 'gallery_images', $gallery_ids);
                    }
                }
                 wp_send_json(['success' => true, 'message' => 'Post published successfully!']);
                 wp_redirect(get_permalink($post_id)); // redirect to new post
                exit;
            } else {
                echo "<p style='color: red;'>Error creating post.</p>";
            }
        } else {
            echo "<p style='color: red;'>Please fill in all required fields.</p>";
        }
    }


    
    // Fetch categories for the select dropdown
    $categories = get_categories([
        'hide_empty' => false,
    ]); ?>

    <form method="post" enctype="multipart/form-data" id="customPostForm">
                <?php wp_nonce_field('create_custom_post', 'custom_post_nonce'); ?>

           <div class="register__block">
            <h4>Announcement Details</h4>


            <div class="register__grid xl">
                <div class="input__wrapper">
                    <label for="post_title">Header</label>
                    <input type="text" id="post_title" name="post_title" required placeholder="Enter Heading">
                </div>

                <div class="input__wrapper">
                    <label for="post_subtitle">Sub-Heading</label>
                  <input type="text" id="post_subtitle" name="post_subtitle" required placeholder="Enter Sub-heading">
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
                            ]
                        );
                        ?>
                            </div>

                <div class="input__wrapper mb-4">
                    <label>Categories</label><br>
                     <select id="category-select" multiple name="post_categories[]">
                        <option value="">-- Select a category --</option>
                        <?php
                        $categories = get_categories(['hide_empty' => 0]);
                        foreach ($categories as $cat) {
                            echo '<option value="'.esc_attr($cat->term_id).'"> '.esc_html($cat->name).'</option>';
                        }
                        ?>
                     </select>
                </div>

                <h4>Featured Image</h4>

                <div class="asset__upload__wrapper">
                <div id="feat__preview" class="featpreview__container"></div>
                <div class="feat__preview__wrapper">
                    <div class="file-input">
                        <input type="file" id="feat-file" class="file" name="featured_image"  accept="image/*">
                        <label for="feat-file"><svg width="15" height="15" viewBox="0 0 24 24" fill="transparent" stroke="#0d0629" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15V3"/><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/></svg><span>Upload</span></label>
                    </div>
                </div>
            </div>
            <div id="errorContainerFeature" class="error__container"></div>




                <h4>Upload Assets</h4>

                <div class="asset__upload__wrapper xl">
                    <div  id="previewContainer" class="preview__container"></div>

                    <div class="assets__wrapper dark">
                        <div class="file-input">
                        <input type="file" id="file" class="file" name="gallery_images[]" multiple accept="image/*">
                        <label for="file"><svg width="15" height="15" viewBox="0 0 24 24" fill="transparent" stroke="#0d0629" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 15V3"/><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5"/></svg><span>Upload</span></label>
                        </div>
                    </div>
                </div>
                <div id="errorContainer" class="error__container"></div>


                          


                <div class="medialink">
                    <h5>Media Links</h5>
                    <ul>
                        <li><button type="button" class="insert__link" onclick="addExternalLink()">Insert Media Link <img src="<?php echo get_theme_file_uri()?>/images/plus.svg" alt=""> </button></li>
                    </ul>


                    <div id="external-links-fields">
                        <input type="url" name="external_links[]" placeholder="https://example.com" />
                    </div>
                </div>

                  
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
                <input type="submit" name="submit_post" value="Submit" class="btn-custom btn-outline" id="submitBtn">
                 <div id="spinner" style="display:none;margin-top:10px;">Saving...</div>
                 <div id="response" style="margin-top:10px;"></div>
            </div>
        </div>
    </form>
    </div>
</section>


<script>

    const fileInput = document.getElementById("file");
    const preview = document.getElementById("previewContainer");
    const errorContainer = document.getElementById('errorContainer');
    const errorContainerFeature = document.getElementById('errorContainerFeature');
    let filesArray = [];

    // fileInput.addEventListener("change", (e) => {
    //     filesArray = Array.from(e.target.files);
    //     renderPreviews();
    // });

   fileInput.addEventListener("change", (e) => {
    const newFiles = Array.from(e.target.files);

    newFiles.forEach((file) => {
     
        if (file.size > 5 * 1024 * 1024) {
        //    alert(file.name + " exceeds 5MB and will be ignored.");
          errorContainer.innerHTML = `${file.name} exceeds 5MB and will be ignored.`; 
           
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
            if (file.size > 5 * 1024 * 1024) {
                // alert(file.name + " exceeds 5MB and will be ignored.");
                 errorContainer.innerHTML = `${file.name} exceeds 5MB and will be ignored.`; 
                filesArray.splice(index, 1);
                return;
            }
            const fileUrl = URL.createObjectURL(file);
            preview.innerHTML += `
                <div class="preview">
               <img src="${fileUrl}" alt="${file.name}"/>
                <h5>${file.name}</h5>
                <ul>
                    <li><small>${(file.size / 1024).toFixed(2)} KB</small></li>
                    <li> <button type="button" onclick="removeFile(${index})"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x-icon lucide-x"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button></li>
                </ul>
                <div>
            `;
        });
        syncInputFiles();
    }
     window.removeFile = function(index) {
        filesArray.splice(index, 1);
        renderPreviews();
    }
    function syncInputFiles() {
        const dataTransfer = new DataTransfer();
        filesArray.forEach((file) => dataTransfer.items.add(file));
        fileInput.files = dataTransfer.files;
    }



const fileFeatInput = document.querySelector('#feat-file');
const fileFeatPreview = document.querySelector('#feat__preview');
let filesFeatArray = [];

function renderFeaturePreview(e) {
    fileFeatPreview.innerHTML = "";
    filesFeatArray.forEach((file, index) => {
         if (file.size > 5 * 1024 * 1024) {
                errorContainerFeature.innerHTML = `${file.name} exceeds 5MB and will be ignored.`; 
                return;
            }
    const fileUrlFeat = URL.createObjectURL(file);
    fileFeatPreview.innerHTML = `
        <div class="preview">
            <img src="${fileUrlFeat}" alt="${file.name}"/>
            <h5>${file.name}</h5>
            <ul>
                <li><small>${(file.size / 1024).toFixed(2)} KB</small></li>
            </ul>
        <div>
    `;
});
}

fileFeatInput.addEventListener("change", (e) => {
    filesFeatArray = Array.from(e.target.files);
    renderFeaturePreview();
} );




function addExternalLink() {
    const container = document.querySelector('#external-links-fields');
    const inputLink = document.querySelector('#external-links-fields input');
    const input = document.createElement('input');
    input.type = 'url';
    input.name = 'external_links[]';
    input.placeholder = 'https://example.com';
    container.appendChild(input);

   container.classList.add('open')

}

function submitExternalLinks() {
    const inputs = document.querySelectorAll('input[name="external_links[]"]');
    const links = [];
    inputs.forEach(input => {
        if (input.value.trim() !== '') {
            links.push(input.value.trim());
        }
    });

    const data = {
        action: 'save_external_links',
        external_links: links,
        security: '<?php echo wp_create_nonce("save_external_links_nonce"); ?>'
    };

    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(data).toString()
    })
    .then(response => response.text())
    .then(result => {
        document.getElementById('response').innerHTML = result;
    });
}






document.getElementById('customPostForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const spinner = document.getElementById('spinner');
    const response = document.getElementById('response');
    const submitBtn = document.getElementById('submitBtn');

    spinner.style.display = 'block';
    response.innerHTML = '';
    submitBtn.disabled = true;

    const formData = new FormData(this);

    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        spinner.style.display = 'none';
        submitBtn.disabled = false;
        response.innerHTML = data.message;
        if (data.success) {
            document.getElementById('customPostForm').reset();
        }
    })
    .catch(err => {
        spinner.style.display = 'none';
        submitBtn.disabled = false;
        response.innerHTML = 'An error occurred.';
    });
});

</script>


<?php get_footer() ?>

