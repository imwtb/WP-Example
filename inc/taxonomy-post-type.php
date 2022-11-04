<?php

// supports: 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'author', 'comments', 'trackbacks', 'page-attributes', 'post-formats', 'custom-fields'
// menu_icon @link https://developer.wordpress.org/resource/dashicons/#email
add_action('init', function () {
  register_custom_post_type(__('产品', 'example-text'), 'product', ['products', 'brands'], 'dashicons-store', ['title', 'editor', 'thumbnail', 'comments', 'custom-fields']);
}, 0);

add_action('init', function () {
  register_custom_taxonomy(__('产品类别', 'example-text'), 'products', ['product']);
}, 0);

// Register Custom Post Type
function register_custom_post_type($label, $key, $taxonomies, $menu_icon, $supports)
{

  $labels = [
    'name'                  => sprintf(_x('%s', '%s类型 通用名称', 'example-text'), $label),
    'singular_name'         => sprintf(_x('%s', '%s类型 单数名称', 'example-text'), $label),
    'menu_name'             => sprintf(_x('%s', '管理员菜单文本', 'example-text'), $label),
    'name_admin_bar'        => sprintf(_x('%s', '工具栏上文本', 'example-text'), $label),
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
    'description'         => sprintf(__('%s介绍', 'example-text'), $label),
    'labels'              => $labels,
    'supports'            => $supports, //模块支持
    'taxonomies'          => $taxonomies, // 加入分类
    'menu_icon'           => $menu_icon, // 图标
    'hierarchical'        => false, // 分层
    'exclude_from_search' => false, // 从搜索中排除
    'publicly_queryable'  => true, // 可公开查询
    'has_archive'         => true, // 加入存档
    'public'              => true, // 公开
    'show_ui'             => true, // 用户界面显示
    'show_in_menu'        => true, // 菜单中显示
    'show_in_admin_bar'   => true, // 管理栏显示
    'can_export'          => true, // 可以导出
    'show_in_nav_menus'   => true, // 导航菜单中显示
    'menu_position'       => 5, // 位置
    'capability_type'     => 'post', // 返回
    'show_in_rest'        => true, // REST API 中显示
  ];
  register_post_type($key, $args);
}

// Register Custom Taxonomy
function register_custom_taxonomy($label, $key, $post_type)
{

  $labels = [
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
  ];
  $args = [
    'labels'                     => $labels,
    'hierarchical'               => true,
    'public'                     => true,
    'show_ui'                    => true,
    'show_admin_column'          => true,
    'show_in_nav_menus'          => true,
    'show_tagcloud'              => true,
    'show_in_rest'               => true,
  ];
  register_taxonomy($key, $post_type, $args);
}
