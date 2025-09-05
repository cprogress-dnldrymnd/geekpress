jQuery(function ($) {
  $("#loadmore-post").on("click", function () {
    var button = $(this);
    var page = button.data("page");
    page++;

    $.ajax({
      url: my_loadmore_params.ajaxurl,
      type: "POST",
      data: {
        action: "loadmore",
        paged: page,
      },
      beforeSend: function () {
        button.text("Loading...");
      },
      success: function (response) {
        if (response.trim() !== "") {
          $(".post-container").append(response);
          button.data("page", page);
          button.text("Load More");
        } else {
          button.remove();
        }
      },
    });
  });
});
