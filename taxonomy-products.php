<?php get_header(); ?>

<?php
the_archive_title('<h2>', edit_term_link(__('编辑', 'example-text'), '', '', get_query_var('cat'), false) . '</h2>');
the_archive_description();
?>

<?php get_template_part('template-parts/query', 'posts'); ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>