<?php
/**
 * Quietly Class
 * Where everything is put into action.
 * @package Quietly
 */

class Quietly {

	/**
	 * Initializes the plugin.
	 */
	public function __construct() {
		QuietlyOptions::create_options();
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		if ( is_admin() ) {
			$admin = new QuietlyAdmin();
			$settings = new QuietlySettings();
			$content_insert = new QuietlyContentInsert();
			$api = new QuietlyAPI();
		}
		$embed = new QuietlyEmbed();
		$analytics = new QuietlyAnalytics();
	}

	/**
	 * Fired when the plugin is activated.
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		add_option(  QUIETLY_WP_SLUG . '_admin_activation_notice', 'true' );
	}

	/**
	 * Fired when the plugin is deactivated.
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		delete_option( QUIETLY_WP_SLUG . '_admin_activation_notice' );
	}

	/**
	 * Outputs the content of a view file.
	 * @param    string    $view_file    The view file.
	 */
	public static function display_view( $view_file ) {
		ob_start();
		include_once( QUIETLY_WP_PATH_DIR . $view_file );
		echo ob_get_clean();
	}

	/**
	 * Loads the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {
		$domain = QUIETLY_WP_SLUG;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, QUIETLY_WP_PATH_DIR . '/languages/' );
	}

}