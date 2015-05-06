<?php
/**
 * Global functions
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Core
 *
 * @since    5.0
 * @version  5.0
 *
 * Contents:
 *
 * 10) CSS functions
 */





/**
 * 10) CSS functions
 */

	/**
	 * Generate main CSS file
	 *
	 * @since    3.0
	 * @version  5.0
	 *
	 * @param  array $args
	 */
	function wmtf_generate_main_css( $args = array() ) {

		//Pre

			$pre = apply_filters( 'wmhook_wmtf_generate_main_css_pre', false, $args );

			if ( false !== $pre ) {
				return $pre;
			}


		//Requirements check

			if ( ! function_exists( 'wma_amplifier' ) ) {
				return;
			}


		//Helper viariables

			$args = wp_parse_args( $args, apply_filters( 'wmhook_wmtf_generate_main_css_defaults', array(
					'message'        => _x( "The main theme CSS stylesheet was regenerated.<br /><strong>Please refresh your web browser's and server's cache</strong> <em>(if you are using a website server caching solution)</em>.", 'Translators, please, keep the HTML tags.', 'wm_domain' ),
					'message_after'  => '',
					'message_before' => '',
					'type'           => '',
				) ) );
			$args = apply_filters( 'wmhook_wmtf_generate_main_css_args', $args );

			$output = $output_min = '';

			$args['type'] = trim( $args['type'] );


		//Processing

			//Get the file content with output buffering

				ob_start();

				//Get the file from child theme if exists

					$css_dir_child      = get_stylesheet_directory() . '/assets/css/';
					$css_generator_file = '_generate' . $args['type'] . '-css.php';

					if ( file_exists( $css_dir_child . $css_generator_file ) ) {
						$css_generator_file_check = $css_dir_child . $css_generator_file;
					} else {
						$css_generator_file_check = get_template_directory() . '/assets/css/' . $css_generator_file;
					}

					if ( file_exists( $css_generator_file_check ) ) {
						locate_template( 'assets/css/' . $css_generator_file, true );
					}

				$output = trim( ob_get_clean() );

			//Requirements check

				if ( ! $output ) {
					return;
				}

			//Minify output if set

				$output_min = apply_filters( 'wmhook_wmtf_generate_main_css_output_min', $output, $args );

			//Create the theme CSS folder

				$wp_upload_dir = wp_upload_dir();

				$theme_css_url = trailingslashit( $wp_upload_dir['baseurl'] ) . 'wmtheme-' . WM_THEME_SHORTNAME;
				$theme_css_dir = trailingslashit( $wp_upload_dir['basedir'] ) . 'wmtheme-' . WM_THEME_SHORTNAME;

				if ( ! wma_create_folder( $theme_css_dir ) ) {
					set_transient( 'wmamp-admin-notice', array( "<strong>ERROR: Wasn't able to create a theme CSS folder! Contact the theme support.</strong>", 'error', 'switch_themes', 2 ), ( 60 * 60 * 48 ) );

					delete_option( 'wm-' . WM_THEME_SHORTNAME . $args['type'] . '-css' );
					delete_option( 'wm-' . WM_THEME_SHORTNAME . $args['type'] . '-files' );

					return false;
				}

			$css_file_name       = apply_filters( 'wmhook_wmtf_generate_main_css_css_file_name',       'global' . $args['type'],                                        $args                 );
			$global_css_path     = apply_filters( 'wmhook_wmtf_generate_main_css_global_css_path',     trailingslashit( $theme_css_dir ) . $css_file_name . '.css',     $args, $css_file_name );
			$global_css_url      = apply_filters( 'wmhook_wmtf_generate_main_css_global_css_url',      trailingslashit( $theme_css_url ) . $css_file_name . '.css',     $args, $css_file_name );
			$global_css_path_dev = apply_filters( 'wmhook_wmtf_generate_main_css_global_css_path_dev', trailingslashit( $theme_css_dir ) . $css_file_name . '.dev.css', $args, $css_file_name );

			if ( $output ) {
				wma_write_local_file( $global_css_path, $output_min );
				wma_write_local_file( $global_css_path_dev, $output );

				//Store the CSS files paths and urls in DB

					update_option( 'wm-' . WM_THEME_SHORTNAME . $args['type'] . '-css', $global_css_url );
					update_option( 'wm-' . WM_THEME_SHORTNAME . $args['type'] . '-files', str_replace( $wp_upload_dir['basedir'], '', $theme_css_dir ) );

				//Admin notice

					set_transient( 'wmamp-admin-notice', array( $args['message_before'] . $args['message'] . $args['message_after'], '', 'switch_themes' ), ( 60 * 60 * 24 ) );

				//Run custom actions

					do_action( 'wmhook_wmtf_generate_main_css', $args );

				return true;
			}

			delete_option( 'wm-' . WM_THEME_SHORTNAME . $args['type'] . '-css' );
			delete_option( 'wm-' . WM_THEME_SHORTNAME . $args['type'] . '-files' );

			return false;

	} // /wmtf_generate_main_css



		/**
		 * Generate visual editor CSS file
		 *
		 * @since    3.0
		 * @version  5.0
		 */
		function wmtf_generate_ve_css() {

			//Output

				return wmtf_generate_main_css( array( 'type' => '-ve' ) );

		} // /wmtf_generate_ve_css



		/**
		 * Generate RTL CSS file
		 *
		 * @since    3.0
		 * @version  5.0
		 */
		function wmtf_generate_rtl_css() {

			//Output

				if ( is_rtl() ) {
					return wmtf_generate_main_css( array( 'type' => '-rtl' ) );
				}

		} // /wmtf_generate_rtl_css



		/**
		 * Generate all CSS files
		 *
		 * @since    3.0
		 * @version  5.0
		 */
		function wmtf_generate_all_css() {

			//Output

				if ( wmtf_generate_main_css() ) {
					wmtf_generate_rtl_css();
					wmtf_generate_ve_css();
				}

		} // /wmtf_generate_all_css
