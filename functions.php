<?php

define('HOME_URI', home_url());
define('THEME_PATH', get_stylesheet_directory());
define('THEME_URI', get_stylesheet_directory_uri());
define('THEME_IMAGES', THEME_URI . '/assets/images');
define('THEME_CSS', THEME_URI . '/assets/css');
define('THEME_JS', THEME_URI . '/assets/js');

// WP升级失败，删除core_updater.lock文件
//delete_option('core_updater.lock');
//delete_option('auto_updater.lock');
// 移除所有特色图像
//delete_post_meta_by_key('_thumbnail_id');

if (!isset($content_width)) $content_width = 768;

add_action('after_setup_theme', function () {
  // add_theme_support('post-formats', array('link', 'aside', 'gallery', 'image', 'quote', 'status', 'video', 'audio', 'chat',));
  load_theme_textdomain('example-text', THEME_PATH . '/lang');
  add_theme_support('html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script'));
  add_theme_support('customize-selective-refresh-widgets');
  add_theme_support('title-tag');
  add_theme_support('custom-logo');
  add_theme_support('post-thumbnails');
  register_nav_menus(array('primary' => __('主菜单', 'example-text'), 'secondary' => __('次级菜单', 'example-text'),));
});

// 禁止生成图片尺寸
add_action('intermediate_image_sizes_advanced', function ($sizes) {
  unset($sizes['thumbnail']);     // 150
  // unset($sizes['medium']);        // 300
  // unset($sizes['medium_large']);  // 768
  // unset($sizes['large']);         // 1024
  unset($sizes['1536x1536']);
  unset($sizes['2048x2048']);
  return $sizes;
}, 10);

// 引入样式脚本
add_action('wp_enqueue_scripts',  function () {
  wp_enqueue_style('style', get_stylesheet_uri(), [], filemtime(get_theme_file_path('style.css')), 'all');
  wp_enqueue_script('jquery');
  wp_enqueue_script('scripts', THEME_JS . 'scripts.js', [], filemtime(get_theme_file_path('assets/js/scripts.js')), true);
  if (is_singular() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }
  /* $custom_css = '';
  $main_color = get_theme_mod('wtb_main_color', 'default');
  if ($main_color != 'default') $custom_css .= ":root { --cw-base: var(--cw-{$main_color}) !important; --cw-base-rgb: var(--cw-{$main_color}-rgb) !important; --cw-base-dark: var(--cw-{$main_color}-dark) !important;}";
  wp_add_inline_style('style', $custom_css); */
});

// 注册小工具
add_action('widgets_init', function () {
  register_sidebar(array(
    'id'            => 'sidebar',
    'name'          => esc_html__('侧边栏', 'example-text'),
    'description'   => esc_html__('默认侧边栏', 'example-text'),
    'before_widget' => '<section id="%1$s" class="widget %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h2 class="widget__title">',
    'after_title'   => '</h2>',
  ));
});

// 引入一些文件
require_once get_theme_file_path('/inc/optimize.php');
require_once get_theme_file_path('/inc/hooks.php');

require_once get_theme_file_path('/inc/taxonomy-post-type.php');
require_once get_theme_file_path('/inc/schema-org.php');

require_once get_theme_file_path('/inc/metabox-post.php');
$metabox = new ThemeMetaBox();
$metabox->fields([
  'options' => [
    'id'          => 'metabox',
    'title'       => 'title',
    'description' => 'description',
    'screen'      => ['post'],
    'context'     => 'advanced',
    'priority'    => 'default',
  ],
  'fields' => [
    // text
    // email
    // url
    // number
    // tel
    // date
    // time
    // password
    // textarea
    // checkbox
    // users
    // pages
    [
      'label' => __('文本', 'example-text'),
      'type'  => 'text',
      'id'    => 'text_id',
    ],
    [
      'label' => __('页面', 'example-text'),
      'type'  => 'pages',
      'id'    => 'pages_id',
    ],
    [
      'label'    => __('分类', 'example-text'),
      'type'     => 'categories',
      'id'       => 'categories_id',
      'taxonomy' => ['videos'],
    ],
    // radio
    // select
    [
      'label'   => __('单选', 'example-text'),
      'type'    => 'radio',
      'id'      => 'radio_id',
      'default' => '2',
      'options' => [
        'one',
        'two',
        'other',
      ]
    ],
    [
      'label'       => __('媒体', 'example-text'),
      'type'        => 'media',
      'id'          => 'media_id',
      'returnvalue' => 'id', // 可选id或url模式
    ],
    [
      'label' => 'wysiwyg',
      'type'  => 'wysiwyg',
      'id'    => 'wysiwyg_id',
    ],
  ]
]);

// 是否设置静态页面
$front_page    = get_theme_file_path('front-page.php');
$no_front_page = get_theme_file_path('no-front-page.php');

if (get_option('show_on_front') == 'page' && file_exists($no_front_page)) {
  rename($no_front_page, $front_page);
} elseif (get_option('show_on_front') == 'posts' && file_exists($front_page)) {
  rename($front_page, $no_front_page);
}

if (!function_exists('wp_body_open')) {
  function wp_body_open()
  {
    do_action('wp_body_open');
  }
}

// 添加维护模式
/* function maintenance_mode()
{
  if (!current_user_can('edit_themes') || !is_user_logged_in()) {
    $logo            = 'https://www.itbulu.com';     // 请将此图片地址换为自己站点的 logo 图片地址
    $blogname        = get_bloginfo('name');
    $blogdescription = get_bloginfo('description');
    wp_die('<div style="text-align:center"><img src="' . $logo . '" alt="' . $blogname . '" /><br /><br />' . $blogname . '正在例行维护中，请稍候...</div>', '站点维护中 - ' . $blogname . ' - ' . $blogdescription, array('response' => '503'));
  }
}
add_action('get_header', 'maintenance_mode'); */

// 面板信息
function add_admin_notices()
{
?>
  <div className="notice notice-success is-dismissible">
    <p><?php _e('这是success信息！', 'example-text'); ?></p>
  </div>
  <div className="notice notice-danger is-dismissible">
    <p><?php _e('这是danger信息！', 'example-text'); ?></p>
  </div>
  <div className="notice notice-warning is-dismissible">
    <p><?php _e('这是warning信息！', 'example-text'); ?></p>
  </div>
  <div className="notice notice-info is-dismissible">
    <p><?php _e('这是info信息！', 'example-text'); ?></p>
  </div>
<?php
}

add_action('admin_notices', 'add_admin_notices');
