<?php
/**
 * Admin functions
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Admin
 *
 * @since    1.2
 * @version  1.2
 *
 * Contents:
 *
 *  0) Requirements check
 *  1) Required files
 * 10) Init
 */





/**
 * 0) Requirements check
 */

	if ( ! is_admin() ) {
		return;
	}





/**
 * 1) Required files
 */

	// Load the theme About page

		locate_template( {%= prefix_constant %}_INCLUDES_DIR . 'admin/about-page/about-page.php' );

	// Admin class

		require_once( trailingslashit( get_template_directory() ) . {%= prefix_constant %}_LIBRARY_DIR . 'includes/classes/class-admin.php' );





/**
 * 10) Init
 */

	add_action( 'admin_init', array( '{%= prefix_class %}_Theme_Framework_Admin', 'init' ) );
