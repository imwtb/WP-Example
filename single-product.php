<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>

  <?php the_title(); ?>

  <?php the_time('Y/m/d'); ?>
  <?php the_modified_time('Y/m/d'); ?>

  <?php get_template_part('template-parts/posts', 'edit-del');  ?>

  <?php
  the_content();
  the_post_navigation(array(
    'prev_text'    => '&lt;',
    'next_text'    => '&gt;',
    'in_same_term' => true,
    'taxonomy'     => 'products',
  ));
  ?>

  <?php echo strip_tags(get_the_term_list(get_the_ID(), 'products')); ?>
  <?php echo get_the_term_list(get_the_ID(), 'post_tag'); ?>

  <?php
  if (comments_open() || get_comments_number()) {
    comments_template();
  }
  ?>

<?php endwhile; ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>