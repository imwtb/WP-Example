<?php
/*
Template Name: 无边栏 576
Template Post Type: page, post
*/
get_header(); ?>

<div class="max__576">
  <?php
  while (have_posts()) : the_post();
    get_template_part('template-parts/content', 'page');
  endwhile;
  ?>
</div>

<?php get_footer(); ?>