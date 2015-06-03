<?php
/**
 * Quietly Content Insert Class
 * @package Quietly
 */

class QuietlyContentInsert {

	/**
	 * Whitelisted screen hooks.
	 * @var array
	 */
	protected $post_screens = array(
		'post',
		'post-new',
		'page',
		'page-new'
	);

	/**
	 * Initializes the object.
	 */
	public function __construct() {
		// Insert views and assets
		foreach ($this->post_screens as $screen) {
			add_action( 'admin_footer-' . $screen . '.php', array( $this, 'add_insert_content_modal' ) );
		}
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'media_buttons_context', array( $this, 'add_insert_button' ) );
		// Register TinyMCE plugin
		// add_filter( 'mce_external_plugins', array( $this, 'register_tinymce_plugins' ) );
		// add_filter( 'mce_buttons', array( $this, 'add_tinymce_buttons' ) );
	}

	/**
	 * Adds the insert content modal view.
	 */
	public function add_insert_content_modal() {
		Quietly::display_view( 'admin/views/quietly-content-insert-modal.php' );
	}

	/**
	 * Enqueues content insert scripts.
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen()->base;
		$api_token = QuietlyOptions::get_option( 'api_token' );
		if ( ! empty( $api_token ) ) {
			$has_token = 'true';
		} else {
			$has_token = 'false';
		}
		if ( $screen === 'post' ) {
			wp_enqueue_script( QUIETLY_WP_SLUG . '-admin-angular', QUIETLY_WP_PATH_ABS . 'admin/js/quietly-angular.js', array(), QUIETLY_WP_VERSION, true );
			wp_enqueue_script( QUIETLY_WP_SLUG . '-admin-api', QUIETLY_WP_PATH_ABS . 'admin/js/quietly-api.js', array(), QUIETLY_WP_VERSION, true );
			wp_enqueue_script( QUIETLY_WP_SLUG . '-admin-content-insert', QUIETLY_WP_PATH_ABS . 'admin/js/quietly-content-insert.js', array(), QUIETLY_WP_VERSION, true );
			wp_localize_script( QUIETLY_WP_SLUG . '-admin-angular', QUIETLY_WP_SLUG . 'WP',
				array(
					'pluginUrl' => QUIETLY_WP_PATH_ABS,
					'quietlyUrl' => QUIETLY_WP_URL,
					'apiUrl' => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( QUIETLY_WP_SLUG . '_api_call' ),
					'hasToken' => $has_token,
					'apiToken' => $api_token,
					'debug' => QUIETLY_WP_DEBUG ? true : false,
					'i18n' => array(
						'insertContentConfirmStory' => /* translators: insert content confirmation */ _( 'Inserting this will replace your post title and post content. Are you sure you want to insert?' )
					)
				)
			);
		}
	}

	/**
	 * Adds an insert content button above the editor.
	 * @param    string    $context    The context HTML.
	 */
	public function add_insert_button( $context ) {
		$context .= '<a href id="quietly-wp-btn-insert-content" class="button" title="' . /* translators: post editor insert button title */ esc_attr__( 'Add Quietly Content', QUIETLY_WP_SLUG ) . '"><img src="' . QUIETLY_WP_PATH_ABS . 'images/btn-insert.png" class="quietly-wp-admin__btn-add" />' . /* translators: post editor insert button label */ __( 'Add Quietly Content', QUIETLY_WP_SLUG ) . '</a>';
		return $context;
	}

	/**
	 * Registers custom TinyMCE plugins.
	 * @param     array    $plugin_array    The default array of plugins.
	 * @return    type     The modified array of plugins.
	 */
	public function register_tinymce_plugins( $plugin_array ) {
		$plugin_array[QUIETLY_WP_SLUG] = QUIETLY_WP_PATH_ABS . 'admin/js/quietly-tinymce.js';
		return $plugin_array;
	}

	/**
	 * Adds custom buttons to the TinyMCE toolbar.
	 * @param     array    $buttons    The default array of buttons.
	 * @return    array    The modified array of buttons.
	 * @since 1.0.0
	 */
	public function add_tinymce_buttons( $buttons ) {
		array_push( $buttons, 'separator', 'quietly_insert' );
		return $buttons;
	}

}