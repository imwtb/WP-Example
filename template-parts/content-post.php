<?php if (is_singular()) : ?>
  <article id="content">
    <?php
    $before = '<figure class="posts__thumb">';
    $after  = '</figure>';
    if (is_singular('video')) {
      switch (get_post_meta(get_the_ID(), 'video_radio', true)) {
        case '1':
          echo $before . '<video width="condition" controls="controls" type="video/mp4" muted="muted" poster="' . get_the_post_thumbnail_url('medium_large') . '"><source src="' . get_post_meta(get_the_ID(), 'video_mp4', true) . '" type="video/mp4">' . __('您的浏览器太low了,不支持播放', 'imwtb') . '</video>' . $after;
          break;
        case '2':
          echo $before . '<iframe src="' . get_post_meta(get_the_ID(), 'video_iframe', true) . '" frameborder="0"></iframe>' . $after;
          break;
        default:
          break;
      }
    } else {
      if (has_post_thumbnail()) {
        echo $before . get_the_post_thumbnail('medium_large') . $after;
      }
    }
    ?>
    <?php the_title('<h1 class="post__title">', '</h1>'); ?>
    <div class="post__meta">
      <a href="<?php echo esc_url(get_author_posts_url($post->post_author)); ?>"><i class="iconoir-user"></i><?php the_author_meta('display_name'); ?></a>
      <time datetime="<?php the_date('Y-m-d H:s'); ?>"><i class="iconoir-clock-outline"></i><?php the_time(); ?></time>
      <a href="<?php comments_link(); ?>"><i class="iconoir-chat-bubble"></i><?php comments_number(0, 1, '%'); ?></a>
      <?php
      if (current_user_can('manage_options')) {
        edit_post_link(__('编辑', 'imwtb'), '', '', get_the_ID(), '');
        delete_post_link(__('删除', 'imwtb'), '', '', get_the_ID(), '');
      }
      ?>
    </div>
    <?php
    $excerpt = preg_replace('/( |　|\s)*/', '', wp_strip_all_tags(get_the_excerpt()));
    echo $excerpt ? '<div class="post__excerpt">' . esc_html($excerpt) . '</div>' : '';
    ?>
    <div class="post__content">
      <?php
      the_content();
      wp_link_pages(['before' => '<nav class="navigation post__nav"><div class="nav-links">', 'after'  => '</div></nav>', 'nextpagelink' => '&gt;', 'previouspagelink' => '&lt;']);
      ?>
    </div>
    <div class="post__catag">
      <?php
      foreach (get_post_taxonomies() as $tax) {
        echo $tax != 'post_format' ? get_the_term_list(get_the_ID(), $tax) : '';
      }
      ?>
    </div>
    <div class="post__precnext">
      <?php
      if (!empty($prev)) echo '<h3><a href="' . esc_url(get_the_permalink($prev)) . '">' . get_the_title($prev) . '</a></h3>';
      if (!empty($next)) echo '<h3><a href="' . esc_url(get_the_permalink($next)) . '">' . get_the_title($next) . '</a></h3>';
      ?>
    </div>
  </article>
  <?php if (comments_open() || get_comments_number()) comments_template(); ?>
<?php else : ?>
  <article>
    <figure>
      <?php the_post_thumbnail(); ?>
      <?php the_content_thumbnail(); ?>
    </figure>
    <?php the_title('<h3><a href="' . esc_url(get_the_permalink()) . '">', '</a></h3>'); ?>
  </article>
<?php endif; ?>