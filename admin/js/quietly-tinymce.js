/**
 * Quietly TinyMCE Plugin
 */
 /* global tinymce */
(function() {

	'use strict';

	// Load plugin specific language pack
	// tinymce.PluginManager.requireLangPack('quietly');

	tinymce.create('tinymce.plugins.quietly', {

		// WordPress plugin root url
		pluginUrl: '',

		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {

			// Get plugin root url
			this.pluginUrl = url.substring(0, url.lastIndexOf('/'));
			this.pluginUrl = this.pluginUrl.substring(0, this.pluginUrl.lastIndexOf('/') + 1);

			// TODO: More stuffs here someday...

		},

		/**
		 * Returns information about the plugin as a name/value array.
		 * The current keys are longname, author, authorurl, infourl and version.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo : function() {
			return {
				longname : 'Quietly',
				author : 'Quietly Media, Inc.',
				authorurl : 'http://quiet.ly'
			};
		}

	});

	// Register plugin
	tinymce.PluginManager.add('quietly', tinymce.plugins.quietly);

})();