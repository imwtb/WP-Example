<?php
$layout = !empty($args['layout']) ? $args['layout'] : 'right';
$types  = !empty($args['types']) ? $args['types'] : 'sidebar';
?>
<aside class="layout__<?php echo esc_attr($layout); ?> sticky__<?php echo esc_attr($layout); ?>">
  <?php if ($types && is_active_sidebar($types)) dynamic_sidebar($types); ?>
</aside>