<?php
/**
 * Quietly Plugin Configurations
 */

define( 'QUIETLY_WP_VERSION', '1.0.0' );
define( 'QUIETLY_WP_SLUG', 'quietly' );

// Paths
define( 'QUIETLY_WP_PATH_ABS', plugin_dir_url(__FILE__) );
define( 'QUIETLY_WP_PATH_DIR', plugin_dir_path( __FILE__ ) );
define( 'QUIETLY_WP_PATH_BASENAME', plugin_basename(  __FILE__ ) );
define( 'QUIETLY_WP_URL', 'beta.quiet.ly');
// define( 'QUIETLY_WP_URL', 'staging.qlydev.com');
define( 'QUIETLY_WP_URL_OEMBED','http://' . QUIETLY_WP_URL . '/oembed' );
define( 'QUIETLY_WP_EMBED_REGEX', '#^http:\/\/' . QUIETLY_WP_URL . '\/list\/.*#i' );