<ul class="posts__share">
  <li>
    <a href="<?php echo post_share_url("weibo"); ?>" target="_blank" rel="nofollow noopener">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/social_sina.svg" alt="<?php _e('分享到：微博', 'imwtb'); ?>" />
      <p><?php _e('微博', 'imwtb'); ?></p>
    </a>
  </li>
  <li>
    <a href="<?php echo post_share_url("qzone"); ?>" target="_blank" rel="nofollow noopener">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/social_qzone.svg" alt="<?php _e('分享到：Qzone', 'imwtb'); ?>" />
      <p><?php _e('Qzone', 'imwtb'); ?></p>
    </a>
  </li>
  <li>
    <a href="javascript:">
      <img src="<?php echo get_template_directory_uri(); ?>/assets/images/social_wechat.svg" alt="<?php _e('分享到：微信', 'imwtb'); ?>" />
      <p><?php _e('微信', 'imwtb'); ?></p>
    </a>
    <div class="posts__share--qrcode">
      <figure id="qrcode"></figure>
      <br><span><?php _e('QQ/微信扫一扫', 'spanmwtb'); ?></span>
    </div>
  </li>
  <script>
    var weibo_share_url = "<?php post_share_url("weibo"); ?>";
    var qzone_share_url = "<?php post_share_url("qzone"); ?>";
  </script>
</ul>