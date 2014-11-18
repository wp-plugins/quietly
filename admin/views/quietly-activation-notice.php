<?php
/**
 * Admin First-time Activation Notice
 */
?>

<div class="quietly-wp-admin__activation-notice updated">
	<p>
		The <strong>Quietly</strong> plugin has been activated!
	</p>
	<p>
		Connect the plugin with your Quietly account to easily access and insert your lists while writing a post.
	</p>
	<p>
		<a href="<?php echo admin_url( 'admin.php?page=' . QUIETLY_WP_SLUG ) . '#setup_token'; ?>" class="button-primary button-large button"><?php /* TRANSLATORS: plugin */ _e( 'Connect with Quietly', QUIETLY_WP_SLUG ); ?></a>
	</p>
</div>