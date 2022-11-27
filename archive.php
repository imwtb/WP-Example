<?php get_header(); ?>

<div class="maing__max max__1200 layout">
  <div class="layout__center">

    <?php
    the_archive_title('<h2>', edit_term_link(__('编辑', 'imwtb'), '', '', is_tax() ? get_term_by('slug', get_query_var('term'), get_query_var('taxonomy')) : get_query_var('cat'), false) . '</h2>');
    the_archive_description();

    $args = [
      'post_type'          => !empty(get_post_type()) ? get_post_type() : 'post',
      'fields'             => 'ids',
      'ignore_sticky_posts' => true,
      'posts_per_page'     => get_option('posts_per_page'),
      'paged'              => (get_query_var('paged')) ? get_query_var('paged') : 1,
    ];
    if (is_tax()) {
      $args['tax_query'] = [[
        'taxonomy' => get_query_var('taxonomy'),
        'field'    => 'slug',
        'terms'    => get_query_var('term'),
      ]];
    }
    $main_query = new WP_Query($args);
    while ($main_query->have_posts()) : $main_query->the_post();
      get_template_part('template-parts/content', 'post');
    endwhile;
    wp_reset_postdata();
    the_posts_pagination(['prev_text' => '&lt;', 'next_text' => '&gt;']);
    ?>

  </div>

  <?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>