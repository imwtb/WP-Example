<?php get_header(); ?>

<div class="maing__max max__1200 layout">
  <div class="layout__center">

    <?php
    the_archive_title('<h2>', '</h2>' . edit_term_link(__('编辑', 'imwtb'), '', '', get_query_var('cat'), false));
    the_archive_description();

    $args = [
      'post_type'           => get_post_type(),
      'fields'              => 'ids',
      'ignore_sticky_posts' => true,
      'posts_per_page'      => get_option('posts_per_page'),
      'paged'               => (get_query_var('paged')) ? get_query_var('paged') : 1,
      'tax_query'           => ['relation' => 'OR'],
    ];
    if (is_tax()) {
      foreach (get_post_taxonomies() as $tax) {
        $args['tax_query'][] = [
          'taxonomy' => $tax,
          'field'    => 'slug',
          'terms'    => get_query_var('term'),
        ];
      }
    } else {
      $args['cat'] = get_query_var('cat');
    }
    $main_query = new WP_Query($args);
    while ($main_query->have_posts()) : $main_query->the_post();
      get_template_part('template-parts/content', 'post');
    endwhile;
    the_posts_pagination(['prev_text' => '&lt;', 'next_text' => '&gt;']);
    wp_reset_postdata();

    ?>

  </div>

  <?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>