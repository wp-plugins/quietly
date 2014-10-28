<?php
/**
 * Plugin Settings Screen
 * @package Quietly
 */

$has_token = QuietlyOptions::get_option( 'api_token' );
if ( ! empty( $has_token ) ) {
	$has_token = true;
} else {
	$has_token = false;
}

?>
<div class="wrap">
	<?php screen_icon( 'quietly' ); ?>
	<h2><?php _e( 'Quietly Settings', QUIETLY_WP_SLUG ); ?></h2>

	<?php if ( ! $has_token ): ?>
	<!-- Empty API token notice -->
	<div class="quietly-wp-admin__activation-notice updated">
		<p>
			Get your Quietly API token and connect the plugin with your Quietly account to easily access and insert your lists while writing a post.
		</p>
		<p>
			<a href class="quietly-btn-get-api-token button-primary button-large button"><?php /* TRANSLATORS: settings */ _e( 'Get API Token', QUIETLY_WP_SLUG ); ?></a>
		</p>
	</div>
	<?php endif; ?>

	<!-- Change token notice -->
	<div id="quietly-blk-change-token" class="quietly-wp-admin__activation-notice updated" style="display: none">
		<p>
			To change the Quietly account associated this plugin, please obtain a new API token for the account you wish to use.
		</p>
		<p>
			<a href class="quietly-btn-get-api-token button-primary button-large button"><?php /* TRANSLATORS: settings */ _e( 'Get API Token', QUIETLY_WP_SLUG ); ?></a>
		</p>
	</div>

	<?php if ( $has_token && isset( $_GET[ 'settings-updated' ] ) && $_GET[ 'settings-updated' ] === 'true' ): ?>
	<!-- Onboarding -->
	<div id="quietly-blk-onboarding" class="quietly-wp-admin__activation-notice updated" style="display: none">
		<p>
			Nice work! You can now insert Quietly lists right into your post editor.
		</p>
		<div class="quietly-wp-admin__activation-notice-step">
			<img src="<?php echo QUIETLY_WP_PATH_ABS; ?>/images/instruction-1.png" width="220" height="150">
			<p>
				<strong>1.</strong> Click the <strong>Insert Quietly List</strong> button from the editor toolbar.
			</p>
		</div><!--
		--><div class="quietly-wp-admin__activation-notice-step">
			<img src="<?php echo QUIETLY_WP_PATH_ABS; ?>/images/instruction-2.png" width="220" height="150">
			<p>
				<strong>2.</strong> Browse or search your Quietly lists.
			</p>
		</div><!--
		--><div class="quietly-wp-admin__activation-notice-step">
			<img src="<?php echo QUIETLY_WP_PATH_ABS; ?>/images/instruction-3.png" width="220" height="150">
			<p class="quietly-wp-admin__activation-notice-step-caption">
				<strong>3.</strong> Customize your list to match your WordPress site.
			</p>
		</div><!--
		--><div class="quietly-wp-admin__activation-notice-step">
			<img src="<?php echo QUIETLY_WP_PATH_ABS; ?>/images/instruction-4.png" width="220" height="150">
			<p class="quietly-wp-admin__activation-notice-step-caption">
				<strong>4. </strong>Click <strong>Insert</strong> to finish. In WordPress 4.0+, your lists will show a visual preview.
			</p>
		</div>
	</div>
	<?php endif; ?>

	<!-- Settings -->
	<form id="quietly-settings" method="post" action="options.php" class="quietly-form">
		<?php
			// Print hidden fields
		    settings_fields( QUIETLY_WP_SLUG_OPTIONS );
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