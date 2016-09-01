<?php
/**
 * The template for displaying all single posts and attachments
 *
 * @package WordPress
 * @subpackage sunny-and-blue
 */

get_header(); ?>
    <article class="type-page">


        <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

            <h1 class="inner-title"><?php the_title(); ?></h1>

            <h6 class="article-meta-extra">

                <?php _e('Posted on', 'sunny-and-blue'); ?> <?php the_date(get_option('date_format')); ?> <?php _e('at', 'sunny-and-blue'); ?> <?php the_time(get_option('time_format')); ?>
                <?php _e('by', 'sunny-and-blue'); ?> <?php the_author_posts_link(); ?>

            </h6>
            <?php do_action('comic-post-info'); ?>
            <?php the_content(); ?>
            <?php the_tags(_e('Tags', 'sunny-and-blue').': ', ', ', '<br />'); ?>
            <?php comments_template(); ?>

        <?php endwhile; ?>

        <?php else : ?>

            <h1><?php _e('No Posts Were Found', 'sunny-and-blue'); ?></h1>

        <?php endif; ?>
    </article>

<?php get_footer(); ?>