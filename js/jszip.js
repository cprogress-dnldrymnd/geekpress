const btnDownload = document.getElementById("downloadAll");

btnDownload.addEventListener("click", async () => {
    const zip = new JSZip();
    title = $.trim(jQuery('h1').text());
    const folder = zip.folder("Assets for " + title);

    const fetchPromises = [];

    if (jQuery('#downloadAll').hasClass('download-all')) {
        input = jQuery('input[name="asset-checkbox"]');
    } else {
        input = jQuery('input[name="asset-checkbox"]:checked');
    }

    input.each(function () {
        const url = jQuery(this).val();
        const filename = jQuery(this).attr('filename');

        // Fetch image as blob and add to zip
        const promise = fetch(url)
            .then((response) => response.blob())
            .then((blob) => folder.file(filename, blob));

        fetchPromises.push(promise);
    });


    // Wait for all images to be added
    await Promise.all(fetchPromises);

    // Generate the zip file and trigger download
    zip.generateAsync({ type: "blob" }).then((content) => {
        const link = document.createElement("a");
        link.href = URL.createObjectURL(content);
        link.download = "Assets for " + title + ".zip";
        link.click();
    });
});
