<?php
/**
 * Skinning System
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Skinning System
 * @copyright   2014 WebMan - Oliver Juhas
 * @version     1.0
 * @uses        Theme Customizer Options Array
 * @uses        Custom CSS Styles Generator
 *
 * CONTENT:
 * - 1) Required files
 * - 10) Actions and filters
 * - 20) Helpers
 * - 30) Main customizer function
 * - 40) Saving skins
 */





/**
 * 1) Required files
 */

	//Include function to generate the WordPress Customizer CSS
		locate_template( 'assets/css/_custom-styles.php', true );





/**
 * 10) Actions and filters
 */

	/**
	 * Actions
	 */

		//Register customizer
			add_action( 'customize_register', 'wm_theme_customizer' );
		//Enqueue styles and scripts
			add_action( 'customize_controls_enqueue_scripts', 'wm_theme_customizer_assets' );
		//Save skin
			add_action( 'customize_save_last-trigger-setting', 'wm_save_skin', 10 );
		//Regenerating main stylesheet
			add_action( 'customize_save_last-trigger-setting', 'wm_generate_all_css', 20 );





/**
 * 20) Helpers
 */

	/**
	 * Enqueue styles and scripts to main customizer window
	 *
	 * You can actually control the customizer option fields here.
	 */
	if ( ! function_exists( 'wm_theme_customizer_assets' ) ) {
		function wm_theme_customizer_assets() {
			/**
			 * Scripts
			 */

				wp_enqueue_script( 'wm-customizer' );
		}
	} // /wm_theme_customizer_assets



	/**
	 * Outputs styles in customizer preview head
	 */
	if ( ! function_exists( 'wm_theme_customizer_css' ) ) {
		function wm_theme_customizer_css() {
			//Helper variables
				$output = wm_custom_styles();

			//Output
				if ( $output ) {
					echo apply_filters( 'wmhook_wm_theme_customizer_css_output', '<style type="text/css" id="' . WM_THEME_SHORTNAME . '-customizer-styles">' . "\r\n" . $output . "\r\n" . '</style>' );
				}
		}
	} // /wm_theme_customizer_css



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
	 */
	if ( ! function_exists( 'wm_theme_customizer_js' ) ) {
		function wm_theme_customizer_js() {
			//Helper variables
				$wm_skin_design = apply_filters( 'wmhook_theme_options_skin_array', array() );

				$output = $output_single = '';

			//Preparing output
				if ( is_array( $wm_skin_design ) && ! empty( $wm_skin_design ) ) {
					foreach ( $wm_skin_design as $skin_option ) {
						if ( isset( $skin_option['customizer_js'] ) ) {
							$output_single  = "wp.customize( '" . WM_THEME_SETTINGS_SKIN . "[" . WM_THEME_SETTINGS_PREFIX . $skin_option['id'] . "]" . "', function( value ) {"  . "\r\n";
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
										}
									}

									$output_single .= "\t\t" . '$( "' . $selector . '" )' . $output_single_css . ";\r\n";
								}
							} else {
								$output_single .= "\t\t" . $skin_option['customizer_js']['custom'] . "\r\n";
							}

							$output_single .= "\t" . '} );' . "\r\n";
							$output_single .= '} );'. "\r\n";
							$output_single  = apply_filters( 'wmhook_wm_theme_customizer_js_option_' . $skin_option['id'], $output_single );

							$output .= $output_single;
						}
					}
				}

			//Output
				if ( trim( $output ) ) {
					echo apply_filters( 'wmhook_wm_theme_customizer_js_output', '<!-- Theme custom scripts -->' . "\r\n" . '<script type="text/javascript"><!--' . "\r\n" . '( function( $ ) {' . "\r\n\r\n" . $output . "\r\n\r\n" . '} )( jQuery );' . "\r\n" . '//--></script>' );
				}
		}
	} // /wm_theme_customizer_js



	/**
	 * Class to create additional options
	 *
	 * @link  https://github.com/bueltge/Wordpress-Theme-Customizer-Custom-Controls
	 * @link  http://ottopress.com/2012/making-a-custom-control-for-the-theme-customizer/
	 */
	if ( class_exists( 'WP_Customize_Control' ) ) {

		/**
		 * Hidden
		 */
		class WM_Customizer_Hidden extends WP_Customize_Control {

			public $type = 'hidden';

			public function render_content() {
				?>
				<textarea <?php $this->link(); ?>>
					<?php echo esc_textarea( $this->value() ); ?>
				</textarea>
				<?php
			}

		} // /WM_Customizer_Textarea



		/**
		 * Textarea
		 */
		class WM_Customizer_Textarea extends WP_Customize_Control {

			public $type = 'textarea';

			public function render_content() {
				?>
				<label>
					<span class="customize-control-title"><?php echo $this->label; ?></span>
					<textarea cols="20" rows="4" <?php $this->link(); ?>>
						<?php echo esc_textarea( $this->value() ); ?>
					</textarea>
				</label>
				<?php
			}

		} // /WM_Customizer_Textarea



		/**
		 * Multicheckbox
		 */
		class WM_Customizer_Multiselect extends WP_Customize_Control {

			public function render_content() {
				if ( ! empty( $this->choices ) && is_array( $this->choices ) ) {
					echo '<label><span class="customize-control-title">' . $this->label . '</span>';
					echo '<select name="' . $this->id . '" multiple="multiple" ' . $this->get_link() . '>';
					foreach ( $this->choices as $value => $name ) {
						echo '<option value="' . $value . '" ' . selected( $this->value(), $value, false ) . '>' . $name . '</option>';
					}
					echo '</select>';
					echo '<em>' . __( 'Press CTRL key for multiple selection.', 'wm_domain' ) . '</em>';
					echo '</label>';
				}
			}

		} // /WM_Customizer_Multicheckbox



		/**
		 * Number slider
		 */
		class WM_Customizer_Slider extends WP_Customize_Control {

			public function enqueue() {
				wp_enqueue_style( 'wm-theme-customizer' );
				wp_enqueue_script( 'jquery-ui-slider' );
			}

			public function render_content() {
				if ( empty( $this->json ) || ! is_array( $this->json ) ) {
					$this->json = array( 0, 10, 1 ); // [min, max, step]
				}
				?>
				<label>
					<span class="customize-control-title"><?php echo $this->label; ?></span>

					<span class="slide-number-wrapper">
						<span id="<?php echo sanitize_title( $this->id ); ?>-slider" class="number-slider"></span>
					</span>
					<input type="text" name="<?php echo $this->id; ?>" id="<?php echo sanitize_title( $this->id ); ?>" value="<?php echo esc_attr( $this->value() ); ?>" size="5" maxlength="5" readonly="readonly" <?php $this->link(); ?> />
				</label>

				<script><!--
					jQuery( function() {
						if ( jQuery().slider ) {
							jQuery( '#<?php echo sanitize_title( $this->id ); ?>-slider' ).slider( {
								value : <?php echo $this->value(); ?>,
								min   : <?php echo $this->json[0]; ?>,
								max   : <?php echo $this->json[1]; ?>,
								step  : <?php echo $this->json[2]; ?>,
								slide : function( event, ui ) {
									jQuery( '#<?php echo sanitize_title( $this->id ); ?>' ).val( ui.value ).change();
								}
							} );
						}

					} );
				//--></script>
				<?php
			}

		} // /WM_Customizer_Slider



		/**
		 * Custom HTML (set as label)
		 */
		class WM_Customizer_HTML extends WP_Customize_Control {

			public function render_content() {
				echo $this->label;
			}

		} // /WM_Customizer_HTML



		/**
		 * Add uploaded images tab to Image control
		 */
		class WM_Customize_Image_Control extends WP_Customize_Image_Control {

			/**
			 * Adding an .ico into supported image file formats
			 */
			public $extensions = array( 'ico', 'jpg', 'jpeg', 'gif', 'png' );

			public function __construct( $manager, $id, $args = array() ) {
				parent::__construct( $manager, $id, $args );
			}

			/**
			 * Search for images within the defined context
			 */
			public function tab_uploaded() {
				$wm_context_uploads = get_posts( array(
						'post_type'  => 'attachment',
						'meta_key'   => '_wp_attachment_context',
						'meta_value' => $this->context,
						'orderby'    => 'post_date',
						'nopaging'   => true,
					) );
				?>

				<div class="uploaded-target"></div>

				<?php
				if ( empty( $wm_context_uploads ) ) {
					return;
				}

				foreach ( (array) $wm_context_uploads as $wm_context_upload ) {
					$this->print_tab_image( esc_url_raw( $wm_context_upload->guid ) );
				}
			}

		} // /WM_Customize_Image_Control

	} // /WP_Customize_Control



	/**
	 * Sanitize email
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
	 * Sanitize integer number
	 *
	 * @param  mixed $value WP customizer value to sanitize.
	 */
	if ( ! function_exists( 'wm_sanitize_int' ) ) {
		function wm_sanitize_int( $value ) {
			//Helper variables
				$value = intval( $value );

			//Output
				return apply_filters( 'wmhook_wm_sanitize_int_output', $value );
		}
	} // /wm_sanitize_int



	/**
	 * Sanitize absolute integer number
	 *
	 * @param  mixed $value WP customizer value to sanitize.
	 */
	if ( ! function_exists( 'wm_sanitize_absint' ) ) {
		function wm_sanitize_absint( $value ) {
			//Helper variables
				$value = absint( $value );

			//Output
				return apply_filters( 'wmhook_wm_sanitize_absint_output', $value );
		}
	} // /wm_sanitize_absint





/**
 * 30) Main customizer function
 */

	/**
	 * Registering sections and options for WP Customizer
	 *
	 * @param  object $wp_customize WP customizer object.
	 */
	if ( ! function_exists( 'wm_theme_customizer' ) ) {
		function wm_theme_customizer( $wp_customize ) {
			//Helper variables
				$wm_skin_design = apply_filters( 'wmhook_theme_options_skin_array', array() );

				$allowed_option_types = apply_filters( 'wmhook_wm_theme_customizer_allowed_option_types', array(
						'checkbox',
						'color',
						'hidden',
						'image',
						'multiselect',
						'radio',
						'select',
						'slider',
						'text',
						'textarea',
						'theme-customizer-html'
					) );

				//To make sure our customizer sections start after WordPress default ones
					$priority = 200;
				//Default section name in case not set (should be overwritten anyway)
					$customizer_section = WM_THEME_SHORTNAME;

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

								$option_id = ( isset( $skin_option['id'] ) ) ? ( WM_THEME_SETTINGS_PREFIX . $skin_option['id'] ) : ( null );
								$default   = ( isset( $skin_option['default'] ) ) ? ( $skin_option['default'] ) : ( null );
								$transport = ( isset( $skin_option['customizer_js'] ) ) ? ( 'postMessage' ) : ( 'refresh' );



							/**
							 * Sections
							 */
							if ( isset( $skin_option['theme-customizer-section'] ) && trim( $skin_option['theme-customizer-section'] ) ) {

								$customizer_section = sanitize_title( $skin_option['theme-customizer-section'] );

								$wp_customize->add_section(
										$customizer_section, //section ID
										array(
											'title'       => $skin_option['theme-customizer-section'], //section title
											'description' => '', //"title" attribute applied on section H3, displayed on hover only -> not needed
											'priority'    => $priority,
										)
									);

							}



							/**
							 * Options
							 */
							switch ( $skin_option['type'] ) {

								/**
								 * Color
								 */
								case 'color':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'type'                 => 'option',
												'default'              => $default,
												'transport'            => $transport,
												'sanitize_callback'    => 'sanitize_hex_color_no_hash',
												'sanitize_js_callback' => 'maybe_hash_hex_color',
											)
										);

									$wp_customize->add_control( new WP_Customize_Color_Control(
											$wp_customize,
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'label'    => $skin_option['label'],
												'section'  => $customizer_section,
												'priority' => $priority,
											)
										) );

								break;

								/**
								 * Hidden
								 */
								case 'hidden':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'type'      => 'option',
												'default'   => $default,
												'transport' => $transport,
											)
										);

									$wp_customize->add_control( new WM_Customizer_Hidden(
											$wp_customize,
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
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
								case 'theme-customizer-html':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[custom-title-' . $priority . ']'
										);

									$wp_customize->add_control( new WM_Customizer_HTML(
											$wp_customize,
											WM_THEME_SETTINGS_SKIN . '[custom-title-' . $priority . ']',
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
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'type'      => 'option',
												'default'   => $default,
												'transport' => $transport,
											)
										);

									$wp_customize->add_control( new WM_Customize_Image_Control(
											$wp_customize,
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'label'    => $skin_option['label'],
												'section'  => $customizer_section,
												'priority' => $priority,
												'context'  => WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											)
										) );

								break;

								/**
								 * Checkbox, radio & select
								 */
								case 'checkbox':
								case 'radio':
								case 'select':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'type'      => 'option',
												'default'   => $default,
												'transport' => $transport,
											)
										);

									$wp_customize->add_control(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'label'    => $skin_option['label'],
												'section'  => $customizer_section,
												'priority' => $priority,
												'type'     => $skin_option['type'],
												'choices'  => ( isset( $skin_option['options'] ) ) ? ( $skin_option['options'] ) : ( '' ),
											)
										);

								break;

								/**
								 * Multiselect
								 */
								case 'multiselect':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'type'      => 'option',
												'default'   => $default,
												'transport' => $transport,
											)
										);

									$wp_customize->add_control( new WM_Customizer_Multiselect(
											$wp_customize,
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'label'    => $skin_option['label'],
												'section'  => $customizer_section,
												'priority' => $priority,
												'choices'  => ( isset( $skin_option['options'] ) ) ? ( $skin_option['options'] ) : ( '' ),
											)
										) );

								break;

								/**
								 * Slider
								 */
								case 'slider':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'type'              => 'option',
												'default'           => $default,
												'transport'         => $transport,
												'sanitize_callback' => 'wm_sanitize_' . $skin_option['validate'],
											)
										);

									$wp_customize->add_control( new WM_Customizer_Slider(
											$wp_customize,
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'label'    => $skin_option['label'],
												'section'  => $customizer_section,
												'priority' => $priority,
												'json'     => array( $skin_option['min'], $skin_option['max'], $skin_option['step'] ),
											)
										) );

								break;

								/**
								 * Text
								 */
								case 'text':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'type'      => 'option',
												'default'   => $default,
												'transport' => $transport,
											)
										);

									$wp_customize->add_control(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'label'    => $skin_option['label'],
												'section'  => $customizer_section,
												'priority' => $priority,
											)
										);

								break;

								/**
								 * Textarea
								 */
								case 'textarea':

									$wp_customize->add_setting(
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'type'      => 'option',
												'default'   => $default,
												'transport' => $transport,
											)
										);

									$wp_customize->add_control( new WM_Customizer_Textarea(
											$wp_customize,
											WM_THEME_SETTINGS_SKIN . '[' . $option_id . ']',
											array(
												'label'    => $skin_option['label'],
												'section'  => $customizer_section,
												'priority' => $priority,
											)
										) );

								break;

								/**
								 * Default
								 */
								default:
								break;

							} // /switch

						} // /if suitable option array

					} // /foreach



					/**
					 * THE CODE BELOW IS REQUIRED UNTIL WORDPRESS CREATES A HOOK TRIGGERED AFTER SAVING CUSTOMIZER OPTIONS
					 *
					 * Last hidden setting that triggers the main CSS file regeneration.
					 *
					 * @link  Idea from: http://wordpress.stackexchange.com/questions/57540/wp-3-4-what-action-hook-is-called-when-theme-customisation-is-saved
					 * @link  Suggested: http://wordpress.org/extend/ideas/topic/do-customize_save-action-hook-after-the-settings-are-saved
					 */

						/**
						 * Start of "trigger" option
						 */

							$wp_customize->add_setting(
									'last-trigger-setting',
									array(
										'type'      => 'option',
										'default'   => 'true',
										'transport' => $transport,
									)
								);

							$wp_customize->add_control(
									'last-trigger-setting',
									array(
										'type'     => 'hidden',
										'label'    => 'TRIGGER OPTION',
										'section'  => $customizer_section,
										'priority' => $priority + 999,
									)
								);

						/**
						 * End of "trigger" option
						 */

				} // /if skin options are non-empty array

			//Assets needed for customizer preview
				if ( $wp_customize->is_preview() ) {
					add_action( 'wp_head',   'wm_theme_customizer_css'    );
					add_action( 'wp_footer', 'wm_theme_customizer_js', 99 );
				}
		}
	} // /wm_theme_customizer





/**
 * 40) Saving skins
 */

	/**
	 * Saves and loads a skin
	 *
	 * Creates a new skin JSON file and/or
	 * loads a selected skin settings.
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

				//Process the customizer values and get only those from WM_THEME_SETTINGS_SKIN
					foreach ( $customizer_values as $key => $value ) {
						if (
								false !== strpos( $key, WM_THEME_SETTINGS_SKIN )
								&& false === strpos( $key, 'custom-title-' ) //ignore Customizer sections titles
							) {
							$key = str_replace( array( WM_THEME_SETTINGS_SKIN, '[', ']' ), '', $key );
							$output[ $key ] = $value;
						}
					}

				//Set a new skin file name
					if (
							isset( $output[ WM_THEME_SETTINGS_PREFIX . 'skin-new' ] )
							&& isset( $output[ WM_THEME_SETTINGS_PREFIX . 'skin-load' ] )
						) {
						$skin_load = $output[ WM_THEME_SETTINGS_PREFIX . 'skin-load' ];
						$skin_new  = sanitize_title( $output[ WM_THEME_SETTINGS_PREFIX . 'skin-new' ] );

						unset( $output[ WM_THEME_SETTINGS_PREFIX . 'skin-load' ] );
						unset( $output[ WM_THEME_SETTINGS_PREFIX . 'skin-new' ] );
					}

				//Create a new skin
					if ( $skin_new ) {

						//Create the theme skins folder
							if ( ! wma_create_folder( $theme_skin_dir ) ) {
								exit( "Wasn't able to create a theme skins folder" );
							}

						//Write the skin JSON file
							$json_path = apply_filters( 'wmhook_wm_save_skin_json_path', trailingslashit( $theme_skin_dir ) . $skin_new . '.json' );

							if ( is_array( $output ) && ! empty( $output ) ) {
								$output = apply_filters( 'wmhook_wm_save_skin_output', $output );

								wma_write_local_file( $json_path, json_encode( $output ) );

								//Remove load/save skin names from settings in DB
									$skin_settings = get_option( WM_THEME_SETTINGS_SKIN );
									unset( $skin_settings[ WM_THEME_SETTINGS_PREFIX . 'skin-load' ] );
									unset( $skin_settings[ WM_THEME_SETTINGS_PREFIX . 'skin-new' ] );
									update_option( WM_THEME_SETTINGS_SKIN, $skin_settings );

								update_option( WM_THEME_SETTINGS_PREFIX . WM_THEME_SHORTNAME . '-skins', array_unique( array( WM_SKINS, WM_SKINS_CHILD, $theme_skin_dir ) ) );

								//Run additional actions
									do_action( 'wmhook_save_skin', $skin_new, $customizer_values );

								return true;
							}

						delete_option( WM_THEME_SETTINGS_PREFIX . WM_THEME_SHORTNAME . '-skins' );

				//Load a selected skin
					} elseif ( $skin_load ) {

						//Check if file exists
							if ( ! file_exists( $skin_load ) ) {
								return false;
							}

						//Get the skin slug
							$skin_slug = str_replace( array( '.json', WM_SKINS, WM_SKINS_CHILD, $theme_skin_dir ), '', $skin_load );

						//We don't need to write to the file, so just open for reading.
							$skin_load = wma_read_local_file( $skin_load );

							$replacements = apply_filters( 'wmhook_wm_save_skin_replacements', array(
									'{{get_template_directory}}'       => trailingslashit( get_template_directory() ),
									'{{get_template_directory_uri}}'   => trailingslashit( get_template_directory_uri() ),
									'{{get_stylesheet_directory}}'     => trailingslashit( get_stylesheet_directory() ),
									'{{get_stylesheet_directory_uri}}' => trailingslashit( get_stylesheet_directory_uri() ),
									'{{theme_assets_dir}}'             => trailingslashit( get_template_directory() ) . 'assets/',
									'{{theme_assets_url}}'             => trailingslashit( get_template_directory_uri() ) . 'assets/',
									'{{child_theme_assets_dir}}'       => trailingslashit( get_stylesheet_directory() ) . 'assets/',
									'{{child_theme_assets_url}}'       => trailingslashit( get_stylesheet_directory_uri() ) . 'assets/',
								) );
							$skin_load = strtr( $skin_load, $replacements );

						//Decoding new imported skin JSON string and converting object to array
							if ( ! empty( $skin_load ) ) {
								$skin_load = json_decode( trim( $skin_load ), true );
								update_option( WM_THEME_SETTINGS_SKIN, $skin_load );
								update_option( WM_THEME_SETTINGS_PREFIX . WM_THEME_SHORTNAME . '-skin-used', $skin_slug );
							}

						//Run additional actions
							do_action( 'wmhook_load_skin', $skin_load, $customizer_values );

						return true;

					}

				return false;
		}
	} // /wm_save_skin

?>