<?php
/**
 * WebMan WordPress Theme Framework
 *
 * Textdomain used in the framework: wmtf_domain
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

	//Helper variables

		$theme_data = wp_get_theme();

	//Basic constants

		if ( ! defined( 'WM_THEME_NAME' ) )       define( 'WM_THEME_NAME',       $theme_data->get( 'Name' )                                   );
		if ( ! defined( 'WM_THEME_SHORTNAME' ) )  define( 'WM_THEME_SHORTNAME',  str_replace( array( '-lite', '-plus' ), '', get_template() ) );
		if ( ! defined( 'WM_THEME_VERSION' ) )    define( 'WM_THEME_VERSION',    $theme_data->get( 'Version' )                                );
		if ( ! defined( 'WM_THEME_AUTHOR_URI' ) ) define( 'WM_THEME_AUTHOR_URI', esc_url( $theme_data->get( 'AuthorURI' ) )                   );
		if ( ! defined( 'WM_THEME_URI' ) )        define( 'WM_THEME_URI',        esc_url( $theme_data->get( 'ThemeURI' ) )                    );
		if ( ! defined( 'WM_SCRIPTS_VERSION' ) )  define( 'WM_SCRIPTS_VERSION',  esc_attr( trim( WM_THEME_VERSION ) )                         );

	//Options constants

		if ( ! defined( 'WM_OPTION_PREFIX' ) )     define( 'WM_OPTION_PREFIX',     ''                                 );
		if ( ! defined( 'WM_OPTION_CUSTOMIZER' ) ) define( 'WM_OPTION_CUSTOMIZER', 'theme_mods_' . WM_THEME_SHORTNAME );

	//Dir constants

		if ( ! defined( 'WM_LIBRARY_DIR' ) ) define( 'WM_LIBRARY_DIR', trailingslashit( 'lib' )                                     );
		if ( ! defined( 'WM_SETUP_DIR' ) )   define( 'WM_SETUP_DIR',   trailingslashit( 'setup' )                                   );
		if ( ! defined( 'WM_SETUP' ) )       define( 'WM_SETUP',       trailingslashit( get_template_directory() ) . WM_SETUP_DIR   );
		if ( ! defined( 'WM_SETUP_CHILD' ) ) define( 'WM_SETUP_CHILD', trailingslashit( get_stylesheet_directory() ) . WM_SETUP_DIR );

	//Required to set up in the theme's functions.php file

		if ( ! defined( 'WM_WP_COMPATIBILITY' ) ) define( 'WM_WP_COMPATIBILITY', 4.1 );



	//Global variables

		//Theme options

			$wmtf_theme_options = get_option( WM_OPTION_CUSTOMIZER );

			if ( empty( $wmtf_theme_options ) ) {
				$wmtf_theme_options = array();
			}





/**
 * 1) Required files
 */

	//Main theme action hooks

		locate_template( WM_LIBRARY_DIR . 'inc/hooks.php', true );

	//Customize (has to be fontend accessible, otherwise it hides theme settings)

		locate_template( WM_LIBRARY_DIR . 'customize.php', true );

	//Admin required files

		if ( is_admin() ) {

			//WP admin functionality

				locate_template( WM_LIBRARY_DIR . 'inc/admin.php', true );

			//Plugins suggestions

				if (
						apply_filters( 'wmhook_enable_plugins_integration', true )
						&& locate_template( WM_SETUP_DIR . 'tgmpa/plugins.php' )
					) {
					locate_template( WM_LIBRARY_DIR . 'inc/external/class-tgm-plugin-activation.php', true );
					locate_template( WM_SETUP_DIR . 'tgmpa/plugins.php', true );
				}

		}

	//Main class

		locate_template( WM_LIBRARY_DIR . 'inc/classes/class-wm-theme-framework.php', true );





/**
 * 10) Hooks
 */

	/**
	 * Actions
	 */

		//Theme upgrade action

			add_action( 'init', 'WM_Theme_Framework::theme_upgrade' );

		//Flushing transients

			add_action( 'switch_theme',  'WM_Theme_Framework::image_ids_transient_flusher'      );
			add_action( 'edit_category', 'WM_Theme_Framework::all_categories_transient_flusher' );
			add_action( 'save_post',     'WM_Theme_Framework::all_categories_transient_flusher' );

		//Contextual help

			add_action( 'contextual_help', 'WM_Theme_Framework::contextual_help', 10, 3 );

		//Toolbar (also displayed on front end)

			add_action( 'admin_bar_menu', 'WM_Theme_Framework::toolbar', 998 );



	/**
	 * Filters
	 */

		//Escape inline CSS

			add_filter( 'wmhook_esc_css', 'WM_Theme_Framework::esc_css' );

		//Widgets improvements

			add_filter( 'show_recent_comments_widget_style', '__return_false'                        );
			add_filter( 'widget_text',                       'do_shortcode'                          );
			add_filter( 'widget_title',                      'WM_Theme_Framework::html_widget_title' );

			remove_filter( 'widget_title', 'esc_html' );

		//Table of contents

			add_filter( 'the_content', 'WM_Theme_Framework::add_table_of_contents', 10 );

		//Minify CSS

			add_filter( 'wmhook_wmtf_generate_main_css_output_min', 'WM_Theme_Framework::minify_css', 10 );
