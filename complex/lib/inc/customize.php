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
 * 10) Hooks
 */





/**
 * 1) Required files
 */

	// Visual Editor class

		locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/classes/class-customize.php', true );





/**
 * 10) Hooks
 */

	/**
	 * Actions
	 */

		// Register customizer

			add_action( 'customize_register', '{%= prefix_class %}_Theme_Framework_Customize::init' );

		// Customizer assets

			add_action( 'customize_controls_enqueue_scripts', '{%= prefix_class %}_Theme_Framework_Customize::assets' );

		// Customizer saving

			add_action( 'customize_save_after', '{%= prefix_class %}_Theme_Framework::generate_all_css', 98 );
