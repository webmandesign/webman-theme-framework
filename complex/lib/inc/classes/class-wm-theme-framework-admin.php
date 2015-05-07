<?php
/**
 * Admin class
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Admin
 *
 * @since    5.0
 * @version  5.0
 */





if ( ! class_exists( 'WM_Theme_Framework_Admin' ) ) {
	final class WM_Theme_Framework_Admin {

		/**
		 * Contents:
		 *
		 * 10) Assets
		 * 20) Posts list table
		 * 30) Messages
		 */





		/**
		 * 10) Assets
		 */

			/**
			 * Admin assets
			 *
			 * @since    3.0
			 * @version  5.0
			 */
			static public function assets() {

				/**
				 * Register
				 */

					//Styles

						$register_styles = apply_filters( 'wmhook_wmtf_admin_assets_register_styles', array(
								'wmtf-about'     => array( WM_Theme_Framework::get_stylesheet_directory_uri( WM_LIBRARY_DIR . 'css/about.css' ) ),
								'wmtf-about-rtl' => array( WM_Theme_Framework::get_stylesheet_directory_uri( WM_LIBRARY_DIR . 'css/rtl-about.css' ) ),
								'wmtf-admin'     => array( WM_Theme_Framework::get_stylesheet_directory_uri( WM_LIBRARY_DIR . 'css/admin.css' ) ),
								'wmtf-admin-rtl' => array( WM_Theme_Framework::get_stylesheet_directory_uri( WM_LIBRARY_DIR . 'css/rtl-admin.css' ) ),
							) );

						foreach ( $register_styles as $handle => $atts ) {
							$src   = ( isset( $atts['src'] )   ) ? ( $atts['src']   ) : ( $atts[0]           );
							$deps  = ( isset( $atts['deps'] )  ) ? ( $atts['deps']  ) : ( false              );
							$ver   = ( isset( $atts['ver'] )   ) ? ( $atts['ver']   ) : ( WM_SCRIPTS_VERSION );
							$media = ( isset( $atts['media'] ) ) ? ( $atts['media'] ) : ( 'screen'           );

							wp_register_style( $handle, $src, $deps, $ver, $media );
						}

					//Scripts

						$register_scripts = apply_filters( 'wmhook_wmtf_admin_assets_register_scripts', array(
								'wmtf-admin' => array( WM_Theme_Framework::get_stylesheet_directory_uri( WM_LIBRARY_DIR . 'js/scripts.js' ) ),
							) );

						foreach ( $register_scripts as $handle => $atts ) {
							$src       = ( isset( $atts['src'] )       ) ? ( $atts['src']       ) : ( $atts[0]           );
							$deps      = ( isset( $atts['deps'] )      ) ? ( $atts['deps']      ) : ( array( 'jquery' )  );
							$ver       = ( isset( $atts['ver'] )       ) ? ( $atts['ver']       ) : ( WM_SCRIPTS_VERSION );
							$in_footer = ( isset( $atts['in_footer'] ) ) ? ( $atts['in_footer'] ) : ( true               );

							wp_register_script( $handle, $src, $deps, $ver, $in_footer );
						}


				/**
				 * Enqueue
				 */

					//Styles

						wp_enqueue_style( 'wmtf-admin' );

						//RTL languages support

							if ( is_rtl() ) {
								wp_enqueue_style( 'wmtf-admin-rtl' );
							}

					//Scripts

						wp_enqueue_script( 'wmtf-admin' );

			} // /assets



			/**
			 * Admin inline styles
			 *
			 * @since    3.0
			 * @version  5.0
			 */
			static public function styles_inline() {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_admin_styles_inline_pre', false );

					if ( false !== $pre ) {
						return $pre;
					}


				//Helper variables

					global $current_screen;

					$output = '';


				//Processing

					//Front and blog page color

						if ( 'edit-page' == $current_screen->id ) {
							$output .= '.hentry.post-' . absint( get_option( 'page_on_front' ) ). ' { background: #daecfe; }';
							$output .= '.hentry.post-' . absint( get_option( 'page_for_posts' ) ) . ' { background: #dafcee; }';
						}

						wp_add_inline_style(
								'wmtf-admin',
								apply_filters( 'wmhook_esc_css', $output )
							);

			} // /styles_inline





		/**
		 * 20) Posts list table
		 */

			/**
			 * Register table columns
			 *
			 * @since    3.0
			 * @version  5.0
			 *
			 * @param  array $columns
			 */
			static public function post_columns_register( $columns ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_admin_post_columns_register_pre', false, $columns );

					if ( false !== $pre ) {
						return $pre;
					}


				//Processing

					$add = array_slice( $columns, 0, 1 );

					$add['wm-thumb'] = __( 'Image', 'wmtf_domain' );


				//Output

					return array_merge( $add, array_slice( $columns, 1 ) );

			} // /post_columns_register



			/**
			 * Admin post list columns content
			 *
			 * If WebMan Amplifier's 'admin-thumbnail' image size not supplied,
			 * use the standard WordPress 'thumbnail' image size.
			 *
			 * @since    3.0
			 * @version  5.0
			 *
			 * @param  string $column
			 * @param  absint $post_id
			 */
			static public function post_columns_render( $column, $post_id ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_admin_post_columns_render_pre', false, $column, $post_id );

					if ( false !== $pre ) {
						return $pre;
					}


				//Processing

					//Thumbnail renderer

						if ( 'wm-thumb' === $column ) {

							$size = ( class_exists( 'WM_Amplifier' ) ) ? ( apply_filters( 'wmhook_wmamp_cp_admin_thumb_size', 'admin-thumbnail' ) ) : ( 'thumbnail' );
							$size = (string) apply_filters( 'wmhook_wmtf_admin_post_columns_render_thumb_size', $size );

							$image = ( has_post_thumbnail() ) ? ( get_the_post_thumbnail( $post_id, $size ) ) : ( '' );

							$thumb_class  = ( $image ) ? ( ' has-thumb' ) : ( ' no-thumb' );
							$thumb_class .= ' size-' . sanitize_html_class( $size );

							echo '<span class="wm-image-container' . esc_attr( $thumb_class ) . '">';

								if ( get_edit_post_link() ) {
									edit_post_link( $image );
								} else {
									echo '<a href="' . esc_url( get_permalink() ) . '">' . $image . '</a>';
								}

							echo '</span>';

						}

			} // /post_columns_render





		/**
		 * 30) Messages
		 */

			/**
			 * WordPress admin notification messages
			 *
			 * Displays the message stored in `wmtf_admin_notice` transient cache
			 * once or multiple times, than deletes the message cache.
			 *
			 * Transient structure:
			 *   set_transient(
			 *     'wmtf_admin_notice',
			 *     array( $text, $class, $capability, $number_of_displays )
			 *   );
			 *
			 * @since    3.0
			 * @version  5.0
			 */
			static public function message() {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_admin_message_pre', false );

					if ( false !== $pre ) {
						echo $pre;
						return;
					}


				//Requirements check

					if ( ! is_admin() ) {
						return;
					}


				//Helper variables

					$output = '';

					$class      = 'updated';
					$repeat     = 0;
					$capability = apply_filters( 'wmhook_wmtf_admin_message_capability', 'switch_themes' );
					$message    = get_transient( 'wmtf_admin_notice' );


				//Requirements check

					if ( empty( $message ) ) {
						return;
					}


				//Processing

					if ( ! is_array( $message ) ) {
						$message = array( $message, $class, $capability, $repeat );
					}
					if ( ! isset( $message[1] ) || empty( $message[1] ) ) {
						$message[1] = $class;
					}
					if ( ! isset( $message[2] ) || empty( $message[2] ) ) {
						$message[2] = $capability;
					}
					if ( ! isset( $message[3] ) ) {
						$message[3] = $repeat;
					}

					if ( $message[0] && current_user_can( $message[2] ) ) {
						$output .= '<div class="' . trim( 'wm-notice ' . $message[1] ) . '"><p>' . $message[0] . '</p></div>';
						delete_transient( 'wmtf_admin_notice' );
					}

					//Delete the transient cache after specific number of displays

						if ( 1 < intval( $message[3] ) ) {
							$message[3] = intval( $message[3] ) - 1;
							set_transient( 'wmtf_admin_notice', $message, ( 60 * 60 * 48 ) );
						}


				//Output

					if ( $output ) {
						echo $output;
					}

			} // /message

	}
} // /WM_Theme_Framework_Admin
