<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo('charset'); ?>" />
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <?php wp_body_open(); ?>

  <header class="heading">
    <div class="heading__max max__1200">

      <h1 class="logo">
        <a href="<?php echo home_url(); ?>">
          <?php if (has_custom_logo()) : ?>
            <img src="<?php echo wp_get_attachment_url(get_theme_mod('custom_logo')); ?>" alt="">
          <?php endif; ?>
          <?php if (get_option('site_logo_title', 1)) : ?>
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
    <?php if (!is_bbpress()) breadcrumbs('breadcrumbs', 'max__1200'); ?>