<?php $current_url = set_url_scheme('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>

</main>
<footer class="fooing">

  <div class="fooing__max max__1200">

    <?php qrcode(); ?>

    <?php
    if (has_nav_menu('secondary')) {
      wp_nav_menu([
        'container'       => 'nav',
        'container_class' => 'bottomnav',
        'theme_location'  => 'secondary',
      ]);
    }
    ?>

    <div class="fooing__copyright">
      <p>
        Copyright &copy; <?php echo date('Y') . ' ' . get_bloginfo('name') . ' ' . preg_replace('#^(http)?(s)?(://)#', '', home_url()); ?>
        <?php
        if (get_option('site_record')) echo '<a href="https://beian.miit.gov.cn/">' . get_option('site_record') . '</a>';
        the_privacy_policy_link();
        ?>
      </p>
      <p>
        <?php echo get_option('site_copyright') . get_option('site_script'); ?>
      </p>
    </div>
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