<?php
get_header();

the_archive_title('<h2>', '</h2>' . edit_term_link(__('编辑', 'example-text'), '', '', get_query_var('cat'), false));
the_archive_description();

$main_query = new WP_Query([
  'fields'              => 'ids',
  'ignore_sticky_posts' => true,
  'posts_per_page'      => get_option('posts_per_page'),
  'paged'               => (get_query_var('paged')) ? get_query_var('paged') : 1,
  'post_type'           => 'post',
  'tax_query'           => [
    'taxonomy' => 'taxonomys',
    'terms'    => get_query_var('term'),
  ],
]);
if ($main_query->have_posts()) :
  while ($main_query->have_posts()) : $main_query->the_post();
    //get_template_part('template-parts/content', 'posts');
    //print_r($main_query);
    the_title('<h3><a href="' . get_permalink() . '">', '</a></h3>');
  endwhile;
  the_posts_pagination(['prev_text' => '&lt;', 'next_text' => '&gt;']);
endif;
wp_reset_postdata();

get_footer();
