<?php
if (post_password_required()) {
	return;
}
$comment_count = get_comments_number();
?>

<div id="comments" class="comments__area">

	<?php
	$req           = get_option('require_name_email');
	$user_name     = $user->exists() ? $user->display_name : '';
	$required_text = sprintf(__('必需的地方已用 %s 做标记', 'example-text'), '<span>*</span>');
	comment_form([
		'fields' => [
			'author'	=> sprintf(
				'<p class="comment__form--author"><input id="author" name="author" type="text" value="%s" placeholder="%s %s" autocomplete="name" required /></p>',
				esc_attr($commenter['comment_author']),
				__('昵称', 'example-text'),
				($req ? '*' : ''),
			),
			'email'		=> sprintf(
				'<p class="comment__form--email"><input id="email" name="email" type="email" value="%s" placeholder="%s %s" aria-describedby="email-notes" autocomplete="email" required /></p>',
				esc_attr($commenter['comment_author_email']),
				__('邮箱', 'example-text'),
				($req ? '*' : ''),
			),
			'url'			=> sprintf(
				'<p class="comment__form--url"><input id="url" name="url" type="url" value="%s" placeholder="%s" autocomplete="url" /></p>',
				esc_attr($commenter['comment_author_url']),
				__('网址', 'example-text'),
			),
			'cookies' => sprintf(
				'<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"%s />',
				$consent
			),
		],
		'comment_field'        => '<p class="comment__form--comment"><textarea id="comment" name="comment" placeholder="' . esc_attr__('评论', 'example-text') . ' *" required></textarea></p>',
		'must_log_in'          => '<p class="comment__mustlogin"><a href="' . wp_login_url(apply_filters('the_permalink', get_permalink($post_id), $post_id)) . '">' . __('登录', 'example-text') . '</a>' . __('后发表评论！', 'example-text') . '</p>',
		'comment_notes_before' => '<p class="comment__notes"><span id="email-notes">' . __('您的电子邮件地址不会被公开。', 'example-text') . $required_text . '</span></p>',
		'logged_in_as'         => sprintf(
			__('<p class="comment__loggedinas"><a href="%s" aria-label="%s">登录为 %s</a>. <a href="%s">退出?</a>%s</p>', 'example-text'),
			get_edit_user_link(),
			esc_attr(sprintf(__('登录为 %s. 编辑您的个人资料。'), $user_name)),
			$user_name,
			wp_logout_url(apply_filters('the_permalink', get_permalink($post_id), $post_id)),
			$required_text
		),
		'class_container'      => 'comment__respond',
		'class_form'           => 'comment__form',
		/* 'title_reply'         => __('回复', 'example-text'),
		'title_reply_to'      => __('回复给 %s', 'example-text'), */
		'title_reply_before'  => '<h2 id="reply-title" class="comment__title">',
		'title_reply_after'   => '</h2>',
		/* 'cancel_reply_link'   => __('Cancel reply', 'example-text'),
		'label_submit'        => __('Post Comment', 'example-text'), */
		'submit_field'        => '<p class="comment__form--submit">%1$s %2$s</p>',
	]);
	?>

	<?php if (have_comments()) : ?>
		<h2 class="comments__title">
			<?php
			if ($comment_count === '1') {
				esc_html_e('1 评论', 'example-text');
			} else {
				printf(
					esc_html(_nx('%s 评论', '%s 评论', $comment_count, '评论标题', 'example-text')),
					esc_html(number_format_i18n($comment_count))
				);
			}
			?>
		</h2>

		<ol class="comment-list">
			<?php wp_list_comments(['avatar_size' => 60, 'style' => 'ol', 'short_ping' => true]);			?>
		</ol>

		<?php the_comments_pagination(['prev_text' => '&lt;', 'next_text' => '&gt;']); ?>

		<?php if (!comments_open()) : ?>
			<p class="comments__no"><?php esc_html_e('评论已关闭！', 'example-text'); ?></p>
		<?php endif; ?>
	<?php endif; ?>

</div>