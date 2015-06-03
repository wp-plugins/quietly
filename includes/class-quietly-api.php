<?php
/**
 * Quietly API
 * Handles API call to the Quietly back-end.
 * @package Quietly
 */

class QuietlyAPI {

	/**
	 * Quietly API url.
	 * @var string
	 */
	private $api_url = '';

	/**
	 * WordPress AJAX url prefix.
	 * @var string
	 */
	private $prefix = '';

	/**
	 * Quietly user API token.
	 * @var string
	 */
	private $api_token = '';

	/**
	 * Exposed API endpoints.
	 * @var array
	 */
	private $endpoints = array(
		'get_member'
	);

	/**
	 * Initializes the object.
	 */
	public function __construct() {

		$this->api_url = 'http://' . QUIETLY_WP_URL . '/api/v14/';
		$this->prefix = 'wp_ajax_' . QUIETLY_WP_SLUG . '_api_';

		// Verify user API token
		$token = QuietlyOptions::get_option( 'api_token' );
		if ( is_string( $token ) && strlen( $token ) > 0 ) {
			$this->api_token = $token;
		}

		// Register endpoints
		foreach ( $this->endpoints as $endpoint) {
			add_action( $this->prefix . $endpoint, array( $this, $endpoint ) );
		}

	}

	/**
	 * Returns an error array to be encoded into a JSON response.
	 * @param     string     $message          The error message.
	 * @param     integer    $code             Error code.
	 * @param     integer    $internal_code    Internal error code.
	 * @return    array      An array with error status and message to be encoded into JSON response.
	 */
	protected function get_error_response( $code, $message = null, $internal_code = null ) {
		if ( is_null( $message ) ) {
			// Set default error message
			switch ($code) {
				case 400:
					$message = /* translators: API error message */ __( 'The data sent to the server was invalid.', QUIETLY_WP_SLUG );
					break;
				case 403:
					$message = /* translators: API error message */ __( 'You do not have permission to make this request.', QUIETLY_WP_SLUG );
					break;
				default:
					$message = /* translators: API error message */ __( 'An unrecognized error has occured.', QUIETLY_WP_SLUG );
					break;
			}
		}
		// Return new response
		return array(
			'code' => $code,
			'body' => array(
				'message' => $message,
				'code' => $internal_code
			)
		);
	}

	/**
	 * Does a checked remote request and returns the response. It's basically
	 * a wrapper for wp_remote_request()
	 * @param     string    $url        The request URL.
	 * @param     array     $options    Options for the request.
	 * @param     string    $nonce      WordPress nonce for security check.
	 * @return    array
	 */
	protected function remote_request( $url, $options, $nonce ) {

		// Check if URL is valid
		if ( empty( $url ) ) {
			return $this->get_error_response( 400 );
		}

		// Check if API token exists
		if ( empty( $this->api_token ) ) {
			return $this->get_error_response( 403, /* translators: API error message */ __( 'The API token is invalid.', QUIETLY_WP_SLUG ) );
		}

		// Create a default header for sending API token and merge with options
		$default_options = array(
			'headers' => array(
				'X-Authorization-Token' => $this->api_token
			),
			'timeout' => 10 // In seconds
		);
		$options = array_merge( $default_options, $options );

		// Check nonce and user capability
		if ( ! wp_verify_nonce( $nonce, QUIETLY_WP_SLUG . '_api_call' ) ||
			! current_user_can( 'edit_posts' ) ||
			! current_user_can( 'edit_pages' ) ||
			! current_user_can( 'publish_posts' ) ||
			! current_user_can( 'publish_pages' ) ) {
			return $this->get_error_response( 403, 'Invalid nonce.' );
		}

		// Make the request
		$response = wp_remote_request( $this->api_url . $url, $options );

		// Check that WordPress was able to send the request, otherwise return an error
		if ( is_wp_error( $response ) ) {
			return $this->get_error_response( 500, $response->get_error_message() );
		}

		// Return simplified response
		return array(
			'code' => $response['response']['code'],
			'body' => json_decode( $response['body'], true )
		);

	}

	/**
	 * Gets the post data from a request.
	 * @return    Array    The post data or false if invalid.
	 */
	protected function get_post_data() {
		$data = json_decode( file_get_contents( 'php://input' ), true );
		if ( isset( $data['nonce'] ) ) {
			return $data;
		} else {
			return false;
		}
	}

	/**
	 * Returns an encoded JSON with unescaped slashes.
	 * @param     object    $data    The object to encode.
	 * @return    string    The encoded JSON.
	 */
	protected function get_json_encode( $data ) {
		return str_replace( '\\/', '/', json_encode( $data ) );
	}

	/**
	 * Wraps the fallback for < PHP 5.4 http_response_code() function.
	 * @param     int    $new_code    The response code to set.
	 * @return    int    The final response code sent.
	 */
	protected function http_response_code( $new_code = NULL ) {
		if ( ! function_exists( 'http_response_code' ) ) {
			static $code = 200;
			if( $new_code !== NULL ) {
				header( 'X-PHP-Response-Code: ' . $new_code, true, $new_code );
				if( ! headers_sent() ) {
					$code = $new_code;
				}
			}
		} else {
			http_response_code( $new_code );
			$code = $new_code;
		}
		return $code;
	}

	/**
	 * Returns the final response to client.
	 * @param     array    $response    The response with code and body.
	 * @return    array    The final response with the correct HTTP response code.
	 */
	protected function get_final_response( $response ) {
		$this->http_response_code( $response['code'] );
		echo $this->get_json_encode( $response['body'] );
		die();
	}

	/**
	 * Returns current member data.
	 * @return    string    JSON encoded string of the AJAX response.
	 */
	public function get_member() {
		$request_data = $this->get_post_data();
		if ( false === $request_data ) {
			$response = $this->get_error_response( 400 );
		} else {
			$response = $this->remote_request( 'members/me', array(
				'method' => 'GET'
			), $request_data['nonce'] );
		}
		$this->get_final_response( $response );
	}

}