<?php
/**
 * Loads the WordPress environment and template.
 *
 * @package WordPress
 */

if ( !isset($wp_did_header) ) {

	$wp_did_header = true;

	// Load the WordPress library.
	require_once( dirname(__FILE__) . '/wp-load.php' );

	//LuanDT add to search
	if (isset($_REQUEST['s']) || isset($_REQUEST['S'])) {
		wp(array('post_type'=>'post'));		
	} else {
		// Set up the WordPress query.
		wp();		
	}
	

	// Load the theme template.
	require_once( ABSPATH . WPINC . '/template-loader.php' );

}
