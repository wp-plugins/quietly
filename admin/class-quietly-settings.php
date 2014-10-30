<?php
/**
 * Quietly Settings Class
 * For the plugin admin settings page.
 * @package Quietly
 */

class QuietlySettings {

	/**
	 * Settings screen id.
	 * @var string
	 */
	protected $settings_screen = '';

	/**
	 * Initializes the object.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'init_settings' ) );
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Initializes the settings fields.
	 */
	public function init_settings() {

		// Register settings and validation
		register_setting( QUIETLY_WP_SLUG_OPTIONS, QUIETLY_WP_SLUG_OPTIONS, array( $this, 'validate' ) );

		// Add General section
        add_settings_section(
			'quietly_settings_general',
			null,
			null,
			QUIETLY_WP_SLUG
		);

		// Add API token field
		add_settings_field(
			'api_token',
			'Quietly API Token',
			array( $this, 'display_field_api_token' ),
			QUIETLY_WP_SLUG,
			'quietly_settings_general'
		);

		// Add descript in excerpts field
		add_settings_field(
			'show_description_in_excerpts',
			'Post Excerpts',
			array( $this, 'display_field_show_description_in_excerpts' ),
			QUIETLY_WP_SLUG,
			'quietly_settings_general'
		);

	}

	/**
	 * Register the administration menu.
	 */
	public function add_plugin_admin_menu() {
		$this->settings_screen = add_plugins_page(
			'Quietly Plugin',
			'Quietly',
			'edit_plugins',
			QUIETLY_WP_SLUG,
			array( $this, 'display_plugin_admin_page' )
		);
	}

	/**
	 * Render the settings page.
	 */
	public function display_plugin_admin_page() {
		Quietly::display_view( 'admin/views/quietly-settings.php' );
	}

	/**
	 * Register admin notices.
	 */
	public function admin_notices() {
		settings_errors( QUIETLY_WP_SLUG_OPTIONS );
	}

	/**
	 * Enqueues settings page scripts.
	 */
	public function enqueue_scripts() {
		$screen = get_current_screen()->id;
		if ( $this->settings_screen === $screen ) {
			wp_register_script( QUIETLY_WP_SLUG . '-admin-settings', QUIETLY_WP_PATH_ABS . 'admin/js/quietly-settings.js', array(), QUIETLY_WP_VERSION, true );
			wp_enqueue_script( QUIETLY_WP_SLUG . '-admin-settings' );
			wp_localize_script( QUIETLY_WP_SLUG . '-admin-settings', QUIETLY_WP_SLUG . 'WP',
				array(
					'quietlyUrl' => QUIETLY_WP_URL
				)
			);
		}
	}

	/*
	 * Validates and returns user's inputted options.
	 * @param     array    $input    User inputs for plugin options.
	 * @return    array    Validated options.
	 */
	public function validate( $input ) {
		$options = get_option( QUIETLY_WP_SLUG_OPTIONS );
		$is_options_screen = ( 'options' === get_current_screen()->id );
		if ( false === $options ) {
			$options = QuietlyOptions::$options_default;
		} else {

			// API token
			$key = 'api_token';
			if( isset( $input[ $key ] ) ) {
				if ( strlen( $input[ $key ] ) > 10 ) {
					// Update value
					if ( $options[ $key ] != $input[ $key ] ) {
						if ( $is_options_screen ) {
							add_settings_error( QUIETLY_WP_SLUG_OPTIONS, 'updated-api-token', /* TRANSLATORS: settings */ __( 'API token updated.', QUIETLY_WP_SLUG ), 'updated' );
						}
						$options[ $key ] = esc_html( $input[ $key ] );
					}
				} else if ( strlen ( $input[ $key ] ) > 0) {
					// Invalid
					if ( $is_options_screen ) {
						add_settings_error( QUIETLY_WP_SLUG_OPTIONS, 'invalid-api-token', /* TRANSLATORS: settings */ __( 'You have entered an invalid API token.', QUIETLY_WP_SLUG ) );
					}
				} else {
					// Unset
					if ( $is_options_screen ) {
						add_settings_error( QUIETLY_WP_SLUG_OPTIONS, 'updated-api-token', /* TRANSLATORS: settings */ __( 'API token has been removed. Certain features of the plugin are disabled.', QUIETLY_WP_SLUG ), 'updated' );
					}
					$options[ $key ] = '';
				}
			}

			// Show description in excerpts
			$key = 'show_description_in_excerpts';
			if ( isset( $input[ $key ] ) ) {
				// Turn on
				if ( $options[ $key ] === false) {
					$options[ $key ] = true;
					if ( $is_options_screen ) {
						add_settings_error( QUIETLY_WP_SLUG_OPTIONS, 'updated-show-description-in-excerpts', /* TRANSLATORS: settings */ __( 'List description will now show in post excerpts with a Quietly embed.', QUIETLY_WP_SLUG ), 'updated' );
					}
				}
			} else {
				// Turn off
				if ( $options[ $key ] === true ) {
					$options[ $key ] = false;
					if ( $is_options_screen ) {
						add_settings_error( QUIETLY_WP_SLUG_OPTIONS, 'updated-show-description-in-excerpts', /* TRANSLATORS: settings */ __( 'List description will no longer show in post excerpts with a Quietly embed.', QUIETLY_WP_SLUG ), 'updated' );
					}
				}
			}

		}
		return $options;
    }

	/**
	 * Displays the field for the API token.
	 */
	public function display_field_api_token() {
?>
		<input id="quietly-input-api-token" type="text" name="<?php echo QUIETLY_WP_SLUG_OPTIONS; ?>[api_token]" value="<?php echo QuietlyOptions::get_option( 'api_token' ) ?>" size="30" placeholder="Enter API token here...">
		<p class="description">
			<?php /* TRANSLATORS: settings */ _e( 'This token is required for the plugin to communicate with your Quietly account. It is not mandatory for displaying a Quietly embed in your posts.', QUIETLY_WP_SLUG ); ?>
		</p>
		<p>
			<a href class="quietly-btn-get-api-token button"><?php /* TRANSLATORS: settings */ _e( 'Get API Token', QUIETLY_WP_SLUG ); ?></a>
		</p>
<?php
	}

	/**
	 * Displays the field for the show notifications toggle.
	 */
	public function display_field_show_description_in_excerpts() {
?>
		<input type="checkbox" name="<?php echo QUIETLY_WP_SLUG_OPTIONS; ?>[show_description_in_excerpts]"<?php if ( QuietlyOptions::get_option( 'show_description_in_excerpts' ) ) echo ' checked'; ?>> <?php /* TRANSLATORS: settings */ _e( 'Automatically show list description in your post excerpts', QUIETLY_WP_SLUG ); ?>
		<p class="description">
			<?php /* TRANSLATORS: settings */ _e( 'Enabling this option will show the Quietly list description in place of an excerpt if the embed is the only content.', QUIETLY_WP_SLUG ); ?>
		</p>
<?php
	}

}