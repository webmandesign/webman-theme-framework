<?php
/**
 * WebMan WordPress Theme Framework (Simple)
 *
 * Textdomain used in the framework: {%= text_domain %}
 *
 * Theme options with `__` prefix (`get_theme_mod( '__option_id' )`) are theme
 * settup related options and can not be edited via customizer. Using this method
 * not to create non-sense multiple options in a database.
 *
 * Custom hooks naming convention:
 * - `wmhook_{%= prefix_hook %}_` - global (and other, such as plugins related) hooks
 * - `wmhook_{%= prefix_hook %}_tf_` - theme framework specific hooks (core specific)
 * - `wmhook_{%= prefix_hook %}_tf_admin_` - class method specific hooks
 * - `wmhook_{%= prefix_hook %}_tf_customize_` - class method specific hooks
 * - `wmhook_{%= prefix_hook %}_tf_editor_` - class method specific hooks
 *
 * Used global hooks:
 * @uses  `wmhook_{%= prefix_hook %}_theme_options`
 * @uses  `wmhook_{%= prefix_hook %}_custom_styles`
 * @uses  `wmhook_{%= prefix_hook %}_esc_css`
 *
 * Used development prefixes:
 * - prefix_constant
 * - prefix_var
 * - prefix_class
 * - prefix_fn
 * - prefix_hook
 * - text_domain
 *
 * @copyright  2015 WebMan - Oliver Juhas
 * @license    GPL-2.0+, http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @link  https://github.com/webmandesign/webman-theme-framework
 * @link  http://www.webmandesign.eu
 *
 * @package     WebMan WordPress Theme Framework (Simple)
 * @subpackage  Core
 *
 * @version  5.0
 *
 * Contents:
 *
 *  0) Constants
 *  1) Required files
 * 10) Hooks
 */





/**
 * 0) Constants
 */

	// Basic constants

		if ( ! defined( '{%= prefix_constant %}_THEME_SLUG' ) ) define( '{%= prefix_constant %}_THEME_SLUG',  str_replace( array( '-lite', '-plus' ), '', get_template() ) );

	// Dir constants

		if ( ! defined( '{%= prefix_constant %}_INC_DIR' ) )     define( '{%= prefix_constant %}_INC_DIR',     trailingslashit( 'inc' )     );
		if ( ! defined( '{%= prefix_constant %}_LIBRARY_DIR' ) ) define( '{%= prefix_constant %}_LIBRARY_DIR', trailingslashit( 'inc/lib' ) );





/**
 * 1) Required files
 */

	// Main theme action hooks

		locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/hooks/hooks.php', true );

	// Main class

		locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/classes/class-core.php', true );





/**
 * 10) Hooks
 */

	/**
	 * Actions
	 */

		// Theme upgrade action

			add_action( 'init', '{%= prefix_class %}_Theme_Framework::theme_upgrade' );

		// Flushing transients

			add_action( 'switch_theme',  '{%= prefix_class %}_Theme_Framework::image_ids_transient_flusher'      );
			add_action( 'edit_category', '{%= prefix_class %}_Theme_Framework::all_categories_transient_flusher' );
			add_action( 'save_post',     '{%= prefix_class %}_Theme_Framework::all_categories_transient_flusher' );




	/**
	 * Filters
	 */

		// Escape inline CSS

			add_filter( 'wmhook_{%= prefix_hook %}_esc_css', '{%= prefix_class %}_Theme_Framework::esc_css' );

		// Widgets improvements

			add_filter( 'show_recent_comments_widget_style', '__return_false'                        );
			add_filter( 'widget_text',                       'do_shortcode'                          );
			add_filter( 'widget_title',                      '{%= prefix_class %}_Theme_Framework::html_widget_title' );

			remove_filter( 'widget_title', 'esc_html' );

		// Table of contents

			add_filter( 'the_content', '{%= prefix_class %}_Theme_Framework::add_table_of_contents', 10 );
