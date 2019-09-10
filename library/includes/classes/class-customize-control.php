<?php
/**
 * Customizer control renderer.
 *
 * @package  WebMan WordPress Theme Framework
 *
 * @since    2.8.0
 * @version  2.8.0
 *
 * Contents:
 *
 *  0) Init
 * 10) Create option
 * 20) Getters
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Theme_Slug_Library_Control {





	/**
	 * 0) Init
	 */

		public static $option_type = 'theme_mod';

		public static $sanitize = array(
			'checkbox'      => 'Theme_Slug_Library_Sanitize::checkbox',
			'color'         => 'sanitize_hex_color_no_hash',
			'email'         => 'sanitize_email',
			'image'         => 'esc_url_raw',
			'multicheckbox' => 'multi_array',
			'multiselect'   => 'multi_array',
			'radio'         => 'Theme_Slug_Library_Sanitize::select',
			'range'         => 'absint',
			'select'        => 'Theme_Slug_Library_Sanitize::select',
			'text'          => 'esc_textarea',
			'textarea'      => 'esc_textarea',
			'url'           => 'esc_url',
		);



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

				/**
				 * Load custom controls files.
				 * Has to be done during customizer registration process
				 * to have the `WP_Customize_Control` class available.
				 */
				require_once THEME_SLUG_LIBRARY . 'includes/classes/class-customize-control-html.php';
				require_once THEME_SLUG_LIBRARY . 'includes/classes/class-customize-control-multiselect.php';
				require_once THEME_SLUG_LIBRARY . 'includes/classes/class-customize-control-radio-matrix.php';
				require_once THEME_SLUG_LIBRARY . 'includes/classes/class-customize-control-select.php';
				require_once THEME_SLUG_LIBRARY . 'includes/classes/class-customize-control-text.php';

		} // /init





	/**
	 * 10) Add option
	 */

		/**
		 * Adds, creates customizer option control.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  array                $option
		 * @param  WP_Customize_Manager $wp_customize
		 *
		 * @return  void
		 */
		public static function add_option( array $option, WP_Customize_Manager $wp_customize ) {

			// Processing

				self::add_setting( $option, $wp_customize );
				self::add_control( $option, $wp_customize );

		} // /add_option



		/**
		 * Add setting.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  array                $option
		 * @param  WP_Customize_Manager $wp_customize
		 *
		 * @return  void
		 */
		public static function add_setting( array $option, WP_Customize_Manager $wp_customize ) {

			// Variables

				$default  = ( isset( $option['default'] ) ) ? ( $option['default'] ) : ( '' );
				$sanitize = ( isset( self::$sanitize[ $option['type'] ] ) ) ? ( self::$sanitize[ $option['type'] ] ) : ( 'esc_attr' );

				switch ( $sanitize ) {

					case 'sanitize_hex_color_no_hash':
						$sanitize_js = 'maybe_hash_hex_color';
						break;

					case 'wp_kses_post':
						$sanitize_js = 'wp_filter_post_kses';
						break;

					default:
						$sanitize_js = $sanitize;
						break;

				}


			// Processing

				$wp_customize->add_setting(
					$option['id'],
					array(
						'type'                 => self::$option_type,
						'default'              => ( 'color' === $option['type'] ) ? ( trim( $default, '#' ) ) : ( $default ),
						'transport'            => ( isset( $option['preview_js'] ) ) ? ( 'postMessage' ) : ( 'refresh' ),
						'sanitize_callback'    => $sanitize,
						'sanitize_js_callback' => $sanitize_js,
						'validate_callback'    => ( isset( $option['validate_callback'] ) ) ? ( $option['validate_callback'] ) : ( '' ),
					)
				);

		} // /add_setting



		/**
		 * Add control.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  array                $option
		 * @param  WP_Customize_Manager $wp_customize
		 *
		 * @return  void
		 */
		public static function add_control( array $option, WP_Customize_Manager $wp_customize ) {

			// Processing

				if ( method_exists( __CLASS__, 'add_control_' . $option['type'] ) ) {
					// Render custom or special control.
					call_user_func(
						array( __CLASS__, 'add_control_' . $option['type'] ),
						$option,
						$wp_customize
					);
				} else {
					// Render default control.
					$wp_customize->add_control(
						$option['id'],
						self::get_args( $option )
					);
				}

		} // /add_control



		/**
		 * Control: color.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  array                $option
		 * @param  WP_Customize_Manager $wp_customize
		 *
		 * @return  void
		 */
		public static function add_control_color( array $option, WP_Customize_Manager $wp_customize ) {

			// Processing

				$wp_customize->add_control(
					new WP_Customize_Color_Control(
						$wp_customize,
						$option['id'],
						self::get_args( $option )
					)
				);

		} // /add_control_color



		/**
		 * Control: html.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  array                $option
		 * @param  WP_Customize_Manager $wp_customize
		 *
		 * @return  void
		 */
		public static function add_control_html( array $option, WP_Customize_Manager $wp_customize ) {

			// Processing

				$wp_customize->add_control(
					new Theme_Slug_Customize_Control_HTML(
						$wp_customize,
						$option['id'],
						array_merge(
							self::get_args( $option ),
							array(
								'content' => $option['content'],
							)
						)
					)
				);

		} // /add_control_html



		/**
		 * Control: image.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  array                $option
		 * @param  WP_Customize_Manager $wp_customize
		 *
		 * @return  void
		 */
		public static function add_control_image( array $option, WP_Customize_Manager $wp_customize ) {

			// Processing

				$wp_customize->add_control(
					new WP_Customize_Image_Control(
						$wp_customize,
						$option['id'],
						array_merge(
							self::get_args( $option ),
							array(
								'context' => $option['id'],
							)
						)
					)
				);

		} // /add_control_image



		/**
		 * Control: multicheckbox.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  array                $option
		 * @param  WP_Customize_Manager $wp_customize
		 *
		 * @return  void
		 */
		public static function add_control_multicheckbox( array $option, WP_Customize_Manager $wp_customize ) {

			// Processing

				self::add_control_multiselect( $option, $wp_customize );

		} // /add_control_multicheckbox



		/**
		 * Control: multiselect.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  array                $option
		 * @param  WP_Customize_Manager $wp_customize
		 *
		 * @return  void
		 */
		public static function add_control_multiselect( array $option, WP_Customize_Manager $wp_customize ) {

			// Processing

				$wp_customize->add_control(
					new Theme_Slug_Customize_Control_Multiselect(
						$wp_customize,
						$option['id'],
						self::get_args( $option )
					)
				);

		} // /add_control_multiselect



		/**
		 * Control: range.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  array                $option
		 * @param  WP_Customize_Manager $wp_customize
		 *
		 * @return  void
		 */
		public static function add_control_range( array $option, WP_Customize_Manager $wp_customize ) {

			// Processing

				$wp_customize->add_control(
					$option['id'],
					array_merge(
						self::get_args( $option ),
						array(
							'input_attrs' => array(
								'min'           => $option['min'],
								'max'           => $option['max'],
								'step'          => $option['step'],
								'data-multiply' => ( isset( $option['multiplier'] ) ) ? ( $option['multiplier'] ) : ( 1 ),
								'data-prefix'   => ( isset( $option['prefix'] ) ) ? ( $option['prefix'] ) : ( '' ),
								'data-suffix'   => ( isset( $option['suffix'] ) ) ? ( $option['suffix'] ) : ( '' ),
								'data-decimals' => ( isset( $option['decimal_places'] ) ) ? ( absint( $option['decimal_places'] ) ) : ( 0 ),
							),
						)
					)
				);

		} // /add_control_range



		/**
		 * Control: select.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  array                $option
		 * @param  WP_Customize_Manager $wp_customize
		 *
		 * @return  void
		 */
		public static function add_control_select( array $option, WP_Customize_Manager $wp_customize ) {

			// Processing

				$wp_customize->add_control(
					new Theme_Slug_Customize_Control_Select(
						$wp_customize,
						$option['id'],
						self::get_args( $option )
					)
				);

		} // /add_control_select



		/**
		 * Control: text.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  array                $option
		 * @param  WP_Customize_Manager $wp_customize
		 *
		 * @return  void
		 */
		public static function add_control_text( array $option, WP_Customize_Manager $wp_customize ) {

			// Processing

				$wp_customize->add_control(
					new Theme_Slug_Customize_Control_Text(
						$wp_customize,
						$option['id'],
						array_merge(
							self::get_args( $option ),
							array(
								'choices' => ( isset( $option['datalist'] ) ) ? ( $option['datalist'] ) : ( array() ),
							)
						)
					)
				);

		} // /add_control_text





	/**
	 * 30) Getters
	 */

		/**
		 * Get generic option args.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @param  array $option
		 *
		 * @return  array
		 */
		public static function get_args( array $option ): array {

			// Output

				return array(
					'active_callback' => ( isset( $option['active_callback'] ) ) ? ( $option['active_callback'] ) : ( '' ),
					'choices'         => ( isset( $option['choices'] ) ) ? ( $option['choices'] ) : ( null ),
					'description'     => ( isset( $option['description'] ) ) ? ( $option['description'] ) : ( '' ),
					'input_attrs'     => ( isset( $option['input_attrs'] ) ) ? ( $option['input_attrs'] ) : ( array() ),
					'label'           => ( isset( $option['label'] ) ) ? ( $option['label'] ) : ( '' ),
					'priority'        => ( isset( $option['priority'] ) ) ? ( $option['priority'] ) : ( 0 ),
					'section'         => ( isset( $option['section'] ) ) ? ( $option['section'] ) : ( 'theme-slug' ),
					'type'            => $option['type'],
				);

		} // /get_args





} // /Theme_Slug_Library_Control

add_action( 'customize_register', 'Theme_Slug_Library_Control::init' );
