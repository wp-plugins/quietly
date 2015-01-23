<?php
/**
 * Plugin Name: Quietly
 * Plugin URI:  http://wordpress.org/plugins/quietly
 * Description: The Quietly WP plug-in allows you to quickly and easily embed your Quietly content (e.g. beautiful slideshows, maps, etc) into your WordPress website.
 * Version:     2.0.1
 * Author:      Quietly Media, Inc.
 * Author URI:  http://quiet.ly
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Abort if called directly
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Plugin constants
define( 'QUIETLY_WP_VERSION', '2.0.1' );
define( 'QUIETLY_WP_SLUG', 'quietly' );
define( 'QUIETLY_WP_SLUG_OPTIONS', QUIETLY_WP_SLUG . '_options' );
define( 'QUIETLY_WP_URL_DOMAIN', 'quiet.ly' );
define( 'QUIETLY_WP_URL', 'www.' . QUIETLY_WP_URL_DOMAIN );
define( 'QUIETLY_WP_PATH_ABS', plugin_dir_url(__FILE__) );
define( 'QUIETLY_WP_PATH_DIR', plugin_dir_path( __FILE__ ) );
define( 'QUIETLY_WP_PATH_BASENAME', plugin_basename( __FILE__ ) );
define( 'QUIETLY_WP_DEBUG', false );

// Load plugin class
require_once( 'includes/class-quietly.php' );
require_once( 'includes/class-quietly-options.php' );
require_once( 'includes/class-quietly-embed.php' );
require_once( 'includes/class-quietly-api.php' );
require_once( 'admin/class-quietly-admin.php' );
require_once( 'admin/class-quietly-settings.php' );
require_once( 'admin/class-quietly-list-insert.php' );

register_activation_hook( __FILE__, array( 'Quietly', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Quietly', 'deactivate' ) );

// Run the plugin
function run_quietly() {
	$plugin = new Quietly();
}
run_quietly();