<?php
/**
 * Tag archive template.
 *
 * For Sunny-and-Blue-specific archives, see `sunny-and-blue/archive.php`.
 *
 * @package WordPress
 * @subpackage sunny-and-blue
 */

get_header(); ?>
    <article class="type-page">
        <?php if (have_posts()) : ?>

            <h1 class="inner-title"
                itemprop="headline"><?php single_tag_title(sprintf('<span class="screen-reader-text">%s </span>', __('Posts tagged ', 'sunny-and-blue'))); ?></h1>


            <?php
            $tag = $wp_query->get_queried_object();
            echo "<i>" . $tag->count . __(' results', 'sunny-and-blue') ."</i><br/>";

            while (have_posts()) : the_post();
                echo '<h2><a href="';
                the_permalink();
                echo '">';
                the_title();
                echo '</a>';
                echo "</h2>";
                echo '<h6 class="article-meta-extra">';
               _e('By ', 'sunny-and-blue');
                the_author_posts_link();
                _e(' on ', 'sunny-and-blue');
                the_date(get_option('date_format'));
                _e(' at ', 'sunny-and-blue');
                the_time(get_option('time_format'));
                echo '</h6>';

                //If it's a comic
                if ($post->post_type == 'comic') {
                    do_action('comic-post-info');
                    if (has_post_thumbnail()) { // check if the post has a Post Thumbnail assigned to it.
                        echo '<a href="' . esc_url(get_permalink(get_the_ID())) . '" title="' . esc_attr(get_post_field('post_title', get_the_ID())) . '">';
                        the_post_thumbnail('medium');
                        echo '</a><br/>';
                    }
                    //Display sharing only for comic, regular blog content has sharing already built in.
                    if (function_exists('sharing_display')) {
                        $switched_status = get_post_meta($post->ID, 'sharing_disabled', false);
                        if (empty($switched_status)) echo sharing_display();
                    }
                } // else regular blog post
                else {
                    the_excerpt();
                }

                the_tags(_e('Tags', 'sunny-and-blue').': ', ', ', '<br />');
            endwhile;

        endif;
        ?>
        <!--Display pagination-->
        <?php the_posts_pagination(); ?>

    </article>
<?php get_footer(); ?>