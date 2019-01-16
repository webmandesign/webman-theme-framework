<?php
/**
 * WebMan WordPress Theme Framework.
 *
 * @link  https://github.com/webmandesign/webman-theme-framework
 * @link  https://www.webmandesign.eu
 *
 * @package    WebMan WordPress Theme Framework
 * @copyright  WebMan Design, Oliver Juhas
 * @license    GPL-3.0, http://www.gnu.org/licenses/gpl-3.0.html
 * @version    2.8.0
 *
 * Used development prefixes:
 *
 * @uses theme_slug
 * @uses text_domain
 * @uses prefix_var
 * @uses prefix_hook
 * @uses theme_name
 * @uses prefix_class
 * @uses prefix_constant
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

		if ( ! defined( '{%= prefix_constant %}_THEME_VERSION' ) ) {
			define( '{%= prefix_constant %}_THEME_VERSION', wp_get_theme( '{%= theme_slug %}' )->get( 'Version' ) );
		}

	// Paths

		if ( ! defined( '{%= prefix_constant %}_PATH' ) ) {
			define( '{%= prefix_constant %}_PATH', trailingslashit( get_template_directory() ) );
		}

		if ( ! defined( '{%= prefix_constant %}_LIBRARY_DIR' ) ) {
			define( '{%= prefix_constant %}_LIBRARY_DIR', trailingslashit( basename( dirname( __FILE__ ) ) ) );
		}

		define( '{%= prefix_constant %}_LIBRARY', trailingslashit( {%= prefix_constant %}_PATH . {%= prefix_constant %}_LIBRARY_DIR ) );





/**
 * 20) Load
 */

	// Core class.
	require {%= prefix_constant %}_LIBRARY . 'includes/classes/class-core.php';

	// Customize (has to be frontend accessible, otherwise it hides the theme settings)

		// Sanitize class.
		require {%= prefix_constant %}_LIBRARY . 'includes/classes/class-sanitize.php';

		// Customize class.
		require {%= prefix_constant %}_LIBRARY . 'includes/classes/class-customize.php';

		// CSS variables generator class.
		require {%= prefix_constant %}_LIBRARY . 'includes/classes/class-css-variables.php';

	// Admin

		if ( is_admin() ) {

			// Optional theme welcome page.
			$welcome_page = get_theme_file_path( 'includes/welcome/welcome.php' );
			if ( file_exists( $welcome_page ) ) {
				require $welcome_page;
			}

			// Optional plugins suggestions.
			$plugins_suggestions = get_theme_file_path( 'includes/tgmpa/plugins.php' );
			if ( (bool) apply_filters( 'wmhook_{%= prefix_hook %}_plugins_suggestion_enabled', file_exists( $plugins_suggestions ) ) ) {
				require {%= prefix_constant %}_LIBRARY . 'includes/vendors/tgmpa/class-tgm-plugin-activation.php';
				require $plugins_suggestions;
			}

		}
