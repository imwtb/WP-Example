<?php $types = isset($args['types']) ? $args['types'] : 'sidebar'; ?>
<aside class="layout__<?php echo $layout; ?> sticky__<?php echo $layout; ?>">
  <nav class="aside__nav">
    <h3><?php _e('分类', 'imwtb'); ?></h3>
    <ul>
      <?php
      if (is_singular()) {
        global $post;
        $taxonomys = get_post_taxonomies($post);
        foreach ($taxonomys as $value) {
          if ($value != 'post_tag' && $value != 'post_format') {
            echo wp_list_categories([
              'taxonomy' => $value,
              'title_li' => '',
              'depth'    => 5,
            ]);
          }
        }
      } else {
        echo wp_list_categories([
          'taxonomy' => is_tax() ? get_term(get_queried_object_id())->taxonomy : 'category',
          'title_li' => '',
          'depth'    => 5,
        ]);
      }
      ?>
    </ul>
  </nav>
  <div class="aside__qrcode">
    <div id="qrcode"></div>
    <p><?php _e('移动设备浏览', 'imwtb'); ?></p>
  </div>
  <?php if ($types && is_active_sidebar($types)) dynamic_sidebar($types); ?>
</aside>