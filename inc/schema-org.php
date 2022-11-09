<?php

add_action('wp_head', function () {

  if (is_singular()) {
    global $post;
    $excerpt   = preg_replace('/( |　|\s)*/', '', wp_strip_all_tags(get_the_excerpt($post)));
    $author_id = $post->post_author;
    $thumbnail = has_post_thumbnail() ? get_the_post_thumbnail_url($post->ID, 'full') : '';

    echo '<script type="application/ld+json">{"@context": "https://schema.org/",';
    if (is_singular('post')) {
      echo '"@type": "Article",
        "headline": "' . get_the_title() . '",
        "description": "' . $excerpt . '",
        "datePublished": "' . get_the_time('Y-m-d') . '",
        "dateModified": "' . get_the_modified_time('Y-m-d') . '",
        "image": [';
      foreach (the_content_thumbnails() as  $value) {
        echo '"' . $value . '",';
      }
      echo '"' . $thumbnail . '"],
        "author": [
          {
            "@type": "Person",
            "name": "' . get_the_author_meta('display_name', $author_id) . '",
            "url": "' . get_author_posts_url($author_id) . '",
          }
        ],
        "mainEntityOfPage": {
          "@type": "webPage",
          "id": "' . get_the_permalink() . '"
        },
        "publisher": {
          "@type": "Organization",
          "name": "' . get_the_author_meta('display_name', $author_id) . '",
          "logo": {
            "@type": "ImageObject",
            "url": "' . get_avatar_url($author_id) . '"
          }
        }';
    } elseif (is_singular('product')) {
      $ratingCount = wp_count_comments($post->ID)->approved;
      $code        = preg_replace('/( |　|\s)*/', '', get_post_meta($post->ID, 'product_barcode', true));  // 条码
      $code_len    = mb_strlen($code, 'utf8');
      switch ($code_len) {
        case ($code_len < 9):
          $bar_code = '"gtin8":"' . $code . '"';
          break;
        case ($code_len > 8 && $code_len < 14):
          $bar_code = '"gtin13":"' . $code . '"';
          break;
        case ($code_len > 13):
          $bar_code = '"gtin14":"' . $code . '"';
          break;
        default:
      }

      echo '
      "@type": "Product",
      "name": "' . get_the_title() . '",
      "image": "' . $thumbnail . '",
      "description": "' . $excerpt . '",
      "brand": "' . get_post_meta($post->ID, 'product_brand', true) . '",
      ' . $bar_code . ',
      "offers": {
          "@type": "AggregateOffer",
          "url": "' . get_the_permalink() . '",
          "priceCurrency": "' . get_post_meta($post->ID, 'product_currency', true) . '",
          "lowPrice": "0",
          "highPrice": "' . get_post_meta($post->ID, 'product_price', true) . '",
          "offerCount": "1"
      },
      "aggregateRating": {
          "@type": "AggregateRating",
          "ratingValue": "5",
          "bestRating": "",
          "worstRating": "",
          "ratingCount": "' . $ratingCount . '"
      }';
    } elseif (is_singular('video')) {
    } else {
      echo '
      "@type": "WebSite",
      "name": "' . get_bloginfo('name') . '",
      "url": "' . home_url() . '",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "' . home_url() . '/?s={search_term_string}' . '",
        "query-input": "required name=search_term_string"
      }';
    }
    echo '}</script>';
  }

  if (!is_home() && !is_front_page()) {
    $items = breadcrumblists();
    $count = count($items);
    $num   = 0;
    echo '
    <script type="application/ld+json">{
      "@context": "https://schema.org/",
      "@type": "BreadcrumbList",
      "itemListElement": [';
    foreach ($items as $value) {
      echo '{
        "@type": "ListItem",
        "position": ' . $num . ',
        "name": "' . $value['title'] . '",
        "item": "' . $value['link'] . '"
      }';
      if ($num < $count) echo ',';
      $num++;
    }
    echo ']
    }</script>';
  }
}, 10, 3);

add_action('breadcrumblist', function () {
  if (!is_home() && !is_front_page()) {
    echo '<nav class="breadcrumblist"><ol>';
    foreach (breadcrumblists() as $value) {
      if ($value['link'] == '#') {
        echo '<li>' . $value['title'] . '</li>';
      } else {
        echo '<li><a href="' . $value['link'] . '">' . $value['title'] . '</a></li>';
      }
    }
    echo '</ol></nav>';
  }
}, 10, 3);

function breadcrumblists()
{
  $list = array();

  $list[] = [
    'title' => __('首页', 'imwtb'),
    'link'  => home_url(),
  ];

  if (is_archive()) {
    $cat_terms = get_term(get_queried_object_id());
    $term_id   = $cat_terms->term_id;
    $taxonomy  = $cat_terms->taxonomy;
    if ($cat_terms->parent > 0) {
      $parent_term_ids = get_ancestors($term_id, $taxonomy);
      foreach (array_reverse($parent_term_ids) as $value) {
        $parent_terms = get_term($value, $taxonomy);
        $list[] = [
          'title' => $parent_terms->name,
          'link'  => get_term_link($parent_terms->term_id),
        ];
      }
    }
    $list[] = [
      'title' => $cat_terms->name,
      'link'  => get_term_link($term_id),
    ];
  } elseif (is_singular()) {
    global $post;
    $post_terms = wp_get_object_terms($post->ID, get_post_taxonomies());
    $taxonomy   = $post_terms[0]->taxonomy;
    $term_id    = $post_terms[0]->term_id;
    $cat_terms  = get_term($term_id);
    if ($cat_terms->parent > 0) {
      $parent_term_ids = get_ancestors($term_id, $taxonomy);
      foreach (array_reverse($parent_term_ids) as $value) {
        $parent_terms = get_term($value, $taxonomy);
        $list[] = [
          'title' => $parent_terms->name,
          'link'  => get_term_link($parent_terms->term_id),
        ];
      }
    }
    $list[] = [
      'title' => $cat_terms->name,
      'link'  => get_term_link($term_id),
    ];
    $list[] = [
      'title' => get_the_title(),
      'link'  => get_the_permalink(),
    ];
  } elseif (is_date()) {

    $year  = get_the_date('Y');
    $month = get_the_date('m');
    $day   = get_the_date('d');

    if (is_year()) {
      $lists[] = [
        'title' => $year,
        'link'  => get_year_link($year),
      ];
    } elseif (is_month()) {
      $lists[] = [
        'title' => $year,
        'link'  => get_year_link($year),
      ];
      $lists[] = [
        'title' => $month,
        'link'  => get_month_link($year, $month),
      ];
    } elseif (is_day()) {
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
  } elseif (is_search()) {
    $list[] = [
      'title' => get_query_var('s'),
      'link'  => get_search_link(get_query_var('s')),
    ];
  } elseif (is_author()) {
    global $post;
    $author_id = $post->post_author;
    $list[] = [
      'title' => get_the_author_meta('display_name', $author_id),
      'link'  => get_author_posts_url($author_id),
    ];
  } elseif (is_tag()) {
    $list[] = [
      'title' => single_tag_title('', false),
      'link'  => get_tag_link(get_query_var('tag_id')),
    ];
  } elseif (is_404()) {
    $list[] = [
      'title' => __('404', 'imwtb'),
      'link'  => '#',
    ];
  } elseif (is_attachment()) {
    $list[] = [
      'title' => __('附件', 'imwtb'),
      'link'  => '#',
    ];
  } else {
    $list[] = [
      'title' => get_the_title(),
      'link'  => '#',
    ];
  }

  return $list;
}
