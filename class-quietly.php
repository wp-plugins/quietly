<?php
/**
 * Quietly Class
 * Where everything is put into action.
 * @package Quietly
 */

require_once( 'class-quietly-options.php' );

class Quietly {

	/**
	 * Instance of this class.
	 * @var    object
	 */
	protected static $instance = null;

	/**
	 * Excerpt output flag.
	 * @var    boolean
	 */
	protected static $is_excerpt = false;

	/**
	 * Embed lists in a post.
	 * @var    Array
	 */
	private $embeds = array();

	/**
	 * Initializes the plugin.
	 */
	private function __construct() {
		if ( is_admin() ) {
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'admin_notices', array( $this, 'options_notices' ) );
			add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
			add_filter( 'plugin_action_links_' . QUIETLY_WP_PATH_BASENAME, array( $this, 'add_plugin_action_link' ));
		}
		wp_embed_register_handler( QUIETLY_WP_SLUG, QUIETLY_WP_EMBED_REGEX, array( $this, 'embed_register_handler' ) );
		wp_oembed_add_provider( QUIETLY_WP_EMBED_REGEX, QUIETLY_WP_URL_OEMBED, true );
		add_filter( 'get_the_excerpt', array( $this, 'flag_excerpt' ), 0 );
		add_filter( 'get_the_excerpt', array( $this, 'unflag_excerpt' ), 99 );
	}

	/**
	 * Return an instance of this class.
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
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		add_option(  QUIETLY_WP_SLUG . '_admin_activation_notice', 'true' );
		QuietlyOptions::create_options();
	}

	/**
	 * Fired when the plugin is deactivated.
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		delete_option( QUIETLY_WP_SLUG . '_admin_activation_notice' );
	}

	/**
	 * Loads the plugin text domain for translation.
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
	 * Initializes the admin.
	 */
	public function admin_init() {
		QuietlyOptions::get_instance();
	}

	/**
	 * Enqueues admin scripts.
	 */
	public function admin_enqueue_scripts() {
		if ( 'plugins' === get_current_screen()->id ||
			'plugins_page_' . QUIETLY_WP_SLUG === get_current_screen()->id ) {
			wp_register_style( QUIETLY_WP_SLUG . '-admin', plugins_url( 'admin/style.css', __FILE__ ), array(), QUIETLY_WP_VERSION );
			wp_enqueue_style( QUIETLY_WP_SLUG . '-admin' );
		}
	}

	/**
	 * Add admin notices.
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
	 * Add 'Settings' link in plugins page.
	 * @param     string    $links
	 * @param     string    $file
	 * @return    array
	 */
	public function add_plugin_action_link( $links ) {
		$settings_link = '<a href="' . admin_url( 'plugins.php?page=' . QUIETLY_WP_SLUG ) . '">' . /* TRANSLATORS: plugin */ __( 'Settings', QUIETLY_WP_SLUG ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

	/**
	 * Register the administration menu.
	 */
	public function add_plugin_admin_menu() {
		$this->plugin_screen_hook_suffix = add_plugins_page(
			/* TRANSLATORS: admin */ __( 'Quietly Plugin', QUIETLY_WP_SLUG ),
			/* TRANSLATORS: admin */ __( 'Quietly', QUIETLY_WP_SLUG ),
			'read',
			QUIETLY_WP_SLUG,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page.
	 */
	public function display_plugin_admin_page() {
		$this->display_view( 'admin/options.php' );
	}

	/**
	 * Register options page notices.
	 */
	public function options_notices() {
		settings_errors( QUIETLY_WP_OPTIONS_SLUG );
	}

	/**
	 * Registers the Quietly embed handler for share url.
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
			if ( QuietlyOptions::get_option( 'show_description_in_excerpts' ) === true) {
				// Remember list description to be rendered in excerpt
				require_once( ABSPATH . WPINC . '/class-oembed.php' );
				$oembed = _wp_oembed_get_object();
				$oembed = $oembed->fetch( QUIETLY_WP_URL_OEMBED, $url );
				if (is_object($oembed) && property_exists($oembed, 'description')) {
					array_push( $this->embeds, '<p>' . $oembed->description . '</p>' );
				}
			} else {
				$embed = '';
			}
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
		$this->embeds = array();
		return apply_filters( 'wp_trim_excerpt', $text, $text );
	}

	/**
	 * Unflags the excerpt output status.
	 * @param    string     $text    The excerpt text.
	 */
	public function unflag_excerpt( $text = '' ) {
		Quietly::$is_excerpt = false;
		// Show list description if excerpt is empty
		if ( $text === '') {
			foreach ( $this->embeds as $embed) {
				$text .= $embed;
			}
			$this->embeds = array();
		}
		return apply_filters( 'wp_trim_excerpt', $text, $text );
	}

}