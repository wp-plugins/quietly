<?php
/**
 * Quietly Analytics Class
 * Injects Quietly analytics tracking script for content with story id.
 * @package Quietly
 */

class QuietlyAnalytics {

	/**
	 * Quietly story id marker regex.
	 * @var    string
	 */
	protected $story_id_regex = '';

	/**
	 * Determines if the page has content with story id marker.
	 * @var    boolean
	 */
	protected $has_story_id = false;

	/**
	 * Initializes the object.
	 */
	public function __construct() {
		$this->story_id_regex = '/div.*data-qsid="[\d]*"/i';
		if ( ! is_admin() && QuietlyOptions::get_option( 'enable_analytics' ) === true) {
			add_filter( 'the_content', array( $this, 'detect_story_id' ) );
			add_action( 'wp_footer', array( $this, 'insert_snippet' ) );
		}
	}

	/**
	 * Detects story id marker before inserting content.
	 * @param     string    $content    The post content.
	 * @return    string    The post content.
	 */
	public function detect_story_id( $content ) {
		if ( $this->has_story_id === false ) {
			$this->has_story_id = (preg_match( $this->story_id_regex, $content ) == 1);
		}
		return $content;
	}

	/**
	 * Inserts analytics tracker snippet.
	 */
	public function insert_snippet() {
		if ( $this->has_story_id === true ) {
	?>
<script>;(function(_,q,l,y){l=_.createElement(q);y=_.getElementsByTagName(q)[0];l.async=1;l.src='//<?php echo QUIETLY_WP_URL; ?>/static/js/analytics.js';y.parentNode.insertBefore(l, y);})(document,'script');</script>
	<?php
		}
	}

}