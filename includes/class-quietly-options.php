<?php
/**
 * Quietly Options Class
 * @package Quietly
 */

class QuietlyOptions {

	/**
	 * The default settings for the plugin.
	 * @var       array
	 */
	protected static $options_default = array(
		'show_description_in_excerpts' => true,
		'api_token' => ''
	);

	/**
	 * Creates the plugin options if it doesn't exist.
	 */
	public static function create_options() {
		if( false == get_option( QUIETLY_WP_SLUG_OPTIONS ) ) {
			add_option( QUIETLY_WP_SLUG_OPTIONS, self::$options_default );
		}
	}

	/**
	 * Returns an option from the plugin options array.
	 * @param     string    The option key.
	 * @return    array     The option value.
	 */
	public static function get_option( $key ) {
		$options = get_option( QUIETLY_WP_SLUG_OPTIONS );
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
		$options = get_option( QUIETLY_WP_SLUG_OPTIONS );
		if( isset( $options[ $key ] ) ) {
			$options[ $key ] = $value;
			update_option( QUIETLY_WP_SLUG_OPTIONS, $options );
		}
	}

}