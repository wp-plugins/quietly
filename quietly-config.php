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

// Embed url regex
define( 'QUIETLY_WP_EMBED_REGEX_URL', '/http:\/\/' . QUIETLY_WP_URL . '\/list\/([\d]+)-?([\w\-]+)?\??([\d\w\-=&]*)?\/?$/i' );
define( 'QUIETLY_WP_EMBED_REGEX_SHARE', '/http:\/\/' . QUIETLY_WP_URL . '\/list\/share\/([\d\w]+)-?([\w\-]+)?\??([\d\w\-=&]*)?\/?$/i' );

// Embed loader script tag template
define( 'QUIETLY_WP_EMBED_SCRIPT_URL', '<script async src="http://' . QUIETLY_WP_URL . '/static/js/embed-load.js?id=%1$s&%2$s&type=list_id"></script>' );
define( 'QUIETLY_WP_EMBED_SCRIPT_SHARE', '<script async src="http://' . QUIETLY_WP_URL . '/static/js/embed-load.js?id=%1$s&%2$s"></script>' );