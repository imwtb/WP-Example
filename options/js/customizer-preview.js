(function ($) {

  wp.customize.bind('preview-ready', function () {

    /* wp.customize('wtb_thumb_height', function (data) {
      data.bind(function (to) {
        $('.post-thumb-backg').css('height', to);
        //var text_colour = wp.customize('header_textcolor')();
        // ... now do something with text_colour
      });
    });

    wp.customize('search_menu_icon', function (control) {
      control.bind(function (to) {
        if (to == true) {
          $('.nav-menu').append('<li class="menu-item menu-item-search"></li>');
        } else {
          $('li.menu-item-search').remove();
        }
      });
    }); */

    // 搜索
    wp.customize('wtb_search_title', function (data) {
      data.bind(function (to) {
        $('.searchbox-title').html(to);
      });
    });

    wp.customize('wtb_search_placeholder', function (data) {
      data.bind(function (to) {
        $('.search-input').attr('placeholder', to);
      });
    });

    // 新手/老手
    wp.customize('wtb_newold_title_1', function (data) {
      data.bind(function (to) {
        $('.cwnewold_1 a').html(to);
      });
    });
    wp.customize('wtb_newold_desc_1', function (data) {
      data.bind(function (to) {
        $('.cwnewold_1 span').html(to);
      });
    });
    wp.customize('wtb_newold_title_2', function (data) {
      data.bind(function (to) {
        $('.cwnewold_2 a').html(to);
      });
    });
    wp.customize('wtb_newold_desc_2', function (data) {
      data.bind(function (to) {
        $('.cwnewold_2 span').html(to);
      });
    });

    // 底部
    wp.customize('wtb_copyright', function (data) {
      data.bind(function (to) {
        $('.cwfoo-copyright span').html(to);
      });
    });

  });

})(jQuery);