<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package Gridster
 */

get_header(); ?>
<?php get_sidebar(); ?>
<div id="main">
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<h1 class="page-title">
<?php _e( 'Oops! That page can&rsquo;t be found.', 'gridster-lite' ); ?>
</h1>
<p>
<?php _e( 'It looks like nothing was found at this location. Try searching for your requested page below', 'gridster-lite' ); ?>
</p>
<?php get_search_form(); ?>
</div>