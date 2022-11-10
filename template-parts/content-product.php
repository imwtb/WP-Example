<?php $excerpt = preg_replace('/( |　|\s)*/', '', wp_strip_all_tags(get_the_excerpt())); ?>
<article id="content">
  <?php the_title('<h1 class="post__title">', '</h1>'); ?>
  <div class="post__content">
    <?php
    the_content();
    wp_link_pages(['before' => '<nav class="navigation post__nav"><div class="nav-links">', 'after'  => '</div></nav>', 'nextpagelink' => '&gt;', 'previouspagelink' => '&lt;']);
    ?>
  </div>
</article>
<?php if (comments_open() || get_comments_number()) comments_template(); ?>