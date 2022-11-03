<?php
if (post_password_required()) {
	return;
}
?>

<div id="comments" class="post__comments comments__area <?php echo get_option('show_avatars') ? 'show-avatars' : ''; ?>">

	<?php if (!comments_open()) {
		echo '<p class="no-comments">' . esc_html__('评论已关闭！', 'example-text') . '</p>';
	} else {

		comment_form(array(
			'title_reply'        => esc_html__('写评论', 'example-text'),
			'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h2>',
		));
	} ?>

	<?php if (have_comments()) :	?>
		<h2 class="comments__title">
			<?php
			comments_number(
				sprintf('0 %s', __('评论', 'example-text')),
				sprintf('1 %s', __('评论', 'example-text')),
				sprintf('% %s', __('评论', 'example-text')),
			);
			?>
		</h2>

		<ol class="comments__list">
			<?php
			wp_list_comments(array(
				'avatar_size' => 60,
				'style'       => 'ol',
				'short_ping'  => true,

			));
			?>
		</ol>

		<?php
		the_comments_pagination(array(
			'before_page_number' => '',
			'mid_size'           => 0,
			'prev_text'          => esc_html__('上一页', 'example-text'),
			'next_text'          => esc_html__('下一页', 'example-text'),
		));
		?>

	<?php endif; ?>

</div>