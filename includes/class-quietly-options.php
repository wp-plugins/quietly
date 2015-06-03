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
	public static $options_default = array(
		'api_token' => '',
		'show_description_in_excerpts' => true,
		'enable_analytics' => true
	);

	/**
	 * Creates the plugin options if it doesn't exist.
	 */
	public static function create_options() {
		$options = get_option( QUIETLY_WP_SLUG_OPTIONS );
		if( false === $options ) {
			// Create options
			add_option( QUIETLY_WP_SLUG_OPTIONS, self::$options_default );
		} else {
			// Merge options
			$options = array_merge( self::$options_default, $options );
			update_option( QUIETLY_WP_SLUG_OPTIONS, $options );
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