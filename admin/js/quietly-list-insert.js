/**
 * Quietly List Insert Module
 */
/* global tinyMCE */

(function(angular, app, $) {

	'use strict';

	if (!angular || !app) { return; }

	/**
	 * List Insert Controller
	 */
	app.controller('ListInsertCtrl', function($scope, $filter, config, api) {

		var logPrefix = '[Quietly List Insert] ';
		/**
		 * Options model.
		 * @type {Object}
		 */
		$scope.options = {
			show: false,
			hasToken: config.hasToken,
			isProcessing: false,
			isLoaded: false,
			error: '',
			view: 'insert',
			embedCode: '',
			codeTemplate: '[embed]http:' + config.quietlyUrl + '/list/share/$1[/embed]',
			publishingOptionsUrl: config.quietlyUrl + '/publishing/',
			listsSearch: '',
			listsFilter: '',
			listPlaceholder: config.pluginUrl + 'images/empty.png',
			profilePlaceholder: config.pluginUrl + 'images/placeholder-profile.png'
		};

		/**
		 * Lists model.
		 * @type {Array}
		 */
		$scope.lists = [];

		/**
		 * Filtered lists model.
		 * @type {Array}
		 */
		$scope.listsFiltered = [];

		/**
		 * Member model.
		 * @type {Object}
		 */
		$scope.member = null;

		/**
		 * Selected list model.
		 * @type {Object}
		 */
		$scope.selectedList = null;

		/**
		 * Initializes the controller.
		 */
		$scope.init = function() {
			// Update filtered lists model
			$scope.$watchGroup([ 'options.listsSearch', 'options.listsFilter', 'lists'], function() {
				$scope.listsFiltered = $filter('orderBy')($filter('filter')($scope.lists, {
					name: $scope.options.listsSearch,
					status: $scope.options.listsFilter
				}), '-modified');
			});
		};

		/**
		 * Opens the modal.
		 */
		$scope.open = function(refresh){
			$scope.options.show = true;
			$scope.options.view = 'insert';
			if (refresh && refresh === true) {
				$scope.options.isLoaded = false;
			}
			if ($scope.options.hasToken && !$scope.options.isLoaded && !$scope.options.isProcessing) {
				$scope.options.isProcessing = true;
				api.post('quietly_api_get_lists', {}, function(data) {
					if (!data.data ||
						!angular.isArray(data.data.lists) ||
						!angular.isObject(data.data.member)) {
						console.log(logPrefix + 'Invalid response data:', data);
						$scope.options.isProcessing = false;
						$scope.options.error = 'unknown';
						return;
					}
					// Parse lists model
					$scope.lists = data.data.lists;
					angular.forEach($scope.lists, function(list) {
						list._previewImage = list.thumbnailImage || $scope.options.listPlaceholder;
						if (list.shareId) {
							list._code = $scope.options.codeTemplate.replace('$1', list.shareId);
						}
						console.log(list._code, list.shareId);
					});
					// Parse member model
					$scope.member = data.data.member;
					$scope.member._thumbnailImage = $scope.member.thumbnailImage || $scope.options.profilePlaceholder;
					$scope.member._url = ($scope.member.memberId) ? config.quietlyUrl + '/' + $scope.member.memberId : '#';
					$scope.options.isProcessing = false;
					$scope.options.isLoaded = true;
					$scope.options.error = '';
				}, function(data, status) {
					console.log(data, status);
					$scope.options.isProcessing = false;
					if (status === 403) {
						$scope.options.error = '403';
						return;
					}
					$scope.options.error = 'unknown';
				});
			}
		};

		/**
		 * Closes the modal.
		 */
		$scope.close = function() {
			$scope.options.show = false;
		};

		/**
		 * Gets the Quietly publishing options url.
		 * @return {string} The url.
		 */
		$scope.getPublishingOptionsUrl = function() {
			return $scope.options.publishingOptionsUrl + $scope.selectedList.listId + '?is_wordpress=true&origin=' + encodeURIComponent(config.siteUrl);
		};

		/**
		 * Marks a list as selected.
		 * @param {Object} list - The list model.
		 * @return {Object} The selected list model.
		 */
		$scope.selectList = function(list) {
			$scope.selectedList = list || null;
			$scope.options.embedCode = $scope.selectedList._code || '';
			return $scope.selectedList;
		};

		/**
		 * Inserts the selected list into the WordPress editor.
		 * @param {Object} list - The list model.
		 * @return {Object} The inserted list model.
		 */
		$scope.insertList = function(list) {
			if (list) {
				$scope.selectList(list);
			}
			if (!$scope.selectedList) {
				console.error(logPrefix + 'Invalid list.');
			}
			if (!$scope.options.embedCode) {
				console.error(logPrefix + 'List is missing its embed code.');
				$scope.close();
				return;
			}
			$scope.insertIntoEditor($scope.options.embedCode);
			$scope.close();
			return $scope.selectedList;
		};

		/**
		 * Inserts a text into the active TinyMCE editor.
		 * @param {string} text - The text.
		 * @return {Boolean} true if successful.
		 */
		$scope.insertIntoEditor = function(text) {
			if(tinyMCE && tinyMCE.activeEditor) {
				tinyMCE.activeEditor.execCommand('mceInsertContent', false, text);
				return true;
			}
			return false;
		};

		/**
		 * Shows the embed customization screen.
		 */
		$scope.customizeList = function() {
			if ($scope.selectedList) {
				$scope.options.view = 'customize';
			}
		};

	});

	/**
	 * List Insert Modal Directive
	 * list-insert-modal
	 */
	app.directive('listInsertModal', function(config) {
		return {
			controller: 'ListInsertCtrl',
			link: function(scope, element) {

				var origin = new RegExp('^https?:' + config.quietlyUrl.replace(/\//g, '\\/')),
					$window = $(window),
					$body = $('body'),
					$container = $('.quietly-wp-list-insert__container', element),
					$header = $('.quietly-wp-list-insert__header', element),
					$footer= $('.quietly-wp-list-insert__footer-wrap', element),
					$stretch = $('.quietly-wp-list-insert__stretch', element);

				/**
				 * Fixes the modal content layout.
				 */
				function fixLayout() {
					setTimeout(function() {
						$stretch.each(function() {
							$(this).css('height', $container.height() - $(this).siblings($header).outerHeight() - $footer.outerHeight());
						});
					});
				}

				/**
				 * Handles post message events that updates the embed code.
				 * @param {Event} event - The event object.
				 */
				function handlePostMessage(event) {
					var evt = event.originalEvent,
						data = null;
					if (origin.test(evt.origin)) {
						if (evt.data) {
							try {
								data = JSON.parse(evt.data);
							} catch (ex) {}
						}
						if (!data) {
							return;
						}
						if (data.action &&
							data.action === 'code' &&
							data.code &&
							data.code.length) {
							scope.options.embedCode = data.code;
							console.log('WP! code', scope.options.embedCode);
							scope.$apply();
						}
					}
				}

				// Capture open command from TinyMCE
				$body.on('quietly', function(event) {
					var detail = event.originalEvent.detail;
					if (detail.hasOwnProperty('action') && detail.action === 'openListInsertModal') {
						if (angular.isFunction(scope.open)) {
							scope.open();
							scope.$apply();
						}
					}
				});

				// Watch for view change
				scope.$watch('options.view', function(view) {
					if (view === 'customize') {
						$window.on('message', handlePostMessage);
					} else {
						$window.off('message', handlePostMessage);
					}
				});

				// Watch for changes to fix layout
				scope.$watchGroup([ 'lists', 'options.view', 'options.show' ], fixLayout);
				$window.on('resize', fixLayout);
				fixLayout();

				// Initialize the controller
				scope.init();

			}
		};
	});

})(window.quietlyWP.angular, window.quietlyWP.app, jQuery);