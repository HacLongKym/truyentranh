<?php
/**
 * Sunny and Blue functions and definitions
 *
 * Set up customizer functions
 */
function sunny_and_blue_customizer_social_media_array() {
 
	/* store social site names in array */
	$social_sites = array( 'facebook','twitter', 'linkedin', 'instagram','google-plus', 'flickr', 'pinterest', 'youtube', 'tumblr', 'dribbble', 'rss',  'email');
 
	return $social_sites;
}
/* add settings to create various social media text areas. */
add_action('customize_register', 'sunny_and_blue_add_social_sites_customizer');
function sunny_and_blue_add_social_sites_customizer($wp_customize) {
	$wp_customize->add_section('my_social_settings', array(
			'title'    => __('Social Media Icons', 'sunny-and-blue'),
			'priority' => 35,
	) );
	$social_sites = sunny_and_blue_customizer_social_media_array();
	$priority = 5;
	foreach($social_sites as $social_site) {
		$wp_customize->add_setting( "$social_site", array(
				'type'              => 'theme_mod',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw'
		) );
		$wp_customize->add_control($social_site, array(
				'label'    =>$social_site.' '.__("url:", 'sunny-and-blue'),
				'section'  => 'my_social_settings',
				'type'     => 'text',
				'priority' => $priority,
		) );
		$priority = $priority + 5;
	}
}

?>
