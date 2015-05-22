<?php
/**
 * WebMan WordPress Theme Framework
 *
 * Textdomain used in the framework: {%= text_domain %}
 *
 * Custom hooks naming convention:
 * - `wmhook_{%= prefix_hook %}_` - global (and other, such as plugins related) hooks
 * - `wmhook_{%= prefix_hook %}_tha_` - Theme Hook Alliance specific hooks
 * - `wmhook_{%= prefix_hook %}_tf_` - theme framework specific hooks (core)
 * - `wmhook_{%= prefix_hook %}_tf_admin_` - class method specific hooks
 * - `wmhook_{%= prefix_hook %}_tf_customize_` - class method specific hooks
 * - `wmhook_{%= prefix_hook %}_tf_editor_` - class method specific hooks
 * - `wmhook_{%= prefix_hook %}_tf_updater_` - class method specific hooks
 *
 * Used global hooks:
 * @uses  `wmhook_{%= prefix_hook %}_theme_options`
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
 * @package     WebMan WordPress Theme Framework
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

	// Options constants

		if ( ! defined( '{%= prefix_constant %}_OPTION_CUSTOMIZER' ) ) define( '{%= prefix_constant %}_OPTION_CUSTOMIZER', 'theme_mods_' . {%= prefix_constant %}_THEME_SLUG );

	// Dir constants

		if ( ! defined( '{%= prefix_constant %}_LIBRARY_DIR' ) ) define( '{%= prefix_constant %}_LIBRARY_DIR', trailingslashit( 'lib' )   );
		if ( ! defined( '{%= prefix_constant %}_SETUP_DIR' ) )   define( '{%= prefix_constant %}_SETUP_DIR',   trailingslashit( 'setup' ) );



	// Global variables

		// Theme options

			${%= prefix_var %}_theme_options = (array) get_option( {%= prefix_constant %}_OPTION_CUSTOMIZER );

			if ( empty( ${%= prefix_var %}_theme_options ) ) {
				${%= prefix_var %}_theme_options = array();
			}





/**
 * 1) Required files
 */

	// Main theme action hooks

		locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/hooks/hooks.php', true );

	// Customize (has to be fontend accessible, otherwise it hides theme settings)

		locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/customize.php', true );

	// Admin required files

		if ( is_admin() ) {

			// WP admin functionality

				locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/admin.php', true );

			// Plugins suggestions

				if (
						apply_filters( 'wmhook_{%= prefix_hook %}_plugins_suggestion_enabled', true )
						&& locate_template( {%= prefix_constant %}_SETUP_DIR . 'tgmpa/plugins.php' )
					) {
					locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/external/class-tgm-plugin-activation.php', true );
					locate_template( {%= prefix_constant %}_SETUP_DIR . 'tgmpa/plugins.php', true );
				}

		}

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

		// Contextual help

			add_action( 'contextual_help', '{%= prefix_class %}_Theme_Framework::contextual_help', 10, 3 );

		// Toolbar (also displayed on front end)

			add_action( 'admin_bar_menu', '{%= prefix_class %}_Theme_Framework::toolbar', 998 );



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

		// Minify CSS

			add_filter( 'wmhook_{%= prefix_hook %}_tf_generate_main_css_output_min', '{%= prefix_class %}_Theme_Framework::minify_css', 10 );
