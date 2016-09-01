<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage sunny-and-blue
 */
get_header();
?>
    <div class="row">
        <!-- blog-contents -->
        <section class="col-md-8">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <article class="blog-item">
                    <div class="row">
                        <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
                        <h6 class="article-meta-extra"><?php _e('Posted on', 'sunny-and-blue'); ?> <?php the_date(get_option('date_format')); ?>
                            <?php _e('at', 'sunny-and-blue'); ?> <?php the_time(get_option('time_format')); ?> <?php _e('by', 'sunny-and-blue'); ?> <?php the_author_posts_link(); ?></h6>
                        <?php the_excerpt(); ?>
                        <?php the_tags(_e('Tags', 'sunny-and-blue').': ', ', ', '<br />'); ?>
                        <a href="<?php the_permalink(); ?>" class="btn btnMore"
                           title="Read More"><?php comments_number(); ?> - <?php _e('Read More', 'sunny-and-blue'); ?></a>
                    </div>
                </article> <!-- /.blog-item -->
            <?php endwhile;
            else: ?>
                <p><?php _e('Sorry, no posts matched your criteria.', 'sunny-and-blue'); ?></p><?php endif; ?>

            <!--Display pagination-->
            <?php the_posts_pagination(); ?>


        </section>
        <!-- end of blog-contents -->
        <?php get_sidebar(); ?>

    </div> <!-- end of /.row -->


<?php get_footer(); ?>