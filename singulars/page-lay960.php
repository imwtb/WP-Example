<?php
/*
Template Name: 无边栏 960
Template Post Type: page, post
*/
get_header(); ?>

<div class="max__960">
  <?php
  while (have_posts()) : the_post();
    if (is_page()) {
      get_template_part('template-parts/content', 'page');
    } else {
      get_template_part('template-parts/content', 'post');
    }
  endwhile;
  ?>
</div>

<?php get_footer(); ?>