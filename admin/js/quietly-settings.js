/**
 * Quietly Settings Interactions
 */

jQuery(function($) {

	'use strict';

	var logPrefix = '[Quietly Settings] ',
		loginUrl = '/login?action=get_api_token_wp',
		$blkChangeToken = $('#quietly-blk-change-token'),
		$blkOnboarding = $('#quietly-blk-onboarding'),
		$btnAPIToken = $('.quietly-btn-get-api-token'),
		$noticeAPIToken = $('#setting-error-updated-api-token');

	// Check for required data
	if (!window.quietlyWP ||
		!window.quietlyWP.quietlyUrl) {
		console.error(logPrefix + 'Missing required data.');
		return false;
	}

	// Check for page state from hash
	switch(window.location.hash) {
		case '#change_token':
			$blkChangeToken.show();
			window.location.hash = '';
			break;
	}

	// Hide onboarding if API token was not updated
	if ($noticeAPIToken.length) {
		$blkOnboarding.removeAttr('style');
	}

	loginUrl = 'http://' + window.quietlyWP.quietlyUrl + loginUrl;

	// Handle Get API Token button click
	$btnAPIToken.on('click', function(event) {
		if (!$(this).val().length) {
			window.open( loginUrl, '_blank', 'scrollbars=1,width=400,height=200');
		}
		event.preventDefault();
	});

});