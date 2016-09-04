<?php
/**
 * Homepage
 *
 *
 * @package Gridster
 */

get_header(); ?>
<?php get_sidebar(); ?>

<div id="main">
<?php if ( have_posts() ) : ?>
<?php /* Start the Loop */ ?>
<?php while ( have_posts() ) : the_post(); ?>
<?php get_template_part( 'content', get_post_format() ); ?>
<?php endwhile; ?>
<?php gridster_content_nav( 'nav-below' ); ?>
<?php else : ?>
<?php get_template_part( 'no-results', 'index' ); ?>
<?php endif; ?>
<?php get_footer(); ?>
