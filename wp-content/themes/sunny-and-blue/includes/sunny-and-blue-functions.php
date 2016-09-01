<?php
/**
 * Sunny and Blue functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @link https://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * {@link https://codex.wordpress.org/Plugin_API}
 *
 * @package WordPress
 * @subpackage sunny-and-blue
 */

 require_once trailingslashit( get_template_directory() ) . 'includes/customizer.php';
/**
 * Set the content width based on the theme's design and stylesheet.
 */
if (!isset($content_width)) {
    $content_width = 1140; /* pixels */
}
/**
 * Enqueue scripts.
 */
function sunny_and_blue_scripts()
{

    // Add Bootstrap default JS
    wp_enqueue_script('bootstrap', get_template_directory_uri() . '/bootstrap/js/bootstrap.min.js', array('jquery'));

// Add ScrollSpeed JS
    wp_enqueue_script('jquery-scrollspeed', get_template_directory_uri() . '/bootstrap/js/jQuery.scrollSpeed.js', array('jquery'));

    // Threaded comments
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}

add_action('wp_enqueue_scripts', 'sunny_and_blue_scripts');

if ( ! function_exists( 'sunny_and_blue_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function sunny_and_blue_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 */
	  load_theme_textdomain( 'sunny-and-blue', get_template_directory_uri().'/languages' );
	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');
	// Let WordPress manage the document title.
add_theme_support('title-tag');
/* Featured Image support */
add_theme_support('post-thumbnails');
/**
 * Setup custom background.
 */
$args = array(
    'default-color' => '000000',
    'default-image' => '%1$s/img/background.png',
);

add_theme_support('custom-background', $args);
add_theme_support( 'custom-logo', array(
		'height'      => 60,
		'width'       => 240,
		'flex-height' => false,
	) );

    add_theme_support('custom-header', apply_filters('sunny_and_blue_custom_header_args', array(
        'default-text-color' => 'FFFFFF',
		'header-text' => true,
        'wp-head-callback' => 'sunny_and_blue_header_style',
        'admin-head-callback' => 'sunny_and_blue_admin_header_style',
        'admin-preview-callback' => 'sunny_and_blue_admin_header_image',
    )));
}
endif;
add_action( 'after_setup_theme', 'sunny_and_blue_setup' );

function sunny_and_blue_new_excerpt_length($length)
{
    return 160;
}

function sunny_and_blue_list_pages($param)
{

    $pages = get_pages($param);
    foreach ($pages as $page) {
        $li = '<li';
        if (is_page($page->ID)) $li .= ' class="current_page_item"';
        $li .= '><a href="' . esc_url(get_page_link($page->ID)) . '" title="';
        $li .= esc_attr($page->post_title);
        $li .= '">';
        $li .= $page->post_title;
        $li .= '</a></li>';
        echo $li;
    }
}

/* Pagination Code */
function sunny_and_blue_wpex_pagination()
{
    $paged_page_nav = wp_link_pages(
        array(
            'before' => '',
            'after' => '',
            'link_before' => '<span>',
            'link_after' => '</span>',
            'echo' => false
        )
    );
}



/*Blog Excerpt */
add_filter('excerpt_length', 'sunny_and_blue_new_excerpt_length');

/*Sidebar Widget */
add_action('widgets_init', 'sunny_and_blue_theme_slug_widgets_init');
function sunny_and_blue_theme_slug_widgets_init()
{
    register_sidebar(array(
        'name' => __('Right Sidebar', 'sunny-and-blue'),
        'id' => 'sidebar-1',
        'description' => __('Widgets in this area will be shown on the right column below the comic.', 'sunny-and-blue'),
        'before_widget' => '',
        'after_widget' => '<hr/>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ));
	register_sidebar( array(
		'name'          => 'Footer Left Column',
		'id'            => 'footer-left-1',
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => 'Footer Right Column',
		'id'            => 'footer-right-1',
		'description' => __('Widgets in this area will be shown in the footer\'s right column.', 'sunny-and-blue'),
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	) );

}


/**
 * Setup editor styling.
 */
function sunny_and_blue_theme_add_editor_styles()
{
    $font_url = str_replace(',', '%2C', '//fonts.googleapis.com/css?family=Roboto:400,300,500,700');
    add_editor_style($font_url);
}

add_action('after_setup_theme', 'sunny_and_blue_theme_add_editor_styles');
/**
 * Setup the WordPress core custom header feature.
 *
 * @uses sunny_and_blue_header_style()
 * @uses sunny_and_blue_admin_header_style()
 * @uses sunny_and_blue_admin_header_image()
 *
 */


if (!function_exists('sunny_and_blue_header_style')) :
    /**
     * Styles the header image and text displayed on the blog
     */
    function sunny_and_blue_header_style()
    {
        $header_text_color = get_header_textcolor();

        // If no custom options for text are set, exit
        // get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value
        if (HEADER_TEXTCOLOR == $header_text_color) {
            return;
        }

        // If we have custom styles.
        ?>
        <style type="text/css">
            <?php
                // Has the text been hidden?
                if ( 'blank' == $header_text_color ) :
            ?>
            .site-title,
            .site-description {
                position: absolute;
                clip: rect(1px, 1px, 1px, 1px);

            }

            <?php
                // If the user has set a custom color for the text use that
                else :
            ?>
            .navbar > .container .navbar-brand{
				margin-top: 10px;
                color: #<?php echo $header_text_color; ?>;
            }

            <?php endif; ?>
        </style>
    <?php
    }
endif; // sunny_and_blue_header_style

if (!function_exists('sunny_and_blue_admin_header_style')) :
    /**
     * Styles the header image displayed on the Appearance > Header admin panel.
     *
     * @see sunny_and_blue_custom_header_setup().
     */
    function sunny_and_blue_admin_header_style()
    {
        ?>
        <style type="text/css">
            .appearance_page_custom-header #headimg {
                border: none;
            }

            #headimg h1,
            #desc {
            }

            #headimg h1 {
            }

            #headimg h1 a {
            }

            #desc {
            }

            #headimg img {
            }
        </style>
    <?php
    }
endif; // sunny_and_blue_admin_header_style

if (!function_exists('sunny_and_blue_admin_header_image')) :
    /**
     * Custom header image markup displayed on the Appearance > Header admin panel.
     */
    function sunny_and_blue_admin_header_image()
    {
        $style = sprintf(' style="color:#%s;"', get_header_textcolor());
        ?>
        <div id="headimg">
            <h1 class="displaying-header-text"><a id="name"<?php echo $style; ?> onclick="return false;"
                                                  href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
            </h1>

            <div class="displaying-header-text" id="desc"<?php echo $style; ?>><?php bloginfo('description'); ?></div>
            <?php if (get_header_image()) : ?>
                <img src="<?php header_image(); ?>" alt="">
            <?php endif; ?>
        </div>
    <?php
    }
endif; // sunny_and_blue_admin_header_image
/* Navigation Menu */
function sunny_and_blue_menu() {
    register_nav_menu( 'primary', 'Main Navigation Menu' );
}
add_action( 'init', 'sunny_and_blue_menu' );
/* Sub Menu Handling */
// Register Bootstrap Navigation Walker
require_once trailingslashit( get_template_directory() ) . 'includes/wp_bootstrap_navwalker.php';


// load Sunny and Blue default css into the website's front-end
function sunny_and_blue_enqueue_style() {
	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/bootstrap/css/bootstrap.min.css');
	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/bootstrap/css/font-awesome.min.css');
	wp_enqueue_style( 'animate', get_template_directory_uri() . '/bootstrap/css/animate.css');
	wp_enqueue_style( 'sunny-and-blue-google-fonts', '//fonts.googleapis.com/css?family=Roboto:400,300,500,700');
    wp_enqueue_style( 'sunny-and-blue-style', get_stylesheet_uri() );
}
add_action( 'wp_enqueue_scripts', 'sunny_and_blue_enqueue_style' );
add_filter('body_class', 'sunny_and_blue_body_class');
function sunny_and_blue_body_class($classes){
    if(is_user_logged_in()){
        $classes[] = 'body-logged-in';
    } else{
        $classes[] = 'body-logged-out';
    }
    return $classes;
}

/* takes user input from the customizer and outputs linked social media icons */
function sunny_and_blue_social_media_icons() {
    $social_sites = sunny_and_blue_customizer_social_media_array();
    /* any inputs that aren't empty are stored in $active_sites array */
    foreach($social_sites as $social_site) {
        if( strlen( get_theme_mod( $social_site ) ) > 0 ) {
            $active_sites[] = $social_site;
        }
    }
    /* for each active social site, add it as a list item */
        if ( ! empty( $active_sites ) ) {
            echo "<h3>" . __('Social Media', 'sunny-and-blue') . "</h3><ul class='social-share text-center'>";
            foreach ( $active_sites as $active_site ) {
	            /* setup the class */
		        $class = 'fa fa-' . $active_site;
                if ( $active_site == 'email' ) {
                    ?>
                         <li><a class="email" target="_blank" href="mailto:<?php echo antispambot(is_email( get_theme_mod( $active_site ) ) ); ?>">
                            <i class="fa fa-envelope" title="<?php _e('email icon', 'sunny-and-blue'); ?>"></i>
                        </a></li>
                 <?php } else { ?>
                         <li><a class="<?php echo $active_site; ?>" target="_blank" href="<?php echo esc_url( get_theme_mod( $active_site) ); ?>">
                            <i class="<?php echo esc_attr( $class ); ?>" title="<?php printf(esc_attr__('%s icon', 'sunny-and-blue'), $active_site ); ?>"></i>
                        </a></li>
                 <?php
                }
            }
            echo "</ul>";
        }
		else{
			if ( current_user_can( 'edit_theme_options' ) ){
			?>
<div class="pre-widget">
		<h3><?php _e('Widgetized Social Media Column', 'sunny-and-blue'); ?></h3>
		<p><?php _e('This panel is active and ready for your social media links via the "Social Media Icons" menu.', 'sunny-and-blue'); ?></p>

	</div>
<?php
			}
			else{
				the_widget( 'WP_Widget_Search' );
			}

			}

}
?>