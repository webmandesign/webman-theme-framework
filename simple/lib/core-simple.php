<?php
/**
 * WebMan WordPress Theme Framework (Simple)
 *
 * Textdomain used in the framework: wmtf_domain
 *
 * Theme options with `__` prefix (`get_theme_mod( '__option_id' )`) are theme
 * settup related options and can not be edited via customizer. Using this method
 * not to create non-sense multiple options in a database.
 *
 * Custom hooks naming convention:
 * - `wmhook_` - global (and other, such as plugins related) hooks
 * - `wmhook_wmtf_` - theme framework specific hooks (core specific)
 * - `wmhook_wmtf_admin_` - class method specific hooks
 * - `wmhook_wmtf_customize_` - class method specific hooks
 * - `wmhook_wmtf_editor_` - class method specific hooks
 *
 * Used global hooks:
 * - `wmhook_theme_upgrade`
 * - `wmhook_theme_options`
 * - `wmhook_custom_styles`
 * - `wmhook_esc_css`
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

	//Basic constants

		if ( ! defined( 'WMTF_THEME_SHORTNAME' ) ) define( 'WMTF_THEME_SHORTNAME',  str_replace( array( '-lite', '-plus' ), '', get_template() ) );

	//Dir constants

		if ( ! defined( 'WMTF_INC_DIR' ) )     define( 'WMTF_INC_DIR',     trailingslashit( 'inc' )     );
		if ( ! defined( 'WMTF_LIBRARY_DIR' ) ) define( 'WMTF_LIBRARY_DIR', trailingslashit( 'inc/lib' ) );





/**
 * 1) Required files
 */

	//Main theme action hooks

		locate_template( WMTF_LIBRARY_DIR . 'inc/hooks/hooks.php', true );

	//Main class

		locate_template( WMTF_LIBRARY_DIR . 'inc/classes/class-wm-theme-framework.php', true );





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
