<?php

// WP升级失败，删除core_updater.lock文件
//delete_option('core_updater.lock');
//delete_option('auto_updater.lock');
// 移除所有特色图像
//delete_post_meta_by_key('_thumbnail_id');

if (!isset($content_width)) $content_width = 768;

add_action('after_setup_theme', function () {
  load_theme_textdomain('example-text', get_template_directory() . '/languages');
  // add_theme_support('post-formats', ['link', 'aside', 'gallery', 'image', 'quote', 'status', 'video', 'audio', 'chat']);
  add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script']);
  add_theme_support('customize-selective-refresh-widgets');
  add_theme_support('title-tag');
  add_theme_support('custom-logo');
  add_theme_support('post-thumbnails');
  register_nav_menus(['primary' => __('主菜单', 'example-text'), 'secondary' => __('次级菜单', 'example-text')]);
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
  wp_enqueue_style('style', get_stylesheet_uri(), [], filemtime(get_template_directory() . '/style.css'), 'all');
  wp_enqueue_style('iconoir', get_template_directory_uri() . '/assets/css/iconoir.css', [], null, 'all');

  wp_enqueue_script('jquery');
  wp_enqueue_script('qrcode.min', get_template_directory_uri() . '/assets/js/qrcode.min.js', [], null, true);
  wp_enqueue_script('resizesensor.min', get_template_directory_uri() . '/assets/js/resizesensor.min.js', [], null, true);
  wp_enqueue_script('stickysidebar.min', get_template_directory_uri() . '/assets/js/stickysidebar.min.js', [], null, true);
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
  echo '<a class="skip-link screen-reader-text" href="#content">' . __('跳到内容', 'example-text') . '</a>';
}, 10, 3);

// 文章内容图片
function the_content_thumbnails()
{
  global $post, $posts;

  $image = '';
  ob_start();
  $output = preg_match_all('/<img.*?src=[\'|\"](.+?)[\'|\"].*?>/i', get_post($post)->post_content, $matches);
  print_r($matches[1]);
  $image = $matches[1];
  ob_end_clean();
  return $image;
}

function the_content_thumbnail($width = '300', $height = 'auto', $number = 1)
{
  foreach (the_content_thumbnails() as $key => $value) {
    if ($key < $number) {
      echo '<img width="' . $width . '" height="' . $height . '" src="' . $value . '">';
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
    $title = sprintf(esc_html__('搜索 %s 结果如下', 'example-text'), get_search_query());
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
    $text = __('Delete This');
  }
  $link = '<a class="' . esc_attr($class) . '" href="' . esc_url($url) . '">' . $text . '</a>';
  echo $before . apply_filters('delete_post_link', $link, $post->ID, $text) . $after;
}

// 添加维护模式
/* function maintenance_mode()
{
  if (!current_user_can('edit_themes') || !is_user_logged_in()) {
    $logo            = 'https://www.itbulu.com';     // 请将此图片地址换为自己站点的 logo 图片地址
    $blogname        = get_bloginfo('name');
    $blogdescription = get_bloginfo('description');
    wp_die('<div style="text-align:center"><img src="' . $logo . '" alt="' . $blogname . '" /><br /><br />' . $blogname . '正在例行维护中，请稍候...</div>', '站点维护中 - ' . $blogname . ' - ' . $blogdescription, ['response' => '503']);
  }
}
add_action('get_header', 'maintenance_mode'); */

// 面板信息
/* function add_admin_notices()
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

add_action('admin_notices', 'add_admin_notices'); */

// 注册小工具
add_action('widgets_init', function () {
  register_sidebar([
    'name'          => esc_html__('侧边栏', 'example-text'),
    'description'   => esc_html__('默认侧边栏', 'example-text'),
    'id'            => 'sidebar',
    'class'         => 'class',
    'before_widget' => '<section id="%1$s" class="widget %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h2 class="widget__title">',
    'after_title'   => '</h2>',
  ]);
});
require_once get_template_directory() . '/widgets/widget.php';

// 引入一些文件
require_once get_template_directory() . '/inc/optimize.php';
require_once get_template_directory() . '/inc/schema-org.php';
require_once get_template_directory() . '/inc/taxonomy-post-type.php';
add_action('init', function () {
  register_custom_post_type(__('产品', 'example-text'), 'product', ['products'], 'dashicons-store', ['title', 'editor', 'thumbnail', 'comments', 'custom-fields']);
}, 0);
add_action('init', function () {
  register_custom_taxonomy(__('产品类别', 'example-text'), 'products', ['product']);
}, 0);

require_once get_template_directory() . '/customize/options.php';
$theme_option = new Theme_Options();
require_once get_template_directory() . '/customize/metabox-tax.php';
$meta_tax = new MetaBoxTax();
require_once get_template_directory() . '/customize/metabox-post.php';
$meta_post = new MetaBoxPost();

$meta_post->fields([

  // textarea
  // text
  // - - email
  // - - url
  // - - number
  // - - tel
  // - - password
  // - - date
  // - - time
  // - checkbox
  // - pages
  // - users
  // - categories ['taxonomy' => ['category']]
  // select
  // radio
  // media ['returnvalue' => 'id' or 'returnvalue' => 'url']
  //
  // wp_editor

  'fields' => [
    [
      'label' => __('多行文本', 'example-text'),
      'id'    => 'color_id',
      'type'  => 'color',
    ],
    [
      'label' => __('多行文本', 'example-text'),
      'id'    => 'textarea_id',
      'type'  => 'textarea',
    ],
    [
      'label' => __('文本', 'example-text'),
      'id'    => 'text_id',
      'type'  => 'text',
    ],
    [
      'label' => __('网站', 'example-text'),
      'id'    => 'url_id',
      'type'  => 'url',
    ],
    [
      'label' => __('选框', 'example-text'),
      'id'    => 'checkbox_id',
      'type'  => 'checkbox',
    ],
    [
      'label' => __('页面', 'example-text'),
      'id'    => 'pages_id',
      'type'  => 'pages',
    ],
    [
      'label' => __('用户', 'example-text'),
      'id'    => 'users_id',
      'type'  => 'users',
    ],
    [
      'label' => __('分类', 'example-text'),
      'id'    => 'categories_id',
      'type'  => 'categories',
    ],
    [
      'label'   => __('下拉框', 'example-text'),
      'id'      => 'select_id',
      'type'    => 'select',
      'default' => '2',
      'options' => [
        'one',
        'two',
        'other',
      ]
    ],
    [
      'label'   => __('单选', 'example-text'),
      'id'      => 'radio_id',
      'type'    => 'radio',
      'default' => '2',
      'options' => [
        'one',
        'two',
        'other',
      ]
    ],
    [
      'label'       => __('媒体', 'example-text'),
      'id'          => 'media_id',
      'type'        => 'media',
      'returnvalue' => 'id',
    ],
    [
      'label'         => __('文本编辑器', 'example-text'),
      'id'            => 'wysiwyg_id',
      'type'          => 'wysiwyg',
      'media_buttons' => false,
      'textarea_rows' => 5,
      'quicktags'     => false,
      'teeny'         => false,
    ],
  ]
]);
