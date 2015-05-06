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
 * CONTENT:
 * - 10) Hooks
 * - 20) Helpers
 * - 30) Sanitizing functions
 * - 40) Main customizer function
 * - 50) CSS styles
 *
 *
 * Contents:
 *
 *  1) Required files
 * 10) Hooks
 */





/**
 * 1) Required files
 */

	//Visual Editor class

		locate_template( WM_LIBRARY_DIR . 'inc/classes/class-wm-theme-framework-customize.php', true );





/**
 * 10) Hooks
 */

	/**
	 * Actions
	 */

		//Register customizer

			add_action( 'customize_register', 'WM_Theme_Framework_Customize::customize' );

		//Customizer assets

			add_action( 'customize_controls_enqueue_scripts', 'WM_Theme_Framework_Customize::assets' );

		//Customizer saving

			add_action( 'customize_save_after', 'wm_generate_all_css', 100 );
