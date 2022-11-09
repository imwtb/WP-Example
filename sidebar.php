<?php $layout = !empty($args['layout']) ? $args['layout'] : 'raight'; ?>
<aside class="layout__<?php echo esc_attr($layout); ?> sticky__<?php echo esc_attr($layout); ?>">
  <?php if (is_active_sidebar('sidebar')) dynamic_sidebar('sidebar'); ?>
</aside>