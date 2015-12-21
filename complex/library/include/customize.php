<?php
/**
 * Customizer
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    1.0
 * @version  1.0.8
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

		locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'include/classes/class-customize.php', true );





/**
 * 10) Init
 */

	add_action( 'after_setup_theme', array( '{%= prefix_class %}_Theme_Framework_Customize', 'init' ) );
