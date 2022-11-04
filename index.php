<?php
get_header();

$main_query = new WP_Query([
  'post_type'           => 'post',
  'fields'              => 'ids',
  'posts_per_page'      => get_option('posts_per_page'),
  'ignore_sticky_posts' => true,
  'no_found_rows'       => true,
]);

if ($main_query->have_posts()) :
  while ($main_query->have_posts()) : $main_query->the_post();
    //get_template_part('template-parts/content', 'posts');
    the_title();
  endwhile;
endif;
wp_reset_postdata();

get_footer();
