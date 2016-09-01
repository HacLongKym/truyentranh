<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "container" div.
 *
 * @package WordPress
 * @subpackage sunny-and-blue
 */

?>
<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html <?php language_attributes(); ?> class="no-js"> <!--<![endif]-->
<head>
    <!-- meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only"><?php _e('Toggle navigation', 'sunny-and-blue'); ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
			<!--brandlogo-->
			<div id="logo">
			<?php if(function_exists( 'the_custom_logo' ) && has_custom_logo()) : the_custom_logo();?>
			<?php else:?>
			<h1 class="site-name">
									<a class="navbar-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
								</h1>
					
			<?php endif;?>
			</div>
        
		   <!--brandlogo-->
        </div>
        <!-- Generate nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">

            <?php
            if ( has_nav_menu( 'primary' ) ) {
            wp_nav_menu( array(
            'container' => false,
            'menu_id' => 'header_menu',
            'menu_class' => 'nav navbar-nav navbar-right',
            'theme_location' => 'primary',
            'depth' => 0,
            'walker' => new wp_bootstrap_navwalker())
            );
            }
            ?>
        </div>
        <!--/.nav-collapse -->
    </div>
    <!-- end of /.container -->
</nav>
<main>
    <div class="container">
        <!--Display comic on home page and single comic post(single.php)-->
        <?php
        if (function_exists('ceo_pluginfo')) {
        if (is_home() || is_front_page() || (is_single() && $post->post_type == 'comic')) {
            do_action('comic-area');
            //Comic Blog Post
            if (!is_paged() && !ceo_pluginfo('disable_comic_blog_on_home_page') && is_home()) {
                echo '<article class="blog-item">';
                do_action('comic-blog-area');

                //Display comic post tags
                if (function_exists('ceo_pluginfo') && is_home()) {
                    $query_args = 'post_type=comic&showposts=1';
                    $comicFrontpage = new WP_Query();
                    $comicFrontpage->query($query_args);
                    $comicFrontpage->the_post();
                    the_tags('Tags: ', ', ', '<br />');
                }

                echo '</article>';
            }
        }
        }
        ?>



