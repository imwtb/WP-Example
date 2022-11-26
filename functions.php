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
  add_image_size('meta_image', 1200, 630, true);
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

  /*----- Woocommerce商店 -----*/
  add_theme_support('woocommerce');
  add_theme_support('wc-product-gallery-zoom');
  add_theme_support('wc-product-gallery-lightbox');
  add_theme_support('wc-product-gallery-slider');
});

// 注册菜单
register_nav_menus([
  'primary'   => __('主菜单', 'imwtb'),
  'secondary' => __('次级菜单', 'imwtb')
]);

// 禁止生成图片尺寸
add_action('intermediate_image_sizes_advanced', function ($sizes) {
  unset($sizes['thumbnail']);        // 150
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
  wp_enqueue_style('iconoir', get_template_directory_uri() . '/assets/css/iconoir.css', [], '5.4', 'all');
  wp_enqueue_style('bbpress', get_template_directory_uri() . '/bbpress/bbpress.css', filemtime(get_template_directory() . '/bbpress/bbpress.css'), null, 'all');
  wp_enqueue_style('woocommerce', get_template_directory_uri() . '/woocommerce/woocommerce.css', filemtime(get_template_directory() . '/woocommerce/woocommerce.css'), null, 'all');

  wp_enqueue_script('jquery');
  wp_enqueue_script('qrcode-min', get_template_directory_uri() . '/assets/js/qrcode.min.js', ['jquery'], null, true);
  wp_enqueue_script('resizesensor-min', get_template_directory_uri() . '/assets/js/resizesensor.min.js', [], '1.2.2', true);
  wp_enqueue_script('stickysidebar-min', get_template_directory_uri() . '/assets/js/stickysidebar.min.js', [], '3.3.1', true);
  wp_enqueue_script('scripts', get_template_directory_uri() . '/assets/js/scripts.js', [], filemtime(get_template_directory() . '/assets/js/scripts.js'), true);
  if (is_singular() && comments_open() && get_option('thread_comments')) {
    wp_enqueue_script('comment-reply');
  }
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
  return get_option('post_excerpt', 64);
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
  $link = '<a class="' . $class . '" href="' . $url . '">' . $text . '</a>';
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
  $time      = get_post_time('G', true);
  $time_diff = time() - $time;
  if ($time_diff > 0 && $time_diff < 24 * 60 * 60) {
    $display = human_time_diff($time) . __('前', 'imetb');
  } else {
    $display = get_the_time(get_option('date_format'));
  }
  return $display;
}

// 文章浏览量
function views_types()
{
  $types = ['post', 'video'];
  return $types;
}

function get_post_views()
{
  return get_post_meta(get_the_ID(), 'views', true);
}

add_action('get_header', function () {
  if (is_singular(views_types())) {
    $views = get_post_views();
    if (get_option('views_cookie')) {
      $cookies = $_COOKIE['views'  . get_the_ID() . COOKIEHASH];
      if (!isset($cookies) && $cookies != '1') {
        update_post_meta(get_the_ID(), 'views', $views + 1);
        setcookie('views'  . get_the_ID() . COOKIEHASH, '1', time() + 86400, COOKIEPATH, COOKIE_DOMAIN);
      }
    } else {
      update_post_meta(get_the_ID(), 'views', $views + 1);
    }
  }
});

foreach (views_types() as $value) {
  add_action('save_post_' . $value, function () {
    $views = get_post_views();
    $array = explode(',', get_option('views_rand_num', '128,256'));
    if (get_option('views_rand') && ($views == '0' || $views == '')) {
      delete_post_meta(get_the_ID(), 'views');
      update_post_meta(get_the_ID(), 'views', (int)rand($array[0], $array[1]));
    } else if ($views == '0' || $views == '') {
      delete_post_meta(get_the_ID(), 'views');
      update_post_meta(get_the_ID(), 'views', '0');
    }
  });
}

// 指定作者浏览总数
function count_users_views($author_id = 1, $display = true)
{
  global $wpdb;
  $sql = "SELECT SUM(meta_value+0) FROM $wpdb->posts left join $wpdb->postmeta on ($wpdb->posts.ID = $wpdb->postmeta.post_id) WHERE meta_key = 'views' AND post_author =$author_id";
  $comment_views = intval($wpdb->get_var($sql));
  if ($display) {
    echo number_format_i18n($comment_views);
  } else {
    return $comment_views;
  }
}

// 文章分享
function post_share_url($shara_name)
{
  $site_description = get_option('site_description') ? get_option('site_description') : get_bloginfo('description');
  $site_logo        = get_option('site_image') ? get_option('site_image') : wp_get_attachment_url(get_theme_mod('custom_logo'));
  $title            = urlencode(get_the_title());
  $site_url         = urlencode(get_the_permalink());
  $excerpt          = has_excerpt() ? preg_replace('/( |　|\s)*/', '', wp_strip_all_tags(get_the_excerpt())) : $site_description;
  $thumbnail        = has_post_thumbnail() ? get_the_post_thumbnail_url(get_the_ID(), 'full') : $site_logo;

  if ($shara_name == "weibo") {
    return 'https://service.weibo.com/share/share.php?url=' . $site_url . '&sharesource=weibo&title=' . $title . '&pic=' . $thumbnail . '&appkey=' . get_option('weibo_appkey') . '&language=zh_cn';
  }
  if ($shara_name == "qzone") {
    return 'https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=' . $site_url . '&sharesource=qzone&title=' . $title . '&pics=' . $thumbnail . '&summary=' . $excerpt;
  }
  if ($shara_name == "qrcode") {
    return urldecode($site_url);
  }
}

// Ajax点赞
function post_likes_list($class = '', $icon = '<i class="iconoir-heart"></i>')
{
  global $post;
  $id     = is_singular() ? get_the_ID() : $post;
  $done   = isset($_COOKIE['likes_' . $id]) ? 'done' : '';
  $count  = get_post_meta($id, 'likes', true);
  $counts = $count ? $count : '0';
  return '<a class="' . $class . ' like-it ' . $done . '" data-action="likeit" data-id="' . $id . '"><span>' . $counts . '</span> | ' . $icon . '</a>';
}

function post_likes()
{
  $id     = $_POST["like_id"];
  $action = $_POST["like_action"];
  if ($action == 'likeit') {
    $raters = get_post_meta($id, 'likes', true);
    $expire = time() + 99999999;
    $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
    setcookie('likes_' . $id, $id, $expire, '/', $domain, false);
    if (!$raters || !is_numeric($raters)) {
      update_post_meta($id, 'likes', 1);
    } else {
      update_post_meta($id, 'likes', ($raters + 1));
    }
    echo get_post_meta($id, 'likes', true);
  }
  die;
}
add_action('wp_ajax_nopriv_post_likes', 'post_likes');
add_action('wp_ajax_post_likes', 'post_likes');

add_action('wp_footer', function () {
?>
  <script type="text/javascript">
    (function($) {
      $.fn.postLike = function() {
        if ($(this).hasClass('done')) {
          return alert('<?php _e('你已经点过赞咯！', 'imwtb'); ?>');
        } else {
          $(this).addClass('done');
          let
            id = $(this).data("id"),
            action = $(this).data('action'),
            span = $(this).children('span');
          let ajax_data = {
            action: "post_likes",
            like_id: id,
            like_action: action
          };
          $.post("/wp-admin/admin-ajax.php", ajax_data, function(data) {
            $(span).html(data);
          });
          return false;
        }
      };
      $(document).on("click", ".like-it", function() {
        $(this).postLike();
      });
    })(jQuery);
  </script>
<?php
}, 99);

// bbsPress论坛可视化编辑器
add_filter('bbp_after_get_the_content_parse_args', function ($args = []) {
  $args['tinymce'] = true;
  $args['quicktags'] = false;
  //$args['teeny'] = false;
  $args['media_buttons'] = true;
  return $args;
});

add_filter('bbp_get_tiny_mce_plugins', function ($plugins = []) {
  $plugins[] = 'paste';
  return $plugins;
});


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
    'name'          => __('默认侧边栏', 'imwtb'),
    'id'            => 'sidebar',
    'before_widget' => '<section id="%1$s" class="widget %2$s">',
    'after_widget'  => '</section>',
    'before_title'  => '<h2 class="widget__head">',
    'after_title'   => '</h2>',
  ]);
});
require_once get_template_directory() . '/widgets/widget.php';

// 引入文件
require_once get_template_directory() . '/inc/optimize.php';
require_once get_template_directory() . '/inc/comments.php';

require_once get_template_directory() . '/inc/meta-schema.php';

require_once get_template_directory() . '/inc/taxonomy-post-type.php';
add_action('init', function () {
  register_custom_post_type(__('视频', 'imwtb'), 'video', ['videos'], 'dashicons-store', ['title', 'editor', 'thumbnail', 'comments']);
}, 0);
add_action('init', function () {
  register_custom_taxonomy(__('视频类别', 'imwtb'), 'videos', ['video']);
}, 0);

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
      'label'   => __('Logo 后面标题', 'imwtb'),
      'default' => true,
      'id'      => 'site_logo_title',
      'type'    => 'checkbox',
    ],
    [
      'label' => __('默认描述', 'imwtb'),
      'id'    => 'site_description',
      'type'  => 'textarea',
    ],
    [
      'label' => __('默认关键词', 'imwtb'),
      'id'    => 'site_keywords',
      'type'  => 'textarea',
    ],
    [
      'label'       => __('分享站点时显示的图', 'imwtb'),
      'description' => __('使用固定分辨率 1200x630 像素大小。', 'imwtb'),
      'id'          => 'site_image',
      'type'        => 'image',
      'returnvalue' => 'url',
    ],
    [
      'label'   => __('文章摘要字数', 'imwtb'),
      'default' => 64,
      'id'      => 'post_excerpt',
      'type'    => 'text',
    ],
    [
      'label'       => __('文章阅读量 Cookie', 'imwtb'),
      'description' => __('每IP在24小时内访问仅增加一个阅读量。', 'imwtb'),
      'id'          => 'views_cookie',
      'type'        => 'checkbox',
    ],
    [
      'label'   => __('文章阅读量随机', 'imwtb'),
      'default' => true,
      'id'      => 'views_rand',
      'type'    => 'checkbox',
    ],
    [
      'label'       => __('文章阅读量随机数', 'imwtb'),
      'default'     => '128,256',
      'description' => __('最大数和最小数用英逗号 , 相隔开。', 'imwtb'),
      'id'          => 'views_rand_num',
      'type'        => 'text',
    ],
    [
      'label' => __('备案号', 'imwtb'),
      'id'    => 'site_record',
      'type'  => 'text',
    ],
    [
      'label'       => __('文章分享：微博 appkey', 'imwtb'),
      'description' => sprintf(__('使用主题自带设置，%s', 'imwtb'), '<a href="' . wp_customize_url() . '">' . __('去设置', 'imwtb') . '</a>'),
      'id'          => 'weibo_appkey',
      'type'        => 'text',
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
      'description' => __('脚本需要加上 类似于这种 <script type="text/javascript"> 脚本 </script> 标签。', 'imwtb'),
      'id'          => 'site_script',
      'type'        => 'textarea',
    ]
  ]
]);
require_once get_template_directory() . '/customize/metabox-tax.php';
$meta_tax = new MetaBoxTax(['category', 'videos', 'locstores']);
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
$meta_post_video = new MetaBoxPost();
$meta_post_video->fields([
  'id'     => 'video_metabox',
  'title'  => '视频信息',
  'screen' => 'video',
  'fields' => [
    [
      'label'   => __('模式', 'imwtb'),
      'id'      => 'video_radio',
      'type'    => 'radio',
      'default' => '0',
      'options' => [
        'none',
        'mp4',
        'iframe',
      ]
    ],
    [
      'label'       => __('MP4 链接', 'imwtb'),
      'id'          => 'video_mp4',
      'type'        => 'file',
      'returnvalue' => 'url',
    ],
    [
      'label' => __('iframe 链接', 'imwtb'),
      'id'    => 'video_iframe',
      'type'  => 'textarea',
    ],
  ]
]);
