<?php
/**
 * Template Name: Full Width (No Sidebar)
 * Template Post Type: page
 */
get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
  <?php the_content(); ?>
<?php endwhile; ?>

<?php get_footer(); ?>
