</main>

<footer class="fooing">

  Copyright &copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>

  <a href="<?php echo esc_url('https://beian.miit.gov.cn/'); ?>"><?php esc_html_e('京ICP备2022018789号'); ?></a>

  <?php the_privacy_policy_link(); ?>

</footer>

<?php wp_footer(); ?>

</body>

</html>