<?php

// WP升级失败，删除core_updater.lock文件
//delete_option('core_updater.lock');
//delete_option('auto_updater.lock');
// 移除所有特色图像
//delete_post_meta_by_key('_thumbnail_id');

if (!isset($content_width)) $content_width = 960;

add_action('after_setup_theme', function () {
  load_theme_textdomain('imwtb', get_template_directory() . '/languages');
  //add_theme_support('post-formats', ['aside', 'gallery', 'link', 'image', 'quote', 'status', 'video', 'audio', 'chat']);
  add_theme_support('post-thumbnails');
  //add_theme_support('custom-background');
  //add_theme_support('custom-header');
  add_theme_support('custom-logo');
  //add_theme_support('automatic-feed-links');
  add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script']);
  add_theme_support('title-tag');
  add_theme_support('customize-selective-refresh-widgets');

  /*----- Gutenberg -----*/
  // @link https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-support/
  //add_theme_support('wp-block-styles');
  add_theme_support('align-wide');
  //add_theme_support('editor-color-palette');
  //add_theme_support('editor-gradient-presets');
  //add_theme_support('editor-font-sizes');
  add_theme_support('disable-custom-font-sizes');
  //add_theme_support('disable-custom-colors');
  //add_theme_support('disable-custom-gradients');
  //add_theme_support('disable-layout-styles');
  add_theme_support('custom-line-height');
  add_theme_support('custom-units');
  add_theme_support('custom-spacing');
  add_theme_support('responsive-embeds');
  //add_theme_support('editor-styles');
  //add_editor_style('/assets/css/style-editor.css');
  remove_theme_support('core-block-patterns');
});

// 注册菜单
register_nav_menus([
  'primary'   => __('主菜单', 'imwtb'),
  'secondary' => __('次级菜单', 'imwtb')
]);

// 引入样式脚本
add_action('wp_enqueue_scripts',  function () {

  wp_enqueue_style('style', get_stylesheet_uri(), [], filemtime(get_template_directory() . '/style.css'), 'all');
  wp_enqueue_style('iconoir', get_template_directory_uri() . '/assets/css/iconoir.css', [], '5.4', 'all');
  wp_enqueue_style('bbpress', get_template_directory_uri() . '/assets/css/bbpress.css', filemtime(get_template_directory() . '/assets/css/bbpress.css'), null, 'all');

  wp_enqueue_script('jquery');
  wp_enqueue_script('qrcode-min', get_template_directory_uri() . '/assets/js/qrcode.min.js', [], null, true);
  wp_enqueue_script('resizesensor-min', get_template_directory_uri() . '/assets/js/resizesensor.min.js', [], '1.2.2', true);
  wp_enqueue_script('stickysidebar-min', get_template_directory_uri() . '/assets/js/stickysidebar.min.js', [], '3.3.1', true);
  wp_enqueue_script('scripts', get_template_directory_uri() . '/assets/js/scripts.js', [], filemtime(get_template_directory() . '/assets/js/scripts.js'), true);
  if (is_singular() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }
  /* $custom_css = '';
  $main_color = get_theme_mod('wtb_main_color', 'default');
  if ($main_color != 'default') $custom_css .= ":root { --cw-base: var(--cw-{$main_color}) !important; --cw-base-rgb: var(--cw-{$main_color}-rgb) !important; --cw-base-dark: var(--cw-{$main_color}-dark) !important;}";
  wp_add_inline_style('style', $custom_css); */
});

// wp_body_open
if (!function_exists('wp_body_open')) {
  function wp_body_open()
  {
    do_action('wp_body_open');
  }
}

// 跳到内容
add_action('wp_body_open', function () {
  echo '<a class="skip-link screen-reader-text" href="#content">' . __('跳到内容', 'imwtb') . '</a>';
}, 10, 3);

// 文章内容图片
function the_content_thumbnails()
{
  global $post;

  $image = '';
  ob_start();
  $output = preg_match_all('/<img.*?src=[\'|\"](.+?)[\'|\"].*?>/i', get_post($post)->post_content, $matches);
  print_r($matches[1]);
  $image = $matches[1];
  ob_end_clean();
  return $image;
}

function the_content_thumbnail($width = '768', $height = 'auto', $number = 1)
{
  foreach (the_content_thumbnails() as $key => $value) {
    if ($key < $number) {
      echo '<img width="' . $width . '" height="' . $height . '" src="' . esc_url($value) . '">';
    }
  }
}

function get_the_content_thumbnail($number = 1)
{
  foreach (the_content_thumbnails() as $key => $value) {
    if ($key < $number) {
      return $value;
    }
  }
}

// 摘要字数
add_filter('excerpt_length', function ($length) {
  return 64;
}, 999);

// 归档标题
add_filter('get_the_archive_title', function ($title) {
  if (is_category()) {
    $title = single_cat_title('', false);
  } elseif (is_tag()) {
    $title = single_tag_title('', false);
  } elseif (is_search()) {
    $title = sprintf(__('搜索%s结果如下', 'imwtb'), '<span>' . get_search_query() . '</span>');
  } elseif (is_author()) {
    $title = get_the_author();
  } elseif (is_year()) {
    $title  = get_the_date('Y');
  } elseif (is_month()) {
    $title  = get_the_date('F Y');
  } elseif (is_day()) {
    $title  = get_the_date('F j, Y');
  } elseif (is_post_type_archive()) {
    $title = post_type_archive_title('', false);
  } elseif (is_tax()) {
    $title = single_term_title('', false);
  }
  return $title;
});

// 删除文章链接
function delete_post_link($text = null, $before = '', $after = '', $id = 0, $class = 'post-delete-link')
{
  $post = get_post($id);
  if (!$post) {
    return;
  }
  $url = get_delete_post_link($post->ID);
  if (!$url) {
    return;
  }
  if (null === $text) {
    $text = __('删除', 'imwtb');
  }
  $link = '<a class="' . esc_attr($class) . '" href="' . esc_url($url) . '">' . $text . '</a>';
  echo $before . apply_filters('delete_post_link', $link, $post->ID, $text) . $after;
}

// 改为多少时间前
/* add_filter('get_the_date', 'post_time_ago', 10, 1);
add_filter('the_date', 'post_time_ago', 10, 1);
add_filter('get_the_time', 'post_time_ago', 10, 1); */
add_filter('the_time', 'post_time_ago', 10, 1);
add_filter('get_comment_date', 'post_time_ago', 10, 1);
function post_time_ago($time)
{
  global $post;
  $time = strtotime($post->post_date);
  return human_time_diff($time, current_time('timestamp')) . __('前', 'imwtb');
}

// bbsPress论坛可视化编辑器
function bbp_enable_visual_editor($args = array())
{
  $args['tinymce'] = true;
  $args['quicktags'] = false;
  //$args['teeny'] = false;
  return $args;
}
add_filter('bbp_after_get_the_content_parse_args', 'bbp_enable_visual_editor');

// 添加维护模式
/* add_action('get_header', function () {
  if (!current_user_can('edit_themes') || !is_user_logged_in()) {
    $logo            = 'https://www.itbulu.com';     // 请将此图片地址换为自己站点的 logo 图片地址
    $blogname        = get_bloginfo('name');
    $blogdescription = get_bloginfo('description');
    wp_die('<div style="text-align:center"><img src="' . $logo . '" alt="' . $blogname . '" /><br /><br />' . $blogname . '正在例行维护中，请稍候...</div>', '站点维护中 - ' . $blogname . ' - ' . $blogdescription, ['response' => '503']);
  }
}); */

// 面板信息
/* add_action('admin_notices', function () {
?>
  <div className="notice notice-success is-dismissible">
    <p><?php _e('这是success信息！', 'imwtb'); ?></p>
  </div>
  <div className="notice notice-danger is-dismissible">
    <p><?php _e('这是danger信息！', 'imwtb'); ?></p>
  </div>
  <div className="notice notice-warning is-dismissible">
    <p><?php _e('这是warning信息！', 'imwtb'); ?></p>
  </div>
  <div className="notice notice-info is-dismissible">
    <p><?php _e('这是info信息！', 'imwtb'); ?></p>
  </div>
<?php
}); */

// 注册小工具
add_action('widgets_init', function () {
  register_sidebar([
    'name'          => __('侧边栏', 'imwtb'),
    'description'   => __('默认侧边栏', 'imwtb'),
    'id'            => 'sidebar',
    'before_widget' => '<section id="%1$s" class="widget %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h2 class="widget__title">',
    'after_title'   => '</h2>',
  ]);
});
require_once get_template_directory() . '/widgets/widget.php';

// 引入文件
require_once get_template_directory() . '/inc/optimize.php';
require_once get_template_directory() . '/inc/comments.php';

require_once get_template_directory() . '/inc/meta-schema.php';

/* require_once get_template_directory() . '/inc/taxonomy-post-type.php';
add_action('init', function () {
  register_custom_post_type(__('产品', 'imwtb'), 'product', ['products'], 'dashicons-store', ['title', 'editor', 'thumbnail', 'comments', 'custom-fields']);
}, 0);
add_action('init', function () {
  register_custom_taxonomy(__('产品类别', 'imwtb'), 'products', ['product']);
  register_custom_taxonomy(__('测试', 'imwtb'), 'ceshis', ['post']);
}, 0); */

require_once get_template_directory() . '/customize/options.php';
$theme_option = new Theme_Options();
$theme_option->fields([
  'fields' => [
    [
      'label'   => __('Logo 与 Ico', 'imwtb'),
      'default' => sprintf(__('使用主题自带设置，%s', 'imwtb'), '<a href="' . wp_customize_url() . '">' . __('去设置', 'imwtb') . '</a>'),
      'id'      => 'site_logo',
      'type'    => 'notes',
    ],
    [
      'label' => __('描述', 'imwtb'),
      'id'    => 'site_description',
      'type'  => 'textarea',
    ],
    [
      'label' => __('关键词', 'imwtb'),
      'id'    => 'site_keywords',
      'type'  => 'textarea',
    ],
    [
      'label'       => __('分享图', 'imwtb'),
      'description' => __('使用固定分辨率 1200x630 像素大小。', 'imwtb'),
      'id'          => 'site_image',
      'type'        => 'image',
      'returnvalue' => 'url',
    ],
    [
      'label' => __('备案号', 'imwtb'),
      'id'    => 'site_record',
      'type'  => 'text',
    ],
    [
      'label'         => __('版权', 'imwtb'),
      'id'            => 'site_copyright',
      'type'          => 'wysiwyg',
      'media_buttons' => true,
      'textarea_rows' => 5,
    ],
    [
      'label'       => __('脚本', 'imwtb'),
      'description' => esc_html__('脚本需要加上 类似于这种 <script type="text/javascript"> 脚本 </script> 标签。', 'imwtb'),
      'id'          => 'site_script',
      'type'        => 'textarea',
    ]
  ]
]);
require_once get_template_directory() . '/customize/metabox-tax.php';
$meta_tax = new MetaBoxTax();
$meta_tax->fields([
  'fields' => [
    [
      'label' => __('关键词', 'imwtb'),
      'id'    => 'term_keywords',
      'type'  => 'textarea',
    ],
    [
      'label'       => __('图片', 'imwtb'),
      'id'          => 'term_image',
      'type'        => 'image',
      'returnvalue' => 'url',
    ]
  ]
]);
require_once get_template_directory() . '/customize/metabox-post.php';
