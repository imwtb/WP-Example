<?php
if (has_nav_menu('secondary')) {
  wp_nav_menu([
    'container'            => 'nav',
    'container_class'      => 'bottomnav',
    'container_aria_label' => '',
    'theme_location'       => 'secondary',
  ]);
}
?>

Copyright &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>

<a href="<?php echo esc_url('https://beian.miit.gov.cn/'); ?>"><?php esc_html_e('京ICP备2022018789号'); ?></a>

<?php the_privacy_policy_link(); ?>

<div id="qrcode"></div>

<?php wp_footer(); ?>

<?php $current_url = set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>
<script>
  jQuery("#qrcode").qrcode({
    width: 128,
    height: 128,
    render: "canvas",
    text: "<?php echo esc_url($current_url); ?>"
  })
</script>

</body>

</html>