<?php

if (!class_exists('Theme_Walker_Comment')) {
  class Theme_Walker_Comment extends Walker_Comment
  {
    protected function html5_comment($comment, $depth, $args)
    {
      $tag       = ('div' === $args['style']) ? 'div' : 'li';
      $commenter = wp_get_current_commenter();
      $shows     = !empty($commenter['comment_author']);
      $notes     = $commenter['comment_author_email'] ? __('您的评论正在等待审核。', 'imwtb') : __('您的评论正在等待审核。 这是预览; 您的评论将在获得批准后可见。', 'imwtb');
?>
      <<?php echo $tag; ?> id="comment__<?php comment_ID(); ?>" <?php comment_class($this->has_children ? 'parent' : '', $comment); ?>>
        <article id="div-comment-<?php comment_ID(); ?>" class="comment__body">

          <figure class="comment__vcard">
            <?php if (0 != $args['avatar_size']) echo get_avatar($comment, $args['avatar_size']) ?>
          </figure>

          <div class="comment__box">

            <header class="comment__head">
              <?php
              $comment_author = ('0' == $comment->comment_approved && !$shows) ? '<span>' . get_comment_author($comment) . '</span>' : get_comment_author_link($comment);
              echo $comment_author;
              ?>
            </header>

            <div class="comment__content">
              <?php if ('0' == $comment->comment_approved) : ?>
                <em class="comment__awaiting"><?php echo $notes; ?></em>
              <?php endif; ?>
              <?php comment_text(); ?>
            </div>

            <footer class="comment__foo">
              <time datetime="<?php echo get_comment_time('c'); ?>"><?php echo get_comment_date('', $comment); ?></time>
              <?php edit_comment_link(__('编辑', 'imetb'), ' <span class="edit-link">', '</span>'); ?>
              <?php
              if ('1' == $comment->comment_approved || $shows) {
                comment_reply_link(array_merge($args, ['add_below' => 'div-comment', 'depth' => $depth, 'max_depth' => $args['max_depth'], 'before' => '<span class="comment__reply">', 'after' => '</span>']));
              }
              ?>
            </footer>

          </div>
        </article>
  <?php
    }
  }
}
