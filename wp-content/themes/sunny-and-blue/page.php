<<?php
/**
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage sunny-and-blue
 */

get_header(); ?>
    <article <?php post_class(); ?>>
        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            <h1 itemprop="headline"><?php the_title(); ?></h1>
            <?php the_content(); ?>

        <?php endwhile;
        else: ?>
            <p><?php _e('Sorry, this page does not exist.', 'sunny-and-blue'); ?></p>
        <?php endif; ?>

    </article>

<?php get_footer(); ?>