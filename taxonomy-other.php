<?php
get_header();

the_archive_title('<h2>', '</h2>' . edit_term_link(__('编辑', 'imwtb'), '', '', get_query_var('cat'), false));
the_archive_description();

$main_query = new WP_Query([
  'post_type'           => get_post_type(),
  'fields'              => 'ids',
  'ignore_sticky_posts' => true,
  'posts_per_page'      => get_option('posts_per_page'),
  'paged'               => (get_query_var('paged')) ? get_query_var('paged') : 1,
  'tax_query'           => [
    'taxonomy' => 'products',
    'field'    => 'slug',
    'terms'    => get_query_var('term'),
  ],
]);
while ($main_query->have_posts()) : $main_query->the_post();
  get_template_part('template-parts/content', 'post');
endwhile;
the_posts_pagination(['prev_text' => '&lt;', 'next_text' => '&gt;']);
wp_reset_postdata();

get_footer();
