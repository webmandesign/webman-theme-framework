<?php defined( 'ABSPATH' ) || exit;
/**
 * CSS Variables Generator class.
 *
 * @subpackage  CSS Variables
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    2.8.0
 * @version  2.8.0
 *
 * Contents:
 *
 *   0) Init
 *  10) Getters
 * 100) Helpers
 */
class Theme_Slug_Library_CSS_Variables {





	/**
	 * 0) Init
	 */

		public static $cache_key = 'theme_slug_css_vars';



		/**
		 * Initialization.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 */
		public static function init() {

			// Processing

				// Hooks

					// Actions

						add_action( 'wp_enqueue_scripts', __CLASS__ . '::compatibility', 0 );

						add_action( 'switch_theme', __CLASS__ . '::cache_flush' );
						add_action( 'customize_save_after', __CLASS__ . '::cache_flush' );
						add_action( 'wmhook_theme_slug_library_theme_upgrade', __CLASS__ . '::cache_flush' );

		} // /init





	/**
	 * 10) Getters
	 */

		/**
		 * Get CSS variables from theme options in array.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 */
		public static function get_variables_array() {

			// Variables

				$is_customize_preview = is_customize_preview();

				$css_vars = (array) get_transient( self::$cache_key );
				$css_vars = array_filter( $css_vars, 'trim', ARRAY_FILTER_USE_KEY );


			// Requirements check

				if (
					! empty( $css_vars )
					&& ! $is_customize_preview
				) {
					return (array) $css_vars;
				}


			// Processing

				foreach ( (array) Theme_Slug_Library_Customize::get_options() as $option ) {
					if ( ! isset( $option['css_var'] ) ) {
						continue;
					}

					// Custom fonts only if they are enabled.
					/**
					 * @todo  Double check the `typography_custom_fonts` option name.
					 */
					if (
						'Theme_Slug_Library_Sanitize::css_fonts' === $option['css_var']
						&& ! get_theme_mod( 'typography_custom_fonts', false )
					) {
						continue;
					}

					if ( isset( $option['default'] ) ) {
						$value = $option['default'];
					} else {
						$value = '';
					}

					$mod = get_theme_mod( $option['id'] );
					if (
						isset( $option['sanitize_callback'] )
						&& is_callable( $option['sanitize_callback'] )
					) {
						$mod = call_user_func( $option['validate'], $mod );
					}
					if (
						! empty( $mod )
						|| 'checkbox' === $option['type']
					) {
						if ( 'color' === $option['type'] ) {
							$value_check = maybe_hash_hex_color( $value );
							$mod         = maybe_hash_hex_color( $mod );
						} else {
							$value_check = $value;
						}
						// No need to output CSS var if it is the same as default.
						if ( $value_check === $mod ) {
							continue;
						}
						$value = $mod;
					} else {
						// No need to output CSS var if it was not changed in customizer.
						continue;
					}

					// Array value to string. Just in case.
					if ( is_array( $value ) ) {
						$value = (string) implode( ',', (array) $value );
					}

					if ( is_callable( $option['css_var'] ) ) {
						$value = call_user_func( $option['css_var'], $value );
					} else {
						$value = str_replace(
							'[[value]]',
							$value,
							(string) $option['css_var']
						);
					}

					$css_vars[ '--' . sanitize_title( $option['id'] ) ] = esc_attr( $value );

					// Allow filtering the whole `$css_vars` for each option individually.
					// This way we can add an option related additional CSS variables.
					$css_vars = apply_filters(
						'wmhook_theme_slug_library_css_variables_get_variables_array_single_option',
						$css_vars,
						$option,
						$value
					);
				}

				// Cache the results.
				if ( ! $is_customize_preview ) {
					set_transient( self::$cache_key, (array) $css_vars );
				}


			// Output

				return (array) apply_filters( 'wmhook_theme_slug_library_css_variables_get_variables_array', $css_vars );

		} // /get_variables_array



		/**
		 * Get CSS variables from theme options in string.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  string $separator
		 */
		public static function get_variables_string( $separator = ' ' ) {

			// Variables

				$css_vars = (array) self::get_variables_array();


			// Processing

				$css_vars = array_map( __CLASS__ . '::get_variable_declaration', array_keys( $css_vars ), $css_vars );
				$css_vars = implode( (string) $separator, $css_vars );


			// Output

				return (string) apply_filters( 'wmhook_theme_slug_library_css_variables_get_variables_string', trim( (string) $css_vars ) );

		} // /get_variables_string



		/**
		 * Get CSS variable declaration.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  string $variable
		 * @param  string $value
		 */
		public static function get_variable_declaration( $variable, $value ) {

			// Output

				return (string) $variable . ': ' . (string) $value . ';';

		} // /get_variable_declaration





	/**
	 * 100) Helpers
	 */

		/**
		 * Ensure CSS variables compatibility with older browsers.
		 *
		 * @link  https://github.com/jhildenbiddle/css-vars-ponyfill
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 */
		public static function compatibility() {

			// Processing

				wp_enqueue_script(
					'css-vars-ponyfill',
					get_theme_file_uri( THEME_SLUG_LIBRARY_DIR . 'js/vendors/css-vars-ponyfill/css-vars-ponyfill.min.js' ),
					array(),
					'1.16.1'
				);

				wp_add_inline_script(
					'css-vars-ponyfill',
					"cssVars( { onlyVars: true, exclude: 'link:not([href^=\"" . esc_url_raw( get_theme_root_uri() ) . "\"])' } );"
				);

		} // /compatibility



		/**
		 * Flush the cached CSS variables array.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 */
		public static function cache_flush() {

			// Processing

				delete_transient( self::$cache_key );

		} // /cache_flush





} // /Theme_Slug_Library_CSS_Variables

add_action( 'after_setup_theme', 'Theme_Slug_Library_CSS_Variables::init', 20 );
