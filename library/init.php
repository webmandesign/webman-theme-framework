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
 * Used development placeholders:
 * @uses Themename
 * @uses theme-slug
 * @uses theme_slug
 * @uses Theme_Slug
 * @uses THEME_SLUG
 *
 * Contents:
 *
 * 10) Constants
 * 20) Load
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;





/**
 * 10) Constants
 */

	// Theme version

		if ( ! defined( 'THEME_SLUG_THEME_VERSION' ) ) {
			define( 'THEME_SLUG_THEME_VERSION', wp_get_theme( 'theme-slug' )->get( 'Version' ) );
		}

	// Paths

		if ( ! defined( 'THEME_SLUG_PATH' ) ) {
			define( 'THEME_SLUG_PATH', trailingslashit( get_template_directory() ) );
		}

		if ( ! defined( 'THEME_SLUG_LIBRARY_DIR' ) ) {
			define( 'THEME_SLUG_LIBRARY_DIR', trailingslashit( basename( dirname( __FILE__ ) ) ) );
		}

		define( 'THEME_SLUG_LIBRARY', trailingslashit( THEME_SLUG_PATH . THEME_SLUG_LIBRARY_DIR ) );





/**
 * 20) Load
 */

	// Core class.
	require THEME_SLUG_LIBRARY . 'includes/classes/class-core.php';

	// Customize (has to be frontend accessible, otherwise it hides the theme settings).

		// Sanitize class.
		require THEME_SLUG_LIBRARY . 'includes/classes/class-sanitize.php';

		// Controls.
		require_once THEME_SLUG_LIBRARY . 'includes/classes/class-customize-control.php';

		// Customize class.
		require THEME_SLUG_LIBRARY . 'includes/classes/class-customize.php';

		// CSS variables generator class.
		require THEME_SLUG_LIBRARY . 'includes/classes/class-css-variables.php';

	// Admin area related functionality.
	if ( is_admin() ) {

		// Optional plugins suggestions.
		$plugins_suggestions = get_theme_file_path( 'includes/tgmpa/plugins.php' );
		/**
		 * Whether to enable TGMPA plugins recommendations.
		 *
		 * @link  http://tgmpluginactivation.com/
		 *
		 * @since  2.8.0
		 *
		 * @param  bool $enabled  Default: file_exists( get_theme_file_path( 'includes/tgmpa/plugins.php' ) ).
		 */
		if ( (bool) apply_filters( 'theme_slug/library/plugins_suggestion_enabled', file_exists( $plugins_suggestions ) ) ) {
			require THEME_SLUG_LIBRARY . 'includes/vendors/tgmpa/class-tgm-plugin-activation.php';
			require $plugins_suggestions;
		}

	}
