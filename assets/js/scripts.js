(function ($) {

  // find
  // children
  // siblings

  const sidebar = $('.sticky__right');
  if (sidebar.length) {
    sidebar.stickySidebar({
      topSpacing: 96,
      bottomSpacing: 32,
      resizeSensor: true,
      minWidth: 1200,
    })
  }

})(jQuery);