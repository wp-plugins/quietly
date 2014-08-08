<?php
/**
 * Plugin Name: Quietly
 * Plugin URI:  http://wordpress.org/plugins/quietly
 * Description: The Quietly WordPress plug-in allows you to quickly embed beautiful slideshows into your content.
 * Version:     1.2.0
 * Author:      Quietly Media, Inc.
 * Author URI:  http://quiet.ly
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Abort if called directly
if ( ! defined( 'WPINC' ) ) die;

// Plugin constants
define( 'QUIETLY_WP_VERSION', '1.2.0' );
define( 'QUIETLY_WP_SLUG', 'quietly' );
define( 'QUIETLY_WP_OPTIONS_SLUG', QUIETLY_WP_SLUG . '_options' );
define( 'QUIETLY_WP_PATH_ABS', plugin_dir_url(__FILE__) );
define( 'QUIETLY_WP_PATH_DIR', plugin_dir_path( __FILE__ ) );
define( 'QUIETLY_WP_PATH_BASENAME', plugin_basename( __FILE__ ) );
define( 'QUIETLY_WP_URL', 'beta.quiet.ly');
define( 'QUIETLY_WP_URL_OEMBED','http://' . QUIETLY_WP_URL . '/oembed' );
define( 'QUIETLY_WP_EMBED_REGEX', '#http://' . QUIETLY_WP_URL . '/list/.*#i' );

// Load plugin class
require_once( 'class-quietly.php' );

register_activation_hook( __FILE__, array( 'Quietly', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Quietly', 'deactivate' ) );

// Create plugin instance
Quietly::get_instance();