<?php
/**
 * @package THE_POST_GRID
 * @version 1.4
 */

/**
 * Plugin Name: The Post Grid
 * Plugin URI: http://demo.radiustheme.com/wordpress/plugins/the-post-grid/
 * Description: Fast & Easy way to display WordPress post in Grid, List & Isotope view ( filter by category, tag, author..)  without a single line of coding.
 * Author: RadiusTheme
 * Version: 1.4
 * Text Domain: the-post-grid
 * Domain Path: /languages
 * Author URI: https://radiustheme.com/
*/
if ( ! defined( 'ABSPATH' ) )  exit;

define('RT_THE_POST_GRID_PLUGIN_PATH', dirname(__FILE__));
define('RT_THE_POST_GRID_PLUGIN_ACTIVE_FILE_NAME', __FILE__);
define('RT_THE_POST_GRID_PLUGIN_URL', plugins_url('', __FILE__));
define('RT_THE_POST_GRID_PLUGIN_SLUG', basename( dirname( __FILE__ ) ));
define('RT_THE_POST_GRID_LANGUAGE_PATH', dirname( plugin_basename( __FILE__ ) ) . '/languages');

require ('lib/init.php');


add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'rt_tpg_marketing');

function rt_tpg_marketing($links){
    
    $links[] = '<a target="_blank" href="'. esc_url( 'http://demo.radiustheme.com/wordpress/plugins/the-post-grid/' ) .'">Demo</a>';
    $links[] = '<a target="_blank" href="'. esc_url( 'https://www.radiustheme.com/how-to-setup-configure-the-post-grid-free-version-for-wordpress/' ) .'">Documentation</a>';
    $links[] = '<a target="_blank" href="'. esc_url( 'https://www.radiustheme.com/the-post-grid-pro-for-wordpress/' ) .'">Get Pro</a>';
    return $links;
}