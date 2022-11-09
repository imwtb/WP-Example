<?php
/*
Template Name: 左边栏
Template Post Type: post
*/
get_header(); ?>

<div class="max__1200 layout">

  <?php get_sidebar(null, ['layout' => 'left']); ?>

  <div class="layout__center">
    <?php
    while (have_posts()) : the_post();
      get_template_part('template-parts/content', 'post');
    endwhile;
    get_template_part('template-parts/section', 'related');
    ?>
  </div>

</div>

<?php get_footer(); ?>