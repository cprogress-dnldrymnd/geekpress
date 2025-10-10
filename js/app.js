jQuery(document).ready(function () {
  filter();
});

function filter() {
  if (window.innerWidth < 992) {
    $height = jQuery('.filter-block-inner').outerHeight();
    jQuery('.filter-block-inner').parent().css('--max-height', $height + 'px');
    jQuery('.block__header--filter').click(function (e) {
      jQuery(this).toggleClass('active');
      e.preventDefault();
    });
  }
}