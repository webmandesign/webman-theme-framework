<?php
/**
 * Theme Update Notifier
 *
 * Provides a notification when a WordPress theme is updated.
 * Use only in themes not available via WordPress.org repository.
 * Based on https://github.com/unisphere/unisphere_notifier by Joao Araujo.
 *
 * @copyright  WebMan Design, Oliver Juhas
 * @license    GPL-3.0, http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @link  https://github.com/webmandesign/webman-theme-framework
 * @link  http://www.webmandesign.eu
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Update Notifier
 *
 * @version  2.2.5
 *
 * Used development prefixes:
 *
 * @uses theme_slug
 * @uses prefix_constant
 * @uses prefix_var
 * @uses prefix_class
 * @uses prefix_hook
 * @uses text_domain
 *
 * Contents:
 *
 *  1) Requirements check
 * 10) Constants
 * 20) Required files
 */





/**
 * 1) Requirements check
 */

	if (
			! is_admin()
			|| ! defined( '{%= prefix_constant %}_PATH_INCLUDES' )
		) {
		return;
	}





/**
 * 10) Constants
 */

	// The time interval for the remote XML cache in the database (86400 seconds = 24 hours)

		if ( ! defined( '{%= prefix_constant %}_UPDATE_NOTIFIER_CACHE_INTERVAL' ) ) {
			define( '{%= prefix_constant %}_UPDATE_NOTIFIER_CACHE_INTERVAL', 86400 );
		}





/**
 * 20) Required files
 */

	require trailingslashit( {%= prefix_constant %}_PATH_INCLUDES . basename( dirname( __FILE__ ) ) ) . 'class-update-notifier.php';
