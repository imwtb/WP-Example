<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

  <?php wp_body_open(); ?>

  <h1 class="logo">
    <a href="<?php home_url(); ?>">
      <?php if (has_custom_logo()) : ?>
        <img src="<?php echo esc_url(wp_get_attachment_url(get_theme_mod('custom_logo'))); ?>" alt="">
      <?php endif; ?>
      <?php if (empty($has_custom_title)) : ?>
        <span><?php bloginfo('name'); ?></span>
      <?php endif; ?>
    </a>
  </h1>

  <?php
  if (has_nav_menu('primary')) {
    wp_nav_menu([
      'container'            => 'nav',
      'container_class'      => 'topnav',
      'container_aria_label' => '',
      'theme_location'       => 'primary',
    ]);
  }
  ?>

  <?php get_search_form(); ?>

  <?php do_action('breadcrumblist'); ?>