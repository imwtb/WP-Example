<?php

//add_filter('use_block_editor_for_post', '__return_false');                                      // 使用 旧版文章编辑器
add_filter('gutenberg_use_widgets_block_editor', '__return_false');                             // 移除 古腾堡中的旧版小部件
add_filter('use_widgets_block_editor', '__return_false');                                       // 使用 旧版小工具
//add_filter('show_admin_bar', '__return_false');                                                 // 禁止 工具栏
add_filter('automatic_updater_disabled', '__return_true');                                      // 禁止 核心自动更新
add_filter('auto_update_plugin', '__return_false');                                             // 禁止 插件自动更新
add_filter('auto_update_theme', '__return_false');                                              // 禁止 主题自动更新
add_filter('pre_site_transient_browser_' . md5($_SERVER['HTTP_USER_AGENT']), '__return_null');  // 禁止 浏览器检查版本
add_filter('run_wptexturize', '__return_false');                                                // 禁止 花括号
add_filter('pre_option_link_manager_enabled', '__return_true');                                 // 添加 友情链接

remove_action('wp_head', 'wp_generator');                               // 移除 Generator
remove_action('wp_head', 'rsd_link');                                   // 移除 EditURI
remove_action('wp_head', 'wlwmanifest_link');                           // 移除 wlwmanifest
//remove_action('wp_head', 'rel_canonical');                            // 移除 canonical
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);                // 移除 Shortlink
remove_action('wp_body_open', 'wp_global_styles_render_svg_filters');   // 移除 SVG
remove_action('wp_head', 'wp_resource_hints', 2);                       // 移除 dns-prefetch
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);             // 移除 next/prev
remove_action('wp_enqueue_scripts', 'wp_enqueue_global_styles');        // 移除 global-styles-inline-css
// remove_action('wp_head', 'wp_robots', 1);                            // 移除 robots

// 移除 RSS
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'feed_links_extra', 3);

// 移除 Emoji
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// 移除 Json api
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('template_redirect', 'rest_output_link_header', 11, 0);
remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');

// 移除 Api.w.org * REST API 功能仍将像以前一样工作；这只会删除正在插入的标头代码
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);

// 移除左右页脚文本
add_filter('admin_footer_text', '__return_empty_string');
add_filter('update_footer', '__return_empty_string', 11);

// 禁止生成图片尺寸
add_filter('big_image_size_threshold', '__return_false');
add_action('intermediate_image_sizes_advanced', function ($sizes) {
  unset($sizes['thumbnail']);     // 150
  // unset($sizes['medium']);        // 300
  // unset($sizes['medium_large']);  // 768
  // unset($sizes['large']);         // 1024
  unset($sizes['1536x1536']);
  unset($sizes['2048x2048']);
  return $sizes;
}, 10);

add_action('wp_enqueue_scripts', function () {
  wp_dequeue_style('classic-theme-styles');    // classic-theme-styles-css
  wp_dequeue_style('wp-block-library');        // wp-block-library-css
  wp_dequeue_style('wp-block-library-theme');  // wp-block-library-theme-inline-css
  wp_dequeue_style('wc-blocks-style');         // WOOCOMMERCE CSS
}, 10);

// 移除 谷歌字体
add_action('init', function () {
  wp_deregister_style('open-sans');
  wp_register_style('open-sans', false);
  wp_enqueue_style('open-sans', '');
});

// 移除 jquery-migrate
add_action('wp_default_scripts', function ($scripts) {
  if (!is_admin() && isset($scripts->registered['jquery'])) {
    $script = $scripts->registered['jquery'];
    if ($script->deps) {
      $script->deps = array_diff($script->deps, ['jquery-migrate']);
    }
  }
});

// Cravatar 替代 Gravatar
add_filter('um_user_avatar_url_filter', 'get_cn_avatar_url', 1);
add_filter('bp_gravatar_url', 'get_cn_avatar_url', 1);
add_filter('get_avatar_url', 'get_cn_avatar_url', 1);
function get_cn_avatar_url($url)
{
  return str_replace(['www.gravatar.com', '0.gravatar.com', '1.gravatar.com', '2.gravatar.com', 'secure.gravatar.com', 'cn.gravatar.com', 'gravatar.com'], 'cravatar.cn', $url);
}
add_filter('avatar_defaults', function ($avatar_defaults) {
  $avatar_defaults['gravatar_default'] = 'Cravatar 标志';
  return $avatar_defaults;
}, 1);
add_filter('user_profile_picture_description', function () {
  return '<a href="https://cravatar.cn" target="_blank">您可以在 Cravatar 修改您的资料图片</a>';
}, 1);

// 移除 所有帮助
add_action('admin_head', function () {
  $screen = get_current_screen();
  $screen->remove_help_tabs();
});

add_action('wp_dashboard_setup', function () {
  remove_action('welcome_panel', 'wp_welcome_panel');               // 移除 欢迎
  remove_meta_box('dashboard_site_health', 'dashboard', 'normal');  // 移除 站点健康
  remove_meta_box('dashboard_quick_press', 'dashboard', 'side');    // 移除 快速草稿
  remove_meta_box('dashboard_primary', 'dashboard', 'side');        // 移除 新闻
});
add_action('wp_before_admin_bar_render',  function () {
  global $wp_admin_bar;
  $wp_admin_bar->remove_menu('wp-logo');  // 移除 WordPress LOGO
  $wp_admin_bar->remove_menu('comments'); // 移除 评论链接
}, 999);
