<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package Gridster
 */

get_header(); ?>
<?php get_sidebar(); ?>

<div id="main">
<?php if ( have_posts() ) : ?>
<h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'gridster-lite' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
<?php /* Start the Loop */ ?>
<?php while ( have_posts() ) : the_post(); ?>
<?php get_template_part( 'content', 'search' ); ?>
<?php endwhile; ?>
<?php gridster_content_nav( 'nav-below' ); ?>
<?php else : ?>
<?php get_template_part( 'no-results', 'search' ); ?>
<?php endif; ?>
<?php get_footer(); ?>