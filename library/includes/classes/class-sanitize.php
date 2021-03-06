<?php
/**
 * Sanitization Methods class
 *
 * @link  https://github.com/WPTRT/code-examples/blob/master/customizer/sanitization-callbacks.php
 *
 * @package    WebMan WordPress Theme Framework
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    2.5.0
 * @version  2.8.0
 *
 * Contents:
 *
 * 10) General
 * 20) CSS
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Theme_Slug_Library_Sanitize {





	/**
	 * 10) General
	 */

		/**
		 * Sanitize checkbox
		 *
		 * Sanitization callback for checkbox type controls.
		 * This callback sanitizes `$checked` as a boolean value, either TRUE or FALSE.
		 *
		 * @since    2.5.0
		 * @version  2.5.0
		 *
		 * @param  bool $value
		 *
		 * @return  bool
		 */
		public static function checkbox( bool $value ): bool {

			// Output

				return ( ( isset( $value ) && $value ) ? ( true ) : ( false ) );

		} // /checkbox



		/**
		 * Sanitize select/radio
		 *
		 * Sanitization callback for select and radio type controls.
		 * This callback sanitizes `$value` against provided array of `$choices`.
		 * The `$choices` has to be associated array!
		 *
		 * @since    2.5.0
		 * @version  2.8.0
		 *
		 * @param  string                      $value
		 * @param  array|WP_Customize_Setting  $choices
		 * @param  string                      $default
		 *
		 * @return  string
		 */
		public static function select( string $value, $choices = array(), string $default = '' ): string {

			// Processing

				/**
				 * If we pass a customizer control as `$choices`,
				 * get the list of choices and default value from it.
				 */
				if ( $choices instanceof WP_Customize_Setting ) {
					$default = $choices->default;
					$choices = $choices->manager->get_control( $choices->id )->choices;
				}


			// Output

				return ( isset( $choices[ $value ] ) ) ? ( esc_attr( $value ) ) : ( esc_attr( $default ) );

		} // /select



		/**
		 * Sanitize array
		 *
		 * Sanitization callback for multiselect type controls.
		 * This callback sanitizes `$value` against provided array of `$choices`.
		 * The `$choices` has to be associated array!
		 * Returns an array of values.
		 *
		 * @since    2.5.0
		 * @version  2.8.0
		 *
		 * @param  string|array               $value
		 * @param  array|WP_Customize_Setting $choices
		 *
		 * @return  array
		 */
		public static function multi_array( $value, $choices = array() ): array {

			// Variables

				/**
				 * If we get a string in `$value`,
				 * split it to array using `,` as delimiter.
				 */
				$value = ( is_string( $value ) ) ? ( explode( ',', (string) $value ) ) : ( (array) $value );

				/**
				 * If we pass a customizer control as `$choices`,
				 * get the list of choices and default value from it.
				 */
				if ( $choices instanceof WP_Customize_Setting ) {
					$choices = $choices->manager->get_control( $choices->id )->choices;
				}


			// Requirements check

				if ( empty( $choices ) ) {
					return array();
				}


			// Processing

				foreach ( $value as $key => $single_value ) {
					if ( ! array_key_exists( $single_value, $choices ) ) {
						unset( $value[ $key ] );
						continue;
					}

					$value[ $key ] = esc_attr( $single_value );
				}


			// Output

				return $value;

		} // /multi_array



		/**
		 * Sanitize fonts
		 *
		 * Sanitization callback for `font-family` CSS property value.
		 * Allows only alphanumeric characters, spaces, commas, underscores,
		 * dashes, single/double quote inside the `$value`.
		 *
		 * @since    2.5.0
		 * @version  2.8.0
		 *
		 * @param  string                      $value
		 * @param  string|WP_Customize_Setting $default
		 *
		 * @return  string
		 */
		public static function fonts( string $value, $default = '' ): string {

			// Processing

				$value = trim( preg_replace( '/[^a-zA-Z0-9 ,_\-\'\"]+/', '', $value ) );

				/**
				 * If we pass a customizer control as `$default`,
				 * get the default value from it.
				 */
				if ( $default instanceof WP_Customize_Setting ) {
					$default = $default->default;
				}


			// Output

				return ( $value ) ? ( $value ) : ( $default );

		} // /fonts



		/**
		 * Sanitize float
		 *
		 * Sanitization callback for float number type controls.
		 * This callback sanitizes `$value` as a float number.
		 * Has to do a wrapper for `floatval()` here as otherwise
		 * you can get a PHP warning when using in customizer
		 * ("floatval() expects exactly 1 parameter, 2 given").
		 *
		 * @since    2.5.6
		 * @version  2.5.6
		 *
		 * @param  float|int|string $value
		 *
		 * @return  float
		 */
		public static function float( $value ): float {

			// Output

				return floatval( $value );

		} // /float





	/**
	 * 20) CSS
	 *
	 * Outputs values formatted for CSS properties.
	 */

		/**
		 * Get numeric value with string suffix.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  number $value
		 * @param  string $suffix
		 * @param  string $sanitize
		 *
		 * @return  string
		 */
		public static function get_number_with_suffix( $value, string $suffix = '%', string $sanitize = 'absint' ): string {

			// Output

				if ( is_callable( $sanitize ) ) {
					return call_user_func( $sanitize, $value ) . trim( $suffix );
				} else {
					return '';
				}

		} // /get_number_with_suffix



		/**
		 * Sanitize CSS pixel value.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  int $value
		 *
		 * @return  string
		 */
		public static function css_pixels( int $value ): string {

			// Output

				return self::get_number_with_suffix( $value, 'px', 'absint' );

		} // /css_pixels



		/**
		 * Sanitize CSS percentage value.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  int $value
		 *
		 * @return  string
		 */
		public static function css_percent( int $value ): string {

			// Output

				return self::get_number_with_suffix( $value, '%' );

		} // /css_percent



		/**
		 * Sanitize CSS rem unit value.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  int $value
		 *
		 * @return  string
		 */
		public static function css_rem( int $value ): string {

			// Output

				return self::get_number_with_suffix( $value, 'rem' );

		} // /css_rem



		/**
		 * Sanitize CSS em unit value.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  int $value
		 *
		 * @return  string
		 */
		public static function css_em( int $value ): string {

			// Output

				return self::get_number_with_suffix( $value, 'em' );

		} // /css_em



		/**
		 * Sanitize CSS vh unit value.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  int $value
		 *
		 * @return  string
		 */
		public static function css_vh( int $value ): string {

			// Output

				return self::get_number_with_suffix( $value, 'vh' );

		} // /css_vh



		/**
		 * Sanitize CSS vw unit value.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  int $value
		 *
		 * @return  string
		 */
		public static function css_vw( int $value ): string {

			// Output

				return self::get_number_with_suffix( $value, 'vw' );

		} // /css_vw



		/**
		 * Sanitize CSS fonts.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  string $fonts
		 *
		 * @return  string
		 */
		public static function css_fonts( string $fonts ): string {

			// Variables

				$css_comment  = '';
				$system_fonts = array(
					'-apple-system',
					'BlinkMacSystemFont',
					'"Segoe UI"',
					'Roboto',
					'Oxygen-Sans',
					'Ubuntu',
					'Cantarell',
					'"Helvetica Neue"',
					'sans-serif',
				);


			// Processing

				$fonts = explode( ',', (string) self::fonts( $fonts ) );

				foreach ( $fonts as $key => $family ) {
					$family = trim( $family, "\"' \t\n\r\0\x0B" );

					// If we are bypassing Google Fonts, let us know in CSS comment.
					if (
						is_callable( 'Theme_Slug_Google_Fonts::get_bypass_font_family' )
						&& $family === Theme_Slug_Google_Fonts::get_bypass_font_family()
					) {
						unset( $fonts[ $key ] );
						$css_comment .= $family;
						continue;
					}

					if ( 'system' === $family ) {
						$family = implode( ', ', $system_fonts );
					} elseif ( strpos( $family, ' ' ) ) {
						$family = '"' . $family . '"';
					}

					$fonts[ $key ] = $family;
				}

				$fonts = implode( ', ', $fonts );

				// Optional CSS debug comment at the end of font-family declaration.
				if (
					defined( 'WP_DEBUG' ) && WP_DEBUG
					&& ! empty( $css_comment )
				) {
					$fonts .= ' /* ' . $css_comment . ' */';
				}


			// Output

				return $fonts;

		} // /css_fonts



		/**
		 * Sanitize CSS image URL.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  array|int|string $image  Could be a URL, numeric image ID or an array with `id` image ID key.
		 *
		 * @return  string
		 */
		public static function css_image_url( $image ): string {

			// Variables

				$value = 'none';


			// Processing

				if ( is_array( $image ) && isset( $image['id'] ) ) {
					$image = absint( $image['id'] );
				}

				if ( is_numeric( $image ) ) {
					$image = wp_get_attachment_image_src( absint( $image ), 'full' );
					$image = $image[0];
				}

				if ( ! empty( $image ) ) {
					$value = 'url("' . esc_url_raw( $image ) . '")';
				}


			// Output

				return $value;

		} // /css_image_url



		/**
		 * Sanitize CSS background-repeat checkbox.
		 *
		 * Available values:
		 * - TRUE: CSS value of `repeat`,
		 * - FALSE: CSS value of `no-repeat`.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  bool|string $repeat
		 *
		 * @return  string
		 */
		public static function css_checkbox_background_repeat( $repeat ): string {

			// Processing

				if ( ! is_string( $repeat ) ) {
					$repeat = ( $repeat ) ? ( 'repeat' ) : ( 'no-repeat' );
				}


			// Output

				return (string) $repeat;

		} // /css_checkbox_background_repeat



		/**
		 * Sanitize CSS background-attachment checkbox.
		 *
		 * Available values:
		 * - TRUE: CSS value of `fixed`,
		 * - FALSE: CSS value of `scroll`.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  bool|string $attachment
		 *
		 * @return  string
		 */
		public static function css_checkbox_background_attachment( $attachment ): string {

			// Processing

				if ( ! is_string( $attachment ) ) {
					$attachment = ( $attachment ) ? ( 'fixed' ) : ( 'scroll' );
				}


			// Output

				return (string) $attachment;

		} // /css_checkbox_background_attachment





} // /Theme_Slug_Library_Sanitize
