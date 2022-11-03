<?php

add_action('init', function () {
  register_custom_post_type(__('产品', 'example-text'), 'product', array('products', 'brands'/* , 'post_tag' */), 'dashicons-store', array('title', 'editor', 'thumbnail', 'comments', 'custom-fields'));
  register_custom_post_type(__('品牌', 'example-text'), 'brand', array('brands'), 'dashicons-awards', array('title', 'editor', 'thumbnail', 'comments'));
  register_custom_post_type(__('视频', 'example-text'), 'video', array('videos'), 'dashicons-format-video', array('title', 'editor', 'thumbnail', 'comments',  'custom-fields'));
  register_custom_post_type(__('商铺', 'example-text'), 'store', array('stores'), 'dashicons-location-alt', array('title', 'editor', 'thumbnail', 'comments', 'custom-fields'));
}, 0);

add_action('init', function () {
  register_custom_taxonomy(__('产品类别', 'example-text'), 'products', array('product'));
  register_custom_taxonomy(__('品牌类别', 'example-text'), 'brands', array('brand', 'product'));
  register_custom_taxonomy(__('视频类别', 'example-text'), 'videos', array('video'));
  register_custom_taxonomy(__('商铺类别', 'example-text'), 'stores', array('store'));
}, 0);

// Register Custom Post Type
// menu_icon @link https://developer.wordpress.org/resource/dashicons/#email
function register_custom_post_type($label, $key, $taxonomies, $menu_icon, $supports)
{

  $labels = array(
    'name'                  => sprintf(_x('%s', '%s类型 一般名称', 'example-text'), $label),
    'singular_name'         => sprintf(_x('%s', '%s类型 单数名称', 'example-text'), $label),
    'menu_name'             => $label,
    'name_admin_bar'        => $label,
    'archives'              => __('归档', 'example-text'),
    'attributes'            => __('属性', 'example-text'),
    'parent_item_colon'     => __('父级：', 'example-text'),
    'all_items'             => __('所有', 'example-text'),
    'add_new_item'          => __('添加新的', 'example-text'),
    'add_new'               => __('添加', 'example-text'),
    'new_item'              => __('新的', 'example-text'),
    'edit_item'             => __('编辑', 'example-text'),
    'update_item'           => __('更新', 'example-text'),
    'view_item'             => __('查看', 'example-text'),
    'view_items'            => __('查看', 'example-text'),
    'search_items'          => __('搜索', 'example-text'),
    'not_found'             => __('未找到', 'example-text'),
    'not_found_in_trash'    => __('垃圾箱中未发现', 'example-text'),
    'featured_image'        => __('特色图片', 'example-text'),
    'set_featured_image'    => __('设置特色图片', 'example-text'),
    'remove_featured_image' => __('移除特色图片', 'example-text'),
    'use_featured_image'    => __('作为特色图片使用', 'example-text'),
    'insert_into_item'      => __('插入', 'example-text'),
    'uploaded_to_this_item' => __('上传', 'example-text'),
    'items_list'            => __('列表', 'example-text'),
    'items_list_navigation' => __('列表分页', 'example-text'),
    'filter_items_list'     => __('列表过滤', 'example-text'),
  );
  $args = array(
    'label'                 => $label,
    'description'           => sprintf(__('%s介绍', 'example-text'), $label),
    'labels'                => $labels,
    'supports'              => $supports,
    'taxonomies'            => $taxonomies,
    'hierarchical'          => false,
    'public'                => true,
    'show_ui'               => true,
    'show_in_menu'          => true,
    'menu_position'         => 5,
    'menu_icon'             => $menu_icon,
    'show_in_admin_bar'     => true,
    'show_in_nav_menus'     => true,
    'can_export'            => true,
    'has_archive'           => true,
    'exclude_from_search'   => false,
    'publicly_queryable'    => true,
    'capability_type'       => 'page',
    'show_in_rest'          => true,
  );
  register_post_type($key, $args);
}

// Register Custom Taxonomy
function register_custom_taxonomy($label, $key, $post_type)
{

  $labels = array(
    'name'                       => sprintf(_x('%s', '%s 一般名称', 'example-text'), $label),
    'singular_name'              => sprintf(_x('%s', '%s 单数名称', 'example-text'), $label),
    'menu_name'                  => $label,
    'all_items'                  => __('所有', 'example-text'),
    'parent_item'                => __('父级', 'example-text'),
    'parent_item_colon'          => __('父级：', 'example-text'),
    'new_item_name'              => __('新的', 'example-text'),
    'add_new_item'               => __('添加新的', 'example-text'),
    'edit_item'                  => __('编辑', 'example-text'),
    'update_item'                => __('更新', 'example-text'),
    'view_item'                  => __('查看', 'example-text'),
    'separate_items_with_commas' => __('用逗号分隔', 'example-text'),
    'add_or_remove_items'        => __('添加或删除', 'example-text'),
    'choose_from_most_used'      => __('从最常用的中选择', 'example-text'),
    'popular_items'              => __('热门', 'example-text'),
    'search_items'               => __('搜索', 'example-text'),
    'not_found'                  => __('未找到', 'example-text'),
    'no_terms'                   => __('未找到', 'example-text'),
    'items_list'                 => __('列表', 'example-text'),
    'items_list_navigation'      => __('列表分页', 'example-text'),
  );
  $args = array(
    'labels'                     => $labels,
    'hierarchical'               => true,
    'public'                     => true,
    'show_ui'                    => true,
    'show_admin_column'          => true,
    'show_in_nav_menus'          => true,
    'show_tagcloud'              => true,
    'show_in_rest'               => true,
  );
  register_taxonomy($key, $post_type, $args);
}
