<?php
if (!class_exists('Theme_Walker_Comment')) {
  class Theme_Walker_Comment extends Walker_Comment
  {
    protected function html5_comment($comment, $depth, $args)
    {
      $commenter = wp_get_current_commenter();
      $approved  = ($comment->comment_approved == '0');
      $shows     = !empty($commenter['comment_author']);
?>
      <<?php echo ('div' === $args['style']) ? 'div' : 'li'; ?> id="comment__<?php comment_ID(); ?>" <?php comment_class($this->has_children ? 'parent' : '', $comment); ?>>
        <article id="div-comment-<?php comment_ID(); ?>" class="comment__body">

          <figure class="comment__vcard">
            </a>
            <?php
            $avatar_url = ($approved && !$shows) ? '' : '<a href="' . get_comment_author_url($comment) . '">';
            $avatar_url .= ($args['avatar_size'] != 0) ? get_avatar($comment, $args['avatar_size']) : '';
            $avatar_url .= ($approved && !$shows) ? '' : '</a>';
            echo $avatar_url;
           ?>
          </figure>

          <div class="comment__box">

            <header class="comment__head">
              <?php echo ($approved && !$shows) ? '<span>' . get_comment_author($comment) . '</span>' : get_comment_author_link($comment); ?>
            </header>

            <div class="comment__content">
              <?php
              $notes = $commenter['comment_author_email'] ? __('您的评论正在等待审核。 这是预览，您的评论将在获得批准后可见。', 'imwtb') : '';
              if ($approved) echo  '<em class="comment__awaiting">' . $notes . '</em>';
              comment_text();
             ?>
            </div>

            <footer class="comment__foo">
              <time datetime="<?php echo get_comment_time('c'); ?>"><?php echo get_comment_date('', $comment); ?></time>
              <?php edit_comment_link(__('编辑', 'imetb'), ' <span class="edit-link">', '</span>'); ?>
              <?php if ($comment->comment_approved == '1' || $shows) comment_reply_link(array_merge($args, ['add_below' => 'div-comment', 'depth' => $depth, 'max_depth' => $args['max_depth'], 'before' => '<span class="comment__reply">', 'after' => '</span>'])); ?>
            </footer>

          </div>
        </article>
  <?php
    }
  }
}
