<?php
/**
 * Quietly Class
 * Where everything is put into action.
 * @package Quietly
 */

require_once('quietly-config.php');

class Quietly {

	/**
	 * Instance of this class.
	 * @since    1.0.0
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Excerpt output flag.
	 * @since     1.0.0
	 * @var       boolean
	 */
	protected static $is_excerpt = false;

	/**
	 * Initializes the plugin.
	 * @since     1.0.0
	 */
	private function __construct() {
		if ( is_admin() ) {
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}
		wp_embed_register_handler( QUIETLY_WP_SLUG, QUIETLY_WP_EMBED_REGEX, array( $this, 'embed_register_handler' ) );
		wp_oembed_add_provider( QUIETLY_WP_EMBED_REGEX, QUIETLY_WP_URL_OEMBED, true );
		add_filter( 'get_the_excerpt', array( $this, 'flag_excerpt' ), 9 );
		add_filter( 'get_the_excerpt', array( $this, 'unflag_excerpt' ), 11 );
	}

	/**
	 * Return an instance of this class.
	 * @since     1.0.0
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 * @since    1.0.0
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		add_option(  QUIETLY_WP_SLUG . '_admin_activation_notice', 'true' );
	}

	/**
	 * Fired when the plugin is deactivated.
	 * @since    1.0.0
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		delete_option( QUIETLY_WP_SLUG . '_admin_activation_notice' );
	}

	/**
	 * Loads the plugin text domain for translation.
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {
		$domain = QUIETLY_WP_SLUG;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Outputs the content of a view file.
	 * @param    string    $view_file    The view file.
	 */
	public static function display_view( $view_file ) {
		ob_start();
		include_once( $view_file );
		echo ob_get_clean();
	}

	/**
	 * Enqueues admin scripts.
	 * @since    1.0.0
	 */
	public function admin_enqueue_scripts() {
		if ( 'plugins' === get_current_screen()->id ) {
			wp_register_style( QUIETLY_WP_SLUG . '-admin', plugins_url( 'admin/style.css', __FILE__ ), array(), QUIETLY_WP_VERSION );
			wp_enqueue_style( QUIETLY_WP_SLUG . '-admin' );
		}
	}

	/**
	 * Add admin notices.
	 * @since    1.0.0
	 */
	public function admin_notices() {
		// Show first-time activation message
		if ( 'plugins' === get_current_screen()->id &&
			current_user_can( 'install_plugins' ) &&
			'true' === get_option( QUIETLY_WP_SLUG . '_admin_activation_notice' ) ) {
			$this->display_view( 'admin/activation-notice.php' );
			delete_option( QUIETLY_WP_SLUG . '_admin_activation_notice' );
		}
	}

	/**
	 * Registers the Quietly embed handler for share url.
	 * @since    1.0.0
	 */
	public function embed_register_handler( $matches, $attr, $url, $rawattr ) {
		$embed = '';
		$url = $matches[0];
		if ( strpos( $url, 'maxheight=' ) === false ) {
			// Override WordPress default maxheight
			$attr['height'] = (int) ((int) $attr['width'] * 0.75);
		}
		// Hide the list markups when showing in an excerpt
		if ( true === Quietly::$is_excerpt ) {
			$embed = '';
		} else {
			$embed = wp_oembed_get( $url, $attr );
		}
		return apply_filters( 'embed_quietly', $embed, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Keeps track of whether an imminent embed output is for a post excerpt.
	 * @param    string     $text    The excerpt text.
	 */
	public function flag_excerpt( $text = '' ) {
		Quietly::$is_excerpt = true;
		return apply_filters( 'wp_trim_excerpt', $text, $text );
	}

	/**
	 * Unflags the excerpt output status.
	 * @param    string     $text    The excerpt text.
	 */
	public function unflag_excerpt( $text = '' ) {
		Quietly::$is_excerpt = false;
		return apply_filters( 'wp_trim_excerpt', $text, $text );
	}

}