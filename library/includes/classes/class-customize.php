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

		public static $options = array();



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

				// Setup

					/**
					 * Filters customizer theme options setup array.
					 *
					 * This is being triggered at `after_setup_theme` action hook
					 * with priority 20, so all theme options setters/modifiers
					 * should be hooked onto this filter beforehand!
					 *
					 * @since  2.8.0
					 *
					 * @param  array $options
					 */
					self::$options = (array) apply_filters( 'theme_slug/library_customize/get_options', array() );

				// Hooks

					// Actions

						add_action( 'customize_register', __CLASS__ . '::render', 100 );

						add_action( 'theme_slug/library_customize/render/option', 'Theme_Slug_Library_Control::add_option', 10, 2 );

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

				$options = self::get_options();

				ksort( $options );

				$output = $output_single = '';


			// Processing

				if (
					is_array( $options )
					&& ! empty( $options )
				) {
					foreach ( $options as $option ) {
						if (
							isset( $option['preview_js'] )
							&& is_array( $option['preview_js'] )
						) {
							$option_id = sanitize_title( $option['id'] );

							$output_single  = "wp.customize("  . PHP_EOL;
							$output_single .= "\t" . "'" . $option_id . "',"  . PHP_EOL;
							$output_single .= "\t" . "function( value ) {"  . PHP_EOL;
							$output_single .= "\t\t" . 'value.bind( function( to ) {' . PHP_EOL;

							// CSS

								if ( isset( $option['preview_js']['css'] ) ) {

									$output_single .= "\t\t\t" . "var newCss = '';" . PHP_EOL.PHP_EOL;
									$output_single .= "\t\t\t" . "if ( $( '#jscss-" . $option_id . "' ).length ) { $( '#jscss-" . $option_id . "' ).remove() }" . PHP_EOL.PHP_EOL;

									foreach ( $option['preview_js']['css'] as $selector => $properties ) {
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

								if ( isset( $option['preview_js']['custom'] ) ) {
									$output_single .= "\t\t" . $option['preview_js']['custom'] . PHP_EOL;
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
		public static function render( WP_Customize_Manager $wp_customize ) {

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
				$pre = apply_filters( 'pre/theme_slug/library_customize/render', null, $wp_customize );

				if ( null !== $pre ) {
					return $pre;
				}


			// Variables

				$panel   = '';
				$section = 'theme-slug';
				$options = self::get_options();

				// Theme options comes first.
				$priority = absint(
					/**
					 * Filters customizer theme options priority.
					 *
					 * @since  2.8.0
					 *
					 * @param  int $priority
					 */
					apply_filters( 'theme_slug/library_customize/render/priority', 0 )
				);


			// Processing

				ksort( $options );

				// Generate customizer options.
				foreach ( $options as $key => $option ) {
					if ( isset( $option['type'] ) ) {
						$priority++;

						// Preset option args.
						if ( ! isset( $option['id'] ) ) {
							$option['id'] = 'theme_slug' . '_key_' . sanitize_title( $key );
						}
						if ( ! isset( $option['priority'] ) ) {
							$option['priority'] = $priority;;
						}

						/**
						 * Create panel.
						 *
						 * Note that the panel will not display unless sections are assigned to it.
						 * Set the panel name in the section declaration with `in_panel`:
						 * - if text, this will become a panel title (ID defaults to `theme-options`),
						 * - if array, you can set `title`, `id` and `type` (the type will affect panel class).
						 * Panel has to be defined for each section to prevent all sections within a single panel.
						 */
						if ( isset( $option['in_panel'] ) ) {
							$panel_type = 'theme-options';

							if ( is_array( $option['in_panel'] ) ) {
								$panel_title = ( isset( $option['in_panel']['title'] ) ) ? ( $option['in_panel']['title'] ) : ( '&mdash;' );
								$panel_id    = ( isset( $option['in_panel']['id'] ) ) ? ( $option['in_panel']['id'] ) : ( $panel_type );
								$panel_type  = ( isset( $option['in_panel']['type'] ) ) ? ( $option['in_panel']['type'] ) : ( $panel_type );
							} else {
								$panel_title = $option['in_panel'];
								$panel_id    = $panel_type;
							}

							/**
							 * Filters customizer theme options panel type.
							 *
							 * @since  2.8.0
							 *
							 * @param  string               $panel_type
							 * @param  array                $option
							 * @param  WP_Customize_Manager $wp_customize
							 * @param  array                $options
							 */
							$panel_type = (string) apply_filters( 'theme_slug/library_customize/render/panel_type', $panel_type, $option, $wp_customize, $options );

							/**
							 * Filters customizer theme options panel id.
							 *
							 * @since  2.8.0
							 *
							 * @param  string               $panel_id
							 * @param  array                $option
							 * @param  WP_Customize_Manager $wp_customize
							 * @param  array                $options
							 */
							$panel_id = (string) apply_filters( 'theme_slug/library_customize/render/panel_id', $panel_id, $option, $wp_customize, $options );

							if ( $panel !== $panel_id ) {
								$wp_customize->add_panel(
									$panel_id,
									array(
										'title'    => esc_html( $panel_title ),
										'priority' => $option['priority'],
										// Type also sets the panel class.
										'type' => $panel_type,
										// Description is hidden at the top of the panel.
										'description' => ( isset( $option['in_panel-description'] ) ) ? ( $option['in_panel-description'] ) : ( '' ),
									)
								);
								$panel = $panel_id;
							}
						}

						// Create section.
						if ( isset( $option['create_section'] ) && trim( $option['create_section'] ) ) {
							$section = array(
								'id'    => $option['id'],
								'setup' => array(
									'title'       => $option['create_section'],
									'description' => ( isset( $option['create_section-description'] ) ) ? ( $option['create_section-description'] ) : ( '' ),
									'priority'    => $option['priority'],
									// Type also sets the section class.
									'type' => 'theme-options',
								)
							);

							if ( ! isset( $option['in_panel'] ) ) {
								$panel = '';
							} else {
								$section['setup']['panel'] = $panel;
							}

							$wp_customize->add_section(
								$section['id'],
								$section['setup']
							);

							$section = $section['id'];
						}

						// Now that the section is created set it for the option.
						if ( ! isset( $option['section'] ) ) {
							$option['section'] = $section;
						}

						// Generate option control.
						if ( ! in_array( $option['type'], array( 'panel', 'section' ) ) ) {
							/**
							 * Action for creating a theme option in customizer.
							 *
							 * @since  2.8.0
							 *
							 * @param  array                $option
							 * @param  WP_Customize_Manager $wp_customize
							 * @param  array                $options
							 */
							do_action( 'theme_slug/library_customize/render/option', $option, $wp_customize, $options );
						}
					}
				}

				// Assets needed for customizer preview.
				if ( $wp_customize->is_preview() ) {
					add_action( 'wp_footer', __CLASS__ . '::preview_scripts', 99 );
				}

		} // /render





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

				return (array) self::$options;

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
		 * @param  array  $option_setup
		 *
		 * @return  mixed  Stored or default theme mod value of any type.
		 */
		public static function get_theme_mod( string $name, array $option_setup = array() ) {

			// Pre

				/**
				 * Bypass filter for Theme_Slug_Library_Customize::get_theme_mod().
				 *
				 * Returning a non-null value will short-circuit the method,
				 * returning the passed value instead.
				 *
				 * @since  2.8.0
				 *
				 * @param  mixed  $pre           Default: false. If not false, method returns this value.
				 * @param  string $name          Theme mod name.
				 * @param  array  $option_setup  Optional single theme option setup array.
				 */
				$pre = apply_filters( 'pre/theme_slug/library_customize/get_theme_mod', null, $name, $option_setup );

				if ( null !== $pre ) {
					return $pre;
				}


			// Variables

				$output = false;

				if ( false === self::$mods ) {
					// Soft cache theme mods in class property.
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
					if ( empty( $option_setup ) ) {

						/**
						 * We don't have single theme option passed,
						 * get the default value checking all theme options.
						 */
						foreach ( self::get_options() as $option ) {
							if (
								isset( $option['id'] )
								&& $name === $option['id']
								&& isset( $option['default'] )
							) {
								$output = $option['default'];
								$option_setup = $option;
								break;
							}
						}

					} else {

						/**
						 * We have single theme option passed,
						 * get the default value from it.
						 */
						if (
							isset( $option_setup['default'] )
							&& isset( $option_setup['id'] )
							&& $name === $option_setup['id']
						) {
							$output = $option_setup['default'];
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

				return apply_filters( "theme_mod_{$name}", $output, $option_setup );

		} // /get_theme_mod





} // /Theme_Slug_Library_Customize

add_action( 'after_setup_theme', 'Theme_Slug_Library_Customize::init', 20 );
