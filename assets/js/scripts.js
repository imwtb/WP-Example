(function ($) {

  // find
  // children
  // siblings

  const sidebar = $('.sticky__right');
  if (sidebar.length) {
    sidebar.stickySidebar({
      containerSelector: '.layout',
      topSpacing: 32,
      bottomSpacing: 32,
      resizeSensor: true,
      minWidth: 1200,
    })
  }

})(jQuery);