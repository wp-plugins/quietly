<?php
/**
 * Quietly Admin Class
 * Handles the admin views.
 * @package Quietly
 */

class QuietlyAdmin {

	/**
	 * Whitelisted screen hooks.
	 * @var array
	 */
	protected $admin_screens = array(
		'plugins',
		'post',
		'post-new',
		'page',
		'page-new'
	);

	/**
	 * Initializes the object.
	 */
	public function __construct() {
		array_push( $this->admin_screens, 'toplevel_page_' . QUIETLY_WP_SLUG );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_notices', array( $this, 'add_activation_notice' ) );
		add_filter( 'plugin_action_links_' . QUIETLY_WP_PATH_BASENAME, array( $this, 'add_plugin_action_link' ));
	}

	/**
	 * Enqueues admin styles and scripts.
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen()->id;
		wp_register_style( QUIETLY_WP_SLUG . '-admin-menu', QUIETLY_WP_PATH_ABS . 'css/quietly-admin-menu.css', array(), QUIETLY_WP_VERSION );
		wp_enqueue_style( QUIETLY_WP_SLUG . '-admin-menu' );
		if ( in_array( $screen, $this->admin_screens ) ) {
			wp_register_style( QUIETLY_WP_SLUG . '-admin', QUIETLY_WP_PATH_ABS . 'css/quietly-admin.css', array(), QUIETLY_WP_VERSION );
			wp_enqueue_style( QUIETLY_WP_SLUG . '-admin' );
		}
	}

	/**
	 * Add admin notices.
	 */
	public function add_activation_notice() {
		// Show first-time activation message
		if ( 'plugins' === get_current_screen()->id &&
			current_user_can( 'install_plugins' ) &&
			'true' === get_option( QUIETLY_WP_SLUG . '_admin_activation_notice' ) ) {
			Quietly::display_view( 'admin/views/quietly-activation-notice.php' );
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
		$settings_link = '<a href="' . admin_url( 'admin.php?page=' . QUIETLY_WP_SLUG ) . '">' . /* translators: plugin */ __( 'Settings', QUIETLY_WP_SLUG ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

}