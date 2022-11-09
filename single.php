<?php get_header(); ?>

<div class="max__1200 layout">
  <div class="layout__center">

    <?php
    while (have_posts()) : the_post();
      get_template_part('template-parts/content', 'post');
    endwhile;
    ?>

    <div class="maing__head">
      <h2>相关文章</h2>
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
      get_template_part('template-parts/content', 'post');
      echo '<br>';
    endwhile;
    wp_reset_postdata();
    ?>
  </div>

  <?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>