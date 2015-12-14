<?php
/**
 * WebMan WordPress Theme Framework
 *
 * Textdomain used in the framework: {%= text_domain %}
 *
 * Theme options with `__` prefix (`get_theme_mod( '__option_id' )`) are theme
 * setup related options and can not be edited via customizer. This way we prevent
 * creating non-sense multiple options in a database.
 *
 * Custom hooks naming convention:
 * - `wmhook_{%= prefix_hook %}_` - global (and other, such as plugins related) hooks
 * - `wmhook_{%= prefix_hook %}_tf_` - theme framework specific hooks (core)
 * - `wmhook_{%= prefix_hook %}_tf_admin_` - class method specific hooks
 * - `wmhook_{%= prefix_hook %}_tf_customize_` - class method specific hooks
 * - `wmhook_{%= prefix_hook %}_tf_editor_` - class method specific hooks
 * - `wmhook_{%= prefix_hook %}_tf_updater_` - class method specific hooks
 * - `tha_` - Theme Hook Alliance specific hooks
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
 * @version  1.0.7
 *
 * Contents:
 *
 *  0) Constants
 *  1) Required files
 * 10) Init
 */





/**
 * 0) Constants
 */

	// Basic constants

		if ( ! defined( '{%= prefix_constant %}_THEME_SLUG' ) ) define( '{%= prefix_constant %}_THEME_SLUG', str_replace( array( '-lite', '-plus' ), '', get_template() ) );

	// Options constants

		if ( ! defined( '{%= prefix_constant %}_OPTION_CUSTOMIZER' ) ) define( '{%= prefix_constant %}_OPTION_CUSTOMIZER', 'theme_mods_' . {%= prefix_constant %}_THEME_SLUG );

	// Dir constants

		if ( ! defined( '{%= prefix_constant %}_LIBRARY_DIR' ) ) define( '{%= prefix_constant %}_LIBRARY_DIR', trailingslashit( 'library' ) );
		if ( ! defined( '{%= prefix_constant %}_SETUP_DIR' ) )   define( '{%= prefix_constant %}_SETUP_DIR',   trailingslashit( 'setup' )   );



	// Global variables

		// Theme options

			${%= prefix_var %}_theme_options = (array) get_option( {%= prefix_constant %}_OPTION_CUSTOMIZER );

			if ( empty( ${%= prefix_var %}_theme_options ) ) {
				${%= prefix_var %}_theme_options = array();
			}





/**
 * 1) Required files
 */

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

	// Core class

		locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/classes/class-core.php', true );





/**
 * 10) Init
 */

	add_action( 'after_setup_theme', array( '{%= prefix_class %}_Theme_Framework', 'init' ), -100 );
