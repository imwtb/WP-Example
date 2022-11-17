<?php
if (post_password_required()) {
	return;
}
?>

<div id="comments" class="comments__area">

	<?php if (!comments_open()) : ?>
		<p class="comment__no"><?php _e('评论已关闭！', 'imwtb'); ?></p>
	<?php else : ?>
		<?php
		$id      = get_the_ID();
		$comment = wp_get_current_commenter();
		$user    = wp_get_current_user();
		$name    = $user->exists() ? $user->display_name : '';
		$req     = get_option('require_name_email') ? ' *' : '';
		$consent = !empty($comment['comment_author_email']) ? 'checked' : '';
		$text    = sprintf(__(' 必需的地方已用 %s 做标记', 'imwtb'), '<span>*</span>');

		comment_form([
			'fields' => [
				'author'  => '<p class="comment__author"><input id="author" name="author" type="text" value="' . $comment['comment_author'] . '" placeholder="' . __('昵称', 'imwtb') . $req . '" autocomplete="name" required /></p>',
				'email'   => '<p class="comment__email"><input id="email" name="email" type="email" value="' . $comment['comment_author_email'] . '" placeholder="' . __('邮箱', 'imwtb') . $req . '"  aria-describedby="email-notes" autocomplete="email" required /></p>',
				'url'     => '<p class="comment__url"><input id="url" name="url" type="url" value="' . $comment['comment_author_url'] . '" placeholder="' . __('网址', 'imwtb') . '" autocomplete="url" /></p>',
				'cookies' => has_action('set_comment_cookies', 'wp_set_comment_cookies') && get_option('show_comments_cookies_opt_in') ? '<p class="comment__cookies"><label><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" ' . $consent . ' />' . sprintf(__('保存信息，并同意%s条款。', 'imwtb'), '<a href="' . get_privacy_policy_url() . '">' . __('隐私政策', 'imwtb') . '</a>') . '<label></p>' : '',
			],
			'comment_field'        => '<p class="comment__comment"><textarea id="comment" name="comment" placeholder="' . __('评论', 'imwtb') . ' *" required></textarea></p>',
			'must_log_in'          => '<p class="comment__mustlogin"><a href="' . wp_login_url(apply_filters('the_permalink', get_the_permalink($id), $id)) . '">' . __('登录', 'imwtb') . '</a>' . __('后发表评论！', 'imwtb') . '</p>',
			'comment_notes_before' => '<p class="comment__notes">' . __('您的电子邮件地址不会被公开。', 'imwtb') . $text . '</p>',
			'logged_in_as'         => '<p class="comment__loggedinas">' . __('登录为', 'imwtb') . '<a href="' . get_edit_user_link() . '">' . $name . '</a>. <a href="' . wp_logout_url(apply_filters('the_permalink', get_the_permalink($id), $id)) . '">' . __('退出?', 'imwtb') . '</a>' . $text . '</p>',
			'class_container'      => 'comment__respond',
			'class_form'           => 'comment__form',
			'title_reply_before'   => '<h2 id="reply-title" class="comment__title">',
			'title_reply_after'    => '</h2>',
			'submit_field'         => '<p class="comment__submit">%1$s %2$s</p>',
		]);
		?>
	<?php endif; ?>

	<?php if (have_comments()) : ?>
		<h2 class="comment__title"><?php comments_number(__('共 0 条评论', 'imwtb'), __('共 1 条评论', 'imwtb'), __('共 % 条评论', 'imwtb')); ?></h2>
		<ol class="comment__list">
			<?php wp_list_comments(['avatar_size' => 60, 'style' => 'li', 'short_ping' => true, 'walker' => new Theme_Walker_Comment()]);			?>
		</ol>
		<?php the_comments_pagination(['prev_text' => '&lt;', 'next_text' => '&gt;']); ?>
	<?php endif; ?>
</div>