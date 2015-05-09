<?php
/**
 * WebMan WordPress Theme Framework (Simple)
 *
 * Textdomain used in the framework: wmtf_domain
 *
 * Custom hooks naming convention:
 * - `wmhook_` - global (and other, such as plugins related) hooks
 * - `wmhook_wmtf_` - theme framework specific hooks (core specific)
 * - `wmhook_wmtf_admin_`,  `wmhook_wmtf_customize_`,  `wmhook_wmtf_editor_` - class method specific hooks
 *
 * Used global hooks:
 * - `wmhook_theme_options`
 * - `wmhook_custom_styles`
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
 * @version  2.0
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

	//Dir constants

		if ( ! defined( 'WM_INC_DIR' ) )     define( 'WM_INC_DIR',     trailingslashit( 'inc' )     );
		if ( ! defined( 'WM_LIBRARY_DIR' ) ) define( 'WM_LIBRARY_DIR', trailingslashit( 'inc/lib' ) );

	//Required to set up in the theme's functions.php file

		if ( ! defined( 'WM_WP_COMPATIBILITY' ) ) define( 'WM_WP_COMPATIBILITY', 4.1 );





/**
 * 1) Required files
 */

	//Main theme action hooks

		locate_template( WM_LIBRARY_DIR . 'inc/hooks/hooks.php', true );

	//Admin required files

		if ( is_admin() ) {

			//WP admin functionality

				locate_template( WM_LIBRARY_DIR . 'inc/admin.php', true );

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