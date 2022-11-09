<?php
/*
Template Name: 小布局768
Template Post Type: page
*/
get_header(); ?>

<div class="max__768">
  <?php while (have_posts()) : the_post(); ?>
    <?php get_template_part('template-parts/content', 'page'); ?>
  <?php endwhile; ?>
</div>

<?php get_footer(); ?>