<?php
/**
 * Gridster Theme Customizer
 *
 * @package Gridster
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function gridster_customize_register( $wp_customize ) {
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
}
add_action( 'customize_register', 'gridster_customize_register' );

/**
 * Custom Background
 */
add_theme_support( 'custom-background' );
	
/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function themefurnace_customize_preview_js() {
	wp_enqueue_script( 'themefurnace_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'themefurnace_customize_preview_js' );

function themefurnace_customizer( $wp_customize ) {
    $wp_customize->add_section( 'themefurnacefooter', array(
        'title' => 'Footer', // The title of section
		'priority'    => 50,
        'description' => 'Footer Text', // The description of section
    ) );
 
  $wp_customize->add_setting( 'themefurnacefooter_footer_text', array(
    'default' => 'Hello world',
	'sanitize_callback' => 'sanitize_text_field',
    // Let everything else default
) );
$wp_customize->add_control( 'themefurnacefooter_footer_text', array(
    // wptuts_welcome_text is a id of setting that this control handles
    'label' => 'Footer Text',
    // 'type' =>, // Default is "text", define the content type of setting rendering.
    'section' => 'themefurnacefooter', // id of section to which the setting belongs
    // Let everything else default
) );


$wp_customize->add_section( 'themefurnace_logo_section' , array(
    'title'       => __( 'Logo', 'gridster-lite' ),
    'priority'    => 30,
    'description' => 'Upload a logo to replace the default site name and description in the header',
) );



$wp_customize->add_setting( 'themefurnace_logo', array(
		'default'           => get_template_directory_uri() . '/img/logo.png',
		'sanitize_callback' => 'esc_url_raw',
	) );

$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'themefurnace_logo', array(
    'label'    => __( 'Logo', 'gridster-lite' ),
    'section'  => 'themefurnace_logo_section',
    'settings' => 'themefurnace_logo',
	'sanitize_callback' => 'esc_url_raw',
) ) );

}
add_action( 'customize_register', 'themefurnace_customizer', 11 );

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function gridster_customize_preview_js() {
	wp_enqueue_script( 'gridster_customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20130508', true );
}
add_action( 'customize_preview_init', 'gridster_customize_preview_js' );