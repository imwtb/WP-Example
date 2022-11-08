<?php

// Register Custom Post Type
// supports: 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'comments', 'trackbacks', 'page-attributes', 'post-formats', 'custom-fields'
// menu_icon @link https://developer.wordpress.org/resource/dashicons
function register_custom_post_type($label, $key, $taxonomies, $menu_icon, $supports, $description = '')
{

  $labels = [
    'name'                  => $label,
    'singular_name'         => $label,
    'menu_name'             => $label,
    'name_admin_bar'        => $label,
    'archives'              => $label,
    'attributes'            => $label,
    'parent_item_colon'     => $label,
    'add_new'               => __('添加', 'imwtb'),
    'add_new_item'          => sprintf(__('添加新%s', 'imwtb'), $label),
    /* 'all_items'             => __('All Custom Posts', 'imwtb'),
    'new_item'              => __('New Custom Post', 'imwtb'),
    'edit_item'             => __('Edit Custom Post', 'imwtb'),
    'update_item'           => __('Update Custom Post', 'imwtb'),
    'view_item'             => __('View Custom Post', 'imwtb'),
    'view_items'            => __('View Custom Posts', 'imwtb'),
    'search_items'          => __('Search Custom Post', 'imwtb'),
    'not_found'             => __('Not found', 'imwtb'),
    'not_found_in_trash'    => __('Not found in Trash', 'imwtb'),
    'featured_image'        => __('Featured Image', 'imwtb'),
    'set_featured_image'    => __('Set featured image', 'imwtb'),
    'remove_featured_image' => __('Remove featured image', 'imwtb'),
    'use_featured_image'    => __('Use as featured image', 'imwtb'),
    'insert_into_item'      => __('Insert into Custom Post', 'imwtb'),
    'uploaded_to_this_item' => __('Uploaded to this Custom Post', 'imwtb'),
    'items_list'            => __('Custom Posts list', 'imwtb'),
    'items_list_navigation' => __('Custom Posts list navigation', 'imwtb'),
    'filter_items_list'     => __('Filter Custom Posts list', 'imwtb'), */
  ];
  $args = [
    'label'               => $label,
    'description'         => $description,
    'labels'              => $labels,
    'supports'            => $supports,      //模块支持
    'taxonomies'          => $taxonomies,    // 加入分类
    'menu_icon'           => $menu_icon,     // 图标
    'hierarchical'        => false,          // 分层
    'exclude_from_search' => false,          // 从搜索中排除
    'publicly_queryable'  => true,           // 可公开查询
    'has_archive'         => true,           // 加入存档
    'public'              => true,           // 公开
    'show_ui'             => true,           // 用户界面显示
    'show_in_menu'        => true,           // 菜单中显示
    'show_in_admin_bar'   => true,           // 管理栏显示
    'can_export'          => true,           // 可以导出
    'show_in_nav_menus'   => true,           // 导航菜单中显示
    'menu_position'       => 5,              // 位置
    'capability_type'     => 'post',         // 返回
    'show_in_rest'        => true,           // REST API 中显示
  ];
  register_post_type($key, $args);
}

// Register Custom Taxonomy
function register_custom_taxonomy($label, $key, $post_type, $description = '')
{

  $labels = [
    'name'              => $label,
    'singular_name'     => $label,
    /* 'search_items'      => __( 'Search Taxonomys', 'imwtb' ),
    'all_items'         => __( 'All Taxonomys', 'imwtb' ),
    'parent_item'       => __( 'Parent Taxonomy', 'imwtb' ),
    'parent_item_colon' => __( 'Parent Taxonomy:', 'imwtb' ),
    'edit_item'         => __( 'Edit Taxonomy', 'imwtb' ),
    'update_item'       => __( 'Update Taxonomy', 'imwtb' ),
    'add_new_item'      => __( 'Add New Taxonomy', 'imwtb' ),
    'new_item_name'     => __( 'New Taxonomy Name', 'imwtb' ),
    'menu_name'         => __( 'Taxonomy', 'imwtb' ), */
  ];
  $args = [
    'labels'             => $labels,
    'description'        => $description,
    'hierarchical'       => true,           // 分层
    'public'             => true,           // 公开
    'publicly_queryable' => true,           // 可公开查询
    'show_ui'            => true,           // 用户界面显示
    'show_in_menu'       => true,           // 菜单中显示
    'show_in_nav_menus'  => true,           // 导航菜单中显示
    'show_tagcloud'      => true,           // 标签云中显示
    'show_in_quick_edit' => true,           // 快速编辑中显示
    'show_admin_column'  => true,           // 管理栏显示
    'show_in_rest'       => true,           // REST API 中显示
  ];
  register_taxonomy($key, $post_type, $args);
}
