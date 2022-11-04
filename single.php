<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

  <?php the_title(); ?>

  <?php the_time('Y/m/d'); ?>
  <?php the_modified_time('Y/m/d'); ?>

  <?php get_template_part('template-parts/posts', 'edit-del');  ?>

  <?php the_content(); ?>

  <?php echo strip_tags(get_the_term_list(get_the_ID(), 'category')); ?>
  <?php echo get_the_term_list(get_the_ID(), 'post_tag'); ?>

  <?php
  if (comments_open() || get_comments_number()) {
    comments_template();
  }
  ?>

<?php endwhile; ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>