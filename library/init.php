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
 * @version  1.9
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
 * 10) Constants
 * 20) Load
 */





/**
 * 10) Constants
 */

	// Theme version

		if ( ! defined( '{%= prefix_constant %}_THEME_VERSION' ) ) define( '{%= prefix_constant %}_THEME_VERSION', wp_get_theme( '{%= theme_slug %}' )->get( 'Version' ) );





/**
 * 20) Load
 */

	// Core class

		require dirname( __FILE__ ) . '/includes/classes/class-core.php';

	// Customize (has to be frontend accessible, otherwise it hides the theme settings)

		// Customize class

			require dirname( __FILE__ ) . '/includes/classes/class-customize.php';

		// CSS Styles Generator class

			require dirname( __FILE__ ) . '/includes/classes/class-customize-styles.php';

	// Admin

		if ( is_admin() ) {

			// Load the theme welcome page

				locate_template( 'includes/welcome/welcome.php', true );

			// Admin class

				require dirname( __FILE__ ) . '/includes/classes/class-admin.php';

			// Plugins suggestions

				if (
						apply_filters( 'wmhook_{%= prefix_hook %}_plugins_suggestion_enabled', true )
						&& locate_template( 'includes/tgmpa/plugins.php' )
					) {
					require dirname( __FILE__ ) . '/includes/vendor/tgmpa/class-tgm-plugin-activation.php';
					locate_template( 'includes/tgmpa/plugins.php', true );
				}

			// Child theme generator

				require dirname( __FILE__ ) . '/includes/vendor/use-child-theme/class-use-child-theme.php';

		}
