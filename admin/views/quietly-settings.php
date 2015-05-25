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
	<h2>
		<?php
			/* translators: settings page title */
			_e( 'Quietly Settings', QUIETLY_WP_SLUG );
		?>
	</h2>

	<?php if ( ! $has_token ): ?>
	<!-- Empty API token notice -->
	<div class="quietly-wp-admin__notice updated">
		<p>
			<?php
				/* translators: settings api connect message */
				_e ( 'Get your Quietly API token and connect the plugin with your Quietly account to easily access and insert your content while writing a post.', QUIETLY_WP_SLUG );
			?>
		</p>
		<p>
			<a href class="quietly-btn-get-api-token button-primary button-large button">
				<?php
					/* translators: settings get api token button label */
					_e( 'Get API Token', QUIETLY_WP_SLUG );
				?>
			</a>
		</p>
	</div>
	<?php endif; ?>

	<!-- Change token notice -->
	<div id="quietly-blk-change-token" class="quietly-wp-admin__notice updated" style="display: none">
		<p>
			<?php
				/* translators: settings change account message */
				_e( 'To change the Quietly account associated this plugin, please obtain a new API token for the account you wish to use.', QUIETLY_WP_SLUG );
			?>
		</p>
		<p>
			<a href class="quietly-btn-get-api-token button-primary button-large button">
				<?php
					/* translators: settings get api token button label */
					_e( 'Get API Token', QUIETLY_WP_SLUG );
				?>
			</a>
		</p>
	</div>

	<?php if ( $has_token && isset( $_GET[ 'settings-updated' ] ) && $_GET[ 'settings-updated' ] === 'true' ): ?>
	<!-- Onboarding -->
	<div id="quietly-blk-onboarding" class="quietly-wp-admin__notice updated" style="display: none">
		<p>
			<?php
				/* translators: settings onboarding message */
				_e( 'Nice work! You can now insert Quietly content right into your post editor.', QUIETLY_WP_SLUG );
			?>
		</p>
		<div class="quietly-wp-admin__notice-step">
			<img src="<?php echo QUIETLY_WP_PATH_ABS; ?>/images/instruction-1.png" width="220" height="150">
			<p>
				<?php
					/* translators: settings onboarding step 1; %s = <strong></strong> */
					printf(
						__( '%s1.%s Click the %sAdd Quietly Content%s button from the editor toolbar.', QUIETLY_WP_SLUG ),
						'<strong>', '</strong>',
						'<strong>', '</strong>'
					)
				?>
			</p>
		</div><!--
		--><div class="quietly-wp-admin__notice-step">
			<img src="<?php echo QUIETLY_WP_PATH_ABS; ?>/images/instruction-2.png" width="220" height="150">
			<p>
				<?php
					/* translators: settings onboarding step 2; %s = <strong></strong> */
					printf(
						__( '%s2.%s Browse or search your Quietly content.', QUIETLY_WP_SLUG ),
						'<strong>', '</strong>',
						'<strong>', '</strong>'
					)
				?>
			</p>
		</div><!--
		--><div class="quietly-wp-admin__notice-step">
			<img src="<?php echo QUIETLY_WP_PATH_ABS; ?>/images/instruction-3.png" width="220" height="150">
			<p class="quietly-wp-admin__notice-step-caption">
				<?php
					/* translators: settings onboarding step 3; %s = <strong></strong> */
					printf(
						__( '%s1.%s Edit and customize your content to match your WordPress site.', QUIETLY_WP_SLUG ),
						'<strong>', '</strong>',
						'<strong>', '</strong>'
					)
				?>
			</p>
		</div><!--
		--><div class="quietly-wp-admin__notice-step">
			<img src="<?php echo QUIETLY_WP_PATH_ABS; ?>/images/instruction-4.png" width="220" height="150">
			<p class="quietly-wp-admin__notice-step-caption">
				<?php
					/* translators: settings onboarding step 4; %s = <strong></strong> */
					printf(
						__( '%s1.%s Click %sInsert Content%s to finish. In WordPress 4.0+, your content will show a visual preview.', QUIETLY_WP_SLUG ),
						'<strong>', '</strong>',
						'<strong>', '</strong>'
					)
				?>
			</p>
		</div>
		<p>
			<a href="<?php echo admin_url( 'post-new.php '); ?>" class="button-primary button-large button">
				<?php
					/* translators: settings new post button label */
					_e( 'Start Writing', QUIETLY_WP_SLUG );
				?>
			</a>
			<a href="<?php echo admin_url( 'edit.php '); ?>" class="button-large button">
				<?php
					/* translators: settings view all posts button label */
					_e( 'View All Posts', QUIETLY_WP_SLUG );
				?>
			</a>
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
		    submit_button();
		?>
	</form>
	<!-- Footer -->
	<div class="quietly-wp-admin__footer">
		<p>
			<?php
				/* translators: settings footer; %s = <a></a> */
				printf(
					__( 'Like our plugin? %sLeave a rating%s and let the world know! We would be forever grateful :)', QUIETLY_WP_SLUG ),
					'<a href="https://wordpress.org/support/view/plugin-reviews/quietly" target="_blank">',
					'</a>'
				);
			?><br>
			<?php
				/* translators: settings footer; %s = <a></a> */
				printf(
					__( 'Found a bug or have a feature request? Shoot us an email at %swelovefeedback@quiet.ly%s.', QUIETLY_WP_SLUG ),
					'<a href="mailto:welovefeedback@quiet.ly">',
					'</a>'
				);
			?>
		</p>
		<p>
			<strong>
				<?php /* translators: settings footer */ _e( 'Resources:', QUIETLY_WP_SLUG ); ?>
			</strong>
			<a href="https://quietly.uservoice.com/knowledgebase/articles/394005-troubleshooting" target="_blank">
				<?php /* translators: settings footer */ _e( 'Troubleshooting Guide', QUIETLY_WP_SLUG ); ?>
			</a> |
			<a href="https://quietly.uservoice.com" target="_blank">
				<?php /* translators: settings footer */ _e( 'Knowledge Base', QUIETLY_WP_SLUG ); ?>
			</a> |
			<a href="https://wordpress.org/plugins/quietly/" target="_blank">
				<?php /* translators: settings footer */ _e( 'Plugin Website', QUIETLY_WP_SLUG ); ?>
			</a> |
			<a href="http://www.quiet.ly" target="_blank">
				Quietly
			</a>
		</p>
		<p>
			<strong>
				<?php /* translators: settings footer */ _e( 'Plugin version:', QUIETLY_WP_SLUG ); ?>
			</strong>
			<?php echo QUIETLY_WP_VERSION; ?><br>
		</p>
	</div>
</div>