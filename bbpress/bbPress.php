<?php get_header(); ?>

<div class="maing__max max__1200">
  <?php
  while (have_posts()) : the_post();
    the_content();
  endwhile;
  ?>
</div>

<?php get_footer(); ?>