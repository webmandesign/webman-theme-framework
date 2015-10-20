<?php
/**
 * WebMan WordPress Theme Framework (Simple)
 *
 * Textdomain used in the framework: {%= text_domain %}
 *
 * Theme options with `__` prefix (`get_theme_mod( '__option_id' )`) are theme
 * setup related options and can not be edited via customizer. This way we prevent
 * creating non-sense multiple options in a database.
 *
 * Custom hooks naming convention:
 * - `wmhook_{%= prefix_hook %}_` - global (and other, such as plugins related) hooks
 * - `wmhook_{%= prefix_hook %}_tf_` - theme framework specific hooks (core specific)
 * - `wmhook_{%= prefix_hook %}_tf_admin_` - class method specific hooks
 * - `wmhook_{%= prefix_hook %}_tf_customize_` - class method specific hooks
 * - `wmhook_{%= prefix_hook %}_tf_editor_` - class method specific hooks
 * - `tha_` - Theme Hook Alliance specific hooks
 *
 * Used global hooks:
 * @uses  `wmhook_{%= prefix_hook %}_theme_options`
 * @uses  `wmhook_{%= prefix_hook %}_esc_css`
 * @uses  `wmhook_{%= prefix_hook %}_custom_styles`
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
 * @package     WebMan WordPress Theme Framework (Simple)
 * @subpackage  Core
 *
 * @version  1.0.1
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

	// Dir constants

		if ( ! defined( '{%= prefix_constant %}_INC_DIR' ) )     define( '{%= prefix_constant %}_INC_DIR',     trailingslashit( 'inc' )     );
		if ( ! defined( '{%= prefix_constant %}_LIBRARY_DIR' ) ) define( '{%= prefix_constant %}_LIBRARY_DIR', trailingslashit( 'inc/lib' ) );





/**
 * 1) Required files
 */

	// Core class

		locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/classes/class-core.php', true );





/**
 * 10) Init
 */

	add_action( 'after_setup_theme', array( '{%= prefix_class %}_Theme_Framework', 'init' ), -100 );
