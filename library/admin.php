<?php
/**
 * Admin Functions
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Admin Functions
 * @copyright   2014 WebMan - Oliver Juhas
 *
 * CONTENT:
 * - 1) Required files
 * - 10) Actions and filters
 * - 20) Styles and scripts
 * - 30) Admin login
 * - 40) Admin dashboard customization
 * - 50) Visual editor improvements
 * - 60) Other functions
 */





/**
 * 1) Required files
 */

	//Widgets areas
		if (
				file_exists( WM_SETUP . 'widgets.php' )
				|| file_exists( WM_SETUP_CHILD . 'widgets.php' )
			) {
			locate_template( WM_SETUP_DIR . 'widgets.php', true );
		}

	//Load the theme introduction page
		if (
				is_admin()
				&& (
					file_exists( WM_SETUP . 'about/about.php' )
					|| file_exists( WM_SETUP_CHILD . 'about/about.php' )
				)
			) {
			locate_template( WM_SETUP_DIR . 'about/about.php', true );
		}

	//Skinning functionality
		if ( function_exists( 'wma_amplifier' ) ) {
			locate_template( WM_LIBRARY_DIR . 'skinning.php', true );
		}

	//Theme updater
		if (
				is_admin()
				&& ! ( wm_option( 'general-disable-update-notifier' ) || apply_filters( 'wmhook_disable_update_notifier', false ) )
			) {
			locate_template( WM_LIBRARY_DIR . 'updater/update-notifier.php', true );
		}





/**
 * 10) Actions and filters
 */

	/**
	 * Actions
	 */

		//Admin customization
			add_action( 'admin_head', 'wm_admin_head' );
			add_action( 'admin_enqueue_scripts', 'wm_admin_include', 998 );
			add_action( 'admin_footer', 'wm_admin_footer_scripts' );
		//Disable comments
			if (
					is_admin()
					&& ( wm_option( 'general-comments' ) || apply_filters( 'wmhook_admin_comments', '' ) )
				) {
				add_action ( 'admin_footer', 'wm_comments_off' );
			}
		//Display admin notice
			add_action( 'admin_notices', 'wm_admin_notice' );
		//Admin bar link
			add_action( 'admin_bar_menu', 'wm_theme_options_admin_bar', 998 );



	/**
	 * Filters
	 */

		//TinyMCE customization
			if ( is_admin() ) {
				add_filter( 'tiny_mce_before_init', 'wm_custom_mce_format' );
				add_filter( 'mce_buttons', 'wm_add_buttons_row1' );
			}
		//Login customization
			add_filter( 'login_headertitle', 'wm_login_headertitle' );
			add_filter( 'login_headerurl', 'wm_login_headerurl' );
		//Admin customization
			if ( is_admin() ) {
				add_filter( 'admin_footer_text', 'wm_admin_footer' );
				add_filter( 'manage_post_posts_columns', 'wm_post_columns_register' );
				add_filter( 'manage_post_posts_custom_column', 'wm_post_columns_render', 10 );
				add_filter( 'manage_pages_columns', 'wm_post_columns_register' );
				add_filter( 'manage_pages_custom_column', 'wm_post_columns_render', 10 );
			}
		//User profile
			add_filter( 'user_contactmethods', 'wm_user_contact_methods' );





/**
 * 20) Styles and scripts
 */

	/**
	 * Admin assets
	 */
	if ( ! function_exists( 'wm_admin_include' ) ) {
		function wm_admin_include() {
			//Styles
				wp_enqueue_style( 'wm-admin-addons' );

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
	} // /wm_admin_include



	/**
	 * Admin scripts in footer
	 */
	if ( ! function_exists( 'wm_admin_footer_scripts' ) ) {
		function wm_admin_footer_scripts() {
			//Helper variables
				global $current_screen, $page_templates;

				$output = '';

				$remove_page_templates = apply_filters( 'wmhook_wm_admin_footer_scripts_remove_page_templates', array() );

			//Preparing output
				//Remove page templates
					if ( ! empty( $page_templates ) && ! empty( $remove_page_templates ) ) {
						foreach ( $page_templates as $file => $name ) {
							if ( in_array( $file, $remove_page_templates ) ) {
								$output .= "\r\n" . 'jQuery( "#page_template option[value=\"' . $file . '\"]" ).remove();';
							}
						}
					}

			//Output
				if ( $output ) {
					$output = '
						<script type="text/javascript">
						//<![CDATA[
						jQuery(function() {
							' . $output . '
						});
						//]]>
						</script>';

					echo apply_filters( 'wmhook_wm_admin_footer_scripts_output', $output );
				}
		}
	} // /wm_admin_footer_scripts





/**
 * 30) Admin login
 */

	/**
	 * Login logo title
	 */
	if ( ! function_exists( 'wm_login_headertitle' ) ) {
		function wm_login_headertitle() {
			return apply_filters( 'wmhook_wm_login_headertitle_output', get_bloginfo( 'name' ) );
		}
	} // /wm_login_headertitle



	/**
	 * Login logo URL
	 */
	if ( ! function_exists( 'wm_login_headerurl' ) ) {
		function wm_login_headerurl() {
			return apply_filters( 'wmhook_wm_login_headerurl_output', home_url() );
		}
	} // /wm_login_headerurl





/**
 * 40) Admin dashboard customization
 */

	/**
	 * Admin footer text customization
	 */
	if ( ! function_exists( 'wm_admin_footer' ) ) {
		function wm_admin_footer() {
			//Helper variables
				$output = '&copy; ' . get_bloginfo( 'name' ) . ' | Powered by <a href="http://wordpress.org/" target="_blank">WordPress</a> | Theme created by <a href="' . WM_DEVELOPER_URL . '" target="_blank">WebMan</a>';

			//Output
				echo apply_filters( 'wmhook_wm_admin_footer_output', $output );
		}
	} // /wm_admin_footer



	/**
	 * Admin HTML head
	 */
	if ( ! function_exists( 'wm_admin_head' ) ) {
		function wm_admin_head() {
			//Helper variables
				global $current_screen;

				$output     = '';
				$no_preview = apply_filters( 'wmhook_wm_admin_head_no_preview',  array( 'wm_logos', 'wm_modules', 'wm_staff' ) );

			//Preparing output
				//Removing unnecessary view buttons
					if ( in_array( $current_screen->post_type, $no_preview ) ) {
						$output .= "\r\n" . '.row-actions .view, #view-post-btn, #preview-action {display: none}';
					}

				//Homepage and front page colorize
					if ( 'edit-page' == $current_screen->id ) {
						$output .= '.hentry.post-' . get_option( 'page_on_front' ) . ' {background: #d7eef4}' . "\r\n" . '.hentry.post-' . get_option( 'page_for_posts' ) . ' {background: #d7f4e3}' . "\r\n";
					}

				//WooCommerce pages colorize
					if (
							class_exists( 'Woocommerce' )
							&& function_exists( 'wc_get_page_id' )
							&& 'edit-page' == $current_screen->id
						) {
						$output .= //"Products" WC settings tab
						           '.hentry.post-' . wc_get_page_id( 'shop' )       . ' .check-column {background: #CC99C2}' . "\r\n" .
						           //"Checkout" WC settings tab
						           '.hentry.post-' . wc_get_page_id( 'cart' )       . ' .check-column {background: #CC99C2}' . "\r\n" .
						           '.hentry.post-' . wc_get_page_id( 'checkout' )   . ' .check-column {background: #CC99C2}' . "\r\n" .
						           '.hentry.post-' . wc_get_page_id( 'terms' )      . ' .check-column {background: #CC99C2}' . "\r\n" .
						           //"Accounts" WC settings tab
						           '.hentry.post-' . wc_get_page_id( 'myaccount' )  . ' .check-column {background: #CC99C2}' . "\r\n" .
						           //Other WC pages
						           '.hentry.post-' . wc_get_page_id( 'view_order' ) . ' .check-column {background: #CC99C2}' . "\r\n" .
						           '.hentry.post-' . wc_get_page_id( 'logout' )     . ' .check-column {background: #CC99C2}' . "\r\n";
					}

			//Output
				if ( $output ) {
					echo apply_filters( 'wmhook_wm_admin_head_output', '<style type="text/css">' . "\r\n" . $output . '</style>' . "\r\n" );
				}
		}
	} // /wm_admin_head



	/**
	 * Admin post list columns
	 *
	 * @param  array $columns
	 */
	if ( ! function_exists( 'wm_post_columns_register' ) ) {
		function wm_post_columns_register( $columns ) {
			//Helper variables
				$add                = array_slice( $columns, 0, 1 );
				$add['wmamp-thumb'] = __( 'Image', 'wm_domain' );

			//Output
				return apply_filters( 'wmhook_wm_post_columns_register_output', array_merge( $add, array_slice( $columns, 1 ) ) );
		}
	} // /wm_post_columns_register



	/**
	 * Admin post list columns content
	 *
	 * @param  array $column
	 */
	if ( ! function_exists( 'wm_post_columns_render' ) ) {
		function wm_post_columns_render( $column ) {
			//Thumbnail renderer
				if ( 'wmamp-thumb' === $column ) {

					$size  = ( defined( 'WMAMP_HOOK_PREFIX' ) ) ? ( apply_filters( WMAMP_HOOK_PREFIX . 'cp_admin_thumb_size', 'admin-thumbnail' ) ) : ( 'thumbnail' );
					$image = ( has_post_thumbnail() ) ? ( get_the_post_thumbnail( null, $size ) ) : ( '' );

					$hasThumb = ( $image ) ? ( ' has-thumb' ) : ( ' no-thumb' );

					echo '<span class="wm-image-container' . $hasThumb . '">';

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
 * 50) Visual editor improvements
 */

	/**
	 * Add buttons to visual editor
	 *
	 * First row.
	 *
	 * @param  array $buttons
	 */
	if ( ! function_exists( 'wm_add_buttons_row1' ) ) {
		function wm_add_buttons_row1( $buttons ) {
			//Inserting buttons after "more" button
				$pos = array_search( 'wp_more', $buttons, true );
				if ( $pos != false ) {
					$add     = array_slice( $buttons, 0, $pos + 1 );
					$add[]   = 'wp_page';
					$buttons = array_merge( $add, array_slice( $buttons, $pos + 1 ) );
				}

			//Output
				return apply_filters( 'wmhook_wm_add_buttons_row1_output', $buttons );
		}
	} // /wm_add_buttons_row1



	/**
	 * Customizing format dropdown items
	 *
	 * @param  array $init
	 */
	if ( ! function_exists( 'wm_custom_mce_format' ) ) {
		function wm_custom_mce_format( $init ) {
			//Format buttons, default = 'p,address,pre,h1,h2,h3,h4,h5,h6'
				$init['theme_advanced_blockformats'] = apply_filters( 'wmhook_wm_custom_mce_format_theme_advanced_blockformats', 'p,h1,h2,h3,h4,h5,h6,address,div' );

			//Command separated string of extended elements
				$ext = apply_filters( 'wmhook_wm_custom_mce_format_extended_valid_elements', 'pre[id|name|class|style]' );
				if ( isset( $init['extended_valid_elements'] ) ) {
					$init['extended_valid_elements'] .= ',' . $ext;
				} else {
					$init['extended_valid_elements'] = $ext;
				}

			//Output
				return apply_filters( 'wmhook_wm_custom_mce_format_output', $init );
		}
	} // /wm_custom_mce_format





/**
 * 60) Other functions
 */

	/**
	 * Adds a Theme Options links to WordPress admin bar
	 */
	if ( ! function_exists( 'wm_theme_options_admin_bar' ) ) {
		function wm_theme_options_admin_bar() {
			//Requirements check
				if ( ! current_user_can( 'switch_themes' ) ) {
					return;
				}

			//Helper variables
				global $wp_admin_bar;

				//Requirements check
					if ( ! is_admin_bar_showing() ) {
						return;
					}

				$submenu = apply_filters( 'wmhook_wm_theme_options_admin_bar_submenu', array() );

			//Add admin bar links
				$wp_admin_bar->add_menu( apply_filters( 'wmhook_wm_theme_options_admin_bar_parent', array(
						'id'    => 'wm_theme_options',
						'title' => __( 'Theme Options', 'wm_domain' ),
						'href'  => admin_url( 'customize.php' )
					) ) );

				//Submenu items
					if ( is_array( $submenu ) && ! empty( $submenu ) ) {
						foreach ( $submenu as $title => $url ) {
							$wp_admin_bar->add_menu( apply_filters( 'wmhook_wm_theme_options_admin_bar_child_wm_theme_options-' . sanitize_title( $title ), array(
									'parent' => 'wm_theme_options',
									'id'     => WM_THEME_SHORTNAME . '_theme_options-' . sanitize_title( $title ),
									'title'  => $title,
									'href'   => $url,
								) ) );
						}
					}
		}
	} // /wm_theme_options_admin_bar



	/**
	 * WordPress admin notices
	 *
	 * Displays the message stored in "wm-admin-notice" transient cache
	 * just once, than deletes the message cache.
	 */
	if ( ! function_exists( 'wm_admin_notice' ) ) {
		function wm_admin_notice() {
			//Helper variables
				$output     = '';
				$capability = apply_filters( 'wmhook_wm_admin_notice_capability', 'switch_themes' );

				$message = get_transient( 'wm-admin-notice' );

				if ( ! is_array( $message ) ) {
					$message = array( $message, $capability );
				}
				if ( ! isset( $message[1] ) || empty( $message[1] ) ) {
					$message[1] = $capability;
				}

			//Preparing output
				if ( $message[0] && current_user_can( $message[1] ) ) {
					$output .= '<div class="updated wm-notice"><p>' . $message[0] . '</p></div>';
					delete_transient( 'wm-admin-notice' );
				}

			//Output
				echo apply_filters( 'wmhook_wm_admin_notice_output', $output );
		}
	} // /wm_admin_notice



	/**
	 * WordPress user profile contact fields
	 *
	 * @param  array $user_contactmethods
	 */
	if ( ! function_exists( 'wm_user_contact_methods' ) ) {
		function wm_user_contact_methods( $user_contactmethods ) {
			//Preparing output
				if ( ! isset( $user_contactmethods['twitter'] ) ) {
					$user_contactmethods['twitter'] = 'Twitter';
				}
				if ( ! isset( $user_contactmethods['facebook'] ) ) {
					$user_contactmethods['facebook'] = 'Facebook';
				}
				if ( ! isset( $user_contactmethods['googleplus'] ) ) {
					$user_contactmethods['googleplus'] = 'Google+';
				}

			//Output
				return apply_filters( 'wmhook_wm_user_contact_methods_output', $user_contactmethods );
		}
	} // /wm_user_contact_methods



	/**
	 * Switch comments and pingbacks off
	 */
	if ( ! function_exists( 'wm_comments_off' ) ) {
		function wm_comments_off() {
			//Helper variables
				global $current_screen;

				$output = '';

				$post_types = apply_filters( 'wmhook_admin_comments', wm_option( 'general-comments' ) );

				if ( ! is_array( $post_types ) ) {
					$post_types = explode( ',', $post_types );
				}
				$post_types = apply_filters( 'wmhook_wm_comments_off_post_types', array_filter( $post_types ) );

			//Requirements check
				if (
						empty( $post_types )
						|| ! isset( $current_screen->post_type )
						|| ! isset( $current_screen->action )
					) {
					return;
				}

			//Preparing output
				if ( in_array( $current_screen->post_type, $post_types ) && 'add' == $current_screen->action ) {
					$output .= '<script><!--
						if ( document.post ) {
							var the_comment = document.post.comment_status,
							    the_ping    = document.post.ping_status;
							if ( the_comment && the_ping ) {
								the_comment.checked = false;
								the_ping.checked    = false;
							}
						}
						//--></script>';
				}

			//Output
				echo apply_filters( 'wmhook_wm_comments_off_output', $output );
		}
	} // /wm_comments_off

?>