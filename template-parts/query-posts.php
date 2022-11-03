<?php
if (is_tag()) {
  $main_add = ['tag' => get_query_var('tag')];
} else if (is_search()) {
  $main_add = ['s' => get_query_var('s')];
} else {
  $main_add = ['cat' => get_query_var('cat')];
}

$main_args = [
  'post_type'           => 'post',
  'fields'              => 'ids',
  'posts_per_page'      => get_option('posts_per_page'),
  'paged'               => (get_query_var('paged')) ? get_query_var('paged') : 1,
  'ignore_sticky_posts' => true,
  /* 'no_found_rows'       => true, */
];

$main_query = new WP_Query(array_merge($main_args, $main_add));

if ($main_query->have_posts()) :
  while ($main_query->have_posts()) : $main_query->the_post();
    $excerpt = preg_replace('/( |　|\s)*/', '', wp_strip_all_tags(get_the_excerpt())); ?>

    <article>

      <?php the_post_thumbnail('medium_large'); ?>

      <?php the_content_thumbnail(); ?>

      <?php the_title('<h3>', '</h3>'); ?>

      <?php echo get_the_term_list(get_the_ID(), 'category'); ?>

      <?php if ($excerpt) echo '<p>' . $excerpt . '</p>'; ?>

      <?php the_time('Y/m/d'); ?>

      <?php get_template_part('template-parts/posts', 'edit-del');  ?>

    </article>

<?php endwhile;
  get_template_part('template-parts/pagination');
endif;
wp_reset_postdata();
