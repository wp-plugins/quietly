<?php
/**
 * Plugin Options Page
 * @package Quietly
 */
?>
<div class="wrap">
	<?php screen_icon( 'quietly' ); ?>
	<h2><?php _e( 'Quietly Settings', QUIETLY_WP_SLUG ); ?></h2>
	<!-- Settings -->
	<form id="quietly-settings" method="post" action="options.php" class="quietly-form">
		<?php
			// Print hidden fields
		    settings_fields( QUIETLY_WP_OPTIONS_SLUG );
			// Display the fields
		    do_settings_sections( QUIETLY_WP_SLUG );
		?>
		<?php submit_button(); ?>
	</form>
	<!-- Footer -->
	<div class="quietly-wp-admin__footer">
		<p>
			Found a bug or have a feature request? Don't hesistate to contact us at <a href="mailto:welovefeedback@quiet.ly">welovefeedback@quiet.ly</a>!
		</p>
	</div>
</div>