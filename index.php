<?php
get_header();

$main_query = new WP_Query([
  'fields'              => 'ids',
  'ignore_sticky_posts' => true,
  'post__not_in'        => get_option('sticky_posts'),
  'posts_per_page'      => get_option('posts_per_page'),
  //'paged'               => (get_query_var('paged')) ? get_query_var('paged') : 1,
  'no_found_rows'       => true,                                                    // 不需要分页设置为 true 并注销 paged
]);

if ($main_query->have_posts()) :
  while ($main_query->have_posts()) : $main_query->the_post();
    //get_template_part('template-parts/content', 'posts');
    //print_r($main_query);
    the_title();
    echo '<br>';
  endwhile;
endif;
wp_reset_postdata();

get_sidebar();

get_footer();
