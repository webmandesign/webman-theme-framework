<?php
/**
 * Admin class
 *
 * @package     WebMan WordPress Theme Framework (Simple)
 * @subpackage  Admin
 *
 * @since    2.0
 * @version  2.0
 */





if ( ! class_exists( 'WM_Theme_Framework_Admin' ) ) {
	final class WM_Theme_Framework_Admin {

		/**
		 * Contents:
		 *
		 * 10) Assets
		 * 20) Posts list table
		 */





		/**
		 * 10) Assets
		 */

			/**
			 * Admin assets
			 *
			 * @since    1.0
			 * @version  2.0
			 */
			public static function assets() {

				//Helper variables

					global $current_screen;

					$custom_styles = '';


				/**
				 * Enqueue
				 */

					if ( in_array( $current_screen->base, array( 'edit', 'post' ) ) ) {

						//Styles

							wp_enqueue_style(
									'wmtf-admin-styles',
									WM_Theme_Framework::get_stylesheet_directory_uri( 'css/admin.css' ),
									false,
									esc_attr( trim( wp_get_theme()->get( 'Version' ) ) ),
									'screen'
								);

					}

			} // /assets





		/**
		 * 20) Posts list table
		 */

			/**
			 * Register table columns
			 *
			 * @since    1.0
			 * @version  2.0
			 *
			 * @param  array $columns
			 */
			public static function post_columns_register( $columns ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_admin_post_columns_register_pre', false, $columns );

					if ( false !== $pre ) {
						return $pre;
					}


				//Processing

					if ( 'jetpack-portfolio' == get_post_type() ) {
						unset( $columns['thumbnail'] );
					}

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
			 * @since    1.0
			 * @version  2.0
			 *
			 * @param  string $column
			 * @param  absint $post_id
			 */
			public static function post_columns_render( $column, $post_id ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_admin_post_columns_render_pre', false, $column, $post_id );

					if ( false !== $pre ) {
						return $pre;
					}


				//Processing

					//Thumbnail renderer

						if ( 'wm-thumb' === $column ) {

							$size = ( class_exists( 'Jetpack_Portfolio' ) ) ? ( 'jetpack-portfolio-admin-thumb' ) : ( 'thumbnail' );
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

	}
} // /WM_Theme_Framework_Admin
