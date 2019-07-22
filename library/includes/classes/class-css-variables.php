<?php
/**
 * CSS Variables Generator class.
 *
 * Data are being cached in transient as they are global for the website.
 *
 * @package  WebMan WordPress Theme Framework
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

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Theme_Slug_Library_CSS_Variables {





	/**
	 * 0) Init
	 */

		public static $transient_cache_key = 'theme_slug_css_vars';



		/**
		 * Initialization.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @return  void
		 */
		public static function init() {

			// Processing

				// Hooks

					// Actions

						add_action( 'wp_enqueue_scripts', __CLASS__ . '::compatibility', 0 );

						add_action( 'switch_theme', __CLASS__ . '::transient_cache_flush' );
						add_action( 'customize_save_after', __CLASS__ . '::transient_cache_flush' );
						add_action( 'theme_slug/library/theme_upgrade', __CLASS__ . '::transient_cache_flush' );

		} // /init





	/**
	 * 10) Getters
	 */

		/**
		 * Get CSS variables from theme options in array.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @return  array
		 */
		public static function get_variables_array(): array {

			// Variables

				$is_customize_preview = is_customize_preview();

				$css_vars = (array) get_transient( self::$transient_cache_key );
				$css_vars = array_filter( $css_vars, 'trim', ARRAY_FILTER_USE_KEY );


			// Requirements check

				if (
					! empty( $css_vars )
					&& ! $is_customize_preview
				) {
					// The filter is documented below.
					return (array) apply_filters( 'theme_slug/library_css_variables/get_variables_array', $css_vars );
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
						$mod = call_user_func( $option['sanitize_callback'], $mod );
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

					// Do not apply `esc_attr()` as it will escape quote marks, such as in background image URL.
					$css_vars[ '--' . sanitize_title( $option['id'] ) ] = $value;

					/**
					 * Filters CSS variables output in array after each single variable processing.
					 *
					 * Allows filtering the whole `$css_vars` array for each option individually.
					 * This way we can add an option related additional CSS variables.
					 *
					 * @since  2.8.0
					 *
					 * @param  string $css_vars  Array of CSS variable name and value pairs.
					 * @param  array  $option    Single theme option setup array.
					 * @param  string $value     Single CSS variable value.
					 */
					$css_vars = apply_filters( 'theme_slug/library_css_variables/get_variables_array/per_option', $css_vars, $option, $value );
				}

				// Cache the results in transient.
				if ( ! $is_customize_preview ) {
					set_transient( self::$transient_cache_key, (array) $css_vars );
				}


			// Output

				/**
				 * Filters CSS variables output in array.
				 *
				 * @since  2.8.0
				 *
				 * @param  array $css_vars  Array of CSS variable name and value pairs.
				 */
				return (array) apply_filters( 'theme_slug/library_css_variables/get_variables_array', $css_vars );

		} // /get_variables_array



		/**
		 * Get CSS variables from theme options in string.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  string $separator
		 *
		 * @return  string
		 */
		public static function get_variables_string( string $separator = ' ' ): string {

			// Variables

				$css_vars = (array) self::get_variables_array();


			// Processing

				$css_vars = array_map(
					function( $variable, $value ) {
						// Actual CSS code declaring a variable.
						return (string) $variable . ': ' . (string) $value . ';';
					},
					array_keys( $css_vars ), // $variable
					$css_vars // $value
				);

				$css_vars = implode( (string) $separator, $css_vars );


			// Output

				/**
				 * Filters CSS variables output in string.
				 *
				 * @since  2.8.0
				 *
				 * @param  string $css_vars  String of CSS variable name and value pairs ready for CSS code output.
				 */
				return (string) apply_filters( 'theme_slug/library_css_variables/get_variables_string', trim( (string) $css_vars ) );

		} // /get_variables_string





	/**
	 * 100) Helpers
	 */

		/**
		 * Ensure CSS variables compatibility with older browsers.
		 *
		 * @link  https://github.com/jhildenbiddle/css-vars-ponyfill
		 * @link  https://www.jsdelivr.com/package/npm/css-vars-ponyfill?path=dist
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @return  void
		 */
		public static function compatibility() {

			// Processing

				wp_enqueue_script(
					'css-vars-ponyfill',
					get_theme_file_uri( THEME_SLUG_LIBRARY_DIR . 'js/vendors/css-vars-ponyfill/css-vars-ponyfill.min.js' ),
					array(),
					'2.0.2',
					true
				);

				wp_add_inline_script(
					'css-vars-ponyfill',
					'window.onload = function() {' . PHP_EOL .
					"\t" . 'cssVars( {' . PHP_EOL .
					"\t\t" . 'preserveStatic: false,' . PHP_EOL .
					"\t\t" . 'exclude: \'link:not([href^="' . esc_url_raw( get_theme_root_uri() ) . '"])\'' . PHP_EOL .
					"\t" . '} );' . PHP_EOL .
					'};'
				);

		} // /compatibility



		/**
		 * Flush the transient of cached CSS variables array.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @return  void
		 */
		public static function transient_cache_flush() {

			// Processing

				delete_transient( self::$transient_cache_key );

		} // /transient_cache_flush





} // /Theme_Slug_Library_CSS_Variables

add_action( 'after_setup_theme', 'Theme_Slug_Library_CSS_Variables::init', 20 );
