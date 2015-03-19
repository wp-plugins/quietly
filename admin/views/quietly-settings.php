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
	<div class="quietly-wp-admin__notice updated">
		<p>
			Get your Quietly API token and connect the plugin with your Quietly account to easily access and insert your content while writing a post.
		</p>
		<p>
			<a href class="quietly-btn-get-api-token button-primary button-large button"><?php /* TRANSLATORS: settings */ _e( 'Get API Token', QUIETLY_WP_SLUG ); ?></a>
		</p>
	</div>
	<?php endif; ?>

	<!-- Change token notice -->
	<div id="quietly-blk-change-token" class="quietly-wp-admin__notice updated" style="display: none">
		<p>
			To change the Quietly account associated this plugin, please obtain a new API token for the account you wish to use.
		</p>
		<p>
			<a href class="quietly-btn-get-api-token button-primary button-large button"><?php /* TRANSLATORS: settings */ _e( 'Get API Token', QUIETLY_WP_SLUG ); ?></a>
		</p>
	</div>

	<?php if ( $has_token && isset( $_GET[ 'settings-updated' ] ) && $_GET[ 'settings-updated' ] === 'true' ): ?>
	<!-- Onboarding -->
	<div id="quietly-blk-onboarding" class="quietly-wp-admin__notice updated" style="display: none">
		<p>
			Nice work! You can now insert Quietly content right into your post editor.
		</p>
		<div class="quietly-wp-admin__notice-step">
			<img src="<?php echo QUIETLY_WP_PATH_ABS; ?>/images/instruction-1.png" width="220" height="150">
			<p>
				<strong>1.</strong> Click the <strong>Add Quietly Content</strong> button from the editor toolbar.
			</p>
		</div><!--
		--><div class="quietly-wp-admin__notice-step">
			<img src="<?php echo QUIETLY_WP_PATH_ABS; ?>/images/instruction-2.png" width="220" height="150">
			<p>
				<strong>2.</strong> Browse or search your Quietly content.
			</p>
		</div><!--
		--><div class="quietly-wp-admin__notice-step">
			<img src="<?php echo QUIETLY_WP_PATH_ABS; ?>/images/instruction-3.png" width="220" height="150">
			<p class="quietly-wp-admin__notice-step-caption">
				<strong>3.</strong> Edit and customize your content to match your WordPress site.
			</p>
		</div><!--
		--><div class="quietly-wp-admin__notice-step">
			<img src="<?php echo QUIETLY_WP_PATH_ABS; ?>/images/instruction-4.png" width="220" height="150">
			<p class="quietly-wp-admin__notice-step-caption">
				<strong>4. </strong>Click <strong>Insert Content</strong> to finish. In WordPress 4.0+, your content will show a visual preview.
			</p>
		</div>
		<p>
			<a href="<?php echo admin_url( 'post-new.php '); ?>" class="button-primary button-large button">Start Writing</a>
			<a href="<?php echo admin_url( 'edit.php '); ?>" class="button-large button">View All Posts</a>
		</p>
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
			Like our plugin? <a href="https://wordpress.org/support/view/plugin-reviews/quietly" target="_blank">Leave a rating</a> and let the world know! We would be forever grateful :)<br>
			Found a bug or have a feature request? Shoot us an email at <a href="mailto:welovefeedback@quiet.ly">welovefeedback@quiet.ly</a>.
		</p>
		<p>
			<strong>Resources:</strong> <a href="https://quietly.uservoice.com/knowledgebase/articles/394005-troubleshooting" target="_blank">Troubleshooting Guide</a> | <a href="https://quietly.uservoice.com" target="_blank">Knowledge Base</a> | <a href="https://wordpress.org/plugins/quietly/" target="_blank">Plugin Website</a> | <a href="http://www.quiet.ly" target="_blank">Quietly</a>
		</p>
		<p>
			<strong>Plugin version:</strong> <?php echo QUIETLY_WP_VERSION; ?><br>
		</p>
	</div>
</div>