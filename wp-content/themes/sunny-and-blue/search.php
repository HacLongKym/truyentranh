<?php
/**
 * The template for displaying search results pages
 *
 * @package WordPress
 * @subpackage sunny-and-blue
 */

get_header(); ?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article class="blog-item">
        <div class="row">
            <h1 itemprop="headline"><?php the_title(); ?></h1>
            <h6 class="article-meta-extra"><?php _e('Posted on', 'sunny-and-blue'); ?> <?php the_date(get_option('date_format')); ?>
                <?php _e('at', 'sunny-and-blue'); ?> <?php the_time(get_option('time_format')); ?> <?php _e('by', 'sunny-and-blue'); ?> <?php the_author_posts_link(); ?></h6>
            <?php the_excerpt(); ?>
            <a href="<?php the_permalink(); ?>" class="btn btnMore" title="Read More"><?php comments_number(); ?> - <?php _e('Read
                More', 'sunny-and-blue'); ?></a>
        </div>
    </article> <!-- /.blog-item -->
<?php endwhile;
else: ?>

    <article class="type-page">

        <h1><?php _e('No posts were found.', 'sunny-and-blue'); ?></h1>

    </article>

<?php endif; ?>

    <div class="page-turn">
        <div class="row">
            <div class="col-md-6 col-md-offset-3 text-center">
                <nav>
                    <!--Display pagination-->
                    <?php the_posts_pagination(); ?>
                </nav>
            </div>
        </div>
    </div> <!-- /.page-turn -->
    </div> <!-- end of /.container -->
    </main>
<?php get_footer(); ?>