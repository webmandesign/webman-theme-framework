<?php
/**
 * Customize class
 *
 * @uses  `wmhook_{%= prefix_hook %}_theme_options` global hook
 *
 * @package     WebMan WordPress Theme Framework (Simple)
 * @subpackage  Customize
 */





/**
 * Customize class
 *
 * @since    1.0
 * @version  1.6
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
		 * @since    1.0
		 * @version  1.5
		 */
		private function __construct() {

			// Processing

				// Hooks

					// Actions

						// Register customizer

							add_action( 'customize_register', __CLASS__ . '::customize', 100 ); // After Jetpack logo action

		} // /__construct



		/**
		 * Initialization (get instance)
		 *
		 * @since    1.0
		 * @version  1.0
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
		 * Outputs customizer JavaScript
		 *
		 * This function automatically outputs theme customizer preview JavaScript for each theme option,
		 * where the `preview_js` property is set.
		 *
		 * For CSS theme option change it works by inserting a `<style>` tag into a preview HTML head for
		 * each theme option separately. This is to prevent inline styles on elements when applied with
		 * pure JS.
		 * Also, we need to create the `<style>` tag for each option separately so way we gain control
		 * over the output. If we put all the CSS into a single `<style>` tag, it would be bloated with
		 * CSS styles for every single subtle change in the theme option(s).
		 *
		 * It is possible to set up a custom JS action, not just CSS styles change. That can be used
		 * to trigger a class on an element, for example.
		 *
		 * If `preview_js => false` set, the change of the theme option won't trigger the customizer
		 * preview refresh. This is useful to disable "about theme page", for example.
		 *
		 * The actual JavaScript is outputted in the footer of the page.
		 *
		 * Use this structure for `preview_js` property:
		 *
		 * @example
		 *
		 *   'preview_js' => array(
		 *
		 *     // Setting CSS styles:
		 *
		 *       'css' => array(
		 *
		 *           // Sets the whole value to the `css-property-name` of the `selector`
		 *
		 *             'selector' => array(
		 *                 'css-property-name',...
		 *               ),
		 *
		 *           // Sets the `css-property-name` of the `selector` with value followed by the `suffix` (such as "px")
		 *
		 *             'selector' => array(
		 *                 array( 'css-property-name', 'suffix', 'prefix' ),...
		 *               ),
		 *
		 *           // Replaces "@" in `selector` for `selector-replace-value` (such as "@ h2, @ h3" to ".footer h2, .footer h3")
		 *
		 *             'selector' => array(
		 *                 'selector_replace' => 'selector-replace-value', // Must be the first array item
		 *                 'css-property-name',...
		 *               ),
		 *
		 *         ),
		 *
		 *     // Or setting custom JavaScript:
		 *
		 *       'custom' => 'JavaScript here', // Such as "jQuery( '.site-title.type-text' ).toggleClass( 'styled' );"
		 *
		 *   );
		 *
		 * @uses  `wmhook_{%= prefix_hook %}_theme_options` global hook
		 *
		 * @since    1.0
		 * @version  1.4
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

						if ( isset( $theme_option['preview_js'] ) && is_array( $theme_option['preview_js'] ) ) {

							$output_single  = "wp.customize("  . "\r\n";
							$output_single .= "\t" . "'" . $theme_option['id'] . "'," . "\r\n";
							$output_single .= "\t" . "function( value ) {"  . "\r\n";
							$output_single .= "\t\t" . 'value.bind( function( to ) {' . "\r\n";

							if ( isset( $theme_option['preview_js']['css'] ) ) {

								$output_single .= "\t\t\t" . "var newCss = '';" . "\r\n\r\n";
								$output_single .= "\t\t\t" . "if ( jQuery( '#jscss-" . $theme_option['id'] . "' ).length ) { jQuery( '#jscss-" . $theme_option['id'] . "' ).remove() }" . "\r\n\r\n";

								foreach ( $theme_option['preview_js']['css'] as $selector => $properties ) {

									if ( is_array( $properties ) ) {

										$output_single_css = '';

										foreach ( $properties as $key => $property ) {

											if ( 'selector_replace' === $key ) {
												$selector = str_replace( '@', $property, $selector );
												continue;
											}

											if ( ! is_array( $property ) ) {
												$property = array( $property, '' );
											}
											if ( ! isset( $property[1] ) ) {
												$property[1] = '';
											}
											if ( ! isset( $property[2] ) ) {
												$property[2] = '';
											}

											/**
											 * $property[0] = CSS style property
											 * $property[1] = suffix (such as CSS unit)
											 * $property[2] = prefix (such as CSS linear gradient)
											 */

											$output_single_css .= $property[0] . ": " . $property[2] . "' + to + '" . $property[1] . "; ";

										} // /foreach

										$output_single .= "\t\t\t" . "newCss += '" . $selector . " { " . $output_single_css . "} ';" . "\r\n";

									}

								} // /foreach

								$output_single .= "\r\n\t\t\t" . "jQuery( document ).find( 'head' ).append( jQuery( '<style id=\'jscss-" . $theme_option['id'] . "\'> ' + newCss + '</style>' ) );" . "\r\n";

							} elseif ( isset( $theme_option['preview_js']['custom'] ) ) {

								$output_single .= "\t\t" . $theme_option['preview_js']['custom'] . "\r\n";

							}

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
		 * @since    1.0
		 * @version  1.0
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
		 * @since    1.0
		 * @version  1.0
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
		 * @version  1.0
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
		 * @version  1.6
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
						'range',
						'select',
						'text',
						'textarea',
					) );

				// To make sure our customizer sections start after WordPress default ones

					$priority = absint( apply_filters( 'wmhook_{%= prefix_hook %}_tf_customize_priority', 200 ) );

				// Default section name in case not set (should be overwritten anyway)

					$customizer_panel   = '';
					$customizer_section = '{%= theme_slug %}';


				/**
				 * @todo  Consider switching from 'type' => 'theme_mod' to 'option' for better theme upgradability.
				 */
				$type = apply_filters( 'wmhook_{%= prefix_hook %}_tf_customize_type', 'theme_mod' );


			// Processing

				// Moving "Widgets" panel after the custom "Theme" panel
				// @link  https://developer.wordpress.org/themes/advanced-topics/customizer-api/#sections

					if ( $wp_customize->get_panel( 'widgets' ) ) {
						$wp_customize->get_panel( 'widgets' )->priority = $priority + 10;
					}

				// Set live preview for predefined controls

					$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
					$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
					$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

				// Move background color setting alongside background image

					$wp_customize->get_control( 'background_color' )->section  = 'background_image';
					$wp_customize->get_control( 'background_color' )->priority = 20;

				// Change background image section title & priority

					$wp_customize->get_section( 'background_image' )->title    = esc_html_x( 'Background', 'Customizer section title.', '{%= text_domain %}' );
					$wp_customize->get_section( 'background_image' )->priority = 30;

				// Change header image section title & priority

					$wp_customize->get_section( 'header_image' )->title    = esc_html_x( 'Header', 'Customizer section title.', '{%= text_domain %}' );
					$wp_customize->get_section( 'header_image' )->priority = 25;

				// Custom controls

					/**
					 * @link  https://github.com/bueltge/Wordpress-Theme-Customizer-Custom-Controls
					 * @link  http://ottopress.com/2012/making-a-custom-control-for-the-theme-customizer/
					 */

					require_once( trailingslashit( get_template_directory() ) . {%= prefix_constant %}_LIBRARY_DIR . 'includes/classes/controls/class-control-hidden.php' );
					require_once( trailingslashit( get_template_directory() ) . {%= prefix_constant %}_LIBRARY_DIR . 'includes/classes/controls/class-control-html.php' );
					require_once( trailingslashit( get_template_directory() ) . {%= prefix_constant %}_LIBRARY_DIR . 'includes/classes/controls/class-control-multiselect.php' );
					require_once( trailingslashit( get_template_directory() ) . {%= prefix_constant %}_LIBRARY_DIR . 'includes/classes/controls/class-control-select.php' );

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

									$transport = ( isset( $theme_option['preview_js'] ) ) ? ( 'postMessage' ) : ( 'refresh' );

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

									if ( is_array( $theme_option['in_panel'] ) ) {

										$panel_title = $theme_option['in_panel'][0];
										$panel_id    = trim( $theme_option['in_panel'][1] );

									} else {

										$panel_title = $theme_option['in_panel'];
										$panel_id    = 'theme';

									}

									$panel_id = apply_filters( 'wmhook_{%= prefix_hook %}_tf_customize_panel_id', $panel_id, $theme_option, $theme_options );

									if ( $customizer_panel !== $panel_id ) {

										$wp_customize->add_panel(
												$panel_id,
												array(
													'title'       => esc_html( $panel_title ),
													'description' => ( isset( $theme_option['in_panel-description'] ) ) ? ( $theme_option['in_panel-description'] ) : ( '' ), // Hidden at the top of the panel
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
								 * Generic settings
								 */
								$generic = array(
										'label'           => $theme_option['label'],
										'description'     => $description,
										'section'         => $customizer_section,
										'priority'        => $priority,
										'type'            => $theme_option['type'],
										'active_callback' => ( isset( $theme_option['active_callback'] ) ) ? ( $theme_option['active_callback'] ) : ( null ),
										'input_attrs'     => ( isset( $theme_option['input_attrs'] ) ) ? ( $theme_option['input_attrs'] ) : ( array() ),
									);



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
												array_merge( $generic, array(
													'choices' => ( isset( $theme_option['choices'] ) ) ? ( $theme_option['choices'] ) : ( '' ),
												) )
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
												$generic
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
												array_merge( $generic, array(
													'context' => $option_id,
												) )
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
												array_merge( $generic, array(
													'choices' => ( isset( $theme_option['choices'] ) ) ? ( $theme_option['choices'] ) : ( '' ),
												) )
											) );

									break;

									/**
									 * Range
									 *
									 * Since WP4.0 there is also a "range" native input field. This will output
									 * HTML5 <input type="range" /> element - thus still using custom one.
									 *
									 * intval() used as sanitize callback causes PHP errors!
									 *
									 * Displaying pure text input field with `absint()` validation.
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
												$generic
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
												array_merge( $generic, array(
													'choices' => ( isset( $theme_option['choices'] ) ) ? ( $theme_option['choices'] ) : ( '' ),
												) )
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
												$generic
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
												$generic
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
