<?php
get_header();
$id = get_the_author_meta('ID');

the_author_meta('display_name', $id);
?>

<?php the_archive_title('<h2>', '</h2>'); ?>

<?php get_sidebar(); ?>

<?php get_footer(); ?>