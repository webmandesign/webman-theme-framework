<?php
/**
 * Customize Options Generator class
 *
 * @package    WebMan WordPress Theme Framework
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  2.8.0
 *
 * Contents:
 *
 *  0) Init
 * 10) Assets
 * 20) Customizer core
 * 30) Getters
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Theme_Slug_Library_Customize {





	/**
	 * 0) Init
	 */

		public static $mods = false;

		public static $theme_options_setup = false;



		/**
		 * Initialization.
		 *
		 * @since    1.0.0
		 * @version  2.8.0
		 *
		 * @return  void
		 */
		public static function init() {

			// Processing

				// Hooks

					// Actions

						add_action( 'customize_register', __CLASS__ . '::register', 100 );

						add_action( 'customize_controls_enqueue_scripts', __CLASS__ . '::assets' );

		} // /init





	/**
	 * 10) Assets
	 */

		/**
		 * Customizer controls assets
		 *
		 * @since    1.0.0
		 * @version  2.8.0
		 *
		 * @return  void
		 */
		public static function assets() {

			// Processing

				// Styles

					wp_enqueue_style(
						'theme_slug-customize-controls',
						get_theme_file_uri( THEME_SLUG_LIBRARY_DIR . 'css/customize.css' ),
						false,
						THEME_SLUG_THEME_VERSION,
						'screen'
					);
					wp_style_add_data( 'theme_slug-customize-controls', 'rtl', 'replace' );

				// Scripts

					wp_enqueue_script(
						'theme_slug-customize-controls',
						get_theme_file_uri( THEME_SLUG_LIBRARY_DIR . 'js/customize-controls.js' ),
						array( 'customize-controls' ),
						THEME_SLUG_THEME_VERSION,
						true
					);

		} // /assets



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
		 * preview refresh. This is useful to disable welcome page, for example.
		 *
		 * The actual JavaScript is outputted in the footer of the page.
		 *
		 * @example
		 *   'preview_js' => array(
		 *
		 *     // Setting CSS styles:
		 *     'css' => array(
		 *
		 *       // CSS variables (the `[[id]]` gets replaced with option ID)
		 * 			 ':root' => array(
		 *         '--[[id]]',
		 *       ),
		 * 			 ':root' => array(
		 *         array(
		 *           'property' => '--[[id]]',
		 *           'suffix'   => 'px',
		 *         ),
		 *       ),
		 *
		 *       // Sets the whole value to the `css-property-name` of the `selector`
		 *       'selector' => array(
		 *         'background-color',...
		 *       ),
		 *
		 *       // Sets the `css-property-name` of the `selector` with specific settings
		 *       'selector' => array(
		 *         array(
		 *           'property'         => 'text-shadow',
		 *           'prefix'           => '0 1px 1px rgba(',
		 *           'suffix'           => ', .5)',
		 *           'process_callback' => 'themeSlug.Customize.hexToRgb',
		 *           'custom'           => '0 0 0 1em [[value]] ), 0 0 0 2em transparent, 0 0 0 3em [[value]]',
		 *         ),...
		 *       ),
		 *
		 *       // Replaces "@" in `selector` for `selector-replace-value` (such as "@ h2, @ h3" to ".footer h2, .footer h3")
		 *       'selector' => array(
		 *         'selector_replace' => 'selector-replace-value',
		 *         'selector_before'  => '@media (min-width: 80em) {',
		 *         'selector_after'   => '}',
		 *         'background-color',...
		 *       ),
		 *
		 *     ),
		 *
		 *     // And/or setting custom JavaScript:
		 *     'custom' => 'JavaScript here', // Such as "$( '.site-header' ).toggleClass( 'sticky' );"
		 *
		 *   );
		 *
		 * @since    1.0.0
		 * @version  2.8.0
		 *
		 * @return  void
		 */
		public static function preview_scripts() {

			// Pre

				/**
				 * Bypass filter for Theme_Slug_Library_Customize::preview_scripts().
				 *
				 * Returning a non-false value will short-circuit the method,
				 * returning the passed value instead.
				 *
				 * @since  2.8.0
				 *
				 * @param  mixed $pre  Default: false. If not false, method returns this value as string.
				 */
				$pre = apply_filters( 'pre/theme_slug/library_customize/preview_scripts', false );

				if ( false !== $pre ) {
					return (string) $pre;
				}


			// Variables

				$theme_options = self::get_options();

				ksort( $theme_options );

				$output = $output_single = '';


			// Processing

				if (
					is_array( $theme_options )
					&& ! empty( $theme_options )
				) {
					foreach ( $theme_options as $theme_option ) {
						if (
							isset( $theme_option['preview_js'] )
							&& is_array( $theme_option['preview_js'] )
						) {
							$option_id = sanitize_title( $theme_option['id'] );

							$output_single  = "wp.customize("  . PHP_EOL;
							$output_single .= "\t" . "'" . $option_id . "',"  . PHP_EOL;
							$output_single .= "\t" . "function( value ) {"  . PHP_EOL;
							$output_single .= "\t\t" . 'value.bind( function( to ) {' . PHP_EOL;

							// CSS

								if ( isset( $theme_option['preview_js']['css'] ) ) {

									$output_single .= "\t\t\t" . "var newCss = '';" . PHP_EOL.PHP_EOL;
									$output_single .= "\t\t\t" . "if ( $( '#jscss-" . $option_id . "' ).length ) { $( '#jscss-" . $option_id . "' ).remove() }" . PHP_EOL.PHP_EOL;

									foreach ( $theme_option['preview_js']['css'] as $selector => $properties ) {
										if ( is_array( $properties ) ) {
											$output_single_css = $selector_before = $selector_after = '';

											foreach ( $properties as $key => $property ) {

												// Selector setup

													if ( 'selector_replace' === $key ) {
														if ( is_array( $property ) ) {
															$selector_replaced = array();
															foreach ( $property as $replace ) {
																$selector_replaced[] = str_replace( '@', (string) $replace, $selector );
															}
															$selector = implode( ', ', $selector_replaced );
														} else {
															$selector = str_replace( '@', (string) $property, $selector );
														}
														continue;
													}

													if ( 'selector_before' === $key ) {
														$selector_before = $property;
														continue;
													}

													if ( 'selector_after' === $key ) {
														$selector_after = $property;
														continue;
													}

												// CSS properties setup

													if ( ! is_array( $property ) ) {
														$property = array( 'property' => (string) $property );
													}

													$property = wp_parse_args( (array) $property, array(
														'custom'           => '',
														'prefix'           => '',
														'process_callback' => '',
														'property'         => '',
														'suffix'           => '',
													) );

													// Replace `[[id]]` placeholder with an option ID.
													$property['property'] = str_replace(
														'[[id]]',
														$option_id,
														$property['property']
													);

													$value = ( empty( $property['process_callback'] ) ) ? ( 'to' ) : ( trim( $property['process_callback'] ) . '( to )' );

													if ( empty( $property['custom'] ) ) {
														$output_single_css .= $property['property'] . ": " . $property['prefix'] . "' + " . esc_attr( $value ) . " + '" . $property['suffix'] . "; ";
													} else {
														$output_single_css .= $property['property'] . ": " . str_replace( '[[value]]', "' + " . esc_attr( $value ) . " + '", $property['custom'] ) . "; ";
													}

											}

											$output_single .= "\t\t\t" . "newCss += '" . $selector_before . $selector . " { " . $output_single_css . "}" . $selector_after . " ';" . PHP_EOL;

										}
									}

									$output_single .= PHP_EOL . "\t\t\t" . "$( document ).find( 'head' ).append( $( '<style id=\'jscss-" . $option_id . "\'> ' + newCss + '</style>' ) );" . PHP_EOL;

								}

							// Custom JS

								if ( isset( $theme_option['preview_js']['custom'] ) ) {
									$output_single .= "\t\t" . $theme_option['preview_js']['custom'] . PHP_EOL;
								}

							$output_single .= "\t\t" . '} );' . PHP_EOL;
							$output_single .= "\t" . '}'. PHP_EOL;
							$output_single .= ');'. PHP_EOL;

							/**
							 * Filters single customizer theme option preview JavaScript code.
							 *
							 * The dynamic portion of the hook name, `$option_id`, refers to the single theme
							 * option ID. For example, 'color_accent', 'color_accent_text', and so on depending
							 * on the theme options.
							 *
							 * @since  2.8.0
							 *
							 * @param  string $output_single
							 */
							$output_single = (string) apply_filters( "theme_slug/library_customize/preview_scripts/option_{$option_id}", $output_single );

							$output .= $output_single;

						}
					}
				}

				$output = trim( $output );


			// Output

				if ( $output ) {
					/**
					 * Filters final output of customizer theme options preview JavaScript code.
					 *
					 * @since  2.8.0
					 *
					 * @param  string $output
					 */
					echo (string) apply_filters( 'theme_slug/library_customize/preview_scripts/output', '<!-- Theme custom scripts -->' . PHP_EOL . '<script type="text/javascript"><!--' . PHP_EOL . '( function( $ ) {' . PHP_EOL.PHP_EOL . trim( $output ) . PHP_EOL.PHP_EOL . '} )( jQuery );' . PHP_EOL . '//--></script>' );
				}

		} // /preview_scripts





	/**
	 * 20) Customizer core
	 */

		/**
		 * Customizer renderer.
		 *
		 * @since    1.0.0
		 * @version  2.8.0
		 *
		 * @param  WP_Customize_Manager $wp_customize
		 *
		 * @return  void
		 */
		public static function register( WP_Customize_Manager $wp_customize ) {

			// Pre

				/**
				 * Bypass filter for Theme_Slug_Library_Customize::register().
				 *
				 * Returning a non-null value will short-circuit the method,
				 * returning the passed value instead.
				 *
				 * @since  2.8.0
				 *
				 * @param  mixed                $pre           Default: null. If not null, method returns the value.
				 * @param  WP_Customize_Manager $wp_customize  Customizer object.
				 */
				$pre = apply_filters( 'pre/theme_slug/library_customize/register', null, $wp_customize );

				if ( null !== $pre ) {
					return $pre;
				}


			// Variables

				$theme_options = self::get_options();

				ksort( $theme_options );

				/**
				 * Filters customizer allowed option types for the theme options.
				 *
				 * @since  2.8.0
				 *
				 * @param  array $option_types
				 */
				$allowed_option_types = (array) apply_filters( 'theme_slug/library_customize/register/allowed_option_types', array(
					'checkbox',
					'color',
					'email',
					'hidden',
					'html',
					'image',
					'multicheckbox',
					'multiselect',
					'password',
					'radio',
					'radiomatrix',
					'range',
					'section',
					'select',
					'text',
					'textarea',
					'url',
				) );

				// Theme options comes first.

					$priority = absint(
						/**
						 * Filters customizer theme options priority.
						 *
						 * @since  2.8.0
						 *
						 * @param  int $priority
						 */
						apply_filters( 'theme_slug/library_customize/register/priority', 0 )
					);

				// Default section name in case not set (should be overwritten anyway)

					$customizer_panel   = '';
					$customizer_section = 'theme-slug';

				// Option type

					$type = 'theme_mod';


			// Processing

				// Custom controls

					/**
					 * @link  https://github.com/bueltge/Wordpress-Theme-Customizer-Custom-Controls
					 * @link  http://ottopress.com/2012/making-a-custom-control-for-the-theme-customizer/
					 */

					require_once THEME_SLUG_LIBRARY . 'includes/classes/class-customize-control-hidden.php';
					require_once THEME_SLUG_LIBRARY . 'includes/classes/class-customize-control-html.php';
					require_once THEME_SLUG_LIBRARY . 'includes/classes/class-customize-control-multiselect.php';
					require_once THEME_SLUG_LIBRARY . 'includes/classes/class-customize-control-radio-matrix.php';
					require_once THEME_SLUG_LIBRARY . 'includes/classes/class-customize-control-select.php';
					require_once THEME_SLUG_LIBRARY . 'includes/classes/class-customize-control-text.php';

				// Generate customizer options

					if (
						is_array( $theme_options )
						&& ! empty( $theme_options )
					) {
						foreach ( $theme_options as $theme_option ) {
							if (
								is_array( $theme_option )
								&& isset( $theme_option['type'] )
								&& in_array( $theme_option['type'], $allowed_option_types )
							) {

								// Variables

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

									if ( isset( $theme_option['sanitize_callback'] ) ) {
										$sanitize_callback = $theme_option['sanitize_callback'];
									} else {
										$sanitize_callback = '';
									}

									if ( isset( $theme_option['validate_callback'] ) ) {
										$validate_callback = $theme_option['validate_callback'];
									} else {
										$validate_callback = '';
									}

									$transport = ( isset( $theme_option['preview_js'] ) ) ? ( 'postMessage' ) : ( 'refresh' );



								/**
								 * Panels
								 *
								 * Panels are wrappers for customizer sections.
								 * Note that the panel will not display unless sections are assigned to it.
								 * Set the panel name in the section declaration with `in_panel`:
								 * - if text, this will become a panel title (ID defaults to `theme-options`)
								 * - if array, you can set `title`, `id` and `type` (the type will affect panel class)
								 * Panel has to be defined for each section to prevent all sections within a single panel.
								 *
								 * @link  http://make.wordpress.org/core/2014/07/08/customizer-improvements-in-4-0/
								 */
								if ( isset( $theme_option['in_panel'] ) ) {

									$panel_type = 'theme-options';

									if ( is_array( $theme_option['in_panel'] ) ) {
										$panel_title = ( isset( $theme_option['in_panel']['title'] ) ) ? ( $theme_option['in_panel']['title'] ) : ( '&mdash;' );
										$panel_id    = ( isset( $theme_option['in_panel']['id'] ) ) ? ( $theme_option['in_panel']['id'] ) : ( $panel_type );
										$panel_type  = ( isset( $theme_option['in_panel']['type'] ) ) ? ( $theme_option['in_panel']['type'] ) : ( $panel_type );
									} else {
										$panel_title = $theme_option['in_panel'];
										$panel_id    = $panel_type;
									}

									/**
									 * Filters customizer theme options panel type.
									 *
									 * @since  2.8.0
									 *
									 * @param  string $panel_type
									 * @param  string $theme_option
									 * @param  array  $theme_options
									 */
									$panel_type = (string) apply_filters( 'theme_slug/library_customize/register/panel_type', $panel_type, $theme_option, $theme_options );

									/**
									 * Filters customizer theme options panel id.
									 *
									 * @since  2.8.0
									 *
									 * @param  string $panel_id
									 * @param  string $theme_option
									 * @param  array  $theme_options
									 */
									$panel_id = (string) apply_filters( 'theme_slug/library_customize/register/panel_id', $panel_id, $theme_option, $theme_options );

									if ( $customizer_panel !== $panel_id ) {
										$wp_customize->add_panel(
											$panel_id,
											array(
												'title'       => esc_html( $panel_title ),
												'description' => ( isset( $theme_option['in_panel-description'] ) ) ? ( $theme_option['in_panel-description'] ) : ( '' ), // Hidden at the top of the panel
												'priority'    => $priority,
												'type'        => $panel_type, // Sets also the panel class
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
											'type'        => 'theme-options', // Sets also the section class
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
									'label'           => ( isset( $theme_option['label'] ) ) ? ( $theme_option['label'] ) : ( '' ),
									'description'     => $description,
									'section'         => ( isset( $theme_option['section'] ) ) ? ( $theme_option['section'] ) : ( $customizer_section ),
									'priority'        => ( isset( $theme_option['priority'] ) ) ? ( $theme_option['priority'] ) : ( $priority ),
									'type'            => $theme_option['type'],
									'active_callback' => ( isset( $theme_option['active_callback'] ) ) ? ( $theme_option['active_callback'] ) : ( '' ),
									'input_attrs'     => ( isset( $theme_option['input_attrs'] ) ) ? ( $theme_option['input_attrs'] ) : ( array() ),
								);



								/**
								 * Options generator
								 */
								switch ( $theme_option['type'] ) {

									case 'checkbox':
									case 'radio':
										$wp_customize->add_setting(
											$option_id,
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( 'checkbox' === $theme_option['type'] ) ? ( 'Theme_Slug_Library_Sanitize::checkbox' ) : ( 'Theme_Slug_Library_Sanitize::select' ),
												'sanitize_js_callback' => ( 'checkbox' === $theme_option['type'] ) ? ( 'Theme_Slug_Library_Sanitize::checkbox' ) : ( 'Theme_Slug_Library_Sanitize::select' ),
												'validate_callback'    => $validate_callback,
											)
										);
										$wp_customize->add_control(
											$option_id,
											array_merge( $generic, array(
												'choices' => ( isset( $theme_option['choices'] ) ) ? ( $theme_option['choices'] ) : ( '' ),
											) )
										);
										break;

									case 'multicheckbox':
									case 'multiselect':
										$wp_customize->add_setting(
											$option_id,
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'Theme_Slug_Library_Sanitize::multi_array' ),
												'sanitize_js_callback' => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'Theme_Slug_Library_Sanitize::multi_array' ),
												'validate_callback'    => $validate_callback,
											)
										);
										$wp_customize->add_control( new Theme_Slug_Customize_Control_Multiselect(
											$wp_customize,
											$option_id,
											array_merge( $generic, array(
												'choices' => ( isset( $theme_option['choices'] ) ) ? ( $theme_option['choices'] ) : ( '' ),
											) )
										) );
										break;

									case 'color':
										$wp_customize->add_setting(
											$option_id,
											array(
												'type'                 => $type,
												'default'              => trim( $default, '#' ),
												'transport'            => $transport,
												'sanitize_callback'    => 'sanitize_hex_color_no_hash',
												'sanitize_js_callback' => 'maybe_hash_hex_color',
												'validate_callback'    => $validate_callback,
											)
										);
										$wp_customize->add_control( new WP_Customize_Color_Control(
											$wp_customize,
											$option_id,
											$generic
										) );
										break;

									case 'email':
										$wp_customize->add_setting(
											$option_id,
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => 'sanitize_email',
												'sanitize_js_callback' => 'sanitize_email',
												'validate_callback'    => $validate_callback,
											)
										);
										$wp_customize->add_control(
											$option_id,
											$generic
										);
										break;

									case 'hidden':
										$wp_customize->add_setting(
											$option_id,
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'esc_attr' ),
												'sanitize_js_callback' => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'esc_attr' ),
												'validate_callback'    => $validate_callback,
											)
										);
										$wp_customize->add_control( new Theme_Slug_Customize_Control_Hidden(
											$wp_customize,
											$option_id,
											array(
												'label'    => 'HIDDEN FIELD',
												'section'  => $customizer_section,
												'priority' => $priority,
											)
										) );
										break;

									case 'html':
										if ( empty( $option_id ) ) {
											$option_id = 'custom-title-' . $priority;
										}
										$wp_customize->add_setting(
											$option_id,
											array(
												'sanitize_callback'    => 'wp_kses_post',
												'sanitize_js_callback' => 'wp_filter_post_kses',
												'validate_callback'    => $validate_callback,
											)
										);
										$wp_customize->add_control( new Theme_Slug_Customize_Control_HTML(
											$wp_customize,
											$option_id,
											array_merge( $generic, array(
												'content' => $theme_option['content'],
											) )
										) );
										break;

									case 'image':
										$wp_customize->add_setting(
											$option_id,
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'esc_url_raw' ),
												'sanitize_js_callback' => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'esc_url_raw' ),
												'validate_callback'    => $validate_callback,
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

									case 'range':
										$wp_customize->add_setting(
											$option_id,
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'absint' ),
												'sanitize_js_callback' => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'absint' ),
												'validate_callback'    => $validate_callback,
											)
										);
										$wp_customize->add_control(
											$option_id,
											array_merge( $generic, array(
												'input_attrs' => array(
													'min'           => $theme_option['min'],
													'max'           => $theme_option['max'],
													'step'          => $theme_option['step'],
													'data-multiply' => ( isset( $theme_option['multiplier'] ) ) ? ( $theme_option['multiplier'] ) : ( 1 ),
													'data-prefix'   => ( isset( $theme_option['prefix'] ) ) ? ( $theme_option['prefix'] ) : ( '' ),
													'data-suffix'   => ( isset( $theme_option['suffix'] ) ) ? ( $theme_option['suffix'] ) : ( '' ),
													'data-decimals' => ( isset( $theme_option['decimal_places'] ) ) ? ( absint( $theme_option['decimal_places'] ) ) : ( 0 ),
												),
											) )
										);
										break;

									case 'password':
										$wp_customize->add_setting(
											$option_id,
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'esc_attr' ),
												'sanitize_js_callback' => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'esc_attr' ),
												'validate_callback'    => $validate_callback,
											)
										);
										$wp_customize->add_control(
											$option_id,
											$generic
										);
										break;

									case 'radiomatrix':
										$wp_customize->add_setting(
											$option_id,
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'esc_attr' ),
												'sanitize_js_callback' => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'esc_attr' ),
												'validate_callback'    => $validate_callback,
											)
										);
										$wp_customize->add_control( new Theme_Slug_Customize_Control_Radio_Matrix(
											$wp_customize,
											$option_id,
											array_merge( $generic, array(
												'choices' => ( isset( $theme_option['choices'] ) ) ? ( $theme_option['choices'] ) : ( '' ),
												'class'   => ( isset( $theme_option['class'] ) ) ? ( $theme_option['class'] ) : ( '' ),
											) )
										) );
										break;

									case 'select':
										// Supports optgroups too.
										$wp_customize->add_setting(
											$option_id,
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => 'Theme_Slug_Library_Sanitize::select',
												'sanitize_js_callback' => 'Theme_Slug_Library_Sanitize::select',
												'validate_callback'    => $validate_callback,
											)
										);
										$wp_customize->add_control( new Theme_Slug_Customize_Control_Select(
											$wp_customize,
											$option_id,
											array_merge( $generic, array(
												'choices' => ( isset( $theme_option['choices'] ) ) ? ( $theme_option['choices'] ) : ( '' ),
											) )
										) );
										break;

									case 'text':
										// Supports datalist too.
										$wp_customize->add_setting(
											$option_id,
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'esc_textarea' ),
												'sanitize_js_callback' => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'esc_textarea' ),
												'validate_callback'    => $validate_callback,
											)
										);
										$wp_customize->add_control( new Theme_Slug_Customize_Control_Text(
											$wp_customize,
											$option_id,
											array_merge( $generic, array(
												'choices' => ( isset( $theme_option['datalist'] ) ) ? ( $theme_option['datalist'] ) : ( array() ),
											) )
										) );
										break;

									case 'textarea':
										$wp_customize->add_setting(
											$option_id,
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'esc_textarea' ),
												'sanitize_js_callback' => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'esc_textarea' ),
												'validate_callback'    => $validate_callback,
											)
										);
										$wp_customize->add_control(
											$option_id,
											$generic
										);
										break;

									case 'url':
										$wp_customize->add_setting(
											$option_id,
											array(
												'type'                 => $type,
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'esc_url' ),
												'sanitize_js_callback' => ( $sanitize_callback ) ? ( $sanitize_callback ) : ( 'esc_url' ),
												'validate_callback'    => $validate_callback,
											)
										);
										$wp_customize->add_control(
											$option_id,
											$generic
										);
										break;

									default:
										break;

								}

							}
						}
					}

				// Assets needed for customizer preview

					if ( $wp_customize->is_preview() ) {
						add_action( 'wp_footer', __CLASS__ . '::preview_scripts', 99 );
					}

		} // /register





	/**
	 * 30) Getters
	 */

		/**
		 * Get theme options setup array.
		 *
		 * @since    2.8.0
		 * @version  2.8.0
		 *
		 * @return  array
		 */
		public static function get_options(): array {

			// Output

				/**
				 * Filters customizer theme options setup array.
				 *
				 * @since  2.8.0
				 *
				 * @param  array $theme_options
				 */
				return (array) apply_filters( 'theme_slug/library_customize/get_options', array() );

		} // /get_options



		/**
		 * Get theme mod or fall back to default automatically
		 *
		 * @link  https://developer.wordpress.org/reference/functions/get_theme_mod/
		 *
		 * @since    2.7.0
		 * @version  2.8.0
		 *
		 * @param  string $name
		 * @param  array  $theme_option_setup
		 *
		 * @return  mixed  Stored or default theme mod value of any type.
		 */
		public static function get_theme_mod( string $name, array $theme_option_setup = array() ) {

			// Pre

				/**
				 * Bypass filter for Theme_Slug_Library_Customize::get_theme_mod().
				 *
				 * Returning a non-null value will short-circuit the method,
				 * returning the passed value instead.
				 *
				 * @since  2.8.0
				 *
				 * @param  mixed  $pre                 Default: false. If not false, method returns this value.
				 * @param  string $name                Theme mod name.
				 * @param  array  $theme_option_setup  Optional single theme option setup array.
				 */
				$pre = apply_filters( 'pre/theme_slug/library_customize/get_theme_mod', null, $name, $theme_option_setup );

				if ( null !== $pre ) {
					return $pre;
				}


			// Variables

				$output = false;

				if ( false === self::$mods ) {
					// Cache theme mods
					self::$mods = get_theme_mods();
				}


			// Processing

				if ( isset( self::$mods[ $name ] ) ) {

					/**
					 * Theme option has been modified,
					 * so we don't need the default value.
					 */
					$output = self::$mods[ $name ];

				} else {

					/**
					 * We haven't found a modified theme option,
					 * so we need its default value.
					 */
					if ( empty( $theme_option_setup ) ) {

						/**
						 * We don't have single theme option passed,
						 * get all theme options setup.
						 */
						if ( empty( self::$theme_options_setup ) ) {
							// Cache theme options setup
							self::$theme_options_setup = self::get_options();
						}

						foreach ( self::$theme_options_setup as $option ) {
							if (
								isset( $option['id'] )
								&& $name === $option['id']
								&& isset( $option['default'] )
							) {
								$output = $option['default'];
								$theme_option_setup = $option;
								break;
							}
						}

					} else {

						/**
						 * We have single theme option passed,
						 * get the default value from it.
						 */
						if (
							isset( $theme_option_setup['default'] )
							&& isset( $theme_option_setup['id'] )
							&& $name === $theme_option_setup['id']
						) {
							$output = $theme_option_setup['default'];
						}

					}

					/**
					 * @see  https://developer.wordpress.org/reference/functions/get_theme_mod/
					 */
					if ( is_string( $output ) ) {
						$output = sprintf(
							$output,
							get_template_directory_uri(),
							get_stylesheet_directory_uri()
						);
					}

				}


			// Output

				return apply_filters( "theme_mod_{$name}", $output, $theme_option_setup );

		} // /get_theme_mod





} // /Theme_Slug_Library_Customize

add_action( 'after_setup_theme', 'Theme_Slug_Library_Customize::init', 20 );
