<?php get_header(); ?>

<div class="max__1200 layout">
  <div class="layout__center">
    <?php while (have_posts()) : the_post(); ?>
      <article id="content">

        <figure>
          <?php the_post_thumbnail(); ?>
          <?php the_content_thumbnail(); ?>
        </figure>

        <?php the_title('<h1 class="post__title">', '</h1>'); ?>

        <div class="post__meta">
          <time datetime="<?php the_date('Y-m-d H:s'); ?>"><?php the_time('Y/m/d'); ?></time>
          <span><?php comments_number(0, 1, '%'); ?></span>
          <?php the_author_link(); ?>
          <?php
          if (current_user_can('manage_options')) {
            edit_post_link(esc_html__('编辑', 'example-text'), '', '', get_the_ID(), '');
            delete_post_link(esc_html__('删除', 'example-text'), '', '', get_the_ID(), '');
          }
          ?>
        </div>

        <div class="post__content">
          <?php the_content(); ?>
        </div>

        <div class="post__catag">
          <?php
          foreach (get_taxonomies() as $value) {
            echo get_the_term_list(get_the_ID(), $value);
          }
          ?>
        </div>

      </article>

      <?php
      if (comments_open() || get_comments_number()) {
        comments_template();
      }
      ?>

    <?php endwhile; ?>
  </div>

  <?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>