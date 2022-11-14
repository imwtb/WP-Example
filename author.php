<?php get_header(); ?>

<div class="maing__max max__1200 layout">
  <div class="layout__center">
    <?php
    the_archive_title('<h2>', '</h2>');

    $user_id   = get_the_author_meta('ID');
    $user_name = get_the_author_meta('display_name', $user_id) ? get_the_author_meta('display_name', $user_id) : __('游客', 'news-text');
    switch ($user_role) {
      case 'administrator':
        $user_purview = __('管理员', 'news-text');
        break;
      case 'editor':
        $user_purview = __('编辑', 'news-text');
        break;
      case 'author':
        $user_purview = __('作者', 'news-text');
        break;
      case 'contributor':
        $user_purview = __('贡献者', 'news-text');
        break;
      case 'subscriber':
        $user_purview = __('订阅者', 'news-text');
        break;
      default:
        $user_purview = __('游客', 'news-text');
        break;
    }

    echo get_avatar($user_id) . '<br>';
    echo __('ID：', 'imwtb') . $user_id . '<br>';
    echo __('用户名：', 'imwtb') . $user_purview . '<br>';
    echo __('权限：', 'imwtb') . get_the_author_meta('user_login', $user_id) . '<br>';
    echo __('注册时间：', 'imwtb') . get_the_author_meta('user_registered', $user_id) . '<br>';
    echo __('等级：', 'imwtb') . get_the_author_meta('user_level', $user_id) . '<br>';
    echo __('名字：', 'imwtb') . get_the_author_meta('first_name', $user_id) . '<br>';
    echo __('姓氏：', 'imwtb') . get_the_author_meta('last_name', $user_id) . '<br>';
    echo __('昵称：', 'imwtb') . get_the_author_meta('nickname', $user_id) . '<br>';
    echo __('公开显示：', 'imwtb') . get_the_author_meta('display_name', $user_id) . '<br>';
    echo __('邮箱：', 'imwtb') . get_the_author_meta('user_email', $user_id) . '<br>';
    echo __('网址：', 'imwtb') . get_the_author_meta('user_url', $user_id) . '<br>';
    echo __('简介：', 'imwtb') . get_the_author_meta('description', $user_id) . '<br>';
    echo __('文章数量：', 'imwtb') . count_user_posts($user_id) . '<br>';
    echo __('评论数量：', 'imwtb') . count(get_comments($user_id)) . '<br>';

    $main_query = new WP_Query([
      'fields'              => 'ids',
      'ignore_sticky_posts' => true,
      'posts_per_page'      => get_option('posts_per_page'),
      'paged'               => (get_query_var('paged')) ? get_query_var('paged') : 1,
    ]);
    while ($main_query->have_posts()) : $main_query->the_post();
      get_template_part('template-parts/content', 'post');
    endwhile;
    the_posts_pagination(['prev_text' => '&lt;', 'next_text' => '&gt;']);
    wp_reset_postdata();

    ?>
  </div>

  <?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>