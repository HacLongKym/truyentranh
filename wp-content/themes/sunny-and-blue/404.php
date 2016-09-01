<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package WordPress
 * @subpackage sunny-and-blue
 */

get_header(); ?>
    <article class="type-page">
        <h2><?php _e('Page not Found!', 'sunny-and-blue'); ?></h2>

        <p class="not-found-paragraph">
		<?php _e('To help you find what you are looking for simply use the navigation above or
            search for what you are looking for below.', 'sunny-and-blue'); ?>
		
        </p>

        <?php get_search_form(); ?>
    </article>

<?php get_footer(); ?>