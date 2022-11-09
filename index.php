<?php get_header(); ?>

<div class="maing__max max__1200 layout">
  <div class="layout__center">
    <div class="maing__head">
      <h2>置顶</h2>
    </div>
    <?php
    $sticky_query = new WP_Query([
      'fields'              => 'ids',
      'ignore_sticky_posts' => true,
      'post__in'            => get_option('sticky_posts'),
      'posts_per_page'      => 5,
      'no_found_rows'       => true,
    ]);

    while ($sticky_query->have_posts()) : $sticky_query->the_post();
      get_template_part('template-parts/content', 'post');
      //print_r($sticky_query);
      if (is_sticky()) {
        the_title('<h3><span>Top</span> <a href="' . esc_url(get_the_permalink()) . '">', '</a></h3>');
        echo '<br>';
      }
    endwhile;
    wp_reset_postdata();
    ?>

    <div class="maing__head">
      <h2>最新</h2>
    </div>
    <?php
    $main_query = new WP_Query([
      'fields'              => 'ids',
      'ignore_sticky_posts' => true,
      'posts_per_page'      => get_option('posts_per_page'),
      'no_found_rows'       => true,                           // 不需要分页设置为 true 并注销 paged
    ]);

    while ($main_query->have_posts()) : $main_query->the_post();
      get_template_part('template-parts/content', 'post');
    endwhile;
    wp_reset_postdata();
    ?>

  </div>

  <?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>