<?php
/**
 * WebMan WordPress Theme Framework
 *
 * Theme options with `__` prefix (`get_theme_mod( '__option_id' )`) are theme
 * setup related options and can not be edited via customizer.
 * This way we prevent creating additional options in the database.
 *
 * @copyright  WebMan Design, Oliver Juhas
 * @license    GPL-3.0, http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @link  https://github.com/webmandesign/webman-theme-framework
 * @link  http://www.webmandesign.eu
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Core
 *
 * @version  1.8.1
 *
 * Used global hooks:
 *
 * @uses  wmhook_{%= prefix_hook %}_theme_options
 * @uses  wmhook_{%= prefix_hook %}_esc_css
 * @uses  wmhook_{%= prefix_hook %}_custom_styles
 *
 * Used development prefixes:
 *
 * @uses theme_slug
 * @uses prefix_constant
 * @uses prefix_var
 * @uses prefix_class
 * @uses prefix_hook
 * @uses text_domain
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

			/**
			 * Not using direct `theme_mods` here to prevent issues with child themes upgrade
			 * and possible advanced, "pro" versions of themes.
			 *
			 * Basically, forcing using the parent theme mods.
			 */
			${%= prefix_var %}_theme_options = (array) get_option( {%= prefix_constant %}_OPTION_CUSTOMIZER );

			if ( empty( ${%= prefix_var %}_theme_options ) ) {
				${%= prefix_var %}_theme_options = array();
			}





/**
 * 1) Required files
 */

	// Core class

		require_once( 'includes/classes/class-core.php' );

	// Customize (has to be frontend accessible, otherwise it hides the theme settings)

		// CSS Styles Generator class

			require_once( 'includes/classes/class-generate-styles.php' );

		// Customize class

			require_once( 'includes/classes/class-customize.php' );

	// Admin

		if ( is_admin() ) {

			// Load the theme welcome page

				locate_template( {%= prefix_constant %}_INCLUDES_DIR . 'welcome/welcome.php', true );

			// Admin class

				require_once( 'includes/classes/class-admin.php' );

			// Plugins suggestions

				if (
						apply_filters( 'wmhook_{%= prefix_hook %}_plugins_suggestion_enabled', true )
						&& locate_template( {%= prefix_constant %}_INCLUDES_DIR . 'tgmpa/plugins.php' )
					) {
					require_once( 'includes/vendor/tgmpa/class-tgm-plugin-activation.php' );
					locate_template( {%= prefix_constant %}_INCLUDES_DIR . 'tgmpa/plugins.php', true );
				}

			// Child theme generator

				require_once( 'includes/vendor/use-child-theme/class-use-child-theme.php' );

		}
