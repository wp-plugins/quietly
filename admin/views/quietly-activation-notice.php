<?php
/**
 * Admin First-time Activation Notice
 */
?>

<div class="quietly-wp-admin__activation-notice updated">
	<p>
		<?php
			/* translators: first time activation title; %s = <strong></strong> */
			printf( __( 'The %sQuietly%s plugin has been activated!', QUIETLY_WP_SLUG ), '<strong>', '</strong>' );
		?>
	</p>
	<p>
		<?php
			/* translators: first time activation description */
			_e( 'Connect the plugin with your Quietly account to easily access and insert Quietly content while writing a post.', QUIETLY_WP_SLUG );
		?>
	</p>
	<p>
		<a href="<?php echo admin_url( 'admin.php?page=' . QUIETLY_WP_SLUG ) . '#setup_token'; ?>" class="button-primary button-large button">
			<?php
				/* translators: first time activation connect button label */
				_e( 'Connect with Quietly', QUIETLY_WP_SLUG );
			?>
		</a>
	</p>
</div>