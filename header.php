<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

  <?php wp_body_open(); ?>

  <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('跳到内容', 'example-text'); ?></a>

  <figure class="heading__logo">
    <a href="<?php echo HOME_URI; ?>">
      <?php if (has_custom_logo()) : ?>
        <img src="<?php echo esc_url(wp_get_attachment_url(get_theme_mod('custom_logo'))); ?>" alt="">
      <?php endif; ?>
      <?php if (empty($has_custom_title)) : ?>
        <span><?php bloginfo('name'); ?></span>
      <?php endif; ?>
    </a>
  </figure>

  <?php if (has_nav_menu('primary')) : ?>
    <nav id="heading__nav" class="heading__nav">
      <button id="heading__nav--mobile--menu" class="heading__nav--mobile">
        <span class="heading__nav--mobile--open">Open</span>
        <span class="heading__nav--mobile--close">Close</span>
      </button>
      <?php
      wp_nav_menu(array(
        'container'       => 'nav',
        'container_class' => 'mainnav',
        'theme_location'  => 'primary',
        'depth'           => '1',
        'fallback_cb'     => false,
      ));
      ?>
    </nav>
  <?php endif; ?>

  <?php get_search_form(); ?>

  <div>
    <?php do_action('breadcrumblist'); ?>