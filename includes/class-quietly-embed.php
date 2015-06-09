<?php
/**
 * Quietly Embed Class
 * Handles detection and insertion of Quietly embeds.
 * @package Quietly
 */

class QuietlyEmbed {

	/**
	 * Quietly oEmbed endpoint url.
	 * @var string
	 */
	protected $oembed_url = '';

	/**
	 * Quietly embed code regex.
	 * @var    string
	 */
	protected $embed_regex = '';

	/**
	 * Determines if the current output is a post excerpt.
	 * @var    boolean
	 */
	protected $is_excerpt = false;

	/**
	 * List of embed descriptions to output in place of excerpt.
	 * @var    array
	 */
	protected $embed_descriptions = array();

	/**
	 * Initializes the object.
	 */
	public function __construct() {
		$this->oembed_url = 'https://' . QUIETLY_WP_URL . '/oembed';
		$this->embed_regex = '#https?://(beta\.|www\.)?' . QUIETLY_WP_URL_DOMAIN . '/list/.*#i';
		// Register embed handler
		wp_embed_register_handler( QUIETLY_WP_SLUG, $this->embed_regex, array( $this, 'embed_register_handler' ) );
		wp_oembed_add_provider( $this->embed_regex, $this->oembed_url, true );
		// Register excerpt detection
		add_filter( 'get_the_excerpt', array( $this, 'flag_excerpt' ), 0 );
		add_filter( 'get_the_excerpt', array( $this, 'unflag_excerpt' ), 99 );
		add_action( 'wp_footer', array( $this, 'display_footer' ) );
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
		if ( true === $this->is_excerpt ) {
			if ( QuietlyOptions::get_option( 'show_description_in_excerpts' ) === true) {
				// Remember list description to be rendered in excerpt
				require_once( ABSPATH . WPINC . '/class-oembed.php' );
				$oembed = _wp_oembed_get_object();
				$oembed = $oembed->fetch( $this->oembed_url, $url );
				if (is_object($oembed) && property_exists($oembed, 'description')) {
					if ( count( $this->embed_descriptions ) === 0 ) {
						array_push( $this->embed_descriptions, '<p>' . $oembed->description . '</p>' );
					}
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
		$this->is_excerpt = true;
		$this->embed_descriptions = array();
		$content = get_the_content('');
		$content = apply_filters( 'the_content', $content );
		$this->is_excerpt = false;
		return apply_filters( 'wp_trim_excerpt', $text, $text );
	}

	/**
	 * Unflags the excerpt output status.
	 * @param    string     $text    The excerpt text.
	 */
	public function unflag_excerpt( $text = '' ) {
		// Show list description if excerpt is empty
		if ( trim( $text ) === '') {
			foreach ( $this->embed_descriptions as $embed) {
				$text .= $embed;
			}
			$this->embed_descriptions = array();
		}
		return apply_filters( 'wp_trim_excerpt', $text, $text );
	}

	/**
	 * Displays plugin info in the footer.
	 */
	public function display_footer() {
		echo '<!-- QWP v' . QUIETLY_WP_VERSION . ' -->' . "\r\n";
	}

}