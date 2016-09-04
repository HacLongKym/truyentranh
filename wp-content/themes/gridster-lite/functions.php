<?php
/**
 * Gridster functions and definitions
 *
 * @package Gridster
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 990; /* pixels */

if ( ! function_exists( 'gridster_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
function gridster_setup() {

	/**
	 * Make theme available for translation
	 * Translations can be filed in the /languages/ directory
	 * If you're building a theme based on Gridster, use a find and replace
	 * to change 'gridster-lite' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'gridster-lite', get_template_directory() . '/languages' );

	/**
	 * Add default posts and comments RSS feed links to head
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable support for Post Thumbnails on posts and pages
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	//add_theme_support( 'post-thumbnails' );
	
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'post-full');
	add_image_size( 'post-page', 990, 525, true );
	add_image_size( 'post-thumb', 225, 158, true );
	
	/* Add support for Title Tag */    
	add_theme_support( 'title-tag' );

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'gridster-lite' ),
	) );
}
endif; // gridster_setup
add_action( 'after_setup_theme', 'gridster_setup' );

/**
 * Setup the WordPress core custom background feature.
 *
 * Use add_theme_support to register support for WordPress 3.4+
 * as well as provide backward compatibility for WordPress 3.3
 * using feature detection of wp_get_theme() which was introduced
 * in WordPress 3.4.
 *
 * @todo Remove the 3.3 support when WordPress 3.6 is released.
 *
 * Hooks into the after_setup_theme action.
 */
function gridster_register_custom_background() {
	$args = array(
		'default-color' => 'ffffff',
		'default-image' => '',
	);

	$args = apply_filters( 'gridster_custom_background_args', $args );

	if ( function_exists( 'wp_get_theme' ) ) {
		add_theme_support( 'custom-background', $args );
	} else {
		define( 'BACKGROUND_COLOR', $args['default-color'] );
		if ( ! empty( $args['default-image'] ) )
			define( 'BACKGROUND_IMAGE', $args['default-image'] );
		add_theme_support( 'custom-background');
	}
}
add_action( 'after_setup_theme', 'gridster_register_custom_background' );

/**
 * Register widgetized area and update sidebar with default widgets
 */
function gridster_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'gridster-lite' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<div class="sidebarwidget">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="sidetitle">',
		'after_title'   => '</h3>',
	) );
	
	/*
	* Tạo sidebar cho theme
	*/
	$sidebar = array(
	   'name' => __('Main Sidebar', 'LuanDT'),
	   'id' => 'main-sidebar-luandt',
	   'description' => 'Main sidebar by luandt',
	   'class' => 'main-sidebar-luandt',
	   'before_widget' => '<div id="luandt-widget">',
	   'after_widget'  => '</div>',
	   'before_title' => '<h3 class="luandt-title">',
	   'after_title' => '</h3>'
	);
	register_sidebar( $sidebar );
}
add_action( 'widgets_init', 'gridster_widgets_init' );

/**
 * Enqueue scripts and styles
 */
function gridster_scripts() {
	wp_enqueue_style( 'gridster-style', get_stylesheet_uri() );
	wp_enqueue_script( 'gridster-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'gridster-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );
	
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	
// Google Font
	wp_enqueue_style( 'open-sans-condensed', 'http://fonts.googleapis.com/css?family=Open+Sans+Condensed:300', 'style' );

	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'gridster-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
	}
}
add_action( 'wp_enqueue_scripts', 'gridster_scripts' );


// Style the Tag Cloud
function custom_tag_cloud_widget($args) {
	$args['largest'] = 12; //largest tag
	$args['smallest'] = 12; //smallest tag
	$args['unit'] = 'px'; //tag font unit
	$args['number'] = '8'; //number of tags
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'custom_tag_cloud_widget' );

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
