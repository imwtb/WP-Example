<?php get_header(); ?>

<div class="max__1200 layout">
  <div class="layout__center">
    <?php
    $sticky_query = new WP_Query([
      'fields'              => 'ids',
      'ignore_sticky_posts' => true,
      'post__in'            => get_option('sticky_posts'),
      'posts_per_page'      => 5,
      'no_found_rows'       => true,
    ]);

    if ($sticky_query->have_posts()) :
      while ($sticky_query->have_posts()) : $sticky_query->the_post();
        //get_template_part('template-parts/content', 'posts');
        //print_r($sticky_query);
        if (is_sticky()) {
          the_title('<h3><a href="' . get_permalink() . '">', '</a></h3>');
          echo '<br>';
        }
      endwhile;
    endif;
    wp_reset_postdata();

    $main_query = new WP_Query([
      'fields'              => 'ids',
      'ignore_sticky_posts' => true,
      'posts_per_page'      => get_option('posts_per_page'),
      //'paged'               => (get_query_var('paged')) ? get_query_var('paged') : 1,
      'no_found_rows'       => true,                                                    // 不需要分页设置为 true 并注销 paged
    ]);

    if ($main_query->have_posts()) :
      while ($main_query->have_posts()) : $main_query->the_post();
        //get_template_part('template-parts/content', 'posts');
        //print_r($main_query);
        the_title('<h3><a href="' . get_permalink() . '">', '</a></h3>');
      endwhile;
    endif;
    wp_reset_postdata();
    ?>

  </div>

  <?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>