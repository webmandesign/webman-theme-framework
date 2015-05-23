<?php
/**
 * Customizer
 *
 * @package     WebMan WordPress Theme Framework (Simple)
 * @subpackage  Customize
 *
 * @since    1.0
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

	// Theme options arrays

		locate_template( {%= prefix_constant %}_INC_DIR . 'setup-theme-options.php', true );

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

		// Customizer saving

			add_action( 'customize_save_after', '{%= prefix_class %}_Theme_Framework::custom_styles_cache' );

		// Flushing transients

			add_action( 'switch_theme', '{%= prefix_class %}_Theme_Framework::custom_styles_transient_flusher' );
			add_action( 'wmhook_{%= prefix_hook %}_tf_theme_upgrade', '{%= prefix_class %}_Theme_Framework::custom_styles_transient_flusher' );



	/**
	 * Filters
	 */

		// Minify custom CSS

			add_filter( 'wmhook_{%= prefix_hook %}_tf_custom_styles_output_cache', '{%= prefix_class %}_Theme_Framework::minify_css' );
