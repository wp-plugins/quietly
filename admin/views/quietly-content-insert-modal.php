<div id="quietly-wp"
	ng-cloak>
	<div class="quietly-wp-content-insert__modal quietly-wp-ng-show-fade"
		ng-show="options.show"
		content-insert-modal>
		<div class="quietly-wp-content-insert__overlay"
			ng-click="close()"></div>
		<div class="quietly-wp-content-insert__container">
			<a href class="quietly-wp-content-insert__btn-close media-modal-close" title="<?php /* translators: content insert modal close button tooltip */ esc_attr_e( 'Close', QUIETLY_WP_SLUG ); ?>"
				ng-click="close()">
				<span class="media-modal-icon"></span>
			</a>
			<div class="quietly-wp-content-insert__wrap">

				<div class="quietly-wp-content-insert__body">
					<div class="quietly-wp-content-insert__header">
						<div class="quietly-wp-content-insert__user clearfix"
							ng-show="member">
							<img ng-src="{{ member._thumbnailImage }}" class="quietly-wp-content-insert__user-avatar">
							<div class="quietly-wp-content-insert__user-body">
								<a href="{{ member._url }}" class="quietly-wp-content-insert__user-name" target="_blank" alt="View Quietly profile">
									{{ member.name }}
								</a>
								<br>
								<a href="<?php echo admin_url( 'admin.php?page=' . QUIETLY_WP_SLUG . '#change_token' ); ?>">
									<?php /* translators: list insert modal log out link label */ _e( 'Not you?', QUIETLY_WP_SLUG ); ?>
								</a>
							</div>
						</div>
						<h1 class="quietly-wp-content-insert__header-title">
							<?php
								/* translators: content insert modal customize view title */
								_e( 'Edit Quietly Content', QUIETLY_WP_SLUG );
							?>
						</h1>
						<p class="quietly-wp-content-insert__description">
							<?php
								/* translators: list insert modal browser view info */
								_e( 'Preview or edit content from your Quietly account, then insert it into your article.', QUIETLY_WP_SLUG );
							?>
						</p>
					</div>
					<div class="quietly-wp-content-insert__stretch">
						<div class="quietly-wp-content-insert__content"
							ng-if="!options.isLoaded">
							<!-- No API token -->
							<p ng-show="!options.hasToken">
								<?php
									/* translators: list insert modal no api token message */
									_e( 'Please connect with your Quietly account in the plugin settings.', QUIETLY_WP_SLUG );
								?>
								<br><br>
								<a href="<?php echo admin_url( 'admin.php?page=' . QUIETLY_WP_SLUG ); ?>" class="button-large button-primary button">
									<?php /* translators: list insert modal */ _e( 'View Plugin Settings', QUIETLY_WP_SLUG ); ?>
								</a>
							</p>
							<!-- Processing -->
							<p ng-show="options.isProcessing">
								<?php
									/* translators: list insert modal getting data message */
									_e( "Hang on, we're connecting with Quietly...", QUIETLY_WP_SLUG );
								?>
								<br><br>
								<img src="<?php echo QUIETLY_WP_PATH_ABS . 'images/throbber-gray.gif'; ?>">
							</p>
							<!-- Failure -->
							<p ng-show="options.error === 'unknown'">
								<?php
									/* translators: list insert modal api error message */
									_e( 'Bummer, there was a problem getting your awesome content.', QUIETLY_WP_SLUG );
								?>
								<br><br>
								<a href class="button-large button-primary button"
									ng-click="open()">
									<?php /* translators: list insert modal refresh button label */ _e( 'Try Again', QUIETLY_WP_SLUG ); ?>
								</a>
							</p>
							<!-- Invalid token -->
							<p ng-show="options.error === '403'">
								<?php
									/* translators: list insert modal invalid api token message */
									_e( 'Your API token is invalid. Please obtain a new one.', QUIETLY_WP_SLUG );
								?>
								<br><br>
								<a href="<?php echo admin_url( 'admin.php?page=' . QUIETLY_WP_SLUG ); ?>#change_token" class="button-large button-primary button">
									<?php /* translators: list insert modal api token button label */ _e( 'Get New API Token', QUIETLY_WP_SLUG ); ?>
								</a>
							</p>
						</div>
						<div id="quietly-wp-content-insert-iframe-wrap" class="quietly-wp-content-insert__stretch -loading"
							ng-if="options.isLoaded && options.show">
							<iframe id="quietly-wp-content-insert-iframe" width="100%" height="100%" frameborder="0"
								ng-src="{{ options.iframeURL }}"></iframe>
						</div>
					</div>
				</div>

				<div class="quietly-wp-content-insert__footer submitbox">
					<div class="quietly-wp-content-insert__footer-wrap clearfix">
						<button class="quietly-wp-content-insert__btn-primary button-large button-primary button"
							ng-disabled="!content"
							ng-click="insert()">
							<?php /* translators: content insert modal footer button label */ _e( 'Insert Content', QUIETLY_WP_SLUG ); ?>
						</button>
						<button class="quietly-wp-content-insert__btn-primary button-large button"
							ng-click="resetFrame()">
							<?php /* translators: content insert modal footer button label */ _e( 'View All Content', QUIETLY_WP_SLUG ); ?>
						</button>
						<a href class="quietly-wp-content-insert__btn-cancel submitdelete"
							ng-click="close()">
							<?php /* translators: content insert modal footer button label */ _e( 'Cancel', QUIETLY_WP_SLUG ); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>