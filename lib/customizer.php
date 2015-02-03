<?php
/**
 * Customizer
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customizer
 * @copyright   2015 WebMan - Oliver Juhas
 *
 * @uses  Customizer options array
 * @uses  Custom CSS styles generator
 *
 * @since    3.0
 * @version  4.0
 *
 * CONTENT:
 * -  1) Required files
 * - 10) Actions and filters
 * - 20) Helpers
 * - 30) Sanitizing functions
 * - 40) Main customizer function
 * - 50) CSS styles
 */





/**
 * 1) Required files
 */

	//Customizer options array
		locate_template( WM_SETUP_DIR . 'setup-theme-options.php', true );





/**
 * 10) Actions and filters
 */

	/**
	 * Actions
	 */

		//Register customizer
			add_action( 'customize_register', 'wm_theme_customizer' );
		//Customizer assets
			add_action( 'customize_controls_enqueue_scripts', 'wm_customizer_enqueue_assets' );
		//Customizer saving
			add_action( 'update_option_' . WM_OPTION_CUSTOMIZER, 'wm_save_skin',         10 );
			add_action( 'update_option_' . WM_OPTION_CUSTOMIZER, 'wm_generate_all_css', 100 );
			add_action( 'update_option_' . 'background_color',   'wm_generate_all_css', 100 );





/**
 * 20) Helpers
 */

	/**
	 * Customizer controls assets enqueue
	 *
	 * @since    3.0 (named as wm_theme_customizer_assets())
	 * @version  4.0
	 */
	if ( ! function_exists( 'wm_customizer_enqueue_assets' ) ) {
		function wm_customizer_enqueue_assets() {

			/**
			 * Register
			 */

				//Styles
					wp_register_style( 'wm-customizer', wm_get_stylesheet_directory_uri( WM_LIBRARY_DIR . 'css/customizer.css' ), false, WM_SCRIPTS_VERSION, 'screen' );

				//Scripts
					wp_register_script( 'wm-customizer', wm_get_stylesheet_directory_uri( WM_LIBRARY_DIR . 'js/customizer.js' ), array( 'customize-controls' ), WM_SCRIPTS_VERSION, true );

			/**
			 * Enqueue
			 */

				//Styles
					wp_enqueue_style( 'wm-customizer' );

				//Scripts
					wp_enqueue_script( 'wm-customizer' );

		}
	} // /wm_customizer_enqueue_assets



	/**
	 * Outputs customizer JavaScript in footer
	 *
	 * Use this structure for customizer_js property:
	 * 'customizer_js' => array(
	 * 			'css'    => array(
	 * 					'.selector'         => array( 'css-property-name' ),
	 * 					'.another-selector' => array( array( 'padding-left', 'px' ) ),
	 * 				),
	 * 			'custom' => 'your_custom_JavaScript_here',
	 * 		)
	 *
	 * @since    3.0
	 * @version  4.0
	 */
	if ( ! function_exists( 'wm_theme_customizer_js' ) ) {
		function wm_theme_customizer_js() {
			//Helper variables
				$wm_skin_design = apply_filters( 'wmhook_theme_options', array() );

				$output = $output_single = '';

			//Preparing output
				if ( is_array( $wm_skin_design ) && ! empty( $wm_skin_design ) ) {

					foreach ( $wm_skin_design as $skin_option ) {

						if ( isset( $skin_option['customizer_js'] ) ) {

							$output_single  = "wp.customize( '" . WM_OPTION_CUSTOMIZER . "[" . WM_OPTION_PREFIX . $skin_option['id'] . "]" . "', function( value ) {"  . "\r\n";
							$output_single .= "\t" . 'value.bind( function( newval ) {' . "\r\n";

							if ( ! isset( $skin_option['customizer_js']['custom'] ) ) {

								foreach ( $skin_option['customizer_js']['css'] as $selector => $properties ) {

									if ( is_array( $properties ) ) {

										$output_single_css = '';

										foreach ( $properties as $property ) {

											if ( ! is_array( $property ) ) {
												$property = array( $property, '' );
											}
											if ( ! isset( $property[1] ) ) {
												$property[1] = '';
											}
											if ( trim( $property[1] ) ) {
												$property[1] = ' + "' . $property[1] . '"';
											}

											$output_single_css .= '.css( "' . $property[0] . '", newval' . $property[1] . ' )';

										} // /foreach

									}

									$output_single .= "\t\t" . '$( "' . $selector . '" )' . $output_single_css . ";\r\n";

								} // /foreach

							} else {

								$output_single .= "\t\t" . $skin_option['customizer_js']['custom'] . "\r\n";

							}

							$output_single .= "\t" . '} );' . "\r\n";
							$output_single .= '} );'. "\r\n";
							$output_single  = apply_filters( 'wmhook_wm_theme_customizer_js_option_' . $skin_option['id'], $output_single );

							$output .= $output_single;

						}

					} // /foreach

				}

			//Output
				if ( $output = apply_filters( 'wmhook_wm_theme_customizer_js_output', $output ) ) {
					echo '<!-- Theme custom scripts -->' . "\r\n" . '<script type="text/javascript"><!--' . "\r\n" . '( function( $ ) {' . "\r\n\r\n" . trim( $output ) . "\r\n\r\n" . '} )( jQuery );' . "\r\n" . '//--></script>';
				}
		}
	} // /wm_theme_customizer_js





/**
 * 30) Sanitizing functions
 */

	/**
	 * Sanitize email
	 *
	 * @since    3.0
	 * @version  3.0
	 *
	 * @param  mixed $value WP customizer value to sanitize.
	 */
	if ( ! function_exists( 'wm_sanitize_email' ) ) {
		function wm_sanitize_email( $value ) {
			//Helper variables
				$value = ( is_email( trim( $value ) ) ) ? ( trim( $value ) ) : ( null );

			//Output
				return apply_filters( 'wmhook_wm_sanitize_email_output', $value );
		}
	} // /wm_sanitize_email



	/**
	 * Sanitize texts
	 *
	 * @since    4.0
	 * @version  4.0
	 *
	 * @param  mixed $value WP customizer value to sanitize.
	 */
	if ( ! function_exists( 'wm_sanitize_text' ) ) {
		function wm_sanitize_text( $value ) {
			return apply_filters( 'wmhook_wm_sanitize_text_output', wp_kses_post( force_balance_tags( $value ) ) );
		}
	} // /wm_sanitize_text



	/**
	 * No sanitization at all, simply return the value
	 *
	 * Useful for when the value may be of mixed type, such as array-or-string.
	 *
	 * @since    3.0
	 * @version  3.0
	 *
	 * @param  mixed $value WP customizer value to sanitize.
	 */
	if ( ! function_exists( 'wm_sanitize_return_value' ) ) {
		function wm_sanitize_return_value( $value ) {
			return apply_filters( 'wmhook_wm_sanitize_return_value_output', $value );
		}
	} // /wm_sanitize_return_value





/**
 * 40) Main customizer function
 */

	/**
	 * Registering sections and options for WP Customizer
	 *
	 * @since    3.0
	 * @version  4.0
	 *
	 * @param  object $wp_customize WP customizer object.
	 */
	if ( ! function_exists( 'wm_theme_customizer' ) ) {
		function wm_theme_customizer( $wp_customize ) {

			//Make predefined controls use live preview JS
				$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
				$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
				$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';



			/**
			 * Custom customizer controls
			 *
			 * @link  https://github.com/bueltge/Wordpress-Theme-Customizer-Custom-Controls
			 * @link  http://ottopress.com/2012/making-a-custom-control-for-the-theme-customizer/
			 */

				locate_template( WM_LIBRARY_DIR . 'inc/controls/class-WM_Customizer_Hidden.php',      true );
				locate_template( WM_LIBRARY_DIR . 'inc/controls/class-WM_Customizer_HTML.php',        true );
				locate_template( WM_LIBRARY_DIR . 'inc/controls/class-WM_Customizer_Image.php',       true );
				locate_template( WM_LIBRARY_DIR . 'inc/controls/class-WM_Customizer_Multiselect.php', true );
				locate_template( WM_LIBRARY_DIR . 'inc/controls/class-WM_Customizer_Range.php',       true );
				locate_template( WM_LIBRARY_DIR . 'inc/controls/class-WM_Customizer_Radiocustom.php', true );
				locate_template( WM_LIBRARY_DIR . 'inc/controls/class-WM_Customizer_Select.php',      true );
				if ( ! wm_check_wp_version( 4 ) ) {
					locate_template( WM_LIBRARY_DIR . 'inc/controls/class-WM_Customizer_Textarea.php', true );
				}

				do_action( 'wmhook_wm_theme_customizer_load_controls', $wp_customize );



			//Helper variables
				$wm_skin_design = (array) apply_filters( 'wmhook_theme_options', array() );

				$allowed_option_types = apply_filters( 'wmhook_wm_theme_customizer_allowed_option_types', array(
						'background',
						'checkbox',
						'color',
						'email',
						'hidden',
						'html',
						'image',
						'multiselect',
						'password',
						'radio',
						'radiocustom',
						'range',
						'select',
						'slider', //synonym for 'range'
						'text',
						'textarea',
						'theme-customizer-html', //synonym for 'html'
						'url',
					) );

				//To make sure our customizer sections start after WordPress default ones
					$priority = apply_filters( 'wmhook_wm_theme_customizer_priority', 900 );
				//Default section name in case not set (should be overwritten anyway)
					$customizer_panel   = '';
					$customizer_section = WM_THEME_SHORTNAME;

				/**
				 * Use add_setting() -> 'type' => 'option' (instead of 'theme_mod') for better
				 * upgradability from "lite" to "pro" themes.
				 *
				 * @link  http://wordpress.stackexchange.com/questions/155072/get-option-vs-get-theme-mod-why-is-one-slower
				 */
				$type = apply_filters( 'wmhook_wm_theme_customizer_type', 'option' );

			//Generate customizer options
				if ( is_array( $wm_skin_design ) && ! empty( $wm_skin_design ) ) {

					foreach ( $wm_skin_design as $skin_option ) {

						if (
								is_array( $skin_option )
								&& isset( $skin_option['type'] )
								&& (
										in_array( $skin_option['type'], $allowed_option_types )
										|| isset( $skin_option['theme-customizer-section'] )
									)
							) {

							//Helper variables
								$priority++;

								$option_id = $default = $description = '';

								if ( isset( $skin_option['id'] ) ) {
									$option_id = WM_OPTION_PREFIX . $skin_option['id'];
								}
								if ( isset( $skin_option['default'] ) ) {
									$default = $skin_option['default'];
								}
								if ( isset( $skin_option['description'] ) ) {
									$description = $skin_option['description'];
								}

								$transport = ( isset( $skin_option['customizer_js'] ) ) ? ( 'postMessage' ) : ( 'refresh' );



							/**
							 * Panels
							 *
							 * Panels were introduced in WordPress 4.0 and are wrappers for customizer sections.
							 * Note that the panel will not be displayed unless sections are assigned to it.
							 * Set the panel name in the section declaration with 'theme-customizer-panel' attribute.
							 * Panel has to be defined for each section to prevent all sections residing within a single panel.
							 *
							 * @link  http://make.wordpress.org/core/2014/07/08/customizer-improvements-in-4-0/
							 *
							 * @since    3.2, WordPress 4.0
							 * @version  4.0
							 */
							if (
									wm_check_wp_version( 4 )
									&& isset( $skin_option['theme-customizer-panel'] )
								) {

								$panel_id = sanitize_title( trim( $skin_option['theme-customizer-panel'] ) );

								if ( $customizer_panel != $panel_id ) {

									$wp_customize->add_panel(
											$panel_id,
											array(
												'title'       => $skin_option['theme-customizer-panel'], //panel title
												'description' => ( isset( $skin_option['theme-customizer-panel-description'] ) ) ? ( $skin_option['theme-customizer-panel-description'] ) : ( '' ), //Displayed at the top of panel
												'priority'    => $priority,
											)
										);

									$customizer_panel = $panel_id;

								}

							}



							/**
							 * Sections
							 *
							 * @version  4.0
							 */
							if (
									isset( $skin_option['theme-customizer-section'] )
									&& trim( $skin_option['theme-customizer-section'] )
								) {

								if ( empty( $option_id ) ) {
									$option_id = sanitize_title( trim( $skin_option['theme-customizer-section'] ) );
								}

								$customizer_section = array(
										'id'    => $option_id,
										'setup' => array(
												'title'       => $skin_option['theme-customizer-section'], //section title
												'description' => ( isset( $skin_option['theme-customizer-section-description'] ) ) ? ( $skin_option['theme-customizer-section-description'] ) : ( '' ), //Displayed at the top of section
												'priority'    => $priority,
											)
									);

								if ( wm_check_wp_version( 4 ) ) {
									$customizer_section['setup']['panel'] = $customizer_panel;
								}

								$wp_customize->add_section(
										$customizer_section['id'],
										$customizer_section['setup']
									);

								$customizer_section = $customizer_section['id'];
								$customizer_panel   = ''; //Panel has to be defined for each section to prevent all sections residing within a single panel.

							}



							/**
							 * Options generator
							 */
							switch ( $skin_option['type'] ) {

								/**
								 * Background combo options
								 */
								case 'background':

									$wp_customize->add_setting(
											WM_OPTION_CUSTOMIZER . '[' . $option_id . '-bg-color]',
											array(
												'type'                 => $type,
												'default'              => ( isset( $default['color'] ) ) ? ( $default['color'] ) : ( null ),
												'transport'            => $transport,
												'sanitize_callback'    => 'sanitize_hex_color_no_hash',
												'sanitize_js_callback' => 'maybe_hash_hex_color',
											)
										);

										$wp_customize->add_control( new WP_Customize_Color_Control(
												$wp_customize,
												WM_OPTION_CUSTOMIZER . '[' . $option_id . '-bg-color]',
												array(
													'label'    => __( 'Background color', 'wm_domain' ),
													'section'  => $customizer_section,
													'priority' => $priority,
												)
											) );

									$wp_customize->add_setting(
											WM_OPTION_CUSTOMIZER . '[' . $option_id . '-bg-url]',
											array(
												'type'                 => $type,
												'default'              => ( isset( $default['url'] ) ) ? ( $default['url'] ) : ( null ),
												'transport'            => $transport,
												'sanitize_callback'    => 'wm_sanitize_return_value',
												'sanitize_js_callback' => 'wm_sanitize_return_value',
											)
										);

										$wp_customize->add_control( new WM_Customizer_Image(
												$wp_customize,
												WM_OPTION_CUSTOMIZER . '[' . $option_id . '-bg-url]',
												array(
													'label'    => __( 'Background image', 'wm_domain' ),
													'section'  => $customizer_section,
													'priority' => ++$priority,
													'context'  => WM_OPTION_CUSTOMIZER . '[' . $option_id . '-bg-url]',
												)
											) );

									$wp_customize->add_setting(
											WM_OPTION_CUSTOMIZER . '[' . $option_id . '-bg-url-hidpi]',
											array(
												'type'                 => $type,
												'default'              => ( isset( $default['url-hidpi'] ) ) ? ( $default['url-hidpi'] ) : ( null ),
												'transport'            => $transport,
												'sanitize_callback'    => 'wm_sanitize_return_value',
												'sanitize_js_callback' => 'wm_sanitize_return_value',
											)
										);

										$wp_customize->add_control( new WM_Customizer_Image(
												$wp_customize,
												WM_OPTION_CUSTOMIZER . '[' . $option_id . '-bg-url-hidpi]',
												array(
													'label'    => __( 'Background image (HD)', 'wm_domain' ),
													'section'  => $customizer_section,
													'priority' => ++$priority,
													'context'  => WM_OPTION_CUSTOMIZER . '[' . $option_id . '-bg-url-hidpi]',
												)
											) );

									if ( function_exists( 'wm_helper_var' ) ) {

										$wp_customize->add_setting(
												WM_OPTION_CUSTOMIZER . '[' . $option_id . '-bg-position]',
												array(
													'type'                 => $type,
													'default'              => ( isset( $default['position'] ) ) ? ( $default['position'] ) : ( '50% 0' ),
													'transport'            => $transport,
													'sanitize_callback'    => 'esc_attr',
													'sanitize_js_callback' => 'esc_attr',
												)
											);

											$wp_customize->add_control( new WM_Customizer_Radiocustom(
													$wp_customize,
													WM_OPTION_CUSTOMIZER . '[' . $option_id . '-bg-position]',
													array(
														'label'    => __( 'Background position', 'wm_domain' ),
														'section'  => $customizer_section,
														'priority' => ++$priority,
														'choices'  => wm_helper_var( 'bg-css', 'position' ),
														'class'    => 'matrix',
													)
												) );

										$wp_customize->add_setting(
												WM_OPTION_CUSTOMIZER . '[' . $option_id . '-bg-repeat]',
												array(
													'type'                 => $type,
													'default'              => ( isset( $default['repeat'] ) ) ? ( $default['repeat'] ) : ( 'no-repeat' ),
													'transport'            => $transport,
													'sanitize_callback'    => 'esc_attr',
													'sanitize_js_callback' => 'esc_attr',
												)
											);

											$wp_customize->add_control( new WM_Customizer_Radiocustom(
													$wp_customize,
													WM_OPTION_CUSTOMIZER . '[' . $option_id . '-bg-repeat]',
													array(
														'label'    => __( 'Background repeat', 'wm_domain' ),
														'section'  => $customizer_section,
														'priority' => ++$priority,
														'choices'  => wm_helper_var( 'bg-css', 'repeat' ),
														'class'    => 'image-radio',
													)
												) );

										$wp_customize->add_setting(
												WM_OPTION_CUSTOMIZER . '[' . $option_id . '-bg-attachment]',
												array(
													'type'                 => $type,
													'default'              => ( isset( $default['attachment'] ) ) ? ( $default['attachment'] ) : ( 'scroll' ),
													'transport'            => $transport,
													'sanitize_callback'    => 'esc_attr',
													'sanitize_js_callback' => 'esc_attr',
												)
											);

											$wp_customize->add_control(
													WM_OPTION_CUSTOMIZER . '[' . $option_id . '-bg-attachment]',
													array(
														'label'    => __( 'Background attachment', 'wm_domain' ),
														'section'  => $customizer_section,
														'priority' => ++$priority,
														'type'     => 'select',
														'choices'  => wm_helper_var( 'bg-css', 'scroll' ),
													)
												);

										$wp_customize->add_setting(
												WM_OPTION_CUSTOMIZER . '[' . $option_id . '-bg-size]',
												array(
													'type'                 => $type,
													'default'              => ( isset( $default['size'] ) ) ? ( $default['size'] ) : ( '' ),
													'transport'            => $transport,
													'sanitize_callback'    => 'esc_attr',
													'sanitize_js_callback' => 'esc_attr',
												)
											);

											$wp_customize->add_control( new WM_Customizer_Radiocustom(
													$wp_customize,
													WM_OPTION_CUSTOMIZER . '[' . $option_id . '-bg-size]',
													array(
														'label'    => __( 'Background size', 'wm_domain' ),
														'section'  => $customizer_section,
														'priority' => ++$priority,
														'choices'  => wm_helper_var( 'bg-css', 'size' ),
														'class'    => 'image-radio',
													)
												) );

									}

								break;

								/**
								 * Color
								 */
								case 'color':

									$wp_customize->add_setting(
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
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
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'label'       => $skin_option['label'],
												'description' => $description,
												'section'     => $customizer_section,
												'priority'    => $priority,
											)
										) );

								break;

								/**
								 * Email
								 *
								 * @since  3.2, WordPress 4.0
								 */
								case 'email':

									if ( wm_check_wp_version( 4 ) ) {

										$wp_customize->add_setting(
												WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
												array(
													'type'                 => $type,
													'default'              => $default,
													'transport'            => $transport,
													'sanitize_callback'    => 'wm_sanitize_email',
													'sanitize_js_callback' => 'wm_sanitize_email',
												)
											);

										$wp_customize->add_control(
												WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
												array(
													'type'        => 'email',
													'label'       => $skin_option['label'],
													'description' => $description,
													'section'     => $customizer_section,
													'priority'    => $priority,
												)
											);

									}

								break;

								/**
								 * Hidden
								 */
								case 'hidden':

									$wp_customize->add_setting(
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
												'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
											)
										);

									$wp_customize->add_control( new WM_Customizer_Hidden(
											$wp_customize,
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
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
								case 'theme-customizer-html':

									if ( empty( $option_id ) ) {
										$option_id = 'custom-title-' . $priority;
									}

									$wp_customize->add_setting(
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'sanitize_callback'    => 'wm_sanitize_text',
												'sanitize_js_callback' => 'wm_sanitize_text',
											)
										);

									$wp_customize->add_control( new WM_Customizer_HTML(
											$wp_customize,
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'label'    => $skin_option['content'],
												'section'  => $customizer_section,
												'priority' => $priority,
											)
										) );

								break;

								/**
								 * Image
								 */
								case 'image':

									$wp_customize->add_setting(
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'wm_sanitize_return_value' ),
												'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'wm_sanitize_return_value' ),
											)
										);

									$wp_customize->add_control( new WM_Customizer_Image(
											$wp_customize,
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'label'       => $skin_option['label'],
												'description' => $description,
												'section'     => $customizer_section,
												'priority'    => $priority,
												'context'     => WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											)
										) );

								break;

								/**
								 * Checkbox, radio
								 */
								case 'checkbox':
								case 'radio':

									$wp_customize->add_setting(
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
												'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
											)
										);

									$wp_customize->add_control(
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'label'       => $skin_option['label'],
												'description' => $description,
												'section'     => $customizer_section,
												'priority'    => $priority,
												'type'        => $skin_option['type'],
												'choices'     => ( isset( $skin_option['options'] ) ) ? ( $skin_option['options'] ) : ( '' ),
											)
										);

								break;

								/**
								 * Multiselect
								 */
								case 'multiselect':

									$wp_customize->add_setting(
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'wm_sanitize_return_value' ),
												'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'wm_sanitize_return_value' ),
											)
										);

									$wp_customize->add_control( new WM_Customizer_Multiselect(
											$wp_customize,
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'label'       => $skin_option['label'],
												'description' => $description,
												'section'     => $customizer_section,
												'priority'    => $priority,
												'choices'     => ( isset( $skin_option['options'] ) ) ? ( $skin_option['options'] ) : ( '' ),
											)
										) );

								break;

								/**
								 * Range / Slider
								 *
								 * Since WP4.0 there is also a "range" native input field. This will output
								 * HTML5 <input type="range" /> element - thus still using custom one.
								 */
								case 'range':
								case 'slider':

									$wp_customize->add_setting(
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'intval' ),
												'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'intval' ),
											)
										);

									$wp_customize->add_control( new WM_Customizer_Range(
											$wp_customize,
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'label'       => $skin_option['label'],
												'description' => $description,
												'section'     => $customizer_section,
												'priority'    => $priority,
												'json'        => array( $skin_option['min'], $skin_option['max'], $skin_option['step'] ),
											)
										) );

								break;

								/**
								 * Password
								 *
								 * @since  3.2, WordPress 4.0
								 */
								case 'password':

									if ( wm_check_wp_version( 4 ) ) {

										$wp_customize->add_setting(
												WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
												array(
													'type'                 => $type,
													'default'              => $default,
													'transport'            => $transport,
													'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
													'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
												)
											);

										$wp_customize->add_control(
												WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
												array(
													'type'        => 'password',
													'label'       => $skin_option['label'],
													'description' => $description,
													'section'     => $customizer_section,
													'priority'    => $priority,
												)
											);

									}

								break;

								/**
								 * Radio custom labels
								 */
								case 'radiocustom':

									$wp_customize->add_setting(
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
												'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
											)
										);

									$wp_customize->add_control( new WM_Customizer_Radiocustom(
											$wp_customize,
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'label'       => $skin_option['label'],
												'description' => $description,
												'section'     => $customizer_section,
												'priority'    => $priority,
												'choices'     => ( isset( $skin_option['options'] ) ) ? ( $skin_option['options'] ) : ( '' ),
												'class'       => ( isset( $skin_option['class'] ) ) ? ( $skin_option['class'] ) : ( '' ),
											)
										) );

								break;

								/**
								 * Select (with optgroups)
								 */
								case 'select':

									$wp_customize->add_setting(
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
												'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_attr' ),
											)
										);

									$wp_customize->add_control( new WM_Customizer_Select(
											$wp_customize,
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'label'       => $skin_option['label'],
												'description' => $description,
												'section'     => $customizer_section,
												'priority'    => $priority,
												'choices'     => ( isset( $skin_option['options'] ) ) ? ( $skin_option['options'] ) : ( '' ),
											)
										) );

								break;

								/**
								 * Text
								 */
								case 'text':

									$wp_customize->add_setting(
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_textarea' ),
												'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_textarea' ),
											)
										);

									$wp_customize->add_control(
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'label'       => $skin_option['label'],
												'description' => $description,
												'section'     => $customizer_section,
												'priority'    => $priority,
											)
										);

								break;

								/**
								 * Textarea
								 *
								 * Since WordPress 4.0 this is native input field.
								 */
								case 'textarea':

									$wp_customize->add_setting(
											WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_textarea' ),
												'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_textarea' ),
											)
										);

									if ( wm_check_wp_version( 4 ) ) {

										$wp_customize->add_control(
												WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
												array(
													'type'        => 'textarea',
													'label'       => $skin_option['label'],
													'description' => $description,
													'section'     => $customizer_section,
													'priority'    => $priority,
												)
											);

									} else {

										$wp_customize->add_control( new WM_Customizer_Textarea(
												$wp_customize,
												WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
												array(
													'label'       => $skin_option['label'],
													'description' => $description,
													'section'     => $customizer_section,
													'priority'    => $priority,
												)
											) );

									}

								break;

								/**
								 * URL
								 *
								 * @since  3.2, WordPress 4.0
								 */
								case 'url':

									if ( wm_check_wp_version( 4 ) ) {

										$wp_customize->add_setting(
												WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
												array(
													'type'                 => $type,
													'default'              => $default,
													'transport'            => $transport,
													'sanitize_callback'    => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_url' ),
													'sanitize_js_callback' => ( isset( $skin_option['validate'] ) ) ? ( $skin_option['validate'] ) : ( 'esc_url' ),
												)
											);

										$wp_customize->add_control(
												WM_OPTION_CUSTOMIZER . '[' . $option_id . ']',
												array(
													'type'        => 'url',
													'label'       => $skin_option['label'],
													'description' => $description,
													'section'     => $customizer_section,
													'priority'    => $priority,
												)
											);

									}

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

			//Assets needed for customizer preview
				if ( $wp_customize->is_preview() ) {
					add_action( 'wp_footer', 'wm_theme_customizer_js', 99 );
				}
		}
	} // /wm_theme_customizer





/**
 * 50) CSS styles
 */

	/**
	 * Saves and loads a skin
	 *
	 * Creates a new skin JSON file and/or
	 * loads a selected skin settings.
	 *
	 * @since    3.0
	 * @version  3.4
	 */
	if ( ! function_exists( 'wm_save_skin' ) ) {
		function wm_save_skin() {
			//Requirements check
				if ( ! isset( $_POST['customized'] ) ) {
					return false;
				}

			//Helper variables
				$skin_new = $skin_load = '';
				$output   = array();

				$theme_skin_dir = wp_upload_dir();
				$theme_skin_dir = trailingslashit( $theme_skin_dir['basedir'] ) . 'wmtheme-' . WM_THEME_SHORTNAME . '/skins';
				$theme_skin_dir = apply_filters( 'wmhook_wm_save_skin_theme_skin_dir', $theme_skin_dir );

			//Output
				//Get customizer $_POST values
					$customizer_values = json_decode( wp_unslash( $_POST['customized'] ), true );

				//Process the customizer values and get only those from WM_OPTION_CUSTOMIZER
					foreach ( $customizer_values as $key => $value ) {
						if (
								false !== strpos( $key, WM_OPTION_CUSTOMIZER )
								&& false === strpos( $key, 'custom-title-' ) //ignore Customizer sections titles
							) {
							$key = str_replace( array( WM_OPTION_CUSTOMIZER, '[', ']' ), '', $key );
							$output[ $key ] = $value;
						}
					}

				//Set a new skin file name
					if (
							isset( $output[ WM_OPTION_PREFIX . 'skin-new' ] )
							&& isset( $output[ WM_OPTION_PREFIX . 'skin-load' ] )
						) {
						$skin_load = $output[ WM_OPTION_PREFIX . 'skin-load' ];
						$skin_new  = sanitize_title( $output[ WM_OPTION_PREFIX . 'skin-new' ] );

						unset( $output[ WM_OPTION_PREFIX . 'skin-load' ] );
						unset( $output[ WM_OPTION_PREFIX . 'skin-new' ] );
					}

				//Create a new skin
					if ( $skin_new ) {

						//Create the theme skins folder
							if ( ! wma_create_folder( $theme_skin_dir ) ) {
								set_transient( 'wmamp-admin-notice', array( "<strong>ERROR: Wasn't able to create a theme skins folder! Contact the theme support.</strong>", 'error', 'switch_themes', 2 ), ( 60 * 60 * 48 ) );

								delete_option( 'wm-' . WM_THEME_SHORTNAME . '-skins' );
								return false;
							}

						//Write the skin JSON file
							$json_path = apply_filters( 'wmhook_wm_save_skin_json_path', trailingslashit( $theme_skin_dir ) . $skin_new . '.json' );

							if ( is_array( $output ) && ! empty( $output ) ) {
								$output = apply_filters( 'wmhook_wm_save_skin_output', $output );

								wma_write_local_file( $json_path, json_encode( $output ) );

								//Remove load/save skin names from settings in DB
									$skin_settings = get_option( WM_OPTION_CUSTOMIZER );
									unset( $skin_settings[ WM_OPTION_PREFIX . 'skin-load' ] );
									unset( $skin_settings[ WM_OPTION_PREFIX . 'skin-new' ] );
									update_option( WM_OPTION_CUSTOMIZER, $skin_settings );

								update_option( 'wm-' . WM_THEME_SHORTNAME . '-skins', array_unique( array( WM_SKINS_DIR, WM_SKINS_DIR_CHILD, $theme_skin_dir ) ) );

								//Run additional actions
									do_action( 'wmhook_save_skin', $skin_new, $customizer_values );

								return true;
							}

						delete_option( 'wm-' . WM_THEME_SHORTNAME . '-skins' );

				//Load a selected skin
					} elseif ( $skin_load ) {

						//Check if file exists
							if ( ! file_exists( $skin_load ) ) {
								return false;
							}

						//Get the skin slug
							$skin_slug = str_replace( array( '.json', WM_SKINS_DIR, WM_SKINS_DIR_CHILD, $theme_skin_dir ), '', $skin_load );

						//We don't need to write to the file, so just open for reading.
							$skin_load = wma_read_local_file( $skin_load );

							$replacements = (array) apply_filters( 'wmhook_generate_css_replacements', array() );
							$skin_load    = strtr( $skin_load, $replacements );

						//Decoding new imported skin JSON string and converting object to array
							if ( ! empty( $skin_load ) ) {
								$skin_load = json_decode( trim( $skin_load ), true );
								update_option( WM_OPTION_CUSTOMIZER, $skin_load );
								update_option( 'wm-' . WM_THEME_SHORTNAME . '-skin-used', $skin_slug );
							}

						//Run additional actions
							do_action( 'wmhook_load_skin', $skin_load, $customizer_values );

						return true;

					}

				return false;
		}
	} // /wm_save_skin

?>