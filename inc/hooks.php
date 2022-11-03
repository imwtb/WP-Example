<?php

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
