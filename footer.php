<?php $current_url = set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>

</main>
<footer class="fooing">

  <div class="fooing__max max__1200">
    <div id="qrcode"></div>

    <?php
    if (has_nav_menu('secondary')) {
      wp_nav_menu([
        'container'       => 'nav',
        'container_class' => 'bottomnav',
        'theme_location'  => 'secondary',
      ]);
    }
    ?>

    Copyright &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?> <a href="<?php echo esc_url('https://beian.miit.gov.cn/'); ?>"><?php _e('京ICP备2022018789号'); ?></a> <?php the_privacy_policy_link(); ?>
  </div>

</footer>

<?php wp_footer(); ?>

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