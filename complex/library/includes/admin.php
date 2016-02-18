<?php
/**
 * Admin functions
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Admin
 *
 * @since    1.0
 * @version  1.1
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

		require_once( trailingslashit( get_template_directory() ) . {%= prefix_constant %}_INCLUDES_DIR . 'admin/about-page/about-page.php' );

	// Theme Updater

		if ( apply_filters( 'wmhook_{%= prefix_hook %}_update_notifier_enabled', false ) ) {
			require_once( trailingslashit( get_template_directory() ) . {%= prefix_constant %}_LIBRARY_DIR . 'includes/update-notifier.php' );
		}

	// Admin class

		require_once( trailingslashit( get_template_directory() ) . {%= prefix_constant %}_LIBRARY_DIR . 'includes/classes/class-admin.php' );





/**
 * 10) Init
 */

	add_action( 'admin_init', array( '{%= prefix_class %}_Theme_Framework_Admin', 'init' ) );
