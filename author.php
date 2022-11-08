<?php get_header(); ?>

<div class="max__1200 layout">
  <div class="layout__center">
    <?php
    the_archive_title('<h2>', '</h2>');

    global $post;
    $author_id = $post->post_author;
    echo get_avatar($author_id) . '<br>';
    echo esc_html__('ID：', 'example-text') . get_the_author_meta('ID', $author_id) . '<br>';
    echo esc_html__('用户名：', 'example-text') . get_the_author_meta('user_login', $author_id) . '<br>';
    echo esc_html__('注册时间：', 'example-text') . get_the_author_meta('user_registered', $author_id) . '<br>';
    echo esc_html__('等级：', 'example-text') . get_the_author_meta('user_level', $author_id) . '<br>';
    echo esc_html__('名字：', 'example-text') . get_the_author_meta('first_name', $author_id) . '<br>';
    echo esc_html__('姓氏：', 'example-text') . get_the_author_meta('last_name', $author_id) . '<br>';
    echo esc_html__('昵称：', 'example-text') . get_the_author_meta('nickname', $author_id) . '<br>';
    echo esc_html__('公开显示：', 'example-text') . get_the_author_meta('display_name', $author_id) . '<br>';
    echo esc_html__('邮箱：', 'example-text') . get_the_author_meta('user_email', $author_id) . '<br>';
    echo esc_html__('网址：', 'example-text') . get_the_author_meta('user_url', $author_id) . '<br>';
    echo esc_html__('简介：', 'example-text') . get_the_author_meta('description', $author_id) . '<br>';

    $main_query = new WP_Query([
      'fields'              => 'ids',
      'ignore_sticky_posts' => true,
      'posts_per_page'      => get_option('posts_per_page'),
      'paged'               => (get_query_var('paged')) ? get_query_var('paged') : 1,
    ]);
    if ($main_query->have_posts()) :
      while ($main_query->have_posts()) : $main_query->the_post();
        //get_template_part('template-parts/content', 'posts');
        //print_r($main_query);
        the_title('<h3><a href="' . get_permalink() . '">', '</a></h3>');
      endwhile;
      the_posts_pagination(['prev_text' => '&lt;', 'next_text' => '&gt;']);
    endif;
    wp_reset_postdata();

    ?>
  </div>

  <?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>