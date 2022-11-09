<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no" />
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>

  <header class="heading">
    <div class="heading__max max__1200">

      <h1 class="logo">
        <a href="<?php echo home_url(); ?>">
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
          'theme_location'       => 'primary',
        ]);
      }
      ?>

      <?php get_search_form(); ?>
    </div>
  </header>

  <main class="maing">
    <?php if (!is_home() && !is_front_page()) echo breadcrumblist('<nav class="breadcrumblist"><ol class="max__1200">', '</ol></nav>'); ?>