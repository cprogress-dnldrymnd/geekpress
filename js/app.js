const choices = new Choices("#category-select", {
  removeItemButton: true,
});

jQuery(document).ready(function () {
  filter();
});

function filter() {
  jQuery('.block__header--filter').click(function (e) {
    jQuery(this).toggleClass('active');
    e.preventDefault();
  });
}