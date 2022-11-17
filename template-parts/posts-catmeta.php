<div class="posts__catmeta">
  <p class="posts__cat">
    <?php
    foreach (get_post_taxonomies() as $tax) {
      if (is_singular() && $tax != 'post_format') {
        echo get_the_term_list(get_the_ID(), $tax);
      } else if ($tax != 'post_format' && $tax != 'post_tag' && isset($args['widget']) === false) {
        echo get_the_term_list(get_the_ID(), $tax);
      } else if ($tax != 'post_format' && $tax != 'post_tag') {
        echo get_the_term_list(get_the_ID(), $tax);
      }
    }
    ?>
  </p>
  <p class="posts__meta">
    <?php if (isset($args['widget']) === false) : ?>
      <time datetime="<?php the_date('Y-m-d H:s'); ?>"><i class="iconoir-clock-outline"></i><?php the_time('Y/m/d'); ?></time>
    <?php endif; ?>

    <span><i class="iconoir-eye-empty"></i><?php echo get_post_views() ? get_post_views() : '0'; ?></span>

    <a href="<?php echo get_comments_link(); ?>"><i class="iconoir-chat-lines"></i><?php comments_number(0, 1, '%'); ?></a>

    <?php
    if (current_user_can('manage_options') && is_singular() && isset($args['widget']) === false) {
      edit_post_link(sprintf('<i class="iconoir-edit-pencil"></i>%s', __('编辑', 'news-text')), '', '', get_the_ID(), '');
      delete_post_link(sprintf('<i class="iconoir-trash"></i>%s', __('删除', 'news-text')), '', '', get_the_ID(), '');
    }
    ?>
  </p>
</div>