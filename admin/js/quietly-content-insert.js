/**
 * Quietly Content Insert Module
 */
/* global tinyMCE */

(function(angular, app, $) {

	'use strict';

	if (!angular || !app) { return; }

	// Bind insert button click handler
	$('#quietly-wp-btn-insert-content').on('click', function handleInsertButtonClick(event) {
		var evt = document.createEvent('CustomEvent');
		evt.initCustomEvent('quietly', false, false, {
			action: 'openContentInsertModal'
		});
		document.body.dispatchEvent(evt);
		event.preventDefault();
	});

	/**
	 * Controller
	 */
	app.controller('ContentInsertCtrl', function($scope, $filter, config, api) {

		var logPrefix = '[Quietly Content Insert] ',
			contentURL = config.quietlyUrl + '/content?origin=' + encodeURIComponent(config.siteUrl),
			loginURL = config.quietlyUrl + '/api_token_login?api_token=' + config.apiToken + '&url=',
			profilePlaceholder = config.pluginUrl + 'images/placeholder-profile.png';

		/**
		 * Options model.
		 * @type {Object}
		 */
		$scope.options = {
			show: false,
			iframeURL: loginURL + encodeURIComponent(contentURL),
			hasToken: config.hasToken,
			isProcessing: false,
			isLoaded: false,
			error: ''
		};

		/**
		 * Content model.
		 * @type {string}
		 */
		$scope.content = '';

		/**
		 * Member model.
		 * @type {Object}
		 */
		$scope.member = null;

		/**
		 * Opens the modal.
		 */
		$scope.open = function(){
			$scope.options.show = true;
			$scope.options.error = '';
			$scope.content = '';
			if ($scope.options.hasToken && !$scope.options.isLoaded && !$scope.options.isProcessing) {
				$scope.options.isProcessing = true;
				// Get member data
				api.post('quietly_api_get_member', {}, function(data) {
					if (!data.data) {
						console.log(logPrefix + 'Invalid response data:', data);
						$scope.options.isProcessing = false;
						$scope.options.error = 'unknown';
						return;
					}
					// Parse member model
					$scope.member = data.data;
					$scope.member._thumbnailImage = $scope.member.thumbnailImage || profilePlaceholder;
					$scope.member._url = ($scope.member.memberId) ? config.quietlyUrl + '/' + $scope.member.memberId : '#';
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
		 * Returns the Quietly iframe URL.
		 * @return {string} The url.
		 */
		$scope.getFrameURL = function() {
			return ;
		};

		/**
		 * Inserts the content.
		 */
		$scope.insert = function() {
			if (!$scope.content) {
				console.error(logPrefix, 'Missing embed code!');
				$scope.close();
				return null;
			}
			if (typeof $scope.content === 'string') {
				// Insert embed code
				$scope.insertIntoEditor($scope.content);
			} else if ($scope.content.hasOwnProperty('title') && $scope.content.hasOwnProperty('body')) {
				// Insert story
				if (window.confirm(config.i18n.insertContentConfirmStory)) {
					$scope.replaceEditorContent($scope.content.title, $scope.content.body);
				}
			}
			$scope.close();
		};

		/**
		 * Inserts a text into the active TinyMCE editor.
		 * @param {string} text - The text.
		 */
		$scope.insertIntoEditor = function(text) {
			if(tinyMCE && tinyMCE.activeEditor && !tinyMCE.activeEditor.hidden) {
				tinyMCE.activeEditor.execCommand('mceInsertContent', false, text);
			} else {
				// Insert into Quicktags editor
				edInsertContent(false, text); // jshint ignore:line
			}
		};

		/**
		 * Replaces the post title and content.
		 * @param {string} title - New post title.
		 * @param {string} content - New post content.
		 */
		$scope.replaceEditorContent = function(title, content) {
			if (title && typeof title === 'string' && title.length) {
				$('#title').val(title);
			}
			if (content && typeof content === 'string' && content.length) {
				if(tinyMCE && tinyMCE.activeEditor && !tinyMCE.activeEditor.hidden) {
					tinyMCE.activeEditor.setContent(content);
				} else {
					$('#content').val(content); // jshint ignore:line
				}
			}
		};

	});

	/**
	 * Modal Directive
	 * content-insert-modal
	 */
	app.directive('contentInsertModal', function(config) {
		return {
			controller: 'ContentInsertCtrl',
			link: function(scope, element) {

				var origin = config.quietlyUrl,
					$window = $(window),
					$body = $('body'),
					$container = $('.quietly-wp-content-insert__container', element),
					$header = $('.quietly-wp-content-insert__header', element),
					$footer= $('.quietly-wp-content-insert__footer-wrap', element),
					$stretch = $('.quietly-wp-content-insert__stretch', element);

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
							} catch (e) {
								return;
							}
						}
						if (!data) {
							return;
						}
						if (data.action === 'code') {
							if (data.code && data.code.length) {
								scope.content = data.code;
							} else {
								scope.content = '';
							}
							if (data.insert === true) {
								scope.insert();
							}
							scope.$apply();
						} else if (data.action === 'story') {
							scope.content = {
								title: data.title || '',
								body: data.body || ''
							};
							if (data.insert === true) {
								scope.insert();
							}
							scope.$apply();
						} else if (data.action === 'unload') {
							scope.content = '';
							scope.$apply();
						}
					}
				}

				// Capture open command from TinyMCE
				$body.on('quietly', function(event) {
					var detail = event.originalEvent.detail;
					if (detail.hasOwnProperty('action') && detail.action === 'openContentInsertModal') {
						if (angular.isFunction(scope.open)) {
							scope.open();
							scope.$apply();
						}
					}
				});

				// Watch for view change
				scope.$watch('options.show', function(newVal) {
					if (newVal === true) {
						$window.on('message', handlePostMessage);
					} else {
						$window.off('message', handlePostMessage);
					}
				});

				// Watch for frame loading state
				scope.$watchGroup([ 'options.show', 'options.isLoaded' ], function() {
					if (scope.options.show && scope.options.isLoaded) {
						$('#quietly-wp-content-insert-iframe').on('load', function() {
							$('#quietly-wp-content-insert-iframe-wrap').removeClass('-loading');
						});
					}
				});

				/**
				 * Shows all content in iframe.
				 */
				scope.resetFrame = function() {
					$('#quietly-wp-content-insert-iframe').attr( 'src', function (i, val) {
						return val;
					});
				};

				// Watch for changes to fix layout
				scope.$watchGroup([ 'options.show', 'options.isLoaded' ], fixLayout);
				$window.on('resize', fixLayout);
				fixLayout();

			}
		};
	});

})(window.quietlyWP.angular, window.quietlyWP.app, jQuery);