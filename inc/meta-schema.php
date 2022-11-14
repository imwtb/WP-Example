<?php

add_action('wp_head', function () {

  $url         = home_url();
  $title       = get_bloginfo('name');
  $description = get_option('site_description');
  $keywords    = get_option('site_keywords');
  $image       = get_option('site_image');

  if (is_author()) {
    $author_id   = get_the_author_meta('ID');
    $title       = get_the_author_meta('display_name', $author_id);
    $description = get_the_author_meta('description', $author_id) ?: $description;
  } else if (is_singular()) {
    $id          = get_the_ID();
    $url         = get_the_permalink();
    $title       = get_the_title();
    $description = preg_replace('/( |　|\s)*/', '', wp_strip_all_tags(get_the_excerpt())) ?: $description;
    $keywords    = get_bloginfo('name');
    foreach (get_post_taxonomies() as $tax) {
      $keywords .= $tax != 'post_format' ? strip_tags(get_the_term_list($id, $tax, ',')) : '';
    }
    if (has_post_thumbnail()) {
      $image = get_the_post_thumbnail_url($id, 'metatag');
    } elseif (get_the_content_thumbnail()) {
      $image = get_the_content_thumbnail();
    } else {
      $image = $image;
    }
  } else if (is_archive()) {
    if (is_tax()) {
      $terms       = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));
      $url         = get_term_link(get_query_var('term'), get_query_var('taxonomy'));
      $title       = $terms->name;
      $description = $terms->description ?: $description;
      $keywords    = get_term_meta($terms->term_id, 'term_keywords', true) ?: $keywords;
      $image       = get_term_meta($terms->term_id, 'term_image', true) ?: $image;
    } else {
      $id          = get_query_var('cat');
      $url         = get_category_link($id);
      $title       = get_cat_name($id);
      $description = strip_tags(term_description()) ?: $description;
      $keywords    = get_term_meta($id, 'term_keywords', true) ?: $keywords;
      $image       = get_term_meta($id, 'term_image', true) ?: $image;
    }
  }

  echo '
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, shrink-to-fit=no">
  <meta name="revisit-after" content="5 days">
  <meta name="robots" content="index, follow">
  <meta name="author" content="' . get_bloginfo('name') . '">
  <meta name="title" content="' . $title . '">

  <meta name="description" content="' . $description . '">
  <meta name="keywords" content="' . $keywords . '">

  <meta property="og:title" content="' . $title . '">
  <meta property="og:description" content="' . $description . '">
  <meta property="og:image" content="' . $image . '">
  <meta property="og:url" content="' . $url . '">

  <meta name="twitter:title" content="' . $title . '" />
  <meta name="twitter:description" content="' . $description . '" />
  <meta name="twitter:site" content="@' . $title . '" />
  <meta name="twitter:creator" content="@' . $title . '" />
  <meta property="twitter:image" content="' . $image . '" />
  <meta property="twitter:image:width" content="1200" />
  <meta property="twitter:image:height" content="630" />

  <meta name="og:title" content="' . $title . '" />
  <meta name="og:description" content="' . $description . '" />
  <meta property="og:url" content="' . $url . '" />
  <meta property="og:site_name" content="' . $title . '" />
  <meta property="og:image" content="' . $image . '" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />
  ';

  echo '<script type="application/ld+json">{"@context": "https://schema.org/","@graph": [';

  echo '{"@type": "WebSite", "name": "' . get_bloginfo('name') . '", "url": "' . home_url() . '", "potentialAction": { "@type": "SearchAction", "target": "' . home_url() . '/?s={search_term_string}", "query-input": "required name=search_term_string"}},';

  $items = breadcrumbs_args();
  if (!empty($items)) {
    $count = count($items);
    $num   = 1;
    echo '{"@type": "BreadcrumbList","itemListElement": [';
    foreach ($items as $item) {
      $title = !empty($item['title']) ? $item['title'] : '';
      $link  = isset($item['link']) ? $item['link'] : '';
      echo '{"@type": "ListItem", "position": ' . $num . ', "name": "' . $title . '", "item": "' . $link . '"}';
      if ($num < $count) echo ',';
      $num++;
    }
    echo ']}';
  }

  if (is_singular()) {
    global $post;
    $author_id = $post->post_author;
    $thumbnail = has_post_thumbnail() ? get_the_post_thumbnail_url($post->ID, 'full') : '';

    $count = count(the_content_thumbnails());
    $image = '';
    $num   = 1;
    foreach (the_content_thumbnails() as $img) {
      $image .= '"' . $img . '"';
      $image .= $num < $count ? ',' : '';
      $num++;
    }
    $image .= $thumbnail ? '"' . $thumbnail . '"' : '';
    if (is_single()) {
      echo ',{"@type": "NewsArticle", "mainEntityOfPage": { "@type": "WebPage", "@id": "' . get_the_permalink() . '"}, "headline": "' . get_the_title() . '", "image": [' . $image . '], "author": { "@type": "Person", "name": "' . get_the_author_meta('display_name', $author_id) . '", "url": "' . get_author_posts_url($author_id) . '"}, "publisher": { "@type": "Organization", "name": "' . get_the_author_meta('display_name', $author_id) . '", "logo": {"@type": "ImageObject", "url": "' . get_avatar_url($author_id) . '"}}, "datePublished": "' . get_the_time('Y-m-d') . '", "dateModified": "' . get_the_modified_time('Y-m-d') . '"}';
    }
  }

  echo ']}</script>';
}, 10, 3);

function breadcrumbs($nav_class = 'breadcrumbs', $ol_class = '')
{
  $items  = breadcrumbs_args();
  if (!empty($items)) {
    $output = '';
    foreach ($items as $item) {
      $title = !empty($item['title']) ? $item['title'] : '';
      $link  = isset($item['link']) ? $item['link'] : '';
      if ($item['link'] == '#') {
        $output .= '<li>' . $title . '</li>';
      } else {
        $output .= '<li><a href="' . $link . '">' . $title . '</a></li>';
      }
    }
    echo '<nav class="' . $nav_class . '"><ol class="' . $ol_class . '">' . $output . '</ol></nav>';
  }
};

function breadcrumbs_args()
{
  $lists = [];

  if (!is_home() && !is_front_page()) {
    $lists[] = [
      'title' => __('首页', 'imwtb'),
      'link'  => home_url(),
    ];

    if (is_date()) {

      $year  = get_the_date('Y');
      $month = get_the_date('m');
      $day   = get_the_date('d');

      // 年
      if (is_year()) {
        $lists[] = [
          'title' => $year,
          'link'  => get_year_link($year),
        ];
      }

      // 月
      else if (is_month()) {
        $lists[] = [
          'title' => $year,
          'link'  => get_year_link($year),
        ];
        $lists[] = [
          'title' => $month,
          'link'  => get_month_link($year, $month),
        ];
      }

      // 日
      else if (is_day()) {
        $lists[] = [
          'title' => $year,
          'link'  => get_year_link($year),
        ];
        $lists[] = [
          'title' => $month,
          'link'  => get_month_link($year, $month),
        ];
        $lists[] = [
          'title' => $day,
          'link'  => get_day_link($year, $month, $day),
        ];
      }
    }

    // 标签
    else if (is_tag()) {
      $lists[] = [
        'title' => single_tag_title('', false),
        'link'  => get_tag_link(get_query_var('tag_id')),
      ];
    }

    // 搜索
    else if (is_search()) {
      $lists[] = [
        'title' => get_query_var('s'),
        'link'  => get_search_link(get_query_var('s')),
      ];
    }

    // 作者
    else if (is_author()) {
      global $post;
      $author_id = $post->post_author;
      $lists[] = [
        'title' => get_the_author_meta('display_name', $author_id),
        'link'  => get_author_posts_url($author_id),
      ];
    }

    // 404
    else if (is_404()) {
      $lists[] = [
        'title' => __('404', 'imwtb'),
        'link'  => '#',
      ];
    }

    // 附件
    else if (is_attachment()) {
      $lists[] = [
        'title' => __('附件', 'imwtb'),
        'link'  => get_attachment_link(),
      ];
    }

    // 文章
    else if (is_singular()) {
      global $post;
      if (is_page()) {
        $page = get_post(get_option('page_on_front'));
        for ($i = count($post->ancestors) - 1; $i >= 0; $i--) {
          if (($page->ID) != ($post->ancestors[$i])) {
            $lists[] = [
              'title' => get_the_title($post->ancestors[$i]),
              'link'  => get_permalink($post->ancestors[$i]),
            ];
          }
        }
      } else {
        $post_terms = wp_get_object_terms($post->ID, get_post_taxonomies());
        if ($post_terms) {
          $taxonomy = $post_terms[0]->taxonomy;
          $term_id  = $post_terms[0]->term_id;
          $terms    = get_term($term_id);
          if ($terms->parent > 0) {
            $parent_ids = get_ancestors($term_id, $taxonomy);
            foreach (array_reverse($parent_ids) as $value) {
              $parent_terms = get_term($value, $taxonomy);
              $lists[] = [
                'title' => $parent_terms->name,
                'link'  => get_term_link($parent_terms->term_id),
              ];
            }
          }
          $lists[] = [
            'title' => $terms->name,
            'link'  => get_term_link($term_id),
          ];
        }
      }
      $lists[] = [
        'title' => get_the_title(),
        'link'  => get_the_permalink(),
      ];
    }

    // 文章归档
    else if (is_post_type_archive()) {
      $lists[] = [
        'title' => get_the_title(),
        'link'  => get_post_type_archive_link(get_post_type()),
      ];
    }

    //归档
    else if (is_archive()) {
      $terms    = get_term(get_queried_object_id());
      $term_id  = $terms->term_id;
      $taxonomy = $terms->taxonomy;
      if ($terms->parent > 0) {
        $parent_ids = get_ancestors($term_id, $taxonomy);
        foreach (array_reverse($parent_ids) as $value) {
          $parent_terms = get_term($value, $taxonomy);
          $lists[] = [
            'title' => $parent_terms->name,
            'link'  => get_term_link($parent_terms->term_id),
          ];
        }
      }
      $lists[] = [
        'title' => $terms->name,
        'link'  => get_term_link($term_id),
      ];
    } else {
      $lists[] = [
        'title' => __('页面', 'imwtb'),
        'link'  => '#',
      ];
    }
  }
  return $lists;
}
/* !is_singular(['forum', 'topic'])&& !is_post_type_archive() */