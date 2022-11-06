<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
  <div id="content">

    <?php print_r(get_taxonomies()); ?>

    <figure>
      <?php the_post_thumbnail(); ?>
      <?php the_content_thumbnail(); ?>
    </figure>

    <?php the_title(); ?>

    <time><?php the_time('Y/m/d'); ?></time>
    <span><?php the_modified_time('Y/m/d'); ?></span>
    <span><?php comments_number(0, 1, '%'); ?></span>
    <span><?php get_template_part('template-parts/posts', 'edit-del');  ?></span>

    <?php the_content(); ?>

    <?php echo get_the_term_list(get_the_ID(), get_taxonomies()); ?>

    <?php
    if (comments_open() || get_comments_number()) {
      comments_template();
    }
    ?>
  </div>
<?php endwhile; ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>