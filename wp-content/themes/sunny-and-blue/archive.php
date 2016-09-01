<?php
/**
 * The template for displaying archive pages
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each one. For example, tag.php (Tag archives),
 * category.php (Category archives), author.php (Author archives), etc.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage sunny-and-blue
 */
get_header();

echo '<article class="type-page">';


if (!is_page( 'archive' )) {
    if (have_posts()) {


//init
        $count = $theCatID = '';

// set to empty
        $count = '';
        $count = $wp_query->found_posts;
        if (empty($count)) $count = 'No'

        ?>


        <!-- Page Title -->
        <h1><?php the_archive_title(); ?></h1>
        <!--Archives Begin-->
        <!--Page Description-->
        <?php if (is_tax( 'chapters' ) || is_post_type_archive( 'comic' ) ) { ?>
            <h6 class="article-meta-extra"><?php printf(_n("%d comic.", "%d comics.", $count, 'sunny-and-blue'), $count); ?></h6>
        <?php } else { ?>
            <h6 class="article-meta-extra"><?php printf(_n("%d result.", "%d results.", $count, 'sunny-and-blue'), $count); ?></h6>
        <?php } ?>
        <!-- Start Displaying archives -->
        <?php

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

        ?>

        <!--Display pagination-->
        <?php the_posts_pagination(); ?>
        <!--Archives End-->
    <?php } else { ?>
        <h2 class="page-title"><?php echo $title_string; ?></h2>
        <?php printf(_n("%d result.", "%d results.", $count, 'sunny-and-blue'), $count); ?>
    <?php } ?>
    <!-- End if have posts -->
<?php } else { ?>
    <!-- If no type of archive page is being displayed, show regular archive from Comic Easel plugin -->
    <h1><?php _e('Archive', 'sunny-and-blue'); ?></h1>
    <?php 
	if (function_exists('ceo_pluginfo')) {
	echo do_shortcode("[comic-archive list=0 thumbnail=1]");

	}
	?>
<?php } ?>
    <!--End if is archive -->
    </article>

<?php get_footer(); ?>