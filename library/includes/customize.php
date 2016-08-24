<?php
/**
 * Customizer
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    1.0
 * @version  1.8
 *
 * Contents:
 *
 *  1) Required files
 * 10) Init
 */





/**
 * 1) Required files
 */

	// CSS Styles Generator class

		require_once( trailingslashit( get_template_directory() ) . {%= prefix_constant %}_LIBRARY_DIR . 'includes/classes/class-generate-styles.php' );

	// Customize class

		require_once( trailingslashit( get_template_directory() ) . {%= prefix_constant %}_LIBRARY_DIR . 'includes/classes/class-customize.php' );





/**
 * 10) Init
 */

	add_action( 'after_setup_theme', '{%= prefix_class %}_Theme_Framework_Generate_Styles::init' );

	add_action( 'after_setup_theme', '{%= prefix_class %}_Theme_Framework_Customize::init' );
