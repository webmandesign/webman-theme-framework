<?php
/**
 * Customizer
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    3.0
 * @version  5.0
 *
 * Contents:
 *
 *  1) Required files
 * 10) Init
 */





/**
 * 1) Required files
 */

	// Visual Editor class

		locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/classes/class-customize.php', true );





/**
 * 10) Init
 */

	add_action( 'after_setup_theme', array( '{%= prefix_class %}_Theme_Framework_Customize', 'init' ) );
