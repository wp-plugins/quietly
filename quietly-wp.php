<?php
/**
 * Plugin Name: Quietly
 * Plugin URI:  http://wordpress.org/plugins/quietly
 * Description: The Quietly WordPress plug-in allows you to quickly embed beautiful slideshows into your content.
 * Version:     1.1.0
 * Author:      Quietly Media, Inc.
 * Author URI:  http://quiet.ly
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// Abort if called directly
if ( ! defined( 'WPINC' ) ) die;

// Load plugin class
require_once( 'class-quietly.php' );

register_activation_hook( __FILE__, array( 'Quietly', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Quietly', 'deactivate' ) );

// Create plugin instance
Quietly::get_instance();