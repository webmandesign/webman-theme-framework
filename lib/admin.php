<?php
/**
 * Admin functions
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Admin functions
 * @copyright   2015 WebMan - Oliver Juhas
 *
 * @since    3.0
 * @version  4.0
 *
 * CONTENT:
 * -   1) Required files
 * -  10) Actions and filters
 * -  20) Assets
 * -  30) Posts list table
 * - 100) Other functions
 */





/**
 * 1) Required files
 */

	//Load the theme About page
		locate_template( WM_SETUP_DIR . 'about/about.php', true );

	//Theme Updater
		if ( apply_filters( 'wmhook_enable_update_notifier', false ) ) {
			locate_template( WM_LIBRARY_DIR . 'inc/update-notifier.php', true );
		}





/**
 * 10) Actions and filters
 */

	/**
	 * Actions
	 */

		//Styles and scripts
			add_action( 'admin_enqueue_scripts', 'wm_assets_admin',        998 );
			add_action( 'admin_enqueue_scripts', 'wm_admin_inline_styles', 998 );
		//Admin notices
			add_action( 'admin_notices', 'wm_admin_notice', 998 );
		//Posts list table
			//Posts
				add_action( 'manage_post_posts_columns',       'wm_post_columns_register', 10    );
				add_action( 'manage_post_posts_custom_column', 'wm_post_columns_render',   10, 2 );
			//Pages
				add_action( 'manage_pages_columns',            'wm_post_columns_register', 10    );
				add_action( 'manage_pages_custom_column',      'wm_post_columns_render',   10, 2 );





/**
 * 20) Assets
 */

	/**
	 * Admin assets
	 *
	 * @since    3.0
	 * @version  4.0
	 */
	if ( ! function_exists( 'wm_assets_admin' ) ) {
		function wm_assets_admin() {
			/**
			 * Register
			 */

				//Styles
					$register_styles = apply_filters( 'wmhook_wm_assets_admin_register_styles', array(
					//Backend
						'wm-about'        => array( wm_get_stylesheet_directory_uri( WM_LIBRARY_DIR . 'css/about.css' ) ),
						'wm-about-rtl'    => array( wm_get_stylesheet_directory_uri( WM_LIBRARY_DIR . 'css/rtl-about.css' ) ),
						'wm-admin'        => array( wm_get_stylesheet_directory_uri( WM_LIBRARY_DIR . 'css/admin.css' ) ),
						'wm-admin-rtl'    => array( wm_get_stylesheet_directory_uri( WM_LIBRARY_DIR . 'css/rtl-admin.css' ) ),
						'wm-admin-wc-rtl' => array( wm_get_stylesheet_directory_uri( WM_LIBRARY_DIR . 'css/rtl-admin-woocommerce.css' ) ),
					) );

					foreach ( $register_styles as $handle => $atts ) {
						$src   = ( isset( $atts['src'] ) ) ? ( $atts['src'] ) : ( $atts[0] );
						$deps  = ( isset( $atts['deps'] ) ) ? ( $atts['deps'] ) : ( false );
						$ver   = ( isset( $atts['ver'] ) ) ? ( $atts['ver'] ) : ( WM_SCRIPTS_VERSION );
						$media = ( isset( $atts['media'] ) ) ? ( $atts['media'] ) : ( 'screen' );

						wp_register_style( $handle, $src, $deps, $ver, $media );
					}

				//Scripts
					$register_scripts = apply_filters( 'wmhook_wm_assets_admin_register_scripts', array(
						//Backend
							'wm-wp-admin' => array(
									'src'  => wm_get_stylesheet_directory_uri( WM_LIBRARY_DIR . 'js/wm-scripts.js' ),
									'deps' => array( 'jquery' ),
								),
						) );

					foreach ( $register_scripts as $handle => $atts ) {
						$src       = ( isset( $atts['src'] ) ) ? ( $atts['src'] ) : ( $atts[0] );
						$deps      = ( isset( $atts['deps'] ) ) ? ( $atts['deps'] ) : ( false );
						$ver       = ( isset( $atts['ver'] ) ) ? ( $atts['ver'] ) : ( WM_SCRIPTS_VERSION );
						$in_footer = ( isset( $atts['in_footer'] ) ) ? ( $atts['in_footer'] ) : ( true );

						wp_register_script( $handle, $src, $deps, $ver, $in_footer );
					}

			/**
			 * Enqueue
			 */

				//Styles
					wp_enqueue_style( 'wm-admin' );

					//RTL languages support
						if ( is_rtl() ) {
							wp_enqueue_style( 'wm-admin-rtl' );
							if ( class_exists( 'Woocommerce' ) ) {
								wp_enqueue_style( 'wm-admin-wc-rtl' );
							}
						}

				//Scripts
					wp_enqueue_script( 'wm-wp-admin' );
		}
	} // /wm_assets_admin



	/**
	 * Admin inline styles
	 *
	 * @since    3.0
	 * @version  4.0
	 */
	if ( ! function_exists( 'wm_admin_inline_styles' ) ) {
		function wm_admin_inline_styles() {
			//Helper variables
				global $current_screen;

				$output     = '';
				$no_preview = apply_filters( 'wmhook_wm_admin_inline_styles_no_preview', array( 'wm_logos', 'wm_modules', 'wm_staff' ) );

			//Preparing output
				//Removing unnecessary view buttons
					if ( in_array( $current_screen->post_type, $no_preview ) ) {
						$output .= '.row-actions .view, #view-post-btn, #preview-action { display: none; }';
					}

				//Homepage and front page colorize
					if ( 'edit-page' == $current_screen->id ) {
						$output .= '.hentry.post-' . get_option( 'page_on_front' ) . ' { background: #d7eef4; }';
						$output .= '.hentry.post-' . get_option( 'page_for_posts' ) . ' { background: #d7f4e3; }';
					}

				//WooCommerce pages colorize
					if (
							class_exists( 'Woocommerce' )
							&& function_exists( 'wc_get_page_id' )
							&& 'edit-page' == $current_screen->id
						) {
						$output .= '.hentry.post-' . wc_get_page_id( 'cart' ) . ' .check-column { background: #CC99C2; }';
						$output .= '.hentry.post-' . wc_get_page_id( 'checkout' ) . ' .check-column { background: #CC99C2; }';
						$output .= '.hentry.post-' . wc_get_page_id( 'logout' ) . ' .check-column { background: #CC99C2; }';
						$output .= '.hentry.post-' . wc_get_page_id( 'myaccount' ) . ' .check-column { background: #CC99C2; }';
						$output .= '.hentry.post-' . wc_get_page_id( 'shop' ) . ' .check-column { background: #CC99C2; }';
						$output .= '.hentry.post-' . wc_get_page_id( 'terms' ) . ' .check-column { background: #CC99C2; }';
						$output .= '.hentry.post-' . wc_get_page_id( 'view_order' ) . ' .check-column { background: #CC99C2; }';
					}

			//Output
				if ( $output = apply_filters( 'wmhook_wm_admin_inline_styles_output', $output ) ) {
					wp_add_inline_style( 'wm-admin', $output );
				}
		}
	} // /wm_admin_inline_styles





/**
 * 30) Posts list table
 */

	/**
	 * Register table columns
	 *
	 * @since    3.0
	 * @version  4.0
	 *
	 * @param  array $columns
	 */
	if ( ! function_exists( 'wm_post_columns_register' ) ) {
		function wm_post_columns_register( $columns ) {
			//Preparing output
				$add             = array_slice( $columns, 0, 1 );
				$add['wm-thumb'] = __( 'Image', 'wm_domain' );

			//Output
				return apply_filters( 'wmhook_wm_post_columns_register_output', array_merge( $add, array_slice( $columns, 1 ) ) );
		}
	} // /wm_post_columns_register



	/**
	 * Admin post list columns content
	 *
	 * If WebMan Amplifier's 'admin-thumbnail' image size not supplied,
	 * use the standard WordPress 'thumbnail' image size.
	 *
	 * @since    3.0
	 * @version  4.0
	 *
	 * @param  string $column
	 * @param  absint $post_id
	 */
	if ( ! function_exists( 'wm_post_columns_render' ) ) {
		function wm_post_columns_render( $column, $post_id ) {
			//Thumbnail renderer
				if ( 'wm-thumb' === $column ) {

					$size  = ( defined( 'WMAMP_HOOK_PREFIX' ) ) ? ( apply_filters( WMAMP_HOOK_PREFIX . 'cp_admin_thumb_size', 'admin-thumbnail' ) ) : ( 'thumbnail' );
					$size  = apply_filters( 'wmhook_wm_post_columns_render_wm-thumb_size', $size );

					$image = ( has_post_thumbnail() ) ? ( get_the_post_thumbnail( $post_id, $size ) ) : ( '' );

					$thumb_class  = ( $image ) ? ( ' has-thumb' ) : ( ' no-thumb' );
					$thumb_class .= ' size-' . $size;

					echo '<span class="wm-image-container' . $thumb_class . '">';

					if ( get_edit_post_link() ) {
						edit_post_link( $image );
					} else {
						echo '<a href="' . get_permalink() . '">' . $image . '</a>';
					}

					echo '</span>';

				}
		}
	} // /wm_post_columns_render





/**
 * 100) Other functions
 */

	/**
	 * WordPress admin notices
	 *
	 * Displays the message stored in "wm-admin-notice" transient cache
	 * once or multiple times, than deletes the message cache.
	 * Transient structure:
	 * set_transient(
	 *   'wm-admin-notice',
	 *   array( $text, $class, $capability, $number_of_displays )
	 * );
	 *
	 * @since    3.0
	 * @version  3.4
	 */
	if ( ! function_exists( 'wm_admin_notice' ) ) {
		function wm_admin_notice() {
			//Requirements check
				if ( ! is_admin() ) {
					return;
				}

			//Helper variables
				$output     = '';
				$class      = 'updated';
				$repeat     = 0;
				$capability = apply_filters( 'wmhook_wm_admin_notice_capability', 'switch_themes' );
				$message    = get_transient( 'wm-admin-notice' );

			//Requirements check
				if ( empty( $message ) ) {
					return;
				}

			//Preparing output
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
					delete_transient( 'wm-admin-notice' );
				}

				//Delete the transient cache after specific number of displays
					if ( 1 < intval( $message[3] ) ) {
						$message[3] = intval( $message[3] ) - 1;
						set_transient( 'wm-admin-notice', $message, ( 60 * 60 * 48 ) );
					}

			//Output
				if ( $output ) {
					echo apply_filters( 'wmhook_wm_admin_notice_output', $output, $message );
				}
		}
	} // /wm_admin_notice

?>