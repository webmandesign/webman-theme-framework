<?php
/**
 * Customize class
 *
 * @uses  `wmhook_{%= prefix_hook %}_theme_options` global hook
 *
 * @package     WebMan WordPress Theme Framework (Simple)
 * @subpackage  Customize
 *
 * @since    2.0
 * @version  5.0
 */





/**
 * Customize class
 *
 * @since    5.0
 * @version  5.0
 *
 * Contents:
 *
 *  0) Init
 * 10) Assets
 * 20) Sanitize
 * 30) Customizer core
 */
final class {%= prefix_class %}_Theme_Framework_Customize {





	/**
	 * 0) Init
	 */

		private static $instance;



		/**
		 * Constructor
		 *
		 * @since    5.0
		 * @version  5.0
		 */
		private function __construct() {

			// Processing

				/**
				 * Hooks
				 */

					/**
					 * Actions
					 */

						// Register customizer

							add_action( 'customize_register', array( $this, 'customize' ) );

		} // /__construct



		/**
		 * Initialization (get instance)
		 *
		 * @since    5.0
		 * @version  5.0
		 */
		public static function init() {

			// Processing

				if ( null === self::$instance ) {
					self::$instance = new self;
				}


			// Output

				return self::$instance;

		} // /init





	/**
	 * 10) Assets
	 */

		/**
		 * Outputs customizer JavaScript in footer
		 *
		 * Use this structure for customizer_js property:
		 *
		 * @example
		 *
		 *   'customizer_js' => array(
		 *     'css' => array(
		 *         '.selector'         => array( 'css-property-name' ),
		 *         '.another-selector' => array( array( 'padding-left', 'px' ) ),
		 *       ),
		 *     'custom' => 'your_custom_JavaScript_here',
		 *   )
		 *
		 * @uses  `wmhook_{%= prefix_hook %}_theme_options` global hook
		 *
		 * @since    1.0
		 * @version  5.0
		 */
		public static function preview_scripts() {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_customize_preview_scripts_pre', false );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				$theme_options = apply_filters( 'wmhook_{%= prefix_hook %}_theme_options', array() );

				ksort( $theme_options );

				$output = $output_single = '';


			// Processing

				if ( is_array( $theme_options ) && ! empty( $theme_options ) ) {

					foreach ( $theme_options as $theme_option ) {

						if ( isset( $theme_option['customizer_js'] ) && is_array( $theme_option['customizer_js'] ) ) {

							$output_single  = "wp.customize("  . "\r\n";
							$output_single .= "\t" . "'" . $theme_option['id'] . "'," . "\r\n";
							$output_single .= "\t" . "function( value ) {"  . "\r\n";
							$output_single .= "\t\t" . 'value.bind( function( to ) {' . "\r\n";

							if ( ! isset( $theme_option['customizer_js']['custom'] ) ) {

								$output_single .= "\t\t\t" . "var newCss = '';" . "\r\n\r\n";
								$output_single .= "\t\t\t" . "if ( jQuery( '#jscss-" . $theme_option['id'] . "' ).length ) { jQuery( '#jscss-" . $theme_option['id'] . "' ).remove() }" . "\r\n\r\n";

								foreach ( $theme_option['customizer_js']['css'] as $selector => $properties ) {

									if ( is_array( $properties ) ) {

										$output_single_css = '';

										foreach ( $properties as $property ) {

											if ( ! is_array( $property ) ) {
												$property = array( $property, '' );
											}
											if ( ! isset( $property[1] ) ) {
												$property[1] = '';
											}

											/**
											 * $property[0] = CSS style property
											 * $property[1] = suffix (such as CSS unit)
											 */

											$output_single_css .= $property[0] . ": ' + to + '" . $property[1] . "; ";

										} // /foreach

									}

									$output_single .= "\t\t\t" . "newCss += '" . $selector . " { " . $output_single_css . "} ';" . "\r\n";

								} // /foreach

							} else {

								$output_single .= "\t\t" . $theme_option['customizer_js']['custom'] . "\r\n";

							}

							$output_single .= "\r\n\t\t\t" . "jQuery( document ).find( 'head' ).append( jQuery( '<style id=\'jscss-" . $theme_option['id'] . "\'> ' + newCss + '</style>' ) );" . "\r\n";
							$output_single .= "\t\t" . '} );' . "\r\n";
							$output_single .= "\t" . '}'. "\r\n";
							$output_single .= ');'. "\r\n";
							$output_single  = apply_filters( 'wmhook_{%= prefix_hook %}_tf_customize_preview_scripts_option_' . $theme_option['id'], $output_single );

							$output .= $output_single;

						}

					} // /foreach

				}


			// Output

				if ( $output = trim( $output ) ) {
					echo apply_filters( 'wmhook_{%= prefix_hook %}_tf_customize_preview_scripts_output', '<!-- Theme custom scripts -->' . "\r\n" . '<script type="text/javascript"><!--' . "\r\n" . '( function( $ ) {' . "\r\n\r\n" . trim( $output ) . "\r\n\r\n" . '} )( jQuery );' . "\r\n" . '//--></script>' );
				}

		} // /preview_scripts





	/**
	 * 20) Sanitize
	 */

		/**
		 * Sanitize checkbox
		 *
		 * @since    5.0
		 * @version  5.0
		 *
		 * @param  bool $value WP customizer value to sanitize.
		 */
		public static function sanitize_checkbox( $value ) {

			// Output

				return ( ( isset( $value ) && true == $value ) ? ( true ) : ( false ) );

		} // /sanitize_checkbox



		/**
		 * Sanitize select/radio
		 *
		 * @since    5.0
		 * @version  5.0
		 *
		 * @param  string               $value WP customizer value to sanitize.
		 * @param  WP_Customize_Setting $setting
		 */
		public static function sanitize_select( $value, $setting ) {

			// Processing

				$value = esc_attr( $value );

				// Get list of choices from the control associated with the setting.

					$choices = $setting->manager->get_control( $setting->id )->choices;


			// Output

				return ( array_key_exists( $value, $choices ) ? ( $value ) : ( $setting->default ) );

		} // /sanitize_select



		/**
		 * No sanitization at all, simply return the value in appropriate format
		 *
		 * Useful for when the value may be of mixed type, such as array-or-string.
		 *
		 * @since    1.0
		 * @version  5.0
		 *
		 * @param  mixed $value WP customizer value to sanitize.
		 */
		public static function sanitize_return_value( $value ) {

			// Processing

				if ( is_array( $value ) ) {
					$value = (array) $value;
				} elseif ( is_numeric( $value ) ) {
					$value = intval( $value );
				} elseif ( is_string( $value ) ) {
					$value = (string) $value;
				}


			// Output

				return $value;

		} // /sanitize_return_value





	/**
	 * 30) Customizer core
	 */

		/**
		 * Registering sections and options for WP Customizer
		 *
		 * @uses  `wmhook_{%= prefix_hook %}_theme_options` global hook
		 *
		 * @since    1.0
		 * @version  5.0
		 *
		 * @param  object $wp_customize WP customizer object.
		 */
		public static function customize( $wp_customize ) {

			// Requirements check

				if ( ! isset( $wp_customize ) ) {
					return;
				}


			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_customize_pre', false, $wp_customize );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				$theme_options = (array) apply_filters( 'wmhook_{%= prefix_hook %}_theme_options', array() );

				ksort( $theme_options );

				$allowed_option_types = apply_filters( 'wmhook_{%= prefix_hook %}_tf_customize_allowed_option_types', array(
						'checkbox',
						'color',
						'hidden',
						'html',
						'image',
						'multiselect',
						'radio',
						'range', //This does not display the value indicator, only the slider, unfortunatelly...
						'select',
						'text',
						'textarea',
					) );

				// To make sure our customizer sections start after WordPress default ones

					$priority = apply_filters( 'wmhook_{%= prefix_hook %}_tf_customize_priority', 900 );

				// Default section name in case not set (should be overwritten anyway)

					$customizer_panel   = '';
					$customizer_section = {%= prefix_constant %}_THEME_SLUG;


				/**
				 * @todo  Consider switching from 'type' => 'theme_mod' to 'option' for better theme upgradability.
				 */
				$type = apply_filters( 'wmhook_{%= prefix_hook %}_tf_customize_type', 'theme_mod' );


			// Processing

				// Set live preview for predefined controls

					$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
					$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
					$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

				// Custom controls

					/**
					 * @link  https://github.com/bueltge/Wordpress-Theme-Customizer-Custom-Controls
					 * @link  http://ottopress.com/2012/making-a-custom-control-for-the-theme-customizer/
					 */

					locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/classes/controls/class-control-hidden.php',      true );
					locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/classes/controls/class-control-html.php',        true );
					locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/classes/controls/class-control-image.php',       true );
					locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/classes/controls/class-control-multiselect.php', true );
					locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/classes/controls/class-control-select.php',      true );

					do_action( 'wmhook_{%= prefix_hook %}_tf_customize_load_controls', $wp_customize );

				// Generate customizer options

					if ( is_array( $theme_options ) && ! empty( $theme_options ) ) {

						foreach ( $theme_options as $theme_option ) {

							if (
									is_array( $theme_option )
									&& isset( $theme_option['type'] )
									&& (
											in_array( $theme_option['type'], $allowed_option_types )
											|| isset( $theme_option['create_section'] )
										)
								) {

								// Helper variables

									$priority++;

									$option_id = $default = $description = '';

									if ( isset( $theme_option['id'] ) ) {
										$option_id = $theme_option['id'];
									}
									if ( isset( $theme_option['default'] ) ) {
										$default = $theme_option['default'];
									}
									if ( isset( $theme_option['description'] ) ) {
										$description = $theme_option['description'];
									}

									$transport = ( isset( $theme_option['customizer_js'] ) ) ? ( 'postMessage' ) : ( 'refresh' );

								/**
								 * Panels
								 *
								 * Panels are wrappers for customizer sections.
								 * Note that the panel will not display unless sections are assigned to it.
								 * Set the panel name in the section declaration with `in_panel`.
								 * Panel has to be defined for each section to prevent all sections within a single panel.
								 *
								 * @link  http://make.wordpress.org/core/2014/07/08/customizer-improvements-in-4-0/
								 */
								if ( isset( $theme_option['in_panel'] ) ) {

									$panel_id = '{%= prefix_var %}_' . sanitize_title( trim( $theme_option['in_panel'] ) );

									if ( $customizer_panel !== $panel_id ) {

										$wp_customize->add_panel(
												$panel_id,
												array(
													'title'       => $theme_option['in_panel'], // Panel title
													'description' => ( isset( $theme_option['in_panel-description'] ) ) ? ( $theme_option['in_panel-description'] ) : ( '' ),  // Hidden at the top of the panel
													'priority'    => $priority,
												)
											);

										$customizer_panel = $panel_id;

									}

								}



								/**
								 * Sections
								 */
								if ( isset( $theme_option['create_section'] ) && trim( $theme_option['create_section'] ) ) {

									if ( empty( $option_id ) ) {
										$option_id = sanitize_title( trim( $theme_option['create_section'] ) );
									}

									$customizer_section = array(
											'id'    => $option_id,
											'setup' => array(
													'title'       => $theme_option['create_section'], // Section title
													'description' => ( isset( $theme_option['create_section-description'] ) ) ? ( $theme_option['create_section-description'] ) : ( '' ), // Displayed at the top of section
													'priority'    => $priority,
												)
										);

									if ( ! isset( $theme_option['in_panel'] ) ) {
										$customizer_panel = '';
									} else {
										$customizer_section['setup']['panel'] = $customizer_panel;
									}

									$wp_customize->add_section(
											$customizer_section['id'],
											$customizer_section['setup']
										);

									$customizer_section = $customizer_section['id'];

								}



								/**
								 * Options generator
								 */
								switch ( $theme_option['type'] ) {

									/**
									 * Checkbox, radio
									 */
									case 'checkbox':
									case 'radio':

										$wp_customize->add_setting(
												$option_id,
												array(
													'type'                 => $type,
													'default'              => $default,
													'transport'            => $transport,
													'sanitize_callback'    => ( 'checkbox' === $theme_option['type'] ) ? ( '{%= prefix_class %}_Theme_Framework_Customize::sanitize_checkbox' ) : ( '{%= prefix_class %}_Theme_Framework_Customize::sanitize_select' ),
													'sanitize_js_callback' => ( 'checkbox' === $theme_option['type'] ) ? ( '{%= prefix_class %}_Theme_Framework_Customize::sanitize_checkbox' ) : ( '{%= prefix_class %}_Theme_Framework_Customize::sanitize_select' ),
												)
											);

										$wp_customize->add_control(
												$option_id,
												array(
													'label'           => $theme_option['label'],
													'description'     => $description,
													'section'         => $customizer_section,
													'priority'        => $priority,
													'type'            => $theme_option['type'],
													'choices'         => ( isset( $theme_option['options'] ) ) ? ( $theme_option['options'] ) : ( '' ),
													'active_callback' => ( isset( $theme_option['active_callback'] ) ) ? ( $theme_option['active_callback'] ) : ( null ),
												)
											);

									break;

									/**
									 * Color
									 */
									case 'color':

										$wp_customize->add_setting(
												$option_id,
												array(
													'type'                 => $type,
													'default'              => trim( $default, '#' ),
													'transport'            => $transport,
													'sanitize_callback'    => 'sanitize_hex_color_no_hash',
													'sanitize_js_callback' => 'maybe_hash_hex_color',
												)
											);

										$wp_customize->add_control( new WP_Customize_Color_Control(
												$wp_customize,
												$option_id,
												array(
													'label'           => $theme_option['label'],
													'description'     => $description,
													'section'         => $customizer_section,
													'priority'        => $priority,
													'active_callback' => ( isset( $theme_option['active_callback'] ) ) ? ( $theme_option['active_callback'] ) : ( null ),
												)
											) );

									break;

									/**
									 * Hidden
									 */
									case 'hidden':

										$wp_customize->add_setting(
												$option_id,
												array(
													'type'                 => $type,
													'default'              => $default,
													'transport'            => $transport,
													'sanitize_callback'    => ( isset( $theme_option['validate'] ) ) ? ( $theme_option['validate'] ) : ( 'esc_attr' ),
													'sanitize_js_callback' => ( isset( $theme_option['validate'] ) ) ? ( $theme_option['validate'] ) : ( 'esc_attr' ),
												)
											);

										$wp_customize->add_control( new {%= prefix_class %}_Control_Hidden(
												$wp_customize,
												$option_id,
												array(
													'label'    => 'HIDDEN FIELD',
													'section'  => $customizer_section,
													'priority' => $priority,
												)
											) );

									break;

									/**
									 * HTML
									 */
									case 'html':

										if ( empty( $option_id ) ) {
											$option_id = 'custom-title-' . $priority;
										}

										$wp_customize->add_setting(
												$option_id,
												array(
													'sanitize_callback'    => 'wp_filter_post_kses',
													'sanitize_js_callback' => 'wp_filter_post_kses',
												)
											);

										$wp_customize->add_control( new {%= prefix_class %}_Control_HTML(
												$wp_customize,
												$option_id,
												array(
													'label'           => ( isset( $theme_option['label'] ) ) ? ( $theme_option['label'] ) : ( '' ),
													'description'     => $description,
													'content'         => $theme_option['content'],
													'section'         => $customizer_section,
													'priority'        => $priority,
													'active_callback' => ( isset( $theme_option['active_callback'] ) ) ? ( $theme_option['active_callback'] ) : ( null ),
												)
											) );

									break;

									/**
									 * Image
									 */
									case 'image':

										$wp_customize->add_setting(
												$option_id,
												array(
													'type'                 => $type,
													'default'              => $default,
													'transport'            => $transport,
													'sanitize_callback'    => ( isset( $theme_option['validate'] ) ) ? ( $theme_option['validate'] ) : ( 'absint' ),
													'sanitize_js_callback' => ( isset( $theme_option['validate'] ) ) ? ( $theme_option['validate'] ) : ( 'absint' ),
												)
											);

										$wp_customize->add_control( new WP_Customize_Image_Control(
												$wp_customize,
												$option_id,
												array(
													'label'           => $theme_option['label'],
													'description'     => $description,
													'section'         => $customizer_section,
													'priority'        => $priority,
													'context'         => $option_id,
													'active_callback' => ( isset( $theme_option['active_callback'] ) ) ? ( $theme_option['active_callback'] ) : ( null ),
												)
											) );

									break;

									/**
									 * Multiselect
									 */
									case 'multiselect':

										$wp_customize->add_setting(
												$option_id,
												array(
													'type'                 => $type,
													'default'              => $default,
													'transport'            => $transport,
													'sanitize_callback'    => ( isset( $theme_option['validate'] ) ) ? ( $theme_option['validate'] ) : ( '{%= prefix_class %}_Theme_Framework_Customize::sanitize_return_value' ),
													'sanitize_js_callback' => ( isset( $theme_option['validate'] ) ) ? ( $theme_option['validate'] ) : ( '{%= prefix_class %}_Theme_Framework_Customize::sanitize_return_value' ),
												)
											);

										$wp_customize->add_control( new {%= prefix_class %}_Control_Multiselect(
												$wp_customize,
												$option_id,
												array(
													'label'           => $theme_option['label'],
													'description'     => $description,
													'section'         => $customizer_section,
													'priority'        => $priority,
													'choices'         => ( isset( $theme_option['options'] ) ) ? ( $theme_option['options'] ) : ( '' ),
													'active_callback' => ( isset( $theme_option['active_callback'] ) ) ? ( $theme_option['active_callback'] ) : ( null ),
												)
											) );

									break;

									/**
									 * Range
									 */
									case 'range':

										$wp_customize->add_setting(
												$option_id,
												array(
													'type'                 => $type,
													'default'              => $default,
													'transport'            => $transport,
													'sanitize_callback'    => ( isset( $theme_option['validate'] ) ) ? ( $theme_option['validate'] ) : ( 'absint' ),
													'sanitize_js_callback' => ( isset( $theme_option['validate'] ) ) ? ( $theme_option['validate'] ) : ( 'absint' ),
												)
											);

										$wp_customize->add_control(
												$option_id,
												array(
													'label'           => $theme_option['label'],
													'description'     => $description,
													'section'         => $customizer_section,
													'priority'        => $priority,
													'type'            => 'range',
													'active_callback' => ( isset( $theme_option['active_callback'] ) ) ? ( $theme_option['active_callback'] ) : ( null ),
													'input_attrs'     => array(
														'min'  => ( isset( $theme_option['min'] ) ) ? ( intval( $theme_option['min'] ) ) : ( 0 ),
														'max'  => ( isset( $theme_option['max'] ) ) ? ( intval( $theme_option['max'] ) ) : ( 100 ),
														'step' => ( isset( $theme_option['step'] ) ) ? ( intval( $theme_option['step'] ) ) : ( 1 ),
													),
												)
											);

									break;

									/**
									 * Select (with optgroups)
									 */
									case 'select':

										$wp_customize->add_setting(
												$option_id,
												array(
													'type'                 => $type,
													'default'              => $default,
													'transport'            => $transport,
													'sanitize_callback'    => '{%= prefix_class %}_Theme_Framework_Customize::sanitize_select',
													'sanitize_js_callback' => '{%= prefix_class %}_Theme_Framework_Customize::sanitize_select',
												)
											);

										$wp_customize->add_control( new {%= prefix_class %}_Control_Select(
												$wp_customize,
												$option_id,
												array(
													'label'           => $theme_option['label'],
													'description'     => $description,
													'section'         => $customizer_section,
													'priority'        => $priority,
													'choices'         => ( isset( $theme_option['options'] ) ) ? ( $theme_option['options'] ) : ( '' ),
													'active_callback' => ( isset( $theme_option['active_callback'] ) ) ? ( $theme_option['active_callback'] ) : ( null ),
												)
											) );

									break;

									/**
									 * Text
									 */
									case 'text':

										$wp_customize->add_setting(
												$option_id,
												array(
													'type'                 => $type,
													'default'              => $default,
													'transport'            => $transport,
													'sanitize_callback'    => ( isset( $theme_option['validate'] ) ) ? ( $theme_option['validate'] ) : ( 'esc_textarea' ),
													'sanitize_js_callback' => ( isset( $theme_option['validate'] ) ) ? ( $theme_option['validate'] ) : ( 'esc_textarea' ),
												)
											);

										$wp_customize->add_control(
												$option_id,
												array(
													'label'           => $theme_option['label'],
													'description'     => $description,
													'section'         => $customizer_section,
													'priority'        => $priority,
													'active_callback' => ( isset( $theme_option['active_callback'] ) ) ? ( $theme_option['active_callback'] ) : ( null ),
												)
											);

									break;

									/**
									 * Textarea
									 */
									case 'textarea':

										$wp_customize->add_setting(
												$option_id,
												array(
													'type'                 => $type,
													'default'              => $default,
													'transport'            => $transport,
													'sanitize_callback'    => ( isset( $theme_option['validate'] ) ) ? ( $theme_option['validate'] ) : ( 'esc_textarea' ),
													'sanitize_js_callback' => ( isset( $theme_option['validate'] ) ) ? ( $theme_option['validate'] ) : ( 'esc_textarea' ),
												)
											);

										$wp_customize->add_control(
												$option_id,
												array(
													'type'            => 'textarea',
													'label'           => $theme_option['label'],
													'description'     => $description,
													'section'         => $customizer_section,
													'priority'        => $priority,
													'active_callback' => ( isset( $theme_option['active_callback'] ) ) ? ( $theme_option['active_callback'] ) : ( null ),
												)
											);

									break;

									/**
									 * Default
									 */
									default:
									break;

								} // /switch

							} // /if suitable option array

						} // /foreach

					} // /if skin options are non-empty array

				// Assets needed for customizer preview

					if ( $wp_customize->is_preview() ) {

						add_action( 'wp_footer', '{%= prefix_class %}_Theme_Framework_Customize::preview_scripts', 99 );

					}

		} // /customize





} // /{%= prefix_class %}_Theme_Framework_Customize
