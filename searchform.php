<?php
$unique_id  = wp_unique_id('search-form-');
$aria_label = !empty($args['aria_label']) ? 'aria-label="' . esc_attr($args['aria_label']) . '"' : '';
?>
<form role="search" <?php echo esc_attr($aria_label); ?> method="get" class="search__form" action="<?php echo esc_url(home_url('/')); ?>">
  <label for="<?php echo esc_attr($unique_id); ?>"><?php esc_html_e('搜索', 'imwtb'); ?></label>
  <input type="search" id="<?php echo esc_attr($unique_id); ?>" class="search__field" placeholder="<?php esc_attr_e('搜索', 'imwtb'); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s" />
  <button type="submit" class="search__submit"><i class="iconoir-search"></i></button>
</form>