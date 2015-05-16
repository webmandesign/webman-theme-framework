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
		 * 20) Messages
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
			public static function assets() {

				// Helper variables

					$version = esc_attr( trim( wp_get_theme()->get( 'Version' ) ) );


				// Register

					/**
					 * Styles
					 */

						$register_styles = apply_filters( 'wmhook_wmtf_admin_assets_register_styles', array(
								'wmtf-about'     => array( WM_Theme_Framework::get_stylesheet_directory_uri( WMTF_LIBRARY_DIR . 'css/about.css' ) ),
								'wmtf-about-rtl' => array( WM_Theme_Framework::get_stylesheet_directory_uri( WMTF_LIBRARY_DIR . 'css/rtl-about.css' ) ),
								'wmtf-admin'     => array( WM_Theme_Framework::get_stylesheet_directory_uri( WMTF_LIBRARY_DIR . 'css/admin.css' ) ),
								'wmtf-admin-rtl' => array( WM_Theme_Framework::get_stylesheet_directory_uri( WMTF_LIBRARY_DIR . 'css/rtl-admin.css' ) ),
							) );

						foreach ( $register_styles as $handle => $atts ) {
							$src   = ( isset( $atts['src'] )   ) ? ( $atts['src']   ) : ( $atts[0] );
							$deps  = ( isset( $atts['deps'] )  ) ? ( $atts['deps']  ) : ( false    );
							$ver   = ( isset( $atts['ver'] )   ) ? ( $atts['ver']   ) : ( $version );
							$media = ( isset( $atts['media'] ) ) ? ( $atts['media'] ) : ( 'screen' );

							wp_register_style( $handle, $src, $deps, $ver, $media );
						}


				// Enqueue

					/**
					 * Styles
					 */

						wp_enqueue_style( 'wmtf-admin' );

						// RTL languages support

							if ( is_rtl() ) {
								wp_enqueue_style( 'wmtf-admin-rtl' );
							}

			} // /assets





		/**
		 * 20) Messages
		 */

			/**
			 * WordPress admin notification messages
			 *
			 * Displays the message stored in `wmtf_admin_notice` transient cache
			 * once or multiple times, than deletes the message cache.
			 *
			 * Transient structure:
			 *
			 * @example
			 *
			 *   set_transient(
			 *     'wmtf_admin_notice',
			 *     array(
			 *       $text,
			 *       $class,
			 *       $capability,
			 *       $number_of_displays
			 *     )
			 *   );
			 *
			 * @since    3.0
			 * @version  5.0
			 */
			public static function message() {

				// Pre

					$pre = apply_filters( 'wmhook_wmtf_admin_message_pre', false );

					if ( false !== $pre ) {
						echo $pre;
						return;
					}


				// Requirements check

					if ( ! is_admin() ) {
						return;
					}


				// Helper variables

					$output = '';

					$class      = 'updated';
					$repeat     = 0;
					$capability = apply_filters( 'wmhook_wmtf_admin_message_capability', 'switch_themes' );
					$message    = get_transient( 'wmtf_admin_notice' );


				// Requirements check

					if ( empty( $message ) ) {
						return;
					}


				// Processing

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

					// Delete the transient cache after specific number of displays

						if ( 1 < intval( $message[3] ) ) {
							$message[3] = intval( $message[3] ) - 1;
							set_transient( 'wmtf_admin_notice', $message, ( 60 * 60 * 48 ) );
						}


				// Output

					if ( $output ) {
						echo $output;
					}

			} // /message

	}
} // /WM_Theme_Framework_Admin
