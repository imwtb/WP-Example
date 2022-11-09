<?php get_header(); ?>

<div class="max__1200">
  <?php while (have_posts()) : the_post(); ?>
    <?php get_template_part('template-parts/content', 'page'); ?>
  <?php endwhile; ?>
</div>

<?php get_footer(); ?>