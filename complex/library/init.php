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
 * - theme_slug
 * - prefix_constant
 * - prefix_var
 * - prefix_class
 * - prefix_hook
 * - text_domain
 *
 * @copyright  2016 WebMan Design, Oliver Juhas
 * @license    GPL-3.0, http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @link  https://github.com/webmandesign/webman-theme-framework
 * @link  http://www.webmandesign.eu
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Core
 *
 * @version  1.7
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

	// Theme version

		if ( ! defined( '{%= prefix_constant %}_THEME_VERSION' ) ) define( '{%= prefix_constant %}_THEME_VERSION', wp_get_theme( '{%= theme_slug %}' )->get( 'Version' ) );

	// Options constants

		if ( ! defined( '{%= prefix_constant %}_OPTION_CUSTOMIZER' ) ) define( '{%= prefix_constant %}_OPTION_CUSTOMIZER', 'theme_mods_{%= theme_slug %}' );

	// Dir constants

		if ( ! defined( '{%= prefix_constant %}_LIBRARY_DIR' ) ) define( '{%= prefix_constant %}_LIBRARY_DIR', trailingslashit( 'library' ) );
		if ( ! defined( '{%= prefix_constant %}_INCLUDES_DIR' ) ) define( '{%= prefix_constant %}_INCLUDES_DIR', trailingslashit( 'includes' ) );



	// Global variables

		// Theme options

			${%= prefix_var %}_theme_options = (array) get_option( {%= prefix_constant %}_OPTION_CUSTOMIZER );

			if ( empty( ${%= prefix_var %}_theme_options ) ) {
				${%= prefix_var %}_theme_options = array();
			}





/**
 * 1) Required files
 */

	// Core class

		require_once( trailingslashit( get_template_directory() ) . {%= prefix_constant %}_LIBRARY_DIR . 'includes/classes/class-core.php' );

	// Customize (has to be frontend accessible, otherwise it hides theme settings)

		require_once( trailingslashit( get_template_directory() ) . {%= prefix_constant %}_LIBRARY_DIR . 'includes/customize.php' );

	// Admin

		require_once( trailingslashit( get_template_directory() ) . {%= prefix_constant %}_LIBRARY_DIR . 'includes/admin.php' );





/**
 * 10) Init
 */

	add_action( 'after_setup_theme', '{%= prefix_class %}_Theme_Framework::init', -50 );
