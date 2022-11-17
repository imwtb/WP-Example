<?php
$unique_id  = wp_unique_id('search-form-');
$aria_label = !empty($args['aria_label']) ? 'aria-label="' . $args['aria_label'] . '"' : '';
?>
<form role="search" <?php echo $aria_label; ?> method="get" class="search__form" action="<?php echo home_url('/'); ?>">
  <label for="<?php echo $unique_id; ?>"><?php _e('搜索', 'imwtb'); ?></label>
  <input type="search" id="<?php echo $unique_id; ?>" class="search__field" placeholder="<?php _e('搜索', 'imwtb'); ?>" value="<?php echo get_search_query(); ?>" name="s" />
  <button type="submit" class="search__submit"><i class="iconoir-search"></i></button>
</form>