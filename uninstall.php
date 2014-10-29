<?php
/**
 * Plugin Uninstallation
 */

// Abort if not called from WordPress
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( QUIETLY_WP_SLUG_OPTIONS );