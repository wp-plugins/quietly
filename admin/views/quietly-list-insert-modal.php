<div id="quietly-wp"
	ng-cloak>
	<div class="quietly-wp-list-insert__modal quietly-wp-ng-show-fade"
		ng-show="options.show"
		list-insert-modal>
		<div class="quietly-wp-list-insert__overlay"
			ng-click="close()"></div>
		<div class="quietly-wp-list-insert__container">
			<a href class="media-modal-close" title="Close"
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
								Showing lists from <a href="{{ member._url }}" class="quietly-wp-list-insert__user-name" target="_blank" alt="View Quietly profile">{{ member.name }}</a>
								<br>
								<a href="<?php echo admin_url( 'plugins.php?page=' . QUIETLY_WP_SLUG . '#change_token' ); ?>">Not you?</a>
							</div>
						</div>
						<h1 class="quietly-wp-list-insert__header-title">Insert Quietly List</h1>
						<p class="quietly-wp-list-insert__description">
							Choose a list to insert from your Quietly account.
						</p>
						<div ng-show="lists && lists.length">
							<!-- Filters -->
							<div class="quietly-wp-list-insert__filters">
								<span class="quietly-wp-list-insert__filters-count">
									(<ng-pluralize count="listsFiltered.length"
										when="{ '0': 'none', 'one': '1 list', 'other': '{} lists' }"></ng-pluralize>)</span>
								<span>
									<a href class="quietly-wp-list-insert__filter"
										ng-class="{ '-active': options.listsFilter === '' }"
										ng-click="options.listsFilter = ''">All</a> |
									<a href class="quietly-wp-list-insert__filter"
										ng-class="{ '-active': options.listsFilter === 'published' }"
										ng-click="options.listsFilter = 'published'">Published</a> |
									<a href class="quietly-wp-list-insert__filter"
										ng-class="{ '-active': options.listsFilter === 'draft' }"
										ng-click="options.listsFilter = 'draft'">Drafts</a>
								</span>
							</div>
							<!-- Search -->
							<form name="quietlyListInsertSearch" class="quietly-wp-list-insert__search">
								<input name="search" class="quietly-wp-list-insert__search-input" placeholder="Search lists..."
									ng-model="options.listsSearch">
							</form>
						</div>
					</div>
					<div class="quietly-wp-list-insert__grid quietly-wp-list-insert__stretch">
						<div class="quietly-wp-list-insert__grid-content clearfix">
							<!-- No API token -->
							<p class="quietly-wp-list-insert__grid-none"
								ng-show="!options.hasToken">
								Please connect with your Quietly account in the plugin settings.
								<br><br>
								<a href="<?php echo admin_url( 'plugins.php?page=' . QUIETLY_WP_SLUG ); ?>" class="button-large button-primary button">
									View Plugin Settings
								</a>
							</p>
							<!-- Processing -->
							<p class="quietly-wp-list-insert__grid-none"
								ng-show="options.isProcessing">
								Hang on there, we're getting your lists...
								<br><br>
								<img src="<?php echo QUIETLY_WP_PATH_ABS . 'images/throbber-gray.gif'; ?>">
							</p>
							<!-- Failure -->
							<p class="quietly-wp-list-insert__grid-none"
								ng-show="options.error === 'unknown'">
								Bummer, there was a problem getting your awesome lists.
								<br><br>
								<a href="open()" class="button-large button-primary button">
									Try Again
								</a>
							</p>
							<!-- Invalid token -->
							<p class="quietly-wp-list-insert__grid-none"
								ng-show="options.error === '403'">
								Your API token is invalid. Please obtain a new one.
								<br><br>
								<a href="<?php echo admin_url( 'plugins.php?page=' . QUIETLY_WP_SLUG ); ?>#change_token" class="button-large button-primary button">
									Get New API Token
								</a>
							</p>
							<!-- No lists -->
							<p class="quietly-wp-list-insert__grid-none"
								ng-show="options.isLoaded && !lists.length">
								You don't have any lists on Quietly.
								<br><br>
								<a href="<?php echo QUIETLY_WP_URL . '/list/create'; ?>" target="_blank" class="button-large button-primary button">
									Compose a List
								</a>
								<a href class="button-large button"
									ng-click="open(true)">
									Refresh
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
										<h1 class="quietly-wp-list-insert__grid-item-title">{{ list.name }}</h1>
										<p class="quietly-wp-list-insert__grid-item-field">
											<ng-pluralize count="list.numberOfItems"
												when="{ '0': 'None', 'one': '1 item', 'other': '{} items' }">
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
						<h1 class="quietly-wp-list-insert__header-title">Customize Quietly Embed</h1>
					</div>
					<div class="quietly-wp-list-insert__grid quietly-wp-list-insert__stretch">
						<iframe width="100%" height="100%" frameborder="0"
							ng-src="{{ getPublishingOptionsUrl() }}"
							ng-if="options.view === 'customize' && selectedList"></iframe>
					</div>
				</div>

				<div class="quietly-wp-list-insert__footer submitbox">
					<div class="quietly-wp-list-insert__footer-wrap clearfix">
						<a href="" class="quietly-wp-list-insert__btn-primary button-large button-primary button"
							ng-class="{ 'button-disabled': !selectedList }"
							ng-click="customizeList()"
							ng-show="options.view === 'insert'">
							Customize
						</a>
						<a href class="quietly-wp-list-insert__btn-primary button-large button-primary button"
							ng-class="{ 'button-disabled': !selectedList }"
							ng-click="insertList()">
							Insert
						</a>
						<a href class="quietly-wp-list-insert__btn-primary button-large button"
							ng-click="open()"
							ng-show="options.view === 'customize'">
							Back
						</a>
						<a href class="quietly-wp-list-insert__btn-cancel submitdelete"
							ng-click="close()">
							Cancel
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>