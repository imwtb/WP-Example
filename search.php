<?php
get_header();

the_archive_title('<h2>', '</h2>');

$main_query = new WP_Query([
  'post_type'           => 'any',                                                   // 默认 post 设置为 any 包含自定义文章类型
  's'                   => get_query_var('s'),
  'fields'              => 'ids',
  'ignore_sticky_posts' => true,
  'post__not_in'        => get_option('sticky_posts'),
  'posts_per_page'      => get_option('posts_per_page'),
  'paged'               => (get_query_var('paged')) ? get_query_var('paged') : 1,
  //'no_found_rows'       => true,                                                    // 不需要分页设置为 true
]);
if ($main_query->have_posts()) :
  while ($main_query->have_posts()) : $main_query->the_post();
    //get_template_part('template-parts/content', 'posts');
    //print_r($main_query);
    the_title();
  endwhile;
  the_posts_pagination(['prev_text' => '&lt;', 'next_text' => '&gt;']);
endif;
wp_reset_postdata();

get_footer();
