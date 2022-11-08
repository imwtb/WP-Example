<?php get_header(); ?>

<div class="max__1200 layout">
  <div class="layout__center">
    <?php
    while (have_posts()) : the_post();
    ?>
      <article id="content">
        <figure>
          <?php the_post_thumbnail(); ?>
          <?php the_content_thumbnail(); ?>
        </figure>

        <?php the_title('<h1 class="post__title">', '</h1>'); ?>

        <?php $excerpt = preg_replace('/( |　|\s)*/', '', wp_strip_all_tags(get_the_excerpt())); ?>
        <?php echo $excerpt; ?>

        <div class="post__meta">
          <time datetime="<?php the_date('Y-m-d H:s'); ?>"><?php the_time(); ?></time>
          <span><?php comments_number(0, 1, '%'); ?></span>
          <a href="<?php echo get_author_posts_url($post->post_author); ?>"><?php the_author_meta('display_name'); ?></a>
          <?php
          if (current_user_can('manage_options')) {
            edit_post_link(__('编辑', 'imwtb'), '', '', get_the_ID(), '');
            delete_post_link(__('删除', 'imwtb'), '', '', get_the_ID(), '');
          }
          ?>
        </div>

        <div class="post__content">
          <?php
          the_content();
          wp_link_pages([
            'before'           => '<div class="navigationpost__nav">',
            'after'            => '</div>',
            'nextpagelink'     => '&gt;',
            'previouspagelink' => '&lt;',
          ]);
          ?>
        </div>

        <div class="post__catag">
          <?php
          foreach (get_taxonomies() as $value) {
            echo get_the_term_list(get_the_ID(), $value);
          }
          ?>
        </div>

        <div class="post__precnext">
          <?php
          $prev = get_previous_post();
          $next = get_next_post();
          if (!empty($prev)) {
            echo '<h3><a href="' . get_permalink($prev) . '">' . get_the_title($prev) . '</a></h3>';
          }
          if (!empty($next)) {
            echo '<h3><a href="' . get_permalink($next) . '">' . get_the_title($next) . '</a></h3>';
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

    <?php
    $main_query = new WP_Query([
      'fields'              => 'ids',
      'ignore_sticky_posts' => true,
      'posts_per_page'      => 5,
      'no_found_rows'       => true,
      'tax_query'           => [
        'taxonomy' => 'taxonomys',
        'terms'    => get_query_var('term'),
      ],
    ]);

    if ($main_query->have_posts()) :
      while ($main_query->have_posts()) : $main_query->the_post();
        //get_template_part('template-parts/content', 'posts');
        //print_r($main_query);
        the_title('<h3><a href="' . get_permalink() . '">', '</a></h3>');
        echo '<br>';
      endwhile;
    endif;
    wp_reset_postdata();
    ?>
  </div>

  <?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>