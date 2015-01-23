/**
 * Quietly List Insert Module
 */
/* global tinyMCE */

(function(angular, app, $) {

	'use strict';

	if (!angular || !app) { return; }

	// Bind insert button click handler
	$('#quietly-wp-btn-insert-list').on('click', function handleInsertButtonClick(event) {
		var evt = document.createEvent('CustomEvent');
		evt.initCustomEvent('quietly', false, false, {
			action: 'openListInsertModal'
		});
		document.body.dispatchEvent(evt);
		event.preventDefault();
	});

	/**
	 * List Insert Controller
	 */
	app.controller('ListInsertCtrl', function($scope, $filter, config, api, logger) {

		var logPrefix = '[Quietly List Insert] ',
			codeTemplate = config.quietlyUrl + '/list/share/$1',
			publishingOptionsUrl = config.quietlyUrl + '/publishing/',
			loginUrl = config.quietlyUrl + '/api_token_login?api_token=' + config.apiToken + '&url=',
			listPlaceholder = config.pluginUrl + 'images/empty.png',
			profilePlaceholder = config.pluginUrl + 'images/placeholder-profile.png';

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
			listsSearch: '',
			listsFilter: ''
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
		 * Embed settings model.
		 * @type {Object}
		 */
		$scope.settings = {
			embedType: 'Embed',
			params: ''
		};

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
			$scope.options.error = '';
			if (refresh && refresh === true) {
				$scope.options.isLoaded = false;
			}
			if ($scope.options.hasToken && !$scope.options.isLoaded && !$scope.options.isProcessing) {
				$scope.options.isProcessing = true;
				// Get member, lists, and embed settings data
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
						list._previewImage = list.thumbnailImage || listPlaceholder;
						if (list.shareId) {
							list._code = codeTemplate.replace('$1', list.shareId);
						}
					});
					// Parse member model
					$scope.member = data.data.member;
					$scope.member._thumbnailImage = $scope.member.thumbnailImage || profilePlaceholder;
					$scope.member._url = ($scope.member.memberId) ? config.quietlyUrl + '/' + $scope.member.memberId : '#';
					// Parse settings model
					if (data.data.settings && data.data.settings.length) {
						$scope.settings.params = encodeURIComponent('&' + data.data.settings);
					}
					// Settle down
					$scope.options.isProcessing = false;
					$scope.options.isLoaded = true;
					$scope.options.error = '';
				}, function(data, status) {
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
			return loginUrl + encodeURIComponent(publishingOptionsUrl + $scope.selectedList.listId + '?is_wordpress=true&origin=' + config.siteUrl);
		};

		/**
		 * Marks a list as selected.
		 * @param {Object} list - The list model.
		 * @return {Object} The selected list model.
		 */
		$scope.selectList = function(list) {
			// Generate a settingsId here
			var settingsId = Math.random().toString(36).slice(10);

			$scope.selectedList = list || null;
			$scope.options.embedCode = '[embed]' + ($scope.selectedList._code || '') + '?settingsId=' + settingsId + (($scope.settings.params) ? $scope.settings.params : '') + '[/embed]';
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
			if(tinyMCE && tinyMCE.activeEditor && !tinyMCE.activeEditor.hidden) {
				tinyMCE.activeEditor.execCommand('mceInsertContent', false, text);
				return true;
			} else {
				// Insert into Quicktags editor
				edInsertContent(false, text); // jshint ignore:line
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

				var origin = config.quietlyUrl,
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
					if (origin === evt.origin) {
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
							if (data.settings) {
								scope.settings.params = data.settings;
							}
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