<?php

// Register Custom Post Type
// supports: 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'comments', 'trackbacks', 'page-attributes', 'post-formats', 'custom-fields'
// menu_icon @link https://developer.wordpress.org/resource/dashicons/#email
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
    /* 'all_items'             => __('All Custom Posts', 'example-text'),
    'add_new_item'          => __('Add New Custom Post', 'example-text'),
    'add_new'               => __('Add New', 'example-text'),
    'new_item'              => __('New Custom Post', 'example-text'),
    'edit_item'             => __('Edit Custom Post', 'example-text'),
    'update_item'           => __('Update Custom Post', 'example-text'),
    'view_item'             => __('View Custom Post', 'example-text'),
    'view_items'            => __('View Custom Posts', 'example-text'),
    'search_items'          => __('Search Custom Post', 'example-text'),
    'not_found'             => __('Not found', 'example-text'),
    'not_found_in_trash'    => __('Not found in Trash', 'example-text'),
    'featured_image'        => __('Featured Image', 'example-text'),
    'set_featured_image'    => __('Set featured image', 'example-text'),
    'remove_featured_image' => __('Remove featured image', 'example-text'),
    'use_featured_image'    => __('Use as featured image', 'example-text'),
    'insert_into_item'      => __('Insert into Custom Post', 'example-text'),
    'uploaded_to_this_item' => __('Uploaded to this Custom Post', 'example-text'),
    'items_list'            => __('Custom Posts list', 'example-text'),
    'items_list_navigation' => __('Custom Posts list navigation', 'example-text'),
    'filter_items_list'     => __('Filter Custom Posts list', 'example-text'), */
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
    /* 'search_items'      => __( 'Search Taxonomys', 'example-text' ),
    'all_items'         => __( 'All Taxonomys', 'example-text' ),
    'parent_item'       => __( 'Parent Taxonomy', 'example-text' ),
    'parent_item_colon' => __( 'Parent Taxonomy:', 'example-text' ),
    'edit_item'         => __( 'Edit Taxonomy', 'example-text' ),
    'update_item'       => __( 'Update Taxonomy', 'example-text' ),
    'add_new_item'      => __( 'Add New Taxonomy', 'example-text' ),
    'new_item_name'     => __( 'New Taxonomy Name', 'example-text' ),
    'menu_name'         => __( 'Taxonomy', 'example-text' ), */
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
