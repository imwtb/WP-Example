<?php get_header(); ?>

<div class="maing__max max__1200 layout">

  <div class="layout__center">
    <?php
    while (have_post()) : the_post();
      get_template_part('template-parts/content', 'post');
    endwhile;
    get_template_part('template-parts/section', 'related');
   ?>
  </div>

  <?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>