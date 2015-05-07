<?php
/**
 * Theme update notifier
 *
 * Provides a notification to the user everytime the WordPress theme is updated.
 * This script is disabled by default.
 * Used only in themes not available via WordPress.org.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Updater
 *
 * @since    3.0
 * @version  5.0
 *
 * Contents:
 *
 *  0) Constants
 *  1) Required files
 * 10) Hooks
 */





/**
 * 0) Constants
 */

	//The time interval for the remote XML cache in the database (86400 seconds = 24 hours)

		if ( ! defined( 'WM_UPDATE_NOTIFIER_CACHE_INTERVAL' ) ) define( 'WM_UPDATE_NOTIFIER_CACHE_INTERVAL', 86400 );





/**
 * 1) Required files
 */

	//Updater class

		locate_template( WM_LIBRARY_DIR . 'inc/classes/class-wm-theme-framework-updater.php', true );





/**
 * 10) Hooks
 */

	/**
	 * Actions
	 */

		//Admin menu

			add_action( 'admin_menu', 'WM_Theme_Framework_Updater::menu', 998 );

		//Toolbar

			add_action( 'admin_bar_menu', 'WM_Theme_Framework_Updater::toolbar', 998 );
