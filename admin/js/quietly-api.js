/**
 * Quietly API Interface
 */

(function(angular, app) {

	'use strict';

	if (!angular || !app) { return; }

	app.factory('api', function($http, config) {

		var logPrefix = '[Quietly API] ',
			apiUrl = config.apiUrl,
			nonce = config.nonce;

		/**
		 * Handle server error response.
		 * @param {Object} data - The server response object.
		 * @param {number} status - The status code.
		 * @return {string} The error output.
		 */
		function handleErrors(data, status) {
			var output = logPrefix + status + ' ';
			// Check for error message
			if (data.hasOwnProperty('message')) {
				if (angular.isObject(data.message)) {
					// Show detailed message for object-based response
					output += '\n';
					angular.forEach(data.message, function(message) {
						output += message;
					});
				} else {
					output += data.message;
				}
			} else {
				output += 'Unknown error.';
			}
			console.error(output);
			return output;
		}

		/**
		 * Make a call to the WordPress back-end.
		 * @param {string} action - The action name.
		 * @param {Object} data - The post data.
		 * @param {function} success - Success callback.
		 * @param {function} error - Error callback.
		 * @return {HTTPPromise} Future object.
		 */
		function post(action, data, success, error) {
			var request = $http.post(apiUrl + '?action=' + action, {
				nonce: nonce,
				data: data
			});
			if (angular.isFunction(success)) {
				request.success(success);
			} else {
				request.success(function(data) {
					console.log(logPrefix + 'Call was a success:', data);
				});
			}
			if (angular.isFunction(error)) {
				request.error(error);
			} else {
				request.error(handleErrors);
			}
		}

		return {
			post: post
		};

	});

})(window.quietlyWP.angular, window.quietlyWP.app);