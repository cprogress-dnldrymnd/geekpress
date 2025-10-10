jQuery(document).ready(function () {
  filter();
});

function filter() {
  if (window.innerWidth < 992) {
    $height = jQuery('.filter-block-inner').outerHeight();
    jQuery('.filter-block-inner').parent().css('--max-height', $height + 'px');
    jQuery('.block__header--filter').click(function (e) {
      jQuery('.filter-block-parent').removeClass('remove-max-height');
      jQuery(this).toggleClass('active');
      setTimeout(function () {
        jQuery('.filter-block-parent').addClass('remove-max-height');
      }, 300);
      e.preventDefault();
    });
  }
}