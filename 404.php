<?php get_header(); ?>

<div class="error__404">

  <h1 class="error__404--title"><?php esc_html_e('这里什么也没有', 'example-text'); ?></h1>

  <div class="error__404--content">
    <p><?php esc_html_e('看起来在这个地方没有发现什么。也许可以尝试搜索一下？', 'example-text'); ?></p>
    <?php get_search_form(); ?>
  </div>

</div>

<?php get_footer(); ?>