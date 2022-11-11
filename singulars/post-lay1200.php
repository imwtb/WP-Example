<?php
/*
Template Name: 无边栏 1200
Template Post Type: post
*/
get_header(); ?>

<div class="maing__max max__1200">
  <?php
  while (have_posts()) : the_post();
    get_template_part('template-parts/content', 'post');
  endwhile;
  get_template_part('template-parts/section', 'related');
 ?>
</div>

<?php get_footer(); ?>