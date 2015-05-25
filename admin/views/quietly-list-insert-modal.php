<div id="quietly-wp"
	ng-cloak>
	<div class="quietly-wp-list-insert__modal quietly-wp-ng-show-fade"
		ng-show="options.show"
		list-insert-modal>
		<div class="quietly-wp-list-insert__overlay"
			ng-click="close()"></div>
		<div class="quietly-wp-list-insert__container">
			<a href class="quietly-wp-list-insert__btn-close media-modal-close" title="<?php /* translators: list insert modal close button tooltip */ esc_attr_e( 'Close', QUIETLY_WP_SLUG ); ?>"
				ng-click="close()">
				<span class="media-modal-icon"></span>
			</a>
			<div class="quietly-wp-list-insert__wrap">

				<!-- Insert -->
				<div class="quietly-wp-list-insert__body"
					ng-show="options.view === 'insert'">
					<div class="quietly-wp-list-insert__header">
						<div class="quietly-wp-list-insert__user clearfix"
							ng-show="member">
							<img ng-src="{{ member._thumbnailImage }}" class="quietly-wp-list-insert__user-avatar">
							<div class="quietly-wp-list-insert__user-body">
								<a href="{{ member._url }}" class="quietly-wp-list-insert__user-name" target="_blank" alt="View Quietly profile">
									{{ member.name }}
								</a>
								<br>
								<a href="<?php echo admin_url( 'admin.php?page=' . QUIETLY_WP_SLUG . '#change_token' ); ?>">
									<?php /* translators: list insert modal log out link label */ _e( 'Not you?', QUIETLY_WP_SLUG ); ?>
								</a>
							</div>
						</div>
						<h1 class="quietly-wp-list-insert__header-title">
							<?php
								/* translators: list insert modal browse view title */
								_e( 'Insert Quietly Content', QUIETLY_WP_SLUG );
							?>
						</h1>
						<p class="quietly-wp-list-insert__description">
							<?php
								/* translators: list insert modal browser view info */
								_e( 'Choose content to insert from your Quietly account?', QUIETLY_WP_SLUG );
							?>
						</p>
						<div class="quietly-wp-list-insert__header-bar"
							ng-show="lists && lists.length">
							<!-- Filters -->
							<div class="quietly-wp-list-insert__filters">
								<span class="quietly-wp-list-insert__filters-count">
									(<ng-pluralize count="listsFiltered.length"
										when="{
											'0': '<?php /* translators: list insert model plural (none) */ _e( 'none', QUIETLY_WP_SLUG ); ?>',
											'one': '<?php /* translators: list insert model plural (singular) */ _e( '1 article', QUIETLY_WP_SLUG ); ?>',
											'other': '{} <?php /* translators: list insert model plural */ _e( 'articles', QUIETLY_WP_SLUG ); ?>'
										}"></ng-pluralize>)</span>
								<span>
									<a href class="quietly-wp-list-insert__filter"
										ng-class="{ '-active': options.listsFilter === '' }"
										ng-click="options.listsFilter = ''">
										<?php /* translators: list insert modal filter */ _e( 'All', QUIETLY_WP_SLUG ); ?>
									</a> |
									<a href class="quietly-wp-list-insert__filter"
										ng-class="{ '-active': options.listsFilter === 'published' }"
										ng-click="options.listsFilter = 'published'">
										<?php /* translators: list insert modal filter */ _e( 'Published', QUIETLY_WP_SLUG ); ?>
									</a> |
									<a href class="quietly-wp-list-insert__filter"
										ng-class="{ '-active': options.listsFilter === 'draft' }"
										ng-click="options.listsFilter = 'draft'">
										<?php /* translators: list insert modal filter */ _e( 'Drafts', QUIETLY_WP_SLUG ); ?>
									</a>
								</span>
							</div>
							<!-- Search -->
							<form name="quietlyListInsertSearch" class="quietly-wp-list-insert__search">
								<input name="search" class="quietly-wp-list-insert__search-input" placeholder="<?php /* translators: list insert modal search placeholder */ esc_attr_e( 'Search content...', QUIETLY_WP_SLUG ); ?>"
									ng-model="options.listsSearch">
							</form>
						</div>
					</div>
					<div class="quietly-wp-list-insert__grid quietly-wp-list-insert__stretch">
						<div class="quietly-wp-list-insert__grid-content clearfix">
							<!-- No API token -->
							<p class="quietly-wp-list-insert__grid-none"
								ng-show="!options.hasToken">
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
							<p class="quietly-wp-list-insert__grid-none"
								ng-show="options.isProcessing">
								<?php
									/* translators: list insert modal getting content message */
									_e( "Hang on, we're getting your content...", QUIETLY_WP_SLUG );
								?>
								<br><br>
								<img src="<?php echo QUIETLY_WP_PATH_ABS . 'images/throbber-gray.gif'; ?>">
							</p>
							<!-- Failure -->
							<p class="quietly-wp-list-insert__grid-none"
								ng-show="options.error === 'unknown'">
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
							<p class="quietly-wp-list-insert__grid-none"
								ng-show="options.error === '403'">
								<?php
									/* translators: list insert modal invalid api token message */
									_e( 'Your API token is invalid. Please obtain a new one.', QUIETLY_WP_SLUG );
								?>
								<br><br>
								<a href="<?php echo admin_url( 'admin.php?page=' . QUIETLY_WP_SLUG ); ?>#change_token" class="button-large button-primary button">
									<?php /* translators: list insert modal api token button label */ _e( 'Get New API Token', QUIETLY_WP_SLUG ); ?>
								</a>
							</p>
							<!-- No lists -->
							<p class="quietly-wp-list-insert__grid-none"
								ng-show="options.isLoaded && !lists.length">
								<?php
									/* translators: list insert modal no content message */
									_e( "You don't have any content on Quietly.", QUIETLY_WP_SLUG );
								?>
								<br><br>
								<a href="<?php echo '//' . QUIETLY_WP_URL . '/list/create'; ?>" target="_blank" class="button-large button-primary button">
								<?php /* translators: list insert modal compose button label*/ _e( 'Compose an Article', QUIETLY_WP_SLUG ); ?>
								</a>
								<a href class="button-large button"
									ng-click="open(true)">
									<?php /* translators: list insert modal refresh button label */ _e( 'Refresh', QUIETLY_WP_SLUG ); ?>
								</a>
							</p>
							<!-- Lists -->
							<div class="clearfix"
								ng-show="options.isLoaded && lists">
								<div class="quietly-wp-list-insert__grid-item"
									ng-repeat="list in listsFiltered"
									ng-click="selectList(list)"
									ng-dblclick="insertList(list)">
									<div class="quietly-wp-list-insert__grid-item-wrap clearfix"
										ng-class="{ '-active': selectedList === list }">
										<div class="quietly-wp-list-insert__grid-item-preview" style="background-image: url('{{ list._previewImage }}')"></div>
										<h1 class="quietly-wp-list-insert__grid-item-title">
											{{ list.name }}
										</h1>
										<p class="quietly-wp-list-insert__grid-item-field">
											<ng-pluralize count="list.numberOfItems"
												when="{
													'0': '<?php /* translators: list insert model plural (none) */ _e( 'None', QUIETLY_WP_SLUG ); ?>',
													'one': '<?php /* translators: list insert model plural (singular) */ _e( '1 item', QUIETLY_WP_SLUG ); ?>',
													'other': '{} <?php /* translators: list insert model plural */ _e( 'items', QUIETLY_WP_SLUG ); ?>'
												}"></ng-pluralize>
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Customize -->
				<div class="quietly-wp-list-insert__body"
					ng-show="options.view === 'customize'">
					<div class="quietly-wp-list-insert__header">
						<h1 class="quietly-wp-list-insert__header-title">
							<?php
								/* translators: list insert modal customize view title */
								_e( 'Edit Quietly Content', QUIETLY_WP_SLUG );
							?>
						</h1>
					</div>
					<div class="quietly-wp-list-insert__grid--locked quietly-wp-list-insert__grid quietly-wp-list-insert__stretch">
						<iframe width="100%" height="100%" frameborder="0" allowfullscreen
							ng-src="{{ getFrameURL() }}"
							ng-if="options.view === 'customize'"></iframe>
					</div>
				</div>

				<div class="quietly-wp-list-insert__footer submitbox">
					<div class="quietly-wp-list-insert__footer-wrap clearfix">
						<button class="quietly-wp-list-insert__btn-primary button-large button-primary button"
							ng-disabled="!selectedList"
							ng-click="customizeList()"
							ng-show="options.view === 'insert'">
							<?php /* translators: list insert modal footer button label */ _e( 'Edit/Customize', QUIETLY_WP_SLUG ); ?>
						</button>
						<button class="quietly-wp-list-insert__btn-primary button-large button-primary button"
							ng-disabled="!options.embedCode"
							ng-click="insertList()">
							<?php /* translators: list insert modal footer button label */ _e( 'Insert Content', QUIETLY_WP_SLUG ); ?>
						</button>
						<button class="quietly-wp-list-insert__btn-primary button-large button"
							ng-click="createContent()"
							ng-show="options.view === 'insert'">
							<?php /* translators: list insert modal footer button label */ _e( 'Create Content', QUIETLY_WP_SLUG ); ?>
						</button>
						<button class="quietly-wp-list-insert__btn-primary button-large button"
							ng-click="open()"
							ng-show="options.view === 'customize'">
							<?php /* translators: list insert modal footer button label */ _e( 'Back', QUIETLY_WP_SLUG ); ?>
						</button>
						<a href class="quietly-wp-list-insert__btn-cancel submitdelete"
							ng-click="close()">
							<?php /* translators: list insert modal footer button label */ _e( 'Cancel', QUIETLY_WP_SLUG ); ?>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>