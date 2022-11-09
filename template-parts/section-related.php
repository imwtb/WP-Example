<div class="maing__head">
  <h2><?php echo !empty($args['title']) ? esc_html($args['title']) : __('相关文章', 'imwtb'); ?></h2>
</div>
<?php
$args = [
  'post_type'           => get_post_type(),
  'post__not_in'        => [get_the_ID()],
  'orderby'             => 'rand',
  'fields'              => 'ids',
  'ignore_sticky_posts' => true,
  'posts_per_page'      => 5,
  'no_found_rows'       => true,
  'tax_query'           => ['relation' => 'OR'],
];
foreach (get_post_taxonomies() as $tax) {
  $terms = wp_get_post_terms(get_the_ID(), $tax);
  foreach ($terms as $term) {
    $args['tax_query'][] = [
      'taxonomy' => $tax,
      'terms'    => $term->term_id,
    ];
  }
}
$main_query = new WP_Query($args);
while ($main_query->have_posts()) : $main_query->the_post();
  the_title('<h3><a href="' . esc_url(get_the_permalink()) . '">', '</a></h3>');
endwhile;
wp_reset_postdata();
