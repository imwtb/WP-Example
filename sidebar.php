<?php
$layout = isset($args['layout']) ? $args['layout'] : 'right';
$types  = isset($args['types']) ? $args['types'] : 'sidebar';
?>
<aside class="layout__<?php echo $layout; ?> sticky__<?php echo $layout; ?>">
  <?php if ($types && is_active_sidebar($types)) dynamic_sidebar($types); ?>
</aside>