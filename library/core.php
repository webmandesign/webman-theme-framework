<?php
/**
 * WebMan WordPress Theme Framework
 *
 * A set of core functions.
 *
 * @package    WebMan WordPress Theme Framework
 * @author     WebMan
 * @license    GPL-2.0+
 * @link       http://www.webmandesign.eu
 * @copyright  2014 WebMan - Oliver Juhas
 * @version    3.4
 *
 * CONTENT:
 * - 1) Required files
 * - 10) Actions and filters
 * - 20) Get/save theme options
 * - 30) Branding
 * - 40) SEO and tracking
 * - 50) Post/page
 * - 60) Other functions
 */





/**
 * 1) Required files
 */

	//Layouts and patterns
		locate_template( WM_LIBRARY_DIR . 'includes/hooks.php', true );

	//Plugins activation
		if (
				is_admin()
				&& (
					file_exists( WM_SETUP . 'plugins.php' )
					|| file_exists( WM_SETUP_CHILD . 'plugins.php' )
				)
			) {
			locate_template( WM_LIBRARY_DIR . 'includes/class-tgm-plugin-activation.php', true );
			locate_template( WM_SETUP_DIR . 'plugins.php',                                true );
		}





/**
 * 10) Actions and filters
 */

	/**
	 * Actions
	 */

		//Make sure you are using the most current stylesheet
			add_action( 'init', 'wm_theme_update_regenerate_css' );
		//Modifying HTML head
			add_action( 'wp_head', 'wm_schema_org_meta' );
		//Posts list
			if ( function_exists( 'wma_pagination' ) ) {
				add_action( 'wmhook_postslist_after', 'wma_pagination', 10 );
			}
		//Remove recent comments <style> from HTML head
			add_action( 'widgets_init', 'wm_remove_recent_comments_style' );
		//Blog page query modification
			add_action( 'pre_get_posts', 'wm_home_query', 10 );
		//Contextual help
			add_action( 'contextual_help', 'wm_help', 10, 3 );
		//Display post excerpt
			if ( is_single() && has_excerpt() ) {
				add_action( 'wmhook_content_top', 'wm_excerpt', 10 );
			}



	/**
	 * Filters
	 */

		//Minify CSS
			add_filter( 'wmhook_wm_generate_main_css_output_min', 'wm_minify_css', 10 );
		//Meta title
			add_filter( 'wp_title', 'wm_seo_title', 10, 2 );
		//Excerpt
			add_filter( 'excerpt_length', 'wm_excerpt_length_blog', 999 );
			add_filter( 'excerpt_more', 'wm_excerpt_more' );
		//Search form
			add_filter( 'get_search_form', 'wm_search_form' );
		//Remove invalid HTML5 rel attribute
			add_filter( 'the_category', 'wm_remove_rel' );
		//HTML in widget title of default WordPress widgets
			add_filter( 'widget_title', 'wm_html_widget_title' );
		//Table of contents
			add_filter( 'the_content', 'wm_nextpage_table_of_contents', 10 );
		//Default WordPress content filters only
			add_filter( 'wmhook_content_filters', 'wm_default_content_filters', 10 );
			add_filter( 'wmhook_wm_default_content_filters', 'wptexturize',        10 ); //Default WP
			add_filter( 'wmhook_wm_default_content_filters', 'convert_smilies',    20 ); //Default WP
			add_filter( 'wmhook_wm_default_content_filters', 'convert_chars',      30 ); //Default WP
			add_filter( 'wmhook_wm_default_content_filters', 'do_shortcode',       40 ); //Added by WebMan
			add_filter( 'wmhook_wm_default_content_filters', 'wpautop',            50 ); //Default WP
			add_filter( 'wmhook_wm_default_content_filters', 'shortcode_unautop',  60 ); //Default WP
			add_filter( 'wmhook_wm_default_content_filters', 'prepend_attachment', 70 ); //Default WP
		//OPTIONAL: WordPress [gallery] and [caption] shortcode improvements
			// add_filter( 'post_gallery', 'wm_shortcode_gallery', 10, 2 );





/**
 * 20) Get/save theme options
 */

	/**
	 * Get page ID by its slug
	 *
	 * @param  string $slug
	 */
	if ( ! function_exists( 'wm_page_slug_to_id' ) ) {
		function wm_page_slug_to_id( $slug = null ) {
			$page = get_page_by_path( $slug );

			return ( $slug && is_object( $page ) ) ? ( $page->ID ) : ( null );
		}
	} // /wm_page_slug_to_id



	/**
	 * Get or echo the option
	 *
	 * @param   string $option_name Option name without WM_THEME_SETTINGS_PREFIX prefix
	 * @param   string $css What CSS styles to output ["css" = color, "bgimg" = background image styles]
	 * @param   boolean $addon Will be added to the value if the value is not empty
	 *
	 * @return  mixed Option value
	 */
	if ( ! function_exists( 'wm_option' ) ) {
		function wm_option( $option_name = '', $css = '', $addon = '' ) {
			//Requirements check
				if ( ! $option_name ) {
					return;
				}

			//Helper variables
				global $wm_theme_options, $wp_customize;

				$output = $color = $bg = '';

			//Premature output
				$output = apply_filters( 'wmhook_wm_option_output_premature', $output, $option_name, $css, $addon );

				if ( $output ) {
					return apply_filters( 'wmhook_wm_option_output', $output, $option_name, $css, $addon );
				}

			//Alter $wm_theme_options only in Theme Customizer to provide live preview
				if (
						isset( $wp_customize )
						&& $wp_customize->is_preview()
						&& is_array( get_option( WM_THEME_SETTINGS_SKIN ) )
					) {
					$wm_theme_options = get_option( WM_THEME_SETTINGS_SKIN );
				}

			//Preparing output
				$options     = ( $wm_theme_options ) ? ( $wm_theme_options ) : ( get_option( WM_THEME_SETTINGS_SKIN ) );
				$option_name = WM_THEME_SETTINGS_PREFIX . $option_name;

				if ( ! isset( $options[ $option_name ] ) || ! $options[ $option_name ] ) {
					return;
				}

				//CSS output helper
					if ( $css ) {
						$color  = ( is_string( $css ) && 5 <= strlen( $css ) && 'color' == substr( $css, 0, 5 ) ) ? ( '#' . str_replace( '#', '', stripslashes( $options[ $option_name ] ) ) ) : ( '' );
						$color .= ( $color && 5 < strlen( $css ) ) ? ( str_replace( 'color', '', $css ) ) : ( '' ); // use for example like "color !important"

						$bg  = ( is_string( $css ) && 5 <= strlen( $css ) && 'bgimg' == substr( $css, 0, 5 ) ) ? ( 'url(' . esc_url( stripslashes( $options[ $option_name ] ) ) . ')' ) : ( '' );
						$bg .= ( $bg && 5 < strlen( $css ) ) ? ( str_replace( 'bgimg', '', $css ) ) : ( '' ); // use for example for css positioning, repeat,...
					}

				//Setting the output
					if ( $bg ) {
						$output = $bg;
					} elseif ( $color ) {
						$output = $color;
					} else {
						$output = ( is_array( $options[ $option_name ] ) ) ? ( $options[ $option_name ] ) : ( stripslashes( $options[ $option_name ] ) );
					}

					if ( $output && $addon ) {
						$output .= $addon;
					}

			//Output
				return apply_filters( 'wmhook_wm_option_output', $output, $option_name, $css, $addon );
		}
	} // /wm_option



	/**
	 * Get specific files from specific folder(s)
	 *
	 * @param  array $args
	 */
	if ( ! function_exists( 'wm_get_files' ) ) {
		function wm_get_files( $args = array() ) {
			//Helper variables
				$output = array();

				//Parse arguments
					$args = wp_parse_args( $args, apply_filters( 'wmhook_wm_get_files_args', array(
							'empty_option'    => true,
							'file_extenstion' => 'json',
							'folders'         => array(),
						) ) );

					$args['folders'] = array_unique( $args['folders'] );

				$replacements = apply_filters( 'wmhook_wm_get_files_replacements', array(
						'.' . $args['file_extenstion'] => '',
						'-'                            => ' ',
						'_'                            => ' ',
					) );

			//Requirements check
				if ( empty( $args['folders'] ) ) {
					return;
				}

			//Preparing output
				if ( $args['empty_option'] ) {
					$output[''] = '';
				}

				foreach ( $args['folders'] as $folder ) {
					$folder = trim( $folder );
					if ( $folder && $dir = @opendir( $folder ) ) {
						//This is the correct way to loop over the directory
							while ( false != ( $file = readdir( $dir ) ) ) {
								if ( strpos( $file, $args['file_extenstion'] ) ) {
									$output[ trailingslashit( $folder ) . $file ] = ucwords( str_replace( array_keys( $replacements ), $replacements, $file ) );
								}
							}
						closedir( $dir );
					}
				}

			//Output
				return apply_filters( 'wmhook_wm_get_files_output', $output );
		}
	} // /wm_get_files





/**
 * 30) Branding
 */

	/**
	 * Logo
	 *
	 * @version  3.1
	 */
	if ( ! function_exists( 'wm_logo' ) ) {
		function wm_logo() {
			//Helper variables
				$output = '';

				$args = apply_filters( 'wmhook_wm_logo_args', array(
						'description' => ( get_bloginfo( 'description' ) ) ? ( get_bloginfo( 'name' ) . ' | ' . get_bloginfo( 'description' ) ) : ( get_bloginfo( 'name' ) ),
						'logo_image'  => array( wm_option( 'skin-logo' ), wm_option( 'skin-logo-hidpi' ) ),
						'logo_type'   => 'text',
						'logo_size'   => explode( 'x', WM_DEFAULT_LOGO_SIZE ),
						'url'         => home_url(),
					) );

			//Preparing output
				//Logo image (HiDPI ready)
					if ( $args['logo_image'][0] ) {

						$img_id       = wm_get_image_id_from_url( $args['logo_image'][0] );
						$img_id_hiDPI = wm_get_image_id_from_url( $args['logo_image'][1] );

						if ( $img_id ) {

							$logo_url       = wp_get_attachment_image_src( $img_id, 'full' );
							$logo_url_hiDPI = ( $img_id_hiDPI ) ? ( wp_get_attachment_image_src( $img_id_hiDPI, 'full' ) ) : ( array( '' ) );

							$atts = (array) apply_filters( 'wmhook_wm_logo_image_atts', array(
									'alt'        => esc_attr( sprintf( __( '%s logo', 'wm_domain' ), trim( get_bloginfo( 'name' ) ) ) ),
									'title'      => esc_attr( $args['description'] ),
									'class'      => '',
									'data-hidpi' => ( $logo_url_hiDPI[0] ) ? ( $logo_url_hiDPI[0] ) : ( $logo_url[0] ),
								) );

							$args['logo_image'] = wp_get_attachment_image( $img_id, 'full', false, $atts );

						} else {

							$args['logo_image'] = '<img width="' . $args['logo_size'][0] . '" height="' . $args['logo_size'][1] . '" src="' . $args['logo_image'][0] . '" alt="' . esc_attr( sprintf( __( '%s logo', 'wm_domain' ), trim( get_bloginfo( 'name' ) ) ) ) . '" title="' . esc_attr( $args['description'] ) . '" data-hidpi="' . $args['logo_image'][1] . '" />';

						}

						$args['logo_type'] = 'img';

					}

					$args['logo_image'] = apply_filters( 'wmhook_wm_logo_logo_image', $args['logo_image'] );

				//SEO logo HTML tag
					if ( is_front_page() ) {
						$logo_tag = 'h1';
					} else {
						$logo_tag = 'div';
					}
					$logo_tag = apply_filters( 'wmhook_wm_logo_logo_tag', $logo_tag );

				//Logo HTML
					$output .= '<' . $logo_tag . ' class="' . apply_filters( 'wmhook_wm_logo_class', 'logo type-' . $args['logo_type'], $args['logo_type'] ) . '">';
						$output .= '<a href="' . $args['url'] . '" title="' . esc_attr( $args['description'] ) . '">';

							if ( 'text' === $args['logo_type'] ) {
								$output .= '<span class="text-logo">' . get_bloginfo( 'name' ) . '</span>';
							} else {
								$output .= $args['logo_image'] . '<span class="screen-reader-text">' . get_bloginfo( 'name' ) . ' </span>';
							}

							if ( get_bloginfo( 'description' ) ) {
								$output .= '<span class="description">' . get_bloginfo( 'description' ) . '</span>';
							}

						$output .= '</a>';
					$output .= '</' . $logo_tag . '>';

			//Output
				$output = apply_filters( 'wmhook_wm_logo_output', $output );

				if ( apply_filters( 'wmhook_wm_logo_echo', true ) ) {
					echo $output;
				} else {
					return $output;
				}
		}
	} // /wm_logo



	/**
	 * Favicon and touch icon
	 */
	if ( ! function_exists( 'wm_favicon' ) ) {
		function wm_favicon() {
			//Helper variables
				$output = '';

			//Preparing output
				if ( wm_option( 'skin-touch-icon-144' ) ) {
					$output .= '<link rel="apple-touch-icon-precomposed" sizes="144x144" href="' . esc_url( wm_option( 'skin-touch-icon-144' ) ) . '" /> <!-- for retina iPad -->' . "\r\n";
				}
				if ( wm_option( 'skin-touch-icon-114' ) ) {
					$output .= '<link rel="apple-touch-icon-precomposed" sizes="114x114" href="' . esc_url( wm_option( 'skin-touch-icon-114' ) ) . '" /> <!-- for retina iPhone -->' . "\r\n";
				}
				if ( wm_option( 'skin-touch-icon-72' ) ) {
					$output .= '<link rel="apple-touch-icon-precomposed" sizes="72x72" href="' . esc_url( wm_option( 'skin-touch-icon-72' ) ) . '" /> <!-- for legacy iPad -->' . "\r\n";
				}
				if ( wm_option( 'skin-touch-icon-57' ) ) {
					$output .= '<link rel="apple-touch-icon-precomposed" href="' . esc_url( wm_option( 'skin-touch-icon-57' ) ) . '" /> <!-- for non-retina devices -->' . "\r\n";
				}

				if ( wm_option( 'skin-favicon-png' ) ) {
					$output .= '<link rel="icon" type="image/png" href="' . esc_url( wm_option( 'skin-favicon-png' ) ) . '" /> <!-- standard favicon -->' . "\r\n";
				}
				if ( wm_option( 'skin-favicon-ico' ) ) {
					$output .= '<link rel="shortcut icon" href="' . esc_url( wm_option( 'skin-favicon-ico' ) ) . '" /> <!-- IE favicon -->' . "\r\n";
				}

				if ( $output ) {
					$output = "<!-- icons and favicon -->\r\n" . $output . "\r\n";
				}

			//Output
				return apply_filters( 'wmhook_wm_favicon_output', $output );
		}
	} // /wm_favicon





/**
 * 40) SEO and tracking
 */

	/**
	 * SEO website meta title
	 *
	 * @param  string $title
	 * @param  string $separator
	 */
	if ( ! function_exists( 'wm_seo_title' ) ) {
		function wm_seo_title( $title, $separator ) {
			//Helper variables
				if ( ! $separator ) {
					$separator = ' | ';
				}
				$separator = apply_filters( 'wmhook_wm_seo_title_separator', $separator );
				$title     = apply_filters( 'wmhook_wm_seo_title_separator', $title );

			//Preparing output
				if ( is_feed() ) {
					return $title;
				}

				if ( is_tag() ) {
				//tag archive

					$title = sprintf( __( 'Tag archive for "%s"', 'wm_domain' ), single_tag_title( '', false ) ) . $separator;

				} elseif ( is_search() ) {
				//search

					$title = sprintf( __( 'Search for "%s"', 'wm_domain' ), get_search_query() ) . $separator;

				} elseif ( is_archive() ) {
				//general archive

					$title = sprintf( __( 'Archive for %s', 'wm_domain' ), $title ) . $separator;

				} elseif ( is_singular() && ! is_404() && ! is_front_page() && ! is_home() ) {
				//is page or post but not 404, front page nor home page post list

					$title = trim( $title ) . $separator;

				} elseif ( is_404() ) {
				//404 page

					$title = __( 'Web page was not found', 'wm_domain' ) . $separator;

				} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
				//post page (if set) - get the actual page title

					$title = get_the_title( get_option( 'page_for_posts' ) ) . $separator;

				}

				$title .= get_bloginfo( 'name' );

				//Front page
					if ( is_front_page() ) {
						$title .= $separator . get_bloginfo( 'description' );
					}

				//Pagination / parts
					$title .= wm_paginated_suffix();

			//Output
				return apply_filters( 'wmhook_wm_seo_title_output', esc_attr( $title ) );
		}
	} // /wm_seo_title



	/**
	 * Schema.org markup on HTML meta
	 *
	 * @link  http://leaves-and-love.net/how-to-improve-wordpress-seo-with-schema-org/
	 *
	 * @uses  WPSEO_Frontend class (WordPress SEO by Yoast plugin)
	 * @uses  schema.org
	 */
	if ( ! function_exists( 'wm_schema_org_meta' ) ) {
		function wm_schema_org_meta() {
			if (
					class_exists( 'WPSEO_Frontend' )
					&& defined( 'WMAMP_HOOK_PREFIX' )
					&& ! apply_filters( WMAMP_HOOK_PREFIX . 'disable_schema_org', true )
				) {
				global $wpseo_front;

				$canonical = $wpseo_front->canonical( false );
				echo '<link itemprop="url" href="' . esc_url( $canonical, null, 'other' ) . '" />' . "\n\r";

				$metadesc = $wpseo_front->metadesc( false );
				echo '<meta itemprop="description" content="' . esc_attr( strip_tags( stripslashes( $metadesc ) ) ) . '" />' . "\n\r";
			}
		}
	} // /wm_schema_org_meta





/**
 * 50) Post/page
 */

	/**
	 * Modify blog page query
	 *
	 * @param  object $query WordPress posts query
	 */
	if ( ! function_exists( 'wm_home_query' ) ) {
		function wm_home_query( $query ) {
			//Process only blog query
			if (
					$query->is_home()
					&& $query->is_main_query()
					&& function_exists( 'wma_meta_option' )
				) {

				//Helper variables
					$page_id       = get_option( 'page_for_posts' );
					$article_count = ( wma_meta_option( 'blog-posts-count', $page_id ) ) ? ( wma_meta_option( 'blog-posts-count', $page_id ) ) : ( false );
					$cats_action   = ( wma_meta_option( 'blog-categories-action', $page_id ) ) ? ( wma_meta_option( 'blog-categories-action', $page_id ) ) : ( 'category__in' );
					$cats          = ( wma_meta_option( 'blog-categories', $page_id ) ) ? ( array_filter( wma_meta_option( 'blog-categories', $page_id ) ) ) : ( array() );

					if ( 0 < count( $cats ) ) {
						$cat_temp = array();

						foreach ( $cats as $cat ) {
							if ( isset( $cat['category'] ) && $cat['category'] ) {
								$cat = $cat['category'];

								if ( ! is_numeric( $cat ) ) {
								//Category slugs to IDs

									$cat_object = get_category_by_slug( $cat );
									$cat_temp[] = ( is_object( $cat_object ) && isset( $cat_object->term_id ) ) ? ( $cat_object->term_id ) : ( null );

								} else {

									$cat_temp[] = $cat;

								}
							}
						}

						array_filter( $cat_temp ); //remove empty (if any)

						$cats = $cat_temp;
					}

				//Modify the query
					do_action( 'wmhook_wm_home_query', $query );

					if ( $article_count ) {
						$query->set( 'posts_per_page', absint( $article_count ) );
					}
					if ( 0 < count( $cats ) ) {
						$query->set( $cats_action, $cats );
					}

			}
		}
	} // /wm_home_query



	/**
	 * Thumbnail image
	 *
	 * @param   array $args Heading setup arguments
	 *
	 * @return  string HTML of post thumbnail in image container
	 */
	if ( ! function_exists( 'wm_thumb' ) ) {
		function wm_thumb( $args = array() ) {
			//Helper variables
				$output = apply_filters( 'wmhook_wm_thumb_preprocess_output', '' );

				//Requirements check
					if ( $output ) {
						return apply_filters( 'wmhook_wm_thumb_output', $output );
					}

				//Parse arguments
					$args = wp_parse_args( $args, apply_filters( 'wmhook_wm_thumb_defaults', array(
							'attr-img'    => array(),                                             //array; check WordPress codex on this
							'attr-link'   => array(),                                             //array; additional link HTML attributes
							'class'       => 'image-container post-thumbnail',                    //string; image container additional CSS classes
							'link'        => '',                                                  //string; url
							'addon'       => '',                                                  //string; such as link overlay content
							'placeholder' => false,                                               //boolean; whether to display placeholder image if no featured image
							'post'        => null,                                                //object; WordPress post object
							'size'        => 'medium',                                            //string; image size
							'output'      => '<div class="{class}"><a>{image}{addon}</a></div>',  //output markup
						) ) );

				//Getting parent post ID
					if ( ! $args['post'] ) {
						global $post;

						$args['post'] = $post;
					}
					$post_id = $args['post']->ID;

				//Getting image
					$attachment          = ( has_post_thumbnail( $post_id ) ) ? ( get_post( get_post_thumbnail_id( $post_id ) ) ) : ( '' );
					$attachment_title[0] = ( isset( $attachment->post_title ) ) ? ( trim( strip_tags( $attachment->post_title ) ) ) : ( '' );
					$attachment_title[1] = ( isset( $attachment->post_excerpt ) ) ? ( trim( strip_tags( $attachment->post_excerpt ) ) ) : ( '' );

					$args['attr-img'] = wp_parse_args( $args['attr-img'], array(
							'title' => apply_filters( 'wmhook_wm_thumb_attachment_title', esc_attr( implode( ' | ', array_filter( $attachment_title ) ) ) )
						) );

					$image = '';
					if ( $attachment ) {
						$image = get_the_post_thumbnail( $post_id, $args['size'], $args['attr-img'] );
					} elseif ( $args['placeholder'] ) {
						$image = apply_filters( 'wmhook_wm_thumb_placeholder_image', '<img src="' . wm_get_stylesheet_directory_uri( 'assets/img/placeholder/' . $args['size'] . '.png' ) . '" alt="" />' );
					}

				//Setting link
					if ( trim( $args['link'] ) ) {
						if ( is_array( $args['attr-link'] ) ) {
							$attr_link = '';
							foreach ( $args['attr-link'] as $key => $value ) {
								$attr_link .= ' ' . $key . '="' . esc_attr( $value ) . '"';
							}
							$args['attr-link'] = $attr_link;
						}

						if ( 0 === strpos( $args['link'], 'bigimage' ) && $attachment ) {
							$image_size = trim( str_replace( array( 'bigimage', '|' ), '', $args['link'] ) );
							if ( ! $image_size ) {
								$image_size = 'full';
							}
							$args['link'] = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), $image_size );
							$args['link'] = $args['link'][0];
						}


						$args['link'] = '<a href="' . esc_url( trim( $args['link'] ) ) . '"' . $args['attr-link'] . '>';
					}

			//Preparing output
				if ( $image ) {
					$replacements = apply_filters( 'wmhook_wm_thumb_replacements', array(
							'{addon}' => trim( $args['addon'] ),
							'<a>'     => ( $args['link'] ) ? ( $args['link'] ) : ( '' ),
							'</a>'    => ( $args['link'] ) ? ( '</a>' ) : ( '' ),
							'{class}' => esc_attr( trim( $args['class'] ) ),
							'{image}' => $image,
						) );

					$output = strtr( $args['output'], $replacements );
				}

			//Output
				return apply_filters( 'wmhook_wm_thumb_output', $output );
		}
	} // /wm_thumb



	/**
	 * Get all images attached to the post
	 *
	 * @param   integer $post_id    Specific post id, else current post id used
	 * @param   string  $image_size Image size to get
	 * @param   integer $count      Number of images to retrieve
	 *
	 * @return  Array of images (array keys: name, id, img, title, alt)
	 */
	if ( ! function_exists( 'wm_get_post_images' ) ) {
		function wm_get_post_images( $post_id = null, $image_size = 'widget', $count = -1 ) {
			//Helper variables
				global $post;

				//Requirements check
					if ( ! $post_id && ! $post ) {
						return;
					}

				$post_id    = ( $post_id ) ? ( absint( $post_id ) ) : ( $post->ID );
				$image_size = apply_filters( 'wmhook_wm_get_post_images_image_size', $image_size );
				$output     = array();

			//Preparing output
				$args = apply_filters( 'wmhook_wm_get_post_images_query_args', array(
					'numberposts'    => $count,
					'post_parent'    => $post_id,
					'orderby'        => 'menu_order',
					'order'          => 'asc',
					'post_mime_type' => 'image',
					'post_type'      => 'attachment'
					) );
				$images = get_children( $args );

				if ( ! empty( $images ) ) {
					foreach ( $images as $attachment_id => $attachment ) {
						$image_url     = wp_get_attachment_image_src( $attachment_id, $image_size );
						$image_title   = trim( strip_tags( $attachment->post_title ) );
						$iamge_caption = trim( strip_tags( $attachment->post_excerpt ) );

						$entry = array();

						$entry['name']  = ( $iamge_caption ) ? ( esc_attr( $image_title . ' - ' . $iamge_caption ) ) : ( esc_attr( $image_title ) );
						$entry['name']  = apply_filters( 'wmhook_wm_get_post_images_image_name', $entry['name'] );
						$entry['id']    = esc_attr( $attachment_id );
						$entry['img']   = $image_url[0];
						$entry['title'] = esc_attr( $image_title );
						$entry['alt']   = esc_attr( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );

						$output[] = apply_filters( 'wmhook_wm_get_post_images_single_entry', $entry );
					}
				}

			//Output
				return apply_filters( 'wmhook_wm_get_post_images_output', $output );
		}
	} // /wm_get_post_images



	/**
	 * Table of contents from <!--nextpage--> tag
	 *
	 * Will create a table of content in multipage post from
	 * the first H2 heading in each post part.
	 * Appends the output at the top and bottom of post content.
	 *
	 * @param  string $content
	 */
	if ( ! function_exists( 'wm_nextpage_table_of_contents' ) ) {
		function wm_nextpage_table_of_contents( $content ) {
			//Helper variables
				global $page, $numpages, $multipage, $post;

				$title_text = apply_filters( 'wmhook_wm_nextpage_table_of_contents_title_text', sprintf( __( '"%s" table of contents', 'wm_domain' ), get_the_title() ) );
				$title      = apply_filters( 'wmhook_wm_nextpage_table_of_contents_title', '<h2 class="screen-reader-text">' . $title_text . '</h2>' );

				//Requirements check
					if (
							! $multipage
							|| ! is_single()
						) {
						return $content;
					}

				$atts = apply_filters( 'wmhook_wm_nextpage_table_of_contents_atts', array(
						//If set to TRUE, the first post part will have a title of the post (the part title will not be parsed)
						'disable_first' => true,
						//The output HTML
						'links'         => array( 1 => '<li>' . _wp_link_page( 1 ) . get_the_title() . '</a></li>' ),
						//Get the whole post content
						'post_content'  => ( isset( $post->post_content ) ) ? ( $post->post_content ) : ( '' ),
						//Which HTML heading tag to parse as a post part title
						'tag'           => 'h2',
					) );

				//Post part counter
					$i = 0;

			//Prepare output
				$atts['post_content'] = explode( '<!--nextpage-->', $atts['post_content'] );

				//Get post parts titles
					foreach ( $atts['post_content'] as $part ) {
						$i++;
						if ( absint( $atts['disable_first'] ) < $i ) {
							//Get heading from post part
								preg_match( '/<' . $atts['tag'] . '(.*?)>(.*?)<\/' . $atts['tag'] . '>/', $part, $matches );
							//Fallback to "Part #" if no post part heading found
								if ( ! isset( $matches[2] ) || ! $matches[2] ) {
									$matches[2] = sprintf( __( 'Part %d', 'wm_domain' ), $i );
								}

							$atts['links'][$i] = apply_filters( 'wmhook_wm_nextpage_table_of_contents_part', '<li>' . _wp_link_page( $i ) . $matches[2] . '</a></li>', $matches, $i );
						}
					}

				//Set active part in table of contents
					if ( isset( $atts['links'][$page] ) ) {
						$atts['links'][$page] = str_replace( '<li>', '<li class="active">', $atts['links'][$page] );
					}

				//Add table of contents into the post/page content
					$atts['links'] = implode( '', $atts['links'] );

					$links = apply_filters( 'wmhook_wm_nextpage_table_of_contents_links', array(
							//Display table of contents before the post content only in first post part
								'before' => ( 1 === $page ) ? ( '<div class="post-table-of-contents top" title="' . esc_attr( strip_tags( $title_text ) ) . '">' . $title . '<ol>' . $atts['links'] . '</ol></div>' ) : ( '' ),
							//Display table of cotnnets after the post cotnent on each post part
								'after'  => '<div class="post-table-of-contents bottom" title="' . esc_attr( strip_tags( $title_text ) ) . '">' . $title . '<ol>' . $atts['links'] . '</ol></div>',
						), $atts );

					$content = $links['before'] . $content . $links['after'];

			//Output
				return apply_filters( 'wmhook_wm_nextpage_table_of_contents_output', $content );
		}
	} // /wm_nextpage_table_of_contents



	/**
	 * WP gallery improvements
	 *
	 * Improves WordPress [gallery] shortcode.
	 * Removes inline CSS, changes HTML markup to  valid,
	 * makes it easier to remove images from gallery.
	 *
	 * Original source code from @link wp-includes/media.php
	 *
	 * @version  3.1
	 *
	 * @param  string $output
	 * @param  array  $attr
	 */
	if ( ! function_exists( 'wm_shortcode_gallery' ) ) {
		function wm_shortcode_gallery( $output, $attr ) {
			//Something else is overriding post_gallery, such as a Jetpack plugin's Tiled Gallery
				if ( ! empty( $output ) ) {
					return $output;
				}

			$post = get_post();

			static $instance = 0;
			$instance++;
			//WordPress only passes $attr variable to the filter, so the above needs to be reset

			$output = '';

			// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
			if ( isset( $attr['orderby'] ) ) {
				$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
				if ( ! $attr['orderby'] )
					unset( $attr['orderby'] );
			}

			extract( shortcode_atts( array(
				'order'      => 'ASC',
				'orderby'    => 'menu_order ID',
				'id'         => $post->ID,
				'itemtag'    => 'figure',
				'icontag'    => 'span',
				'captiontag' => 'div',
				'columns'    => 3,
				'size'       => ( wm_option( 'skin-image-gallery' ) ) ? ( 'mobile-' . wm_option( 'skin-image-gallery' ) ) : ( 'mobile-' . WM_DEFAULT_IMAGE_SIZE ),
				'include'    => '',
				'exclude'    => '',
				'link'       => '',
				//custom theme addon:
					'remove'   => '', //remove images by order number
					'flexible' => '', //if set, masonry gallery displayed
					'class'    => '', //additional CSS class on images
				// /custom theme addon
			), $attr, 'gallery' ) );

			//custom theme addon:
				$remove = preg_replace( '/[^0-9,]+/', '', $remove );
				$remove = array_filter( explode( ',', $remove ) );
			// /custom theme addon

			$id = intval( $id );
			if ( 'RAND' == $order ) {
				$orderby = 'none';
			}

			if ( ! empty( $include ) ) {
				$include = preg_replace( '/[^0-9,]+/', '', $include ); //not in WP 3.5 but keeping it
				$_attachments = get_posts( array(
						'include'        => $include,
						'post_status'    => 'inherit',
						'post_type'      => 'attachment',
						'post_mime_type' => 'image',
						'order'          => $order,
						'orderby'        => $orderby
					) );

				$attachments = array();
				foreach ( $_attachments as $key => $val ) {
					$attachments[$val->ID] = $_attachments[$key];
				}
			} elseif ( ! empty( $exclude ) ) {
				$exclude     = preg_replace( '/[^0-9,]+/', '', $exclude ); //not in WP 3.5 but keeping it
				$attachments = get_children( array(
						'post_parent'    => $id,
						'exclude'        => $exclude,
						'post_status'    => 'inherit',
						'post_type'      => 'attachment',
						'post_mime_type' => 'image',
						'order'          => $order,
						'orderby'        => $orderby
					) );
			} else {
				$attachments = get_children( array(
						'post_parent'    => $id,
						'post_status'    => 'inherit',
						'post_type'      => 'attachment',
						'post_mime_type' => 'image',
						'order'          => $order,
						'orderby'        => $orderby
					) );
			}

			if ( empty( $attachments ) || is_feed() )
				return ''; //this will make the default WordPress function to take care of processing

			$itemtag    = tag_escape( $itemtag );
			$captiontag = tag_escape( $captiontag );
			$columns    = absint( $columns );
			$float      = is_rtl() ? 'right' : 'left';

			//custom theme addon:
				$class_container = '';
				$class           = esc_attr( trim( $class ) );
				$wrapper         = ( 'li' == $itemtag ) ? ( '<ul>' ) : ( '' );
				$wrapper_end     = ( $wrapper ) ? ( '</ul>' ) : ( '' );
				$columns         = ( 1 > $columns || 9 < $columns ) ? ( 3 ) : ( $columns ); //only 1 to 9 columns allowed

				if ( 1 === absint( $columns ) ) {
					$size = 'content-width';
				}


				$flexible = ( ( $flexible || 'mobile' == $size || 'content-width' == $size ) && 1 < $columns ) ? ( true ) : ( false );

				if ( $flexible ) {
					$class_container .= ' masonry-container masonry-this';
					if ( 1 !== absint( $columns ) ) {
						$size = 'mobile';
					}
					wp_enqueue_script( 'jquery-masonry' );
				} else {
					$class_container .= ' no-masonry';
				}

				if (
						$class
						&& false !== strpos( $class, 'no-margin' )
					) {
					$class_container .= ' no-margin';
				} else {
					$class_container .= ' with-margin';
					$class           .= ' with-margin';
				}

				$size = apply_filters( 'wmhook_wm_shortcode_gallery_size', $size, $attr, $instance );
			// /custom theme addon

			$selector   = "gallery-{$instance}";
			$size_class = sanitize_html_class( $size );
			$output     = "<div id='$selector' class='gallery galleryid-{$id} clearfix gallery-columns-{$columns} gallery-columns gallery-size-{$size_class}{$class_container}'>" . $wrapper; //custom theme addon

			$i = $j = 0; //$i = every image from gallery, $j = only displayed images
			foreach ( $attachments as $id => $attachment ) { //custom theme addon in this foreach

				$full_image_size  = apply_filters( 'wmhook_wm_shortcode_gallery_full_image_size', 'large', $attr, $instance );

				$full_image_array = wp_get_attachment_image_src( $id, $full_image_size, false );
				$image_array      = wp_get_attachment_image_src( $id, $size, false );

				$title_text       = array( ucfirst( $attachment->post_title ), $attachment->post_excerpt );
				$title_text       = apply_filters( 'wmhook_wm_shortcode_gallery_image_title_array', $title_text );
				$title_separator  = apply_filters( 'wmhook_wm_shortcode_gallery_image_title_separator', ' | ' );
				$title_text       = esc_attr( implode( $title_separator, array_filter( $title_text ) ) );

				$image            = '<img src="' . $image_array[0] . '" alt="' . $title_text . '" title="' . $title_text . '" />';
				$image_link       = '<a href="' . $full_image_array[0] . '" title="' . $title_text . '">' . $image . '</a>';

				$i++;

				if ( ! in_array( $i, $remove ) ) {

					if ( ++$j % $columns == 0 ) {
						$last = ' last';
					} else {
						$last = '';
					}

					$last .= ( $j <= $columns ) ? ( ' first-row' ) : ( null );

					$output .= "<{$itemtag} class='gallery-item wm-column width-1-{$columns}{$last} {$class}'>";
					$output .= "<{$icontag} class='gallery-icon'>{$image_link}</{$icontag}>";

					if ( $captiontag && trim( $attachment->post_excerpt ) ) {
						$output .= "
							<{$captiontag} class='wp-caption-text gallery-caption'>
							" . apply_filters( 'wmhook_content_filters', $attachment->post_excerpt ) . "
							</{$captiontag}>";
					}

					$output .= "</{$itemtag}>";

					if ( $columns > 0 && $i % $columns == 0 ) {
						$output .= '';
					}

				}

			}

			$output .= $wrapper_end . "</div>\r\n"; //custom theme addon

			return apply_filters( 'wmhook_wm_shortcode_gallery_output', $output );
		}
	} // /wm_shortcode_gallery



	/**
	 * Post excerpt
	 */
	if ( ! function_exists( 'wm_excerpt' ) ) {
		function wm_excerpt() {
			//Helper variables
				$output = '';

				//Shortcodes are being stripped out by WordPress by default
					$excerpt = trim( get_the_excerpt() );
					$excerpt = apply_filters( 'wmhook_wm_excerpt_excerpt', $excerpt );

			//Requirements check
				if ( ! $excerpt ) {
					return;
				}

			//Preparing output
				$output .= '<div class="entry-summary"' . wm_schema_org( 'description' ) . '>';
				if ( ! post_password_required() ) {
					$output .= apply_filters( 'wmhook_content_filters', $excerpt );
				} else {
					$output .= '<strong>' . __( 'Password protected', 'wm_domain' ) . '</strong>';
				}
				$output .= '</div>';

			//Output
				return apply_filters( 'wmhook_wm_excerpt_output', $output );
		}
	} // /wm_excerpt



	/**
	 * Set custom excerpt length
	 */
	if ( ! function_exists( 'wm_excerpt_length_blog' ) ) {
		function wm_excerpt_length_blog( $length ) {
			return apply_filters( 'wmhook_wm_excerpt_length_blog_output', WM_DEFAULT_EXCERPT_LENGTH );
		}
	} // /wm_excerpt_length_blog



	/**
	 * Post content or excerpt
	 *
	 * Output depends on using <!--more--> tag.
	 *
	 * @param  object $post
	 * @param  boolean $content_filters
	 */
	if ( ! function_exists( 'wm_content_or_excerpt' ) ) {
		function wm_content_or_excerpt( $post, $content_filters = true ) {
			//Helper variables
				$output = $link = '';

			//Requirements check
				if (
						! $post
						|| ! is_object( $post )
						|| ! isset( $post->post_content )
						|| ! isset( $post->ID )
					) {
					return;
				}

			//Preparing output
				if ( false !== stripos( $post->post_content, '<!--more-->' ) ) {
				//Display excerpt until <!--more--> tag

					//Helper variables
						//Required for <!--more--> tag to work
							global $more;
							$more = 0;

					$output .= '<div class="more-tag-excerpt">';
					if ( ! post_password_required() ) {
						if ( has_excerpt() ) {
							$output .= wm_excerpt();
						}
						$output .= ( $content_filters ) ? ( apply_filters( 'wmhook_content_filters', get_the_content( '' ) ) ) : ( get_the_content( '' ) );
					} else {
						$output .= '<strong>' . __( 'Password protected', 'wm_domain' ) . '</strong>';
					}
					$output .= '</div>';

					$link = get_permalink() . '#more-' . $post->ID;

				} else {
				//Display excerpt only

					$output .= wm_excerpt();

					$link = get_permalink();

				}

				if ( $output ) {
					$output .= '<p class="more-link-container">';
					$output .= wm_more( array(
							'link' => $link,
						) );
					$output .= '</p>';
				}

			//Output
				return apply_filters( 'wmhook_wm_content_or_excerpt_output', $output );
		}
	} // /wm_content_or_excerpt



	/**
	 * Excerpt ellipsis
	 *
	 * @param  string $more
	 */
	if ( ! function_exists( 'wm_excerpt_more' ) ) {
		function wm_excerpt_more( $more ) {
			//Output
				return apply_filters( 'wmhook_wm_excerpt_more_output', '&hellip;' );
		}
	} // /wm_excerpt_more



	/**
	 * "Continue reading" button
	 *
	 * @param  array $args
	 */
	if ( ! function_exists( 'wm_more' ) ) {
		function wm_more( $args = array() ) {
			//Helper variables
				$args = wp_parse_args( $args, apply_filters( 'wmhook_wm_more_defaults', array(
						'attributes' => '',
						'class'      => 'more-link',
						'content'    => sprintf( __( 'Continue reading <span class="screen-reader-text">the "%s" </span>&raquo;', 'wm_domain' ), get_the_title() ),
						'html'       => '<a href="{link}" class="{class}"{attributes}>{content}</a>',
						'link'       => get_permalink(),
					) ) );
				$args = apply_filters( 'wmhook_wm_more_args', $args );

				$output = '';

			//Requirements check
				if ( ! $args['link'] ) {
					return;
				}

			//Preparing output
				$replacements = apply_filters( 'wmhook_wm_more_replacements', array(
						'{attributes}' => esc_attr( $args['attributes'] ),
						'{class}'      => esc_attr( $args['class'] ),
						'{content}'    => $args['content'],
						'{link}'       => esc_url( $args['link'] ),
					) );
				$output = strtr( $args['html'], $replacements );

			//Output
				return apply_filters( 'wmhook_wm_more_output', $output );
		}
	} // /wm_more



	/**
	 * Post meta info
	 *
	 * hAtom microformats compatible. @link http://goo.gl/LHi4Dy
	 *
	 * @param  array $args
	 */
	if ( ! function_exists( 'wm_post_meta' ) ) {
		function wm_post_meta( $args = array() ) {
			//Helper variables
				$output = '';

				$args = wp_parse_args( $args, apply_filters( 'wmhook_wm_post_meta_defaults', array(
						'class'       => 'entry-meta clearfix',
						'date_format' => null,
						'html'        => '<span class="{class}"{attributes}>{content}</span>',
						'html_custom' => array(
								'date' => '<time datetime="{datetime}" class="{class}"{attributes}>{content}</time>',
							),
						'meta'        => array( 'date', 'author', 'category', 'comments', 'permalink' ),
						'post_id'     => null,
						'post'        => null,
					) ) );
				$args = apply_filters( 'wmhook_wm_post_meta_args', $args );

				$args['meta'] = array_filter( (array) $args['meta'] );

				if ( $args['post_id'] ) {
					$args['post_id'] = absint( $args['post_id'] );
					$args['post']    = get_post( $args['post_id'] );
				}

			//Requirements check
				if ( empty( $args['meta'] ) ) {
					return;
				}

			//Preparing output
				foreach ( $args['meta'] as $meta ) {

					//Allow custom metas
						$output .= apply_filters( 'wmhook_wm_post_meta', '', $meta, $args );

					//Predefined metas
						switch ( $meta ) {
							case 'author':

								if ( apply_filters( 'wmhook_wm_post_meta_enable_author', true ) ) {
									$replacements = array(
											'{attributes}' => wm_schema_org( 'creator' ),
											'{class}'      => 'author vcard entry-meta-element',
											'{content}'    => '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" class="fn" rel="author">' . get_the_author() . '</a>',
										);
									$replacements = apply_filters( 'wmhook_wm_post_meta_replacements_author', $replacements );

									if ( isset( $args['html_custom']['author'] ) ) {
										$output .= strtr( $args['html_custom']['author'], $replacements );
									} else {
										$output .= strtr( $args['html'], $replacements );
									}
								}

							break;
							case 'category':
							case 'categories':

								if ( apply_filters( 'wmhook_wm_post_meta_enable_categories', true ) && get_the_category_list( '', '', $args['post_id'] ) ) {
									$replacements = array(
											'{attributes}' => '',
											'{class}'      => 'cat-links entry-meta-element',
											'{content}'    => get_the_category_list( ', ', '', $args['post_id'] ),
										);
									$replacements = apply_filters( 'wmhook_wm_post_meta_replacements_category', $replacements );

									if ( isset( $args['html_custom']['category'] ) ) {
										$output .= strtr( $args['html_custom']['category'], $replacements );
									} else {
										$output .= strtr( $args['html'], $replacements );
									}
								}

							break;
							case 'comments':

								if ( apply_filters( 'wmhook_wm_post_meta_enable_comments', true ) && ( comments_open( $args['post_id'] ) || get_comments_number( $args['post_id'] ) ) ) {
									$element_id   = ( get_comments_number( $args['post_id'] ) ) ? ( '#comments' ) : ( '#respond' );
									$replacements = array(
											'{attributes}' => '',
											'{class}'      => 'comments-link entry-meta-element',
											'{content}'    => '<a href="' . get_permalink( $args['post_id'] ) . $element_id . '" title="' . esc_attr( sprintf( __( 'Comments: %s', 'wm_domain' ), get_comments_number( $args['post_id'] ) ) ) . '">' . sprintf( __( '<span class="comments-title">Comments: </span>%s', 'wm_domain' ), '<span class="comments-count">' . get_comments_number( $args['post_id'] ) . '</span>' ) . '</a>',
										);
									$replacements = apply_filters( 'wmhook_wm_post_meta_replacements_comments', $replacements );

									if ( isset( $args['html_custom']['comments'] ) ) {
										$output .= strtr( $args['html_custom']['comments'], $replacements );
									} else {
										$output .= strtr( $args['html'], $replacements );
									}
								}

							break;
							case 'date':

								if ( apply_filters( 'wmhook_wm_post_meta_enable_date', true ) ) {
									$replacements = array(
											'{attributes}' => ' title="' . esc_attr( get_the_date() ) . ' | ' . esc_attr( get_the_time( '', $args['post'] ) ) . '"' . wm_schema_org( 'publish_date' ),
											'{class}'      => 'entry-date entry-meta-element updated',
											'{content}'    => esc_html( get_the_date( $args['date_format'] ) ),
											'{datetime}'   => esc_attr( get_the_date( 'c' ) ),
										);
									$replacements = apply_filters( 'wmhook_wm_post_meta_replacements_date', $replacements );

									if ( isset( $args['html_custom']['date'] ) ) {
										$output .= strtr( $args['html_custom']['date'], $replacements );
									} else {
										$output .= strtr( $args['html'], $replacements );
									}
								}

							break;
							case 'permalink':

								if ( apply_filters( 'wmhook_wm_post_meta_enable_permalink', true ) ) {
									$the_title_attribute_args = array( 'echo' => false );
									if ( $args['post_id'] ) {
										$the_title_attribute_args['post'] = $args['post'];
									}

									$replacements = array(
											'{attributes}' => wm_schema_org( 'bookmark' ),
											'{class}'      => 'entry-permalink entry-meta-element',
											'{content}'    => '<a href="' . get_permalink( $args['post_id'] ) . '" title="' . esc_attr( sprintf( __( 'Permalink to %s', 'wm_domain' ), the_title_attribute( $the_title_attribute_args ) ) ) . '" rel="bookmark"><span>' . get_the_title( $args['post_id'] ) . '</span></a>',
										);
									$replacements = apply_filters( 'wmhook_wm_post_meta_replacements_permalink', $replacements );

									if ( isset( $args['html_custom']['permalink'] ) ) {
										$output .= strtr( $args['html_custom']['permalink'], $replacements );
									} else {
										$output .= strtr( $args['html'], $replacements );
									}
								}

							break;
							case 'tags':

								if ( apply_filters( 'wmhook_wm_post_meta_enable_tags', true ) && get_the_tag_list( '', '', '', $args['post_id'] ) ) {
									$replacements = array(
											'{attributes}' => wm_schema_org( 'itemprop="keywords"' ),
											'{class}'      => 'tag-links entry-meta-element',
											'{content}'    => sprintf( __( '<strong>Tags:</strong> %s', 'wm_domain' ), get_the_tag_list( '', ', ', '', $args['post_id'] ) ),
										);
									$replacements = apply_filters( 'wmhook_wm_post_meta_replacements_tags', $replacements );

									if ( isset( $args['html_custom']['tags'] ) ) {
										$output .= strtr( $args['html_custom']['tags'], $replacements );
									} else {
										$output .= strtr( $args['html'], $replacements );
									}
								}

							break;

							default:
							break;
						} // /switch

				} // /foreach

				if ( $output ) {
					$output = '<div class="' . esc_attr( $args['class'] ) . '">' . $output . '</div>';
				}

			//Output
				return apply_filters( 'wmhook_wm_post_meta_output', $output );
		}
	} // /wm_post_meta



	/**
	 * Post/page parts pagination
	 *
	 * @param  boolean $echo
	 */
	if ( ! function_exists( 'wm_post_parts' ) ) {
		function wm_post_parts( $echo = true ) {
			wp_link_pages( array(
				'before'         => '<p class="pagination post-parts">',
				'after'          => '</p>',
				'next_or_number' => 'number',
				'pagelink'       => '<span class="page-numbers">' . __( 'Part %', 'wm_domain' ) . '</span>',
				'echo'           => $echo,
			) );
		}
	} // /wm_post_parts



	/**
	 * Paginated heading suffix
	 *
	 * @param  string $tag           Wrapper tag
	 * @param  string $singular_only Display only on singular posts of specific type
	 */
	if ( ! function_exists( 'wm_paginated_suffix' ) ) {
		function wm_paginated_suffix( $tag = '', $singular_only = false ) {
			//Requirements check
				if ( $singular_only && ! is_singular( $singular_only ) ) {
					return;
				}

			//Helper variables
				global $page, $paged;

				$output = '';

				/**
				 * This is just a placeholder for Theme Check plugin.
				 * The theme applies pagination via WebMan Amplifier plugin.
				 */
				$placeholder = paginate_links();

				if ( ! isset( $paged ) ) {
					$paged = 0;
				}
				if ( ! isset( $page ) ) {
					$page = 0;
				}

				$tag = trim( $tag );
				if ( $tag ) {
					$tag = array( '<' . $tag . '>', '</' . $tag . '>' );
				} else {
					$tag = array( '', '' );
				}

			//Preparing output
				if ( 1 < $page ) {
					$output = ' ' . $tag[0] . sprintf( __( '(part %s)', 'wm_domain' ), $page ) . $tag[1];
				} elseif ( 1 < $paged ) {
					$output = ' ' . $tag[0] . sprintf( __( '(page %s)', 'wm_domain' ), $paged ) . $tag[1];
				}

			//Output
				return apply_filters( 'wmhook_wm_paginated_suffix_output', $output );
		}
	} // /wm_paginated_suffix



	/**
	 * No content found message
	 */
	if ( ! function_exists( 'wm_not_found' ) ) {
		function wm_not_found() {
			//Helper variables
				$output  = '<article class="not-found">';
				$output .= '<h1>' . __( 'No item found', 'wm_domain' ) . '</h1>';
				$output .= '</article>';

			//Output
				echo apply_filters( 'wmhook_wm_not_found_output', $output );
		}
	} // /wm_not_found





/**
 * 60) Other functions
 */

	/**
	 * Use default WordPress content filters only
	 *
	 * Some plugins (such as JetPack) extend the "the_content" filters,
	 * causing issue when the filter is applied on different content
	 * sections of the website (such as excerpt...).
	 * Use apply_filters( 'wmhook_content_filters', $content ) to prevent this.
	 *
	 * @param  string $content
	 */
	if ( ! function_exists( 'wm_default_content_filters' ) ) {
		function wm_default_content_filters( $content ) {
			return apply_filters( 'wmhook_wm_default_content_filters', $content );
		}
	} // /wm_default_content_filters



	/**
	 * Check WordPress version
	 *
	 * @param  float $version
	 */
	if ( ! function_exists( 'wm_check_wp_version' ) ) {
		function wm_check_wp_version( $version = WM_WP_COMPATIBILITY ) {
			global $wp_version;

			return apply_filters( 'wmhook_wm_check_wp_version_output', version_compare( (float) $wp_version, $version, '>=' ) );
		}
	} // /wm_check_wp_version



	/**
	 * Prevent your email address from stealing
	 *
	 * Rrequires jQuery function.
	 *
	 * @param  string $email
	 * @param  string $method Set "wp" to use default WordPress method
	 */
	if ( ! function_exists( 'wm_nospam' ) ) {
		function wm_nospam( $email, $method = '' ) {
			//Requirements check
				if ( ! $email || ! is_email( $email ) ) {
					return;
				}

			//Preparing output
				if ( 'wp' == $method ) {
					$email = antispambot( $email );
				} else {
					$email = strrev( $email );
					$email = preg_replace( '[@]', ']ta[', $email );
					$email = preg_replace( '[\.]', '/', $email );
				}

			//Output
				return apply_filters( 'wmhook_wm_nospam_output', $email );
		}
	} // /wm_nospam



	/**
	 * Remove shortcodes from string
	 *
	 * This function keeps the text between shortcodes,
	 * unlike WordPress native strip_shortcodes() function.
	 *
	 * @param  string $content
	 */
	if ( ! function_exists( 'wm_remove_shortcodes' ) ) {
		function wm_remove_shortcodes( $content ) {
			return apply_filters( 'wmhook_wm_remove_shortcodes_output', preg_replace( '|\[(.+?)\]|s', '', $content ) );
		}
	} // /wm_remove_shortcodes



	/**
	 * Remove invalid HTML5 rel attribute
	 *
	 * @param  string $link
	 */
	if ( ! function_exists( 'wm_remove_rel' ) ) {
		function wm_remove_rel( $link ) {
			return ( str_replace ( ' rel="category tag"', '', $link ) );
		}
	} // /wm_remove_rel



	/**
	 * Get post attachments list (except images)
	 */
	if ( ! function_exists( 'wm_post_attachments' ) ) {
		function wm_post_attachments() {
			//Requirements check
				if (
						! is_singular()
						|| ! ( function_exists( 'wma_meta_option' ) && wma_meta_option( 'attachments-list' ) )
					) {
					return;
				}

			//Helper variables
				global $post;

				$output = '';

			//Preparing output
				$args = apply_filters( 'wmhook_wm_post_attachments_args', array(
						'post_type'      => 'attachment',
						'post_mime_type' => 'application,audio,video',
						'numberposts'    => -1,
						'post_status'    => null,
						'post_parent'    => $post->ID,
						'orderby'        => 'menu_order',
						'order'          => 'ASC'
					) );

				$attachments = get_posts( $args );

				if ( is_array( $attachments ) && ! empty( $attachments ) ) {
					foreach ( $attachments as $attachment ) {
						$output .= '<li class="attachment mime-' . sanitize_title( $attachment->post_mime_type ) . '">';
						$output .= '<a href="' . wp_get_attachment_url( $attachment->ID ) . '" title="' . esc_attr( $attachment->post_title ) . '">' . $attachment->post_title . '</a>';
						$output .= '</li>';
					}

					$output = '<div class="list-attachments meta-bottom"><ul class="download">' . $output . '</ul></div>';
				}

			//Output
				echo apply_filters( 'wmhook_wm_post_attachments_output', $output );
		}
	} // /wm_post_attachments



	/**
	 * Get image ID from its URL
	 *
	 * @link   http://pippinsplugins.com/retrieve-attachment-id-from-image-url/
	 * @link   http://make.wordpress.org/core/2012/12/12/php-warning-missing-argument-2-for-wpdb-prepare/
	 *
	 * @param  string $url
	 */
	if ( ! function_exists( 'wm_get_image_id_from_url' ) ) {
		function wm_get_image_id_from_url( $url ) {
			//Helper variables
				global $wpdb;

				$output = null;

			//Preparing output
				if (
						is_object( $wpdb )
						&& isset( $wpdb->prefix )
					) {
					$prefix     = $wpdb->prefix;
					$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM " . $prefix . "posts" . " WHERE guid = %s", esc_url( $url ) ) );
					$output     = ( isset( $attachment[0] ) ) ? ( $attachment[0] ) : ( null );
				}

			//Output
				return apply_filters( 'wmhook_wm_get_image_id_from_url_output', $output );
		}
	} // /wm_get_image_id_from_url



	/**
	 * Search form
	 *
	 * This needs to be a function to maintain return output of get_search_form()
	 *
	 * @param  string $form
	 */
	if ( ! function_exists( 'wm_search_form' ) ) {
		function wm_search_form( $form = '' ) {
			//Helper variables
				$html_array = array();
				$atts       = apply_filters( 'wmhook_wm_search_form_atts', array(
						'action'      => home_url( '/' ),
						'label'       => __( 'Search for:', 'wm_domain' ),
						'name'        => 's',
						'placeholder' => __( 'Search for...', 'wm_domain' ),
					) );
				$value      = ( ! empty( $_GET[ $atts['name'] ] ) ) ? ( get_search_query() ) : ( '' );

			//Preparing output
				$html_array[10]   = '<form class="form-search searchform" action="' . $atts['action'] . '" method="get"><fieldset>';
				if ( $atts['label'] ) {
					$html_array[15] = '<label class="screen-reader-text">' . $atts['label'] . '</label>';
				}
				$html_array[20]   = '<input type="text" name="' . $atts['name'] . '" value="' . $value . '" placeholder="' . $atts['placeholder'] . '" />';
				$html_array[90]   = '<input type="submit" class="submit" value="' . __( 'Submit', 'wm_domain' ) . '" />';
				$html_array[100]  = '</fieldset></form>';

				$html_array = apply_filters( 'wmhook_wm_search_form_html_array', $html_array );

			//Output
				return apply_filters( 'wmhook_wm_search_form_output', implode( '', $html_array ) );
		}
	} // /wm_search_form



	/**
	 * HTML in widget titles
	 *
	 * Just replace the "<" and ">" in HTML tag with "[" and "]".
	 * Examples:
	 * "[em][/em]" will output "<em></em>"
	 * "[br /]" will output "<br />"
	 *
	 * @param  string $title
	 */
	if ( ! function_exists( 'wm_html_widget_title' ) ) {
		function wm_html_widget_title( $title ) {
			//Helper variables
				$replacements = array(
					'[' => '<',
					']' => '>',
				);

			//Preparing output
				$title = strtr( $title, $replacements );

			//Output
				return apply_filters( 'wmhook_wm_html_widget_title_output', $title );
		}
	} // /wm_html_widget_title



	/**
	 * Remove "recent comments" <style> from HTML head
	 *
	 * @param  integer $page_id
	 */
	if ( ! function_exists( 'wm_remove_recent_comments_style' ) ) {
		function wm_remove_recent_comments_style( $page_id = null ) {
			global $wp_widget_factory;

			remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
		}
	} // /wm_remove_recent_comments_style



	/**
	 * Comments pagination
	 *
	 * @param  string $container_id
	 */
	if ( ! function_exists( 'wm_comments_navigation' ) ) {
		function wm_comments_navigation( $container_id = 'comment-nav-above' ) {
			//Helper variables
				$output       = array();
				$container_id = esc_attr( sanitize_html_class( trim( $container_id ) ) );

			//Preparing output
				$output[10] = '<nav id="' . $container_id . '" class="navigation comment-navigation ' . $container_id . '" role="navigation">';
				$output[20] = '<h3 class="screen-reader-text">' . __( 'Comment navigation', 'wm_domain' ) . '</h3>';
				$output[30] = '<div class="nav-previous">' . get_previous_comments_link( __( '&larr; Older comments', 'wm_domain' ) ) . '</div>';
				$output[40] = '<div class="nav-next">' . get_next_comments_link( __( 'Newer comments &rarr;', 'wm_domain' ) ) . '</div>';
				$output[50] = '</nav>';

			//Output
				$output = apply_filters( 'wmhook_wm_comments_navigation_output', $output );
				return implode( '', $output );
		}
	} // /wm_comments_navigation



	/**
	 * Accessibility skip links
	 *
	 * @param  string $type
	 */
	if ( ! function_exists( 'wm_accessibility_skip_link' ) ) {
		function wm_accessibility_skip_link( $type ) {
			//Helper variables
				$links = apply_filters( 'wmhook_wm_accessibility_skip_links', array(
					'to_content'    => '<a class="screen-reader-text" href="#content-section">' . __( 'Skip to content', 'wm_domain' ) . '</a>',
					'to_navigation' => '<a class="screen-reader-text" href="#nav-main">' . __( 'Skip to navigation', 'wm_domain' ) . '</a>',
				) );

			//Output
				if ( ! isset( $links[ $type ] ) ) {
					return;
				}
				return apply_filters( 'wmhook_wm_accessibility_skip_link_output', $links[ $type ] );
		}
	} // /wm_accessibility_skip_link



	/**
	 * Contextual help text
	 *
	 * Hook into 'wmhook_wm_help_texts_array' to add a cotnextual help texts.
	 *
	 * @example  $texts_array = array(
	 *           		//Keys represents the screen IDs where the help text is displayed
	 *           		$screen_id => array(
	 *           			//For each contextual help tab set a new sub-array
	 *           			array(
	 *           				'tab-id'      => 'TAB_ID',
	 *           				'tab-title'   => 'TAB_TITLE',
	 *           				'tab-content' => 'TAB_CONTENT',
	 *           			)
	 *           		)
	 *           );
	 *
	 * @param  string    $contextual_help  Help text that appears on the screen.
	 * @param  string    $screen_id        Screen ID.
	 * @param  WP_Screen $screen           Current WP_Screen instance.
	 */
	if ( ! function_exists( 'wm_help' ) ) {
		function wm_help( $contextual_help, $screen_id, $screen ) {
			//Helper variables
				$texts_array = array_filter( (array) apply_filters( 'wmhook_wm_help_texts_array', array() ) );

			//Requirements check
				if ( empty( $texts_array ) ) {
					return;
				}

			//Output
				if (
						isset( $texts_array[ $screen_id ] )
						&& is_array( $texts_array[ $screen_id ] )
					) {
					$help_tabs = $texts_array[ $screen_id ];

					foreach ( $help_tabs as $tab ) {
						$screen->add_help_tab( array(
							'id'      => $tab['tab-id'],
							'title'   => $tab['tab-title'],
							'content' => $tab['tab-content']
						) );
					}
				}
		}
	} // /wm_help



	/**
	 * CSS functions
	 */

		/**
		 * Outputs URL to the specific file
		 *
		 * This function looks for the file in the child theme first.
		 * If the file is not located in child theme, output the URL from parent theme.
		 *
		 * @param   string $file_relative_path File to look for (insert also the relative path inside the theme)
		 *
		 * @return  string Actual URL to the file
		 */
		if ( ! function_exists( 'wm_get_stylesheet_directory_uri' ) ) {
			function wm_get_stylesheet_directory_uri( $file_relative_path ) {
				//Helper variables
					$output = '';

					$file_relative_path = trim( $file_relative_path );

				//Requirements chek
					if ( ! $file_relative_path ) {
						return apply_filters( 'wm_get_stylesheet_directory_uri_output', esc_url( $output ), $file_relative_path );
					}

				//Praparing output
					if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $file_relative_path ) ) {
						$output = trailingslashit( get_stylesheet_directory_uri() ) . $file_relative_path;
					} else {
						$output = trailingslashit( get_template_directory_uri() ) . $file_relative_path;
					}

				//Output
					return apply_filters( 'wm_get_stylesheet_directory_uri_output', esc_url( $output ), $file_relative_path );
			}
		} // /wm_get_stylesheet_directory_uri



		/**
		 * Outputs path to the specific file
		 *
		 * This function looks for the file in the child theme first.
		 * If the file is not located in child theme, output the path from parent theme.
		 *
		 * @since   3.1
		 *
		 * @param   string $file_relative_path File to look for (insert also the relative path inside the theme)
		 *
		 * @return  string Actual path to the file
		 */
		if ( ! function_exists( 'wm_get_stylesheet_directory' ) ) {
			function wm_get_stylesheet_directory( $file_relative_path ) {
				//Helper variables
					$output = '';

					$file_relative_path = trim( $file_relative_path );

				//Requirements chek
					if ( ! $file_relative_path ) {
						return apply_filters( 'wm_get_stylesheet_directory_output', esc_url( $output ), $file_relative_path );
					}

				//Praparing output
					if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $file_relative_path ) ) {
						$output = trailingslashit( get_stylesheet_directory() ) . $file_relative_path;
					} else {
						$output = trailingslashit( get_template_directory() ) . $file_relative_path;
					}

				//Output
					return apply_filters( 'wm_get_stylesheet_directory_output', $output, $file_relative_path );
			}
		} // /wm_get_stylesheet_directory



		/**
		 * CSS minifier
		 *
		 * @since    3.0
		 * @version  3.1
		 *
		 * @param    string $css Code to minimize
		 */
		if ( ! function_exists( 'wm_minify_css' ) ) {
			function wm_minify_css( $css ) {
				//Praparing output
					//Remove CSS comments
						$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
					//Remove tabs, spaces, line breaks, etc.
						$css = str_replace( array( "\r\n", "\r", "\n", "\t", "  ", "    " ), '', $css );

				//Output
					return apply_filters( 'wmhook_wm_minify_css_output', $css );
			}
		} // /wm_minify_css



		/**
		 * Generate main CSS file
		 *
		 * @since    3.0
		 * @version  3.4
		 *
		 * @param    boolean $args
		 */
		if ( ! function_exists( 'wm_generate_main_css' ) ) {
			function wm_generate_main_css( $args = array() ) {
				//Requirements check
					if ( ! function_exists( 'wma_amplifier' ) ) {
						return false;
					}

				//Helper viariables
					$args = wp_parse_args( $args, apply_filters( 'wmhook_wm_generate_main_css_args', array(
							'gzip'           => false,
							'message'        => __( "<big>The main theme CSS stylesheet was regenerated.<br /><strong>Please refresh your web browser's and server's cache</strong> <em>(if you are using a website server caching solution)</em>.</big>", 'wm_domain' ),
							'message_after'  => '',
							'message_before' => '',
							'type'           => '',
						) ) );

					$output = $output_min = '';

					if ( ! $args['gzip'] ) {
						$args['gzip'] = wm_option( 'general-gzip' ) || wm_option( 'skin-gzip' );
					}
					$args['gzip'] = apply_filters( 'wmhook_wm_generate_main_css_gzip', $args['gzip'], $args );

					$args['type'] = trim( $args['type'] );

				//Preparing output
					//Get the file content with output buffering
						if ( $args['gzip'] ) {
						//GZIP enabled
							ob_start( 'ob_gzhandler' );
						} else {
						//no GZIP
							ob_start();
						}

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

						$output = ob_get_clean();

					if ( ! $output ) {
						return false;
					}

					//Minify output if set
						$output_min = apply_filters( 'wmhook_wm_generate_main_css_output_min', $output, $args );

				//Output
					//Create the theme CSS folder
						$theme_css_dir = wp_upload_dir();
						$theme_css_url = trailingslashit( $theme_css_dir['baseurl'] ) . 'wmtheme-' . WM_THEME_SHORTNAME;
						$theme_css_dir = trailingslashit( $theme_css_dir['basedir'] ) . 'wmtheme-' . WM_THEME_SHORTNAME;

						if ( ! wma_create_folder( $theme_css_dir ) ) {
							set_transient( 'wmamp-admin-notice', array( "<strong>ERROR: Wasn't able to create a theme CSS folder! Contact the theme support.</strong>", 'error', 'switch_themes', 2 ), ( 60 * 60 * 48 ) );

							delete_option( WM_THEME_SETTINGS_PREFIX . WM_THEME_SHORTNAME . $args['type'] . '-css' );
							delete_option( WM_THEME_SETTINGS_PREFIX . WM_THEME_SHORTNAME . $args['type'] . '-files' );
							return false;
						}

					$css_file_name       = apply_filters( 'wmhook_wm_generate_main_css_css_file_name',       'global' . $args['type'],                                        $args                 );
					$global_css_path     = apply_filters( 'wmhook_wm_generate_main_css_global_css_path',     trailingslashit( $theme_css_dir ) . $css_file_name . '.css',     $args, $css_file_name );
					$global_css_url      = apply_filters( 'wmhook_wm_generate_main_css_global_css_url',      trailingslashit( $theme_css_url ) . $css_file_name . '.css',     $args, $css_file_name );
					$global_css_path_dev = apply_filters( 'wmhook_wm_generate_main_css_global_css_path_dev', trailingslashit( $theme_css_dir ) . $css_file_name . '.dev.css', $args, $css_file_name );

					if ( $output ) {
						wma_write_local_file( $global_css_path, $output_min );
						wma_write_local_file( $global_css_path_dev, $output );

						//Store the CSS files paths and urls in DB
							update_option( WM_THEME_SETTINGS_PREFIX . WM_THEME_SHORTNAME . $args['type'] . '-css',   $global_css_url );
							update_option( WM_THEME_SETTINGS_PREFIX . WM_THEME_SHORTNAME . $args['type'] . '-files', $theme_css_dir  );

						//Admin notice
							set_transient( 'wmamp-admin-notice', array( $args['message_before'] . $args['message'] . $args['message_after'], '', 'switch_themes' ), ( 60 * 60 * 24 ) );

						//Run custom actions
							do_action( 'wmhook_wm_generate_main_css', $args );

						return true;
					}

					delete_option( WM_THEME_SETTINGS_PREFIX . WM_THEME_SHORTNAME . $args['type'] . '-css' );
					delete_option( WM_THEME_SETTINGS_PREFIX . WM_THEME_SHORTNAME . $args['type'] . '-files' );
					return false;
			}
		} // /wm_generate_main_css



			/**
			 * Generate visual editor CSS file
			 */
			if ( ! function_exists( 'wm_generate_ve_css' ) ) {
				function wm_generate_ve_css() {
					return wm_generate_main_css( array( 'type' => '-ve' ) );
				}
			} // /wm_generate_ve_css



			/**
			 * Generate RTL CSS file
			 */
			if ( ! function_exists( 'wm_generate_rtl_css' ) ) {
				function wm_generate_rtl_css() {
					if ( is_rtl() ) {
						return wm_generate_main_css( array( 'type' => '-rtl' ) );
					}
				}
			} // /wm_generate_rtl_css



			/**
			 * Generate all CSS files
			 */
			if ( ! function_exists( 'wm_generate_all_css' ) ) {
				function wm_generate_all_css() {
					if ( wm_generate_main_css() ) {
						wm_generate_rtl_css();
						wm_generate_ve_css();
					}
				}
			} // /wm_generate_all_css



			/**
			 * Regenerate the CSS when theme updated
			 */
			if ( ! function_exists( 'wm_theme_update_regenerate_css' ) ) {
				function wm_theme_update_regenerate_css() {
					//Helper variables
						$current_theme_version = get_option( WM_THEME_SETTINGS_VERSION );

					//Processing
						if (
								$current_theme_version
								&& WM_THEME_VERSION != $current_theme_version
								&& wm_generate_main_css()
							) {
							update_option( WM_THEME_SETTINGS_VERSION, WM_THEME_VERSION );

							wm_generate_main_css( array(
									'message_before' => '<big><strong>' . __( 'New theme version installed!', 'wm_domain' ) . '</strong></big><br />',
									'visual_editor'  => true,
								) );
						}
				}
			} // /wm_theme_update_regenerate_css



		/**
		 * Get background CSS styles
		 *
		 * @param  array $args
		 */
		if ( ! function_exists( 'wm_css_background' ) ) {
			function wm_css_background( $args = array() ) {
				//Helper variables
					$args = wp_parse_args( $args, apply_filters( 'wmhook_wm_css_background_defaults', array(
							'option_base' => '',       //Option full name minus function suffixes (bg-color, bg-url,...)
							'high_dpi'    => false,    //Whether to output high DPI image
							'post'        => null,     //If set, the post background will be outputted instead (from post meta). Can be number or object.
							'return'      => 'output', //What to return (see the $output array keys below for values)
						) ) );
					$args = apply_filters( 'wmhook_wm_css_background_args', $args );

					if ( $args['post'] && is_object( $args['post'] ) && isset( $args['post']->ID ) ) {
						$args['post'] = $args['post']->ID;
					} else {
						$args['post'] = absint( $args['post'] );
					}

					//Requirements check
						if ( $args['post'] && ! function_exists( 'wma_meta_option' ) ) {
							return;
						}

					$output = $output_defaults = array(
							'attachment' => '', //image attachment (none/scroll/fixed)
							'color'      => '', //color
							'image'      => '', //image URL
							'output'     => '', //actual output string (the CSS "background:" styles (if size set, the "background-size: styles" will be appended))
							'position'   => '', //image position
							'repeat'     => '', //image repeat
							'size'       => '', //image size
						);

				//Preparing output
					//Background color
						$output['color'] = ( ! $args['post'] ) ? ( wm_option( $args['option_base'] . 'bg-color' ) ) : ( wma_meta_option( $args['option_base'] . 'bg-color', $args['post'] ) );
						if ( $output['color'] ) {
							$output['color'] = '#' . str_replace( '#', '', $output['color'] );
						}

					//Background image
						$output['image'] = ( ! $args['post'] ) ? ( wm_option( $args['option_base'] . 'bg-url' ) ) : ( wma_meta_option( $args['option_base'] . 'bg-url', $args['post'] ) );

						if ( is_array( $output['image'] ) && isset( $output['image']['id'] ) ) {

							$attachment = (array) wp_get_attachment_image_src( $output['image']['id'], 'full' );

							$output['image']  = ( isset( $attachment[0] ) ) ? ( $attachment[0] ) : ( '' );
							$output['size']   = ( isset( $attachment[1] ) ) ? ( $attachment[1] . 'px' ) : ( '' );
							$output['size']  .= ( isset( $attachment[2] ) ) ? ( ' ' . $attachment[2] . 'px' ) : ( '' );

							if ( $args['high_dpi'] ) {
								$attachment = ( ! $args['post'] ) ? ( wm_option( $args['option_base'] . 'bg-url-hidpi' ) ) : ( wma_meta_option( $args['option_base'] . 'bg-url-hidpi', $args['post'] ) );
								$attachment = ( $attachment && isset( $attachment['id'] ) ) ? ( (array) wp_get_attachment_image_src( $attachment['id'], 'full' ) ) : ( false );
								if ( $attachment && isset( $attachment[0] ) ) {
									$output['image'] = $attachment[0];
								}
							}

						} elseif ( $output['image'] ) {

							$attachment_id = wm_get_image_id_from_url( $output['image'] );

							if ( $attachment_id ) {
								$attachment      = (array) wp_get_attachment_image_src( $attachment_id, 'full' );
								$output['size']  = ( isset( $attachment[1] ) ) ? ( $attachment[1] . 'px' ) : ( '' );
								$output['size'] .= ( isset( $attachment[2] ) ) ? ( ' ' . $attachment[2] . 'px' ) : ( '' );
							}

							if ( $args['high_dpi'] ) {
								$output['image'] = ( ! $args['post'] ) ? ( wm_option( $args['option_base'] . 'bg-url-hidpi' ) ) : ( wma_meta_option( $args['option_base'] . 'bg-url-hidpi', $args['post'] ) );
							}

						}

						if ( $output['image'] ) {
							$output['image'] = ' url(' . trim( $output['image'] ) . ')';
						}

					//Background repeat
						if ( $output['image'] ) {
							$output['repeat'] = ( ! $args['post'] ) ? ( wm_option( $args['option_base'] . 'bg-repeat' ) ) : ( wma_meta_option( $args['option_base'] . 'bg-repeat', $args['post'] ) );
							if ( trim( $output['repeat'] ) ) {
								$output['repeat'] = ' ' . trim( $output['repeat'] );
							}
						}

					//Background attachment
						if ( $output['image'] ) {
							$output['attachment'] = ( ! $args['post'] ) ? ( wm_option( $args['option_base'] . 'bg-attachment' ) ) : ( wma_meta_option( $args['option_base'] . 'bg-attachment', $args['post'] ) );
							if ( trim( $output['attachment'] ) ) {
								$output['attachment'] = ' ' . trim( $output['attachment'] );
							}
						}

					//Background position
						if ( $output['image'] ) {
							$output['position'] = ( ! $args['post'] ) ? ( wm_option( $args['option_base'] . 'bg-position' ) ) : ( wma_meta_option( $args['option_base'] . 'bg-position', $args['post'] ) );
							if ( trim( $output['position'] ) ) {
								$output['position'] = ' ' . trim( $output['position'] );
							}
						}

					//Background size
						if ( $output['image'] ) {
							$image_size = ( ! $args['post'] ) ? ( wm_option( $args['option_base'] . 'bg-size' ) ) : ( wma_meta_option( $args['option_base'] . 'bg-size', $args['post'] ) );
							if ( $image_size ) {
								$output['size'] = $image_size;
							}
						}
						$output['size'] = trim( $output['size'] );
						if ( $output['size'] ) {
							$output['size'] = '; background-size: ' . $output['size'];
						}

					//Output string setup
						$output['output'] = $output['color'] . $output['image'] . $output['repeat'] . $output['attachment'] . $output['position'] . $output['size'];

					//If outputing high DPI image, check if image set, if not output nothing (not even background-size)!
						if ( $args['high_dpi'] && ! $output['image'] ) {
							$output = $output_defaults;
						}

					//Filter $output array
						$output = apply_filters( 'wmhook_wm_css_background_output_array', $output, $args );

				//Output
					return apply_filters( 'wmhook_wm_css_background_output', $output[ $args['return'] ], $args );
			}
		} // /wm_css_background

?>