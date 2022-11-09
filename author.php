<?php get_header(); ?>

<div class="maing__max max__1200 layout">
  <div class="layout__center">
    <?php
    the_archive_title('<h2>', '</h2>');

    global $post;
    $author_id = $post->post_author;
    echo get_avatar($author_id) . '<br>';
    echo __('ID：', 'imwtb') . get_the_author_meta('ID', $author_id) . '<br>';
    echo __('用户名：', 'imwtb') . get_the_author_meta('user_login', $author_id) . '<br>';
    echo __('注册时间：', 'imwtb') . get_the_author_meta('user_registered', $author_id) . '<br>';
    echo __('等级：', 'imwtb') . get_the_author_meta('user_level', $author_id) . '<br>';
    echo __('名字：', 'imwtb') . get_the_author_meta('first_name', $author_id) . '<br>';
    echo __('姓氏：', 'imwtb') . get_the_author_meta('last_name', $author_id) . '<br>';
    echo __('昵称：', 'imwtb') . get_the_author_meta('nickname', $author_id) . '<br>';
    echo __('公开显示：', 'imwtb') . get_the_author_meta('display_name', $author_id) . '<br>';
    echo __('邮箱：', 'imwtb') . get_the_author_meta('user_email', $author_id) . '<br>';
    echo __('网址：', 'imwtb') . get_the_author_meta('user_url', $author_id) . '<br>';
    echo __('简介：', 'imwtb') . get_the_author_meta('description', $author_id) . '<br>';

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