<?php
/**
 * Admin functions
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Admin
 *
 * @since    1.0
 * @version  1.0
 *
 * Contents:
 *
 *  1) Required files
 * 10) Init
 */





/**
 * 1) Required files
 */

	// Load the theme About page

		locate_template( {%= prefix_constant %}_SETUP_DIR . 'about/about.php', true );

	// Theme Updater

		if ( apply_filters( 'wmhook_{%= prefix_hook %}_update_notifier_enabled', false ) ) {
			locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/update-notifier.php', true );
		}

	// Admin class

		locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/classes/class-admin.php', true );





/**
 * 10) Init
 */

	add_action( 'admin_init', array( '{%= prefix_class %}_Theme_Framework_Admin', 'init' ) );
