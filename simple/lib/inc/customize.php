<?php
/**
 * Customizer
 *
 * @package     WebMan WordPress Theme Framework (Simple)
 * @subpackage  Customize
 *
 * @since    1.0
 * @version  2.0
 *
 * Contents:
 *
 *  1) Required files
 * 10) Hooks
 */





/**
 * 1) Required files
 */

	//Theme options arrays

		locate_template( WM_INC_DIR . 'setup-theme-options.php', true );

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

		//Customizer saving

			add_action( 'customize_save_after', 'WM_Theme_Framework_Customize::custom_styles_cache' );

		//Flushing transients

			add_action( 'switch_theme',         'WM_Theme_Framework_Customize::custom_styles_transient_flusher' );
			add_action( 'wmhook_theme_upgrade', 'WM_Theme_Framework_Customize::custom_styles_transient_flusher' );



	/**
	 * Filters
	 */

		//Minify custom CSS

			add_filter( 'wmhook_wmtf_customize_custom_styles_output_cache', 'WM_Theme_Framework::minify_css' );
