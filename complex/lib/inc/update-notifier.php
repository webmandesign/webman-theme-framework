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
 * @since    1.0
 * @version  1.0
 *
 * Contents:
 *
 *  0) Constants
 *  1) Required files
 * 10) Init
 */





/**
 * 0) Constants
 */

	// The time interval for the remote XML cache in the database (86400 seconds = 24 hours)

		if ( ! defined( '{%= prefix_constant %}_UPDATE_NOTIFIER_CACHE_INTERVAL' ) ) define( '{%= prefix_constant %}_UPDATE_NOTIFIER_CACHE_INTERVAL', 86400 );





/**
 * 1) Required files
 */

	// Updater class

		locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/classes/class-updater.php', true );





/**
 * 10) Init
 *
 * Using `init` hook as it must be loaded before `admin_init` action is fired.
 */

	add_action( 'init', array( '{%= prefix_class %}_Theme_Framework_Updater', 'init' ) );
