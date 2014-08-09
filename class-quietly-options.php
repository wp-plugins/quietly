<?php
/**
 * Quietly Options Class
 * @package Quietly
 */

class QuietlyOptions {

	/**
	 * Instance of this class.
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * The default settings for the plugin.
	 * @var       array
	 */
	public static $options_default = array(
		'show_description_in_excerpts' => true,
	);

	/**
	 * Initializes the object.
	 */
	public function __construct() {
		// Register settings and validation
		register_setting( QUIETLY_WP_OPTIONS_SLUG, QUIETLY_WP_OPTIONS_SLUG, array( $this, 'validate' ) );

		// Add General section
        add_settings_section(
			'quietly_settings_general',
			null,
			null,
			QUIETLY_WP_SLUG
		);

		// Add show notifications field
		add_settings_field(
			'show_description_in_excerpts',
			'Post Excerpts',
			array( $this, 'display_field_show_description_in_excerpts' ),
			QUIETLY_WP_SLUG,
			'quietly_settings_general'
		);
	}

	/**
	 * Return an instance of this class.
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Creates the plugin options if it doesn't exist.
	 */
	public static function create_options() {
		if( false == get_option( QUIETLY_WP_OPTIONS_SLUG ) ) {
			add_option( QUIETLY_WP_OPTIONS_SLUG, self::$options_default );
		}
	}

	/**
	 * Returns an option from the plugin options array.
	 * @param     string    The option key.
	 * @return    array     The option value.
	 */
	public static function get_option( $key ) {
		$options = get_option( QUIETLY_WP_OPTIONS_SLUG );
		$option = false;
		if( isset( $options[ $key ] ) ) {
			$option = $options[ $key ];
		}
		return $option;
	}

	/**
	 * Sets an option in the options array.
	 * @param     string    $key      Key value of the array.
	 * @param     object    $value    Value to set.
	 */
	public static function set_option( $key, $value ) {
		$options = get_option( QUIETLY_WP_OPTIONS_SLUG );
		if( isset( $options[ $key ] ) ) {
			$options[ $key ] = $value;
			update_option( QUIETLY_WP_OPTIONS_SLUG, $options );
		}
	}

	/**
	 * Validates and returns user's inputted options.
	 * @param     array    $input    User inputs for plugin options.
	 * @return    array    Validated options.
	 */
	public function validate( $input ) {
		$options = get_option( QUIETLY_WP_OPTIONS_SLUG );
		if ( false == $options) {
			// Set options to default if they don't exist
			$options = self::$options_default;
		} else {
			// Show notifications
			$key = 'show_description_in_excerpts';
			if ( isset( $input[ $key ] ) ) {
				// Turn on
				if ( ! $options[ $key ] ) {
					$options[ $key ] = true;
					if ( 'options' == get_current_screen()->id ) {
						add_settings_error( QUIETLY_WP_OPTIONS_SLUG, 'updated-show-description-in-exverpts', /* TRANSLATORS: options */ __( 'List description will now show in post excerpts with a Quietly embed.', QUIETLY_WP_SLUG ), 'updated' );
					}
				}
			} else {
				// Turn off
				if ( $options[ $key ] ) {
					$options[ $key ] = false;
					if ( 'options' == get_current_screen()->id ) {
						add_settings_error( QUIETLY_WP_OPTIONS_SLUG, 'updated-show-description-in-exverpts', /* TRANSLATORS: options */ __( 'List description will no longer show in post excerpts with a Quietly embed.', QUIETLY_WP_SLUG ), 'updated' );
					}
				}
			}
		}
		return $options;
    }

	/**
	 * Displays the field for the show notifications toggle.
	 */
	public function display_field_show_description_in_excerpts() {
?>
		<input type="checkbox" name="<?php echo QUIETLY_WP_OPTIONS_SLUG; ?>[show_description_in_excerpts]"<?php if ( self::get_option( 'show_description_in_excerpts' ) ) echo ' checked'; ?>> <?php /* TRANSLATORS: plugin settings */ _e( 'Automatically show list description in excerpts', QUIETLY_WP_SLUG ); ?>
		<p class="description">
			<?php /* TRANSLATORS: plugin settings */ _e( 'Enabling this option will show the Quietly list description in place of an excerpt if a slideshow is the only content.', QUIETLY_WP_SLUG ); ?>
		</p>
<?php
	}

}