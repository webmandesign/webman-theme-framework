<?php
/**
 * WebMan WordPress Theme Framework
 *
 * A set of core functions.
 *
 * @package    WebMan WordPress Theme Framework
 * @copyright  2015 WebMan - Oliver Juhas
 * @license    GPL-2.0+, http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @version  4.0
 *
 * @link  https://github.com/webmandesign/webman-theme-framework
 * @link  http://www.webmandesign.eu
 *
 * CONTENT:
 * -   0) Constants
 * -   1) Required files
 * -  10) Actions and filters
 * -  20) Branding
 * -  30) SEO
 * -  40) Post/page
 * - 100) Other functions
 */





/**
 * 0) Constants
 */

	//Helper variables
		$theme_data = wp_get_theme();

	//Basic constants
		if ( ! defined( 'WM_THEME_NAME' ) )        define( 'WM_THEME_NAME',        $theme_data->Name                                            );
		if ( ! defined( 'WM_THEME_SHORTNAME' ) )   define( 'WM_THEME_SHORTNAME',   str_replace( array( '-lite', '-plus' ), '', get_template() ) );
		if ( ! defined( 'WM_THEME_VERSION' ) )     define( 'WM_THEME_VERSION',     $theme_data->Version                                         );

		if ( ! defined( 'WM_SCRIPTS_VERSION' ) )   define( 'WM_SCRIPTS_VERSION',   esc_attr( trim( WM_THEME_VERSION ) )                         );

	//Options constants
		if ( ! defined( 'WM_OPTION_PREFIX' ) )     define( 'WM_OPTION_PREFIX',     ''                                                           );
		if ( ! defined( 'WM_OPTION_INSTALL' ) )    define( 'WM_OPTION_INSTALL',    'wm-' . WM_THEME_SHORTNAME . '-install'                      );
		if ( ! defined( 'WM_OPTION_CUSTOMIZER' ) ) define( 'WM_OPTION_CUSTOMIZER', 'theme_mods_' . WM_THEME_SHORTNAME                           );

	//Dir constants
		if ( ! defined( 'WM_LIBRARY_DIR' ) )       define( 'WM_LIBRARY_DIR',       trailingslashit( 'lib' )                                     );
		if ( ! defined( 'WM_SETUP_DIR' ) )         define( 'WM_SETUP_DIR',         trailingslashit( 'setup' )                                   );
		if ( ! defined( 'WM_SETUP' ) )             define( 'WM_SETUP',             trailingslashit( get_template_directory() ) . WM_SETUP_DIR   );
		if ( ! defined( 'WM_SETUP_CHILD' ) )       define( 'WM_SETUP_CHILD',       trailingslashit( get_stylesheet_directory() ) . WM_SETUP_DIR );
		if ( ! defined( 'WM_SKINS_DIR' ) )         define( 'WM_SKINS_DIR',         trailingslashit( WM_SETUP . 'skins' )                        );
		if ( ! defined( 'WM_SKINS_DIR_CHILD' ) )   define( 'WM_SKINS_DIR_CHILD',   trailingslashit( WM_SETUP_CHILD . 'skins' )                  );

	//URL constants
		if ( ! defined( 'WM_DEVELOPER_URL' ) )     define( 'WM_DEVELOPER_URL',     'http://www.webmandesign.eu'                                 );

	//Required to set up in the theme's functions.php file
		if ( ! defined( 'WM_WP_COMPATIBILITY' ) )  define( 'WM_WP_COMPATIBILITY',  4.1                                                          );



	/**
	 * Global variables
	 */

		//Get theme options
			$wm_theme_options = get_option( WM_OPTION_CUSTOMIZER );

			if ( empty( $wm_theme_options ) ) {
				$wm_theme_options = array();
			}





/**
 * 1) Required files
 */

	//Main theme action hooks
		locate_template( WM_LIBRARY_DIR . 'inc/hooks.php', true );

	//Admin required files
		if ( is_admin() ) {

			//WP admin functionality
				locate_template( WM_LIBRARY_DIR . 'admin.php', true );

			//Plugins suggestions
				if (
						apply_filters( 'wmhook_enable_plugins_integration', true )
						&& locate_template( WM_SETUP_DIR . 'tgmpa/plugins.php' )
					) {
					locate_template( WM_LIBRARY_DIR . 'inc/class-tgm-plugin-activation.php', true );
					locate_template( WM_SETUP_DIR . 'tgmpa/plugins.php',                           true );
				}

			//Customizer
				locate_template( WM_LIBRARY_DIR . 'customizer.php', true );

		}





/**
 * 10) Actions and filters
 */

	/**
	 * Actions
	 */

		//Theme upgrade action
			add_action( 'init', 'wm_theme_upgrade' );
		//Remove recent comments <style> from HTML head
			add_action( 'widgets_init', 'wm_remove_recent_comments_style' );
		//Flushing transients
			add_action( 'switch_theme',  'wm_image_ids_transient_flusher'      );
			add_action( 'edit_category', 'wm_all_categories_transient_flusher' );
			add_action( 'save_post',     'wm_all_categories_transient_flusher' );
		//Home query modification
			add_action( 'pre_get_posts', 'wm_home_query', 10 );
		//Contextual help
			add_action( 'contextual_help', 'wm_help', 10, 3 );
		//Admin bar (displayed also on front end)
			add_action( 'admin_bar_menu', 'wm_theme_options_admin_bar', 998 );



	/**
	 * Filters
	 */

		//Escape inline CSS
			add_filter( 'wmhook_esc_css', 'wm_esc_css' );
		//HTML in widget title
			add_filter( 'widget_title', 'wm_html_widget_title' );
		//Table of contents
			add_filter( 'the_content', 'wm_nextpage_table_of_contents', 10 );
		//Minify CSS
			add_filter( 'wmhook_wm_generate_main_css_output_min', 'wm_minify_css', 10 );
		//Default WordPress content filters only
			add_filter( 'wmhook_content_filters', 'wm_default_content_filters', 10 );
			//WP defaults
				add_filter( 'wmhook_wm_default_content_filters', 'wptexturize',        10 );
				add_filter( 'wmhook_wm_default_content_filters', 'convert_smilies',    20 );
				add_filter( 'wmhook_wm_default_content_filters', 'convert_chars',      30 );
				add_filter( 'wmhook_wm_default_content_filters', 'wpautop',            40 );
				add_filter( 'wmhook_wm_default_content_filters', 'shortcode_unautop',  50 );
				add_filter( 'wmhook_wm_default_content_filters', 'prepend_attachment', 60 );
			//Custom additions
				add_filter( 'wmhook_wm_default_content_filters', 'do_shortcode',       35 );





/**
 * 20) Branding
 */

	/**
	 * Logo
	 *
	 * Supports Jetpack Site Logo module.
	 *
	 * @since    3.0
	 * @version  4.0
	 */
	if ( ! function_exists( 'wm_logo' ) ) {
		function wm_logo() {
			//Helper variables
				$output = '';

				$blog_info = apply_filters( 'wmhook_wm_logo_blog_info', array(
						'name'        => trim( get_bloginfo( 'name' ) ),
						'description' => trim( get_bloginfo( 'description' ) ),
					) );

				$args = apply_filters( 'wmhook_wm_logo_args', array(
						'logo_image' => ( function_exists( 'jetpack_get_site_logo' ) ) ? ( array( absint( jetpack_get_site_logo( 'id' ) ) ) ) : ( array( wm_option( 'logo' ), wm_option( 'logo-hidpi' ) ) ),
						'logo_type'  => 'text',
						'title_att'  => ( $blog_info['description'] ) ? ( $blog_info['name'] . ' | ' . $blog_info['description'] ) : ( $blog_info['name'] ),
						'url'        => home_url( '/' ),
					) );

			//Preparing output
				//Logo image
					if ( $args['logo_image'][0] ) {

						$img_id = ( is_numeric( $args['logo_image'][0] ) ) ? ( absint( $args['logo_image'][0] ) ) : ( wm_get_image_id_from_url( $args['logo_image'][0] ) );

						//HiDPI support
							if ( isset( $args['logo_image'][1] ) && is_numeric( $args['logo_image'][1] ) ) {
								$img_id_hiDPI = absint( $args['logo_image'][1] );
							} elseif ( isset( $args['logo_image'][1] ) ) {
								$img_id_hiDPI = wm_get_image_id_from_url( $args['logo_image'][1] );
							} else {
								$img_id_hiDPI = false;
							}

						if ( $img_id ) {

							$logo_url = wp_get_attachment_image_src( $img_id, 'full' );

							$atts = array(
									'alt'   => esc_attr( sprintf( _x( '%s logo', 'Site logo image "alt" HTML attribute text.', 'wm_domain' ), $blog_info['name'] ) ),
									'title' => esc_attr( $args['title_att'] ),
									'class' => '',
								);
							if ( $img_id_hiDPI ) {
								$logo_url_hiDPI     = wp_get_attachment_image_src( $img_id_hiDPI, 'full' );
								$atts['data-hidpi'] = $logo_url_hiDPI[0];
							}
							$atts = (array) apply_filters( 'wmhook_wm_logo_image_atts', $atts );

							$args['logo_image'] = wp_get_attachment_image( $img_id, 'full', false, $atts );

						}

						$args['logo_type'] = 'img';

					}

					$args['logo_image'] = apply_filters( 'wmhook_wm_logo_logo_image', $args['logo_image'] );

				//Logo HTML
					$output .= '<div class="site-branding">';
						$output .= '<h1 class="' . apply_filters( 'wmhook_wm_logo_class', 'site-title logo type-' . $args['logo_type'], $args ) . '">';
						$output .= '<a href="' . esc_url( $args['url'] ) . '" title="' . esc_attr( $args['title_att'] ) . '">';

								if ( 'text' === $args['logo_type'] ) {
									$output .= '<span class="text-logo">' . $blog_info['name'] . '</span>';
								} else {
									$output .= $args['logo_image'];
								}

						$output .= '</a></h1>';

							if ( $blog_info['description'] ) {
								$output .= '<h2 class="site-description">' . $blog_info['description'] . '</h2>';
							}

					$output .= '</div>';

			//Output
				echo apply_filters( 'wmhook_wm_logo_output', $output );
		}
	} // /wm_logo





/**
 * 30) SEO
 */

	/**
	 * SEO website meta title
	 *
	 * Not needed since WordPress 4.1.
	 *
	 * @todo Remove this when WordPress 4.3 is released.
	 *
	 * @since    3.0 (under wm_seo_title() name)
	 * @version  4.0
	 */
	if ( ! function_exists( '_wp_render_title_tag' ) ) {

		/**
		 * SEO website meta title
		 *
		 * @param  string $title
		 * @param  string $sep
		 */
		if ( ! function_exists( 'wm_title' ) ) {
			function wm_title( $title, $sep ) {
				//Requirements check
					if ( is_feed() ) {
						return $title;
					}

				//Helper variables
					$sep = ' ' . trim( $sep ) . ' ';

				//Preparing output
					$title .= get_bloginfo( 'name', 'display' );

					//Site description
						if (
								( $site_description = get_bloginfo( 'description', 'display' ) )
								&& ( is_home() || is_front_page() )
							) {
							$title .= $sep . $site_description;
						}

					//Pagination / parts
						if ( wm_paginated_suffix() && ! is_404() ) {
							$title .= $sep . wm_paginated_suffix();
						}

				//Output
					return esc_attr( $title );
			}

			add_filter( 'wp_title', 'wm_title', 10, 2 );
		} // /wm_title



		/**
		 * Title shim
		 *
		 * @link https://make.wordpress.org/core/2014/10/29/title-tags-in-4-1/
		 */
		function _wp_render_title_tag() {
			?>
			<title><?php wp_title( '|', true, 'right' ); ?></title>
			<?php
		}

		add_action( 'wp_head', '_wp_render_title_tag', -99 );

	} // /wm_title





/**
 * 40) Post/page
 */

	/**
	 * Table of contents from <!--nextpage--> tag
	 *
	 * Will create a table of content in multipage post from
	 * the first H2 heading in each post part.
	 * Appends the output at the top and bottom of post content.
	 *
	 * @since    3.0
	 * @version  4.0
	 *
	 * @param  string $content
	 */
	if ( ! function_exists( 'wm_nextpage_table_of_contents' ) ) {
		function wm_nextpage_table_of_contents( $content ) {
			//Helper variables
				global $page, $numpages, $multipage, $post;

				//translators: %s will be replaced with parted post title. Copy it, do not translate.
				$title_text = apply_filters( 'wmhook_wm_nextpage_table_of_contents_title_text', sprintf( __( '"%s" table of contents', 'wm_domain' ), get_the_title() ) );
				$title      = apply_filters( 'wmhook_wm_nextpage_table_of_contents_title', '<h2 class="screen-reader-text">' . $title_text . '</h2>' );

				//Requirements check
					if (
							! $multipage
							|| ! is_single()
						) {
						return $content;
					}

				$args = apply_filters( 'wmhook_wm_nextpage_table_of_contents_atts', array(
						//If set to TRUE, the first post part will have a title of the post (the part title will not be parsed)
						'disable_first' => true,
						//The output HTML
						'links'         => array(),
						//Get the whole post content
						'post_content'  => ( isset( $post->post_content ) ) ? ( $post->post_content ) : ( '' ),
						//Which HTML heading tag to parse as a post part title
						'tag'           => 'h2',
					) );

				//Post part counter
					$i = 0;

			//Prepare output
				$args['post_content'] = explode( '<!--nextpage-->', $args['post_content'] );

				//Get post parts titles
					foreach ( $args['post_content'] as $part ) {

						//Current post part number
							$i++;

						//Get title for post part
							if ( $args['disable_first'] && 1 === $i ) {

								$part_title = get_the_title();

							} else {

								preg_match( '/<' . $args['tag'] . '(.*?)>(.*?)<\/' . $args['tag'] . '>/', $part, $matches );

								if ( ! isset( $matches[2] ) || ! $matches[2] ) {
									$part_title = sprintf( __( 'Page %s', 'wm_domain' ), $i );
								} else {
									$part_title = $matches[2];
								}

							}

						//Set post part class
							if ( $page === $i ) {
								$class = ' class="current"';
							} elseif ( $page > $i ) {
								$class = ' class="passed"';
							} else {
								$class = '';
							}

						//Post part item output
							$args['links'][$i] = apply_filters( 'wmhook_wm_nextpage_table_of_contents_part', '<li' . $class . '>' . _wp_link_page( $i ) . $part_title . '</a></li>', $i, $part_title, $class, $args );

					}

				//Add table of contents into the post/page content
					$args['links'] = implode( '', $args['links'] );

					$links = apply_filters( 'wmhook_wm_nextpage_table_of_contents_links', array(
							//Display table of contents before the post content only in first post part
								'before' => ( 1 === $page ) ? ( '<div class="post-table-of-contents top" title="' . esc_attr( strip_tags( $title_text ) ) . '">' . $title . '<ol>' . $args['links'] . '</ol></div>' ) : ( '' ),
							//Display table of cotnnets after the post cotnent on each post part
								'after'  => '<div class="post-table-of-contents bottom" title="' . esc_attr( strip_tags( $title_text ) ) . '">' . $title . '<ol>' . $args['links'] . '</ol></div>',
						), $args );

					$content = $links['before'] . $content . $links['after'];

			//Output
				return apply_filters( 'wmhook_wm_nextpage_table_of_contents_output', $content, $args );
		}
	} // /wm_nextpage_table_of_contents



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
	 * Post meta info
	 *
	 * hAtom microformats compatible. @link http://goo.gl/LHi4Dy
	 * Supports ZillaLikes plugin. @link http://www.themezilla.com/plugins/zillalikes/
	 * Supports Post Views Count plugin. @link https://wordpress.org/plugins/baw-post-views-count/
	 *
	 * @since    3.0
	 * @version  4.0
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
						'meta'        => array(), //For example: array( 'date', 'author', 'category', 'comments', 'permalink' )
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
						$helper = '';

						$replacements  = (array) apply_filters( 'wmhook_wm_post_meta_replacements', array(), $meta, $args );
						$single_output = apply_filters( 'wmhook_wm_post_meta', '', $meta, $args );
						$output       .= $single_output;

					//Predefined metas
						switch ( $meta ) {
							case 'author':

								if ( apply_filters( 'wmhook_wm_post_meta_enable_' . $meta, true, $args ) ) {
									$replacements = array(
											'{attributes}' => wm_schema_org( 'Person' ),
											'{class}'      => 'author vcard entry-meta-element',
											'{content}'    => '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" class="url fn n" rel="author"' . wm_schema_org( 'author' ) .'>' . get_the_author() . '</a>',
										);
								}

							break;
							case 'category':

								if (
										apply_filters( 'wmhook_wm_post_meta_enable_' . $meta, true, $args )
										&& wm_is_categorized_blog()
										&& ( $helper = get_the_category_list( ', ', '', $args['post_id'] ) )
									) {
									$replacements = array(
											'{attributes}' => '',
											'{class}'      => 'cat-links entry-meta-element',
											'{content}'    => $helper,
										);
								}

							break;
							case 'comments':

								if (
										apply_filters( 'wmhook_wm_post_meta_enable_' . $meta, true, $args )
										&& ! post_password_required()
										&& (
											comments_open( $args['post_id'] )
											|| get_comments_number( $args['post_id'] )
										)
									) {
									$helper = get_comments_number( $args['post_id'] ); //Don't know why this can not be in IF condition, but otherwise it won't work...
									$element_id   = ( $helper ) ? ( '#comments' ) : ( '#respond' );
									$replacements = array(
											'{attributes}' => '',
											'{class}'      => 'comments-link entry-meta-element',
											'{content}'    => '<a href="' . get_permalink( $args['post_id'] ) . $element_id . '" title="' . esc_attr( sprintf( _x( 'Comments: %s', 'Number of comments in post meta.', 'wm_domain' ), $helper ) ) . '">' . sprintf( _x( '<span class="comments-title">Comments: </span>%s', 'Number of comments in post meta (keep the HTML tags).', 'wm_domain' ), '<span class="comments-count">' . $helper . '</span>' ) . '</a>',
										);
								}

							break;
							case 'date':

								if ( apply_filters( 'wmhook_wm_post_meta_enable_' . $meta, true, $args ) ) {
									$replacements = array(
											'{attributes}' => ' title="' . esc_attr( get_the_date() ) . ' | ' . esc_attr( get_the_time( '', $args['post'] ) ) . '"' . wm_schema_org( 'datePublished' ),
											'{class}'      => 'entry-date entry-meta-element published',
											'{content}'    => esc_html( get_the_date( $args['date_format'] ) ),
											'{datetime}'   => esc_attr( get_the_date( 'c' ) ),
										);
								}

							break;
							case 'edit':

								if (
										apply_filters( 'wmhook_wm_post_meta_enable_' . $meta, true, $args )
										&& ( $helper = get_edit_post_link( $args['post_id'] ) )
									) {
									$the_title_attribute_args = array( 'echo' => false );
									if ( $args['post_id'] ) {
										$the_title_attribute_args['post'] = $args['post'];
									}

									$replacements = array(
											'{attributes}' => '',
											'{class}'      => 'entry-edit entry-meta-element',
											'{content}'    => '<a href="' . esc_url( $helper ) . '" title="' . esc_attr( sprintf( __( 'Edit the "%s"', 'wm_domain' ), the_title_attribute( $the_title_attribute_args ) ) ) . '"><span>' . _x( 'Edit', 'Edit post link.', 'wm_domain' ) . '</span></a>',
										);
								}

							break;
							case 'likes':

								if (
										apply_filters( 'wmhook_wm_post_meta_enable_' . $meta, true, $args )
										&& function_exists( 'zilla_likes' )
									) {
									global $zilla_likes;
									$helper = $zilla_likes->do_likes();

									$replacements = array(
											'{attributes}' => '',
											'{class}'      => 'entry-likes entry-meta-element',
											'{content}'    => $helper,
										);
								}

							break;
							case 'permalink':

								if ( apply_filters( 'wmhook_wm_post_meta_enable_' . $meta, true, $args ) ) {
									$the_title_attribute_args = array( 'echo' => false );
									if ( $args['post_id'] ) {
										$the_title_attribute_args['post'] = $args['post'];
									}

									$replacements = array(
											'{attributes}' => wm_schema_org( 'url' ),
											'{class}'      => 'entry-permalink entry-meta-element',
											'{content}'    => '<a href="' . get_permalink( $args['post_id'] ) . '" title="' . esc_attr( sprintf( __( 'Permalink to "%s"', 'wm_domain' ), the_title_attribute( $the_title_attribute_args ) ) ) . '" rel="bookmark"><span>' . get_the_title( $args['post_id'] ) . '</span></a>',
										);
								}

							break;
							case 'tags':

								if (
										apply_filters( 'wmhook_wm_post_meta_enable_' . $meta, true, $args )
										&& ( $helper = get_the_tag_list( '', ' ', '', $args['post_id'] ) )
									) {
									$replacements = array(
											'{attributes}' => wm_schema_org( 'keywords' ),
											'{class}'      => 'tags-links entry-meta-element',
											'{content}'    => $helper,
										);
								}

							break;
							case 'views':

								if (
										apply_filters( 'wmhook_wm_post_meta_enable_' . $meta, true, $args )
										&& function_exists( 'bawpvc_views_sc' )
										&& ( $helper = bawpvc_views_sc( array() ) )
									) {
									$replacements = array(
											'{attributes}' => ' title="' . __( 'Views count', 'wm_domain' ) . '"',
											'{class}'      => 'entry-views entry-meta-element',
											'{content}'    => $helper,
										);
								}

							break;

							default:
							break;
						} // /switch

						//Single meta output
							$replacements = (array) apply_filters( 'wmhook_wm_post_meta_replacements_' . $meta, $replacements, $args );
							if (
									empty( $single_output )
									&& ! empty( $replacements )
								) {
								if ( isset( $args['html_custom'][ $meta ] ) ) {
									$output .= strtr( $args['html_custom'][ $meta ], (array) $replacements );
								} else {
									$output .= strtr( $args['html'], (array) $replacements );
								}
							}

				} // /foreach

				if ( $output ) {
					$output = '<div class="' . esc_attr( $args['class'] ) . '">' . $output . '</div>';
				}

			//Output
				return apply_filters( 'wmhook_wm_post_meta_output', $output, $args );
		}
	} // /wm_post_meta



	/**
	 * Paginated heading suffix
	 *
	 * @since    3.0
	 * @version  4.0
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

				if ( ! isset( $paged ) ) {
					$paged = 0;
				}
				if ( ! isset( $page ) ) {
					$page = 0;
				}

				$paged = max( $page, $paged );

				$tag = trim( $tag );
				if ( $tag ) {
					$tag = array( '<' . $tag . '>', '</' . $tag . '>' );
				} else {
					$tag = array( '', '' );
				}

			//Preparing output
				if ( 1 < $paged ) {
					$output = ' ' . $tag[0] . sprintf( _x( '(page %s)', 'Paginated content title suffix.', 'wm_domain' ), $paged ) . $tag[1];
				}

			//Output
				return apply_filters( 'wmhook_wm_paginated_suffix_output', $output );
		}
	} // /wm_paginated_suffix



	/**
	 * Checks for <!--more--> tag in post content
	 *
	 * @since    4.0
	 * @version  4.0
	 *
	 * @param  obj/absint $post
	 */
	if ( ! function_exists( 'wm_has_more_tag' ) ) {
		function wm_has_more_tag( $post = null ) {
			//Helper variables
				if ( empty( $post ) ) {
					global $post;
				} elseif ( is_numeric( $post ) ) {
					$post = get_post( absint( $post ) );
				}

			//Requirements check
				if (
						! is_object( $post )
						|| ! isset( $post->post_content )
					) {
					return;
				}

			//Output
				return strpos( $post->post_content, '<!--more-->' );
		}
	} // /wm_has_more_tag





/**
 * 100) Other functions
 */

	/**
	 * Check WordPress version
	 *
	 * @since    1.0
	 * @version  4.0
	 *
	 * @param  float $version
	 */
	if ( ! function_exists( 'wm_check_wp_version' ) ) {
		function wm_check_wp_version( $version = WM_WP_COMPATIBILITY ) {
			global $wp_version;

			return apply_filters( 'wmhook_wm_check_wp_version_output', version_compare( (float) $wp_version, $version, '>=' ), $version, $wp_version );
		}
	} // /wm_check_wp_version



	/**
	 * Do action on theme version change
	 *
	 * @since    4.0
	 * @version  4.0
	 */
	if ( ! function_exists( 'wm_theme_upgrade' ) ) {
		function wm_theme_upgrade() {
			//Helper variables
				$current_theme_version = get_transient( WM_THEME_SHORTNAME . '-version' );

			//Processing
				if (
						empty( $current_theme_version )
						|| WM_THEME_VERSION != $current_theme_version
					) {
					do_action( 'wmhook_theme_upgrade' );
					set_transient( WM_THEME_SHORTNAME . '-version', WM_THEME_VERSION );
				}
		}
	} // /wm_theme_upgrade



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
	 * Accessibility skip links
	 *
	 * @since    3.0
	 * @version  4.0
	 *
	 * @param  string $type
	 */
	if ( ! function_exists( 'wm_accessibility_skip_link' ) ) {
		function wm_accessibility_skip_link( $type ) {
			//Helper variables
				$links = apply_filters( 'wmhook_wm_accessibility_skip_links', array(
					'to_content'    => '<a class="skip-link screen-reader-text" href="#content">' . __( 'Skip to content', 'wm_domain' ) . '</a>',
					'to_navigation' => '<a class="skip-link screen-reader-text" href="#site-navigation">' . __( 'Skip to navigation', 'wm_domain' ) . '</a>',
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
	 * Get theme options and files
	 */

		/**
		 * Get the theme option
		 *
		 * Note: Do not use get_theme_mod() as it is not very transferable from "lite" to "pro" themes.
		 *
		 * @since    3.0
		 * @version  4.0
		 *
		 * @param  string $option_name Option name without WM_OPTION_PREFIX prefix
		 * @param  string $css         CSS to output ["color" = HEX color, "bgimg" = background image styles]
		 * @param  string $addon       Will be added to the value if the value is not empty
		 *
		 * @return  mixed Option value.
		 */
		if ( ! function_exists( 'wm_option' ) ) {
			function wm_option( $option_name = '', $css = '', $addon = '' ) {
				//Requirements check
					if ( ! $option_name ) {
						return;
					}

				//Helper variables
					global $wm_theme_options, $wp_customize;

					$output = '';

					if ( ! isset( $wm_theme_options ) ) {
						$wm_theme_options = null;
					}

				//Premature output
					$output = apply_filters( 'wmhook_wm_option_output_pre', $output, $option_name, $css, $addon );

					if ( $output ) {
						return apply_filters( 'wmhook_wm_option_output', $output, $option_name, $css, $addon );
					}

				//Alter $wm_theme_options only in Theme Customizer to provide live preview
					if (
							isset( $wp_customize )
							&& $wp_customize->is_preview()
							&& is_array( get_option( WM_OPTION_CUSTOMIZER ) )
						) {
						$wm_theme_options = get_option( WM_OPTION_CUSTOMIZER );
					}

				//Preparing output
					$options     = ( $wm_theme_options ) ? ( $wm_theme_options ) : ( get_option( WM_OPTION_CUSTOMIZER ) );
					$option_name = WM_OPTION_PREFIX . $option_name;

					if (
							! isset( $options[ $option_name ] )
							|| ! $options[ $option_name ]
						) {
						return;
					}

					//CSS output helper
						if ( 'bgimg' === $css ) {
							$output = 'url(\'' . esc_url( stripslashes( $options[ $option_name ] ) ) . '\')';
						} elseif ( 'color' === $css ) {
							$output = '#' . trim( stripslashes( $options[ $option_name ] ), '#' );
						} else {
							$output = ( is_array( $options[ $option_name ] ) ) ? ( $options[ $option_name ] ) : ( stripslashes( $options[ $option_name ] ) );
						}

					//Output addon
						if ( $output ) {
							$output .= $addon;
						}

				//Output
					return apply_filters( 'wmhook_wm_option_output', $output, $option_name, $css, $addon );
			}
		} // /wm_option



		/**
		 * Get specific files from specific folder(s)
		 *
		 * @since    3.0
		 * @version  4.0
		 *
		 * @param  array $args
		 *
		 * @return  array Pairs of file path with capitalized file name with no file extension.
		 */
		if ( ! function_exists( 'wm_get_files' ) ) {
			function wm_get_files( $args = array() ) {
				//Helper variables
					$output = array();

					//Parse arguments
						$args = wp_parse_args( $args, array(
								'empty_option'   => true,
								'file_extension' => 'json',
								'folders'        => array(),
							) );
						$args['folders'] = array_unique( $args['folders'] );

						$args = apply_filters( 'wmhook_wm_get_files_args', $args );

					//File name chars replacements
						$replacements = apply_filters( 'wmhook_wm_get_files_replacements', array(
								'.' . $args['file_extension'] => '',
								'-'                           => ' ',
								'_'                           => ' ',
							), $args );

				//Requirements check
					if ( empty( $args['folders'] ) ) {
						return;
					}

				//Preparing output
					if ( $args['empty_option'] ) {
						$output[''] = ( is_string( $args['empty_option'] ) ) ? ( $args['empty_option'] ) : ( '' );
					}

					foreach ( $args['folders'] as $folder ) {
						$folder = trim( $folder );
						if ( $folder && $dir = @opendir( $folder ) ) {
							//This is the correct way to loop over the directory
								while ( false != ( $file = readdir( $dir ) ) ) {
									if ( strpos( $file, $args['file_extension'] ) ) {
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
	 * CSS functions
	 */

		/**
		 * CSS escaping
		 *
		 * Use this for custom CSS output only!
		 * Uses `esc_attr()` while keeping quote marks.
		 *
		 * @uses  esc_attr()
		 *
		 * @since    4.0
		 * @version  4.0
		 *
		 * @param  string $css Code to escape
		 */
		if ( ! function_exists( 'wm_esc_css' ) ) {
			function wm_esc_css( $css ) {
				return apply_filters( 'wmhook_wm_esc_css', str_replace( array( '&gt;', '&quot;', '&#039;' ), array( '>', '"', '\'' ), esc_attr( (string) $css ) ), $css );
			}
		} // /wm_esc_css



		/**
		 * Outputs path to the specific file
		 *
		 * This function looks for the file in the child theme first.
		 * If the file is not located in child theme, output the path from parent theme.
		 *
		 * @since    3.1
		 * @version  3.1
		 *
		 * @param  string $file_relative_path File to look for (insert also the relative path inside the theme)
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
						return apply_filters( 'wmhook_wm_get_stylesheet_directory_output', esc_url( $output ), $file_relative_path );
					}

				//Praparing output
					if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $file_relative_path ) ) {
						$output = trailingslashit( get_stylesheet_directory() ) . $file_relative_path;
					} else {
						$output = trailingslashit( get_template_directory() ) . $file_relative_path;
					}

				//Output
					return apply_filters( 'wmhook_wm_get_stylesheet_directory_output', $output, $file_relative_path );
			}
		} // /wm_get_stylesheet_directory



		/**
		 * Outputs URL to the specific file
		 *
		 * This function looks for the file in the child theme first.
		 * If the file is not located in child theme, output the URL from parent theme.
		 *
		 * @param  string $file_relative_path File to look for (insert also the relative path inside the theme)
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
						return apply_filters( 'wmhook_wm_get_stylesheet_directory_uri_output', esc_url( $output ), $file_relative_path );
					}

				//Praparing output
					if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $file_relative_path ) ) {
						$output = trailingslashit( get_stylesheet_directory_uri() ) . $file_relative_path;
					} else {
						$output = trailingslashit( get_template_directory_uri() ) . $file_relative_path;
					}

				//Output
					return apply_filters( 'wmhook_wm_get_stylesheet_directory_uri_output', esc_url( $output ), $file_relative_path );
			}
		} // /wm_get_stylesheet_directory_uri



		/**
		 * CSS minifier
		 *
		 * @since    3.0
		 * @version  4.0
		 *
		 * @param  string $css Code to minimize
		 */
		if ( ! function_exists( 'wm_minify_css' ) ) {
			function wm_minify_css( $css ) {
				//Requirements check
					if (
							! is_string( $css )
							&& ! apply_filters( 'wmhook_wm_minify_css_disable', false )
						) {
						return $css;
					}

				//Praparing output
					$css = apply_filters( 'wmhook_wm_minify_css_pre', $css );

					//Remove CSS comments
						$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
					//Remove tabs, spaces, line breaks, etc.
						$css = str_replace( array( "\r\n", "\r", "\n", "\t", '//', '  ', '   ' ), '', $css );
						$css = str_replace( array( ' { ', ': ', '; }' ), array( '{', ':', '}' ), $css );

				//Output
					return apply_filters( 'wmhook_wm_minify_css_output', $css );
			}
		} // /wm_minify_css



		/**
		 * Generate main CSS file
		 *
		 * @since    3.0
		 * @version  4.0
		 *
		 * @param  boolean $args
		 */
		if ( ! function_exists( 'wm_generate_main_css' ) ) {
			function wm_generate_main_css( $args = array() ) {
				//Requirements check
					if ( ! function_exists( 'wma_amplifier' ) ) {
						return false;
					}

				//Helper viariables
					$args = wp_parse_args( $args, apply_filters( 'wmhook_wm_generate_main_css_args', array(
							'message'        => _x( "The main theme CSS stylesheet was regenerated.<br /><strong>Please refresh your web browser's and server's cache</strong> <em>(if you are using a website server caching solution)</em>.", 'Translators, please, keep the HTML tags.', 'wm_domain' ),
							'message_after'  => '',
							'message_before' => '',
							'type'           => '',
						) ) );

					$output = $output_min = '';

					$args['type'] = trim( $args['type'] );

				//Preparing output
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
							return false;
						}

					//Minify output if set
						$output_min = apply_filters( 'wmhook_wm_generate_main_css_output_min', $output, $args );

				//Output
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

					$css_file_name       = apply_filters( 'wmhook_wm_generate_main_css_css_file_name',       'global' . $args['type'],                                        $args                 );
					$global_css_path     = apply_filters( 'wmhook_wm_generate_main_css_global_css_path',     trailingslashit( $theme_css_dir ) . $css_file_name . '.css',     $args, $css_file_name );
					$global_css_url      = apply_filters( 'wmhook_wm_generate_main_css_global_css_url',      trailingslashit( $theme_css_url ) . $css_file_name . '.css',     $args, $css_file_name );
					$global_css_path_dev = apply_filters( 'wmhook_wm_generate_main_css_global_css_path_dev', trailingslashit( $theme_css_dir ) . $css_file_name . '.dev.css', $args, $css_file_name );

					if ( $output ) {
						wma_write_local_file( $global_css_path, $output_min );
						wma_write_local_file( $global_css_path_dev, $output );

						//Store the CSS files paths and urls in DB
							update_option( 'wm-' . WM_THEME_SHORTNAME . $args['type'] . '-css', $global_css_url );
							update_option( 'wm-' . WM_THEME_SHORTNAME . $args['type'] . '-files', str_replace( $wp_upload_dir['basedir'], '', $theme_css_dir ) );

						//Admin notice
							set_transient( 'wmamp-admin-notice', array( $args['message_before'] . $args['message'] . $args['message_after'], '', 'switch_themes' ), ( 60 * 60 * 24 ) );

						//Run custom actions
							do_action( 'wmhook_wm_generate_main_css', $args );

						return true;
					}

					delete_option( 'wm-' . WM_THEME_SHORTNAME . $args['type'] . '-css' );
					delete_option( 'wm-' . WM_THEME_SHORTNAME . $args['type'] . '-files' );

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



	/**
	 * Get image ID from its URL
	 *
	 * @since    3.0
	 * @version  4.0
	 *
	 * @link  http://pippinsplugins.com/retrieve-attachment-id-from-image-url/
	 * @link  http://make.wordpress.org/core/2012/12/12/php-warning-missing-argument-2-for-wpdb-prepare/
	 *
	 * @param  string $url
	 */
	if ( ! function_exists( 'wm_get_image_id_from_url' ) ) {
		function wm_get_image_id_from_url( $url ) {
			//Helper variables
				global $wpdb;

				$output = null;

				$cache = array_filter( (array) get_transient( 'wm-image-ids' ) );

			//Returne cached result if found and relevant
				if (
						! empty( $cache )
						&& isset( $cache[ $url ] )
						&& wp_get_attachment_url( absint( $cache[ $url ] ) )
						&& $url == wp_get_attachment_url( absint( $cache[ $url ] ) )
					) {
					return absint( apply_filters( 'wmhook_wm_get_image_id_from_url_output', $cache[ $url ] ) );
				}

			//Preparing output
				if (
						is_object( $wpdb )
						&& isset( $wpdb->prefix )
					) {
					$prefix     = $wpdb->prefix;
					$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM " . $prefix . "posts" . " WHERE guid = %s", esc_url( $url ) ) );
					$output     = ( isset( $attachment[0] ) ) ? ( $attachment[0] ) : ( null );
				}

				//Cache the new record
					$cache[ $url ] = $output;
					set_transient( 'wm-image-ids', array_filter( (array) $cache ) );

			//Output
				return absint( apply_filters( 'wmhook_wm_get_image_id_from_url_output', $output ) );
		}
	} // /wm_get_image_id_from_url



		/**
		 * Flush out the transients used in wm_get_image_id_from_url
		 *
		 * @since    4.0
		 * @version  4.0
		 */
		if ( ! function_exists( 'wm_image_ids_transient_flusher' ) ) {
			function wm_image_ids_transient_flusher() {
				delete_transient( 'wm-image-ids' );
			}
		} // /wm_image_ids_transient_flusher



	/**
	 * Returns true if a blog has more than 1 category
	 *
	 * @since    4.0
	 * @version  4.0
	 */
	if ( ! function_exists( 'wm_is_categorized_blog' ) ) {
		function wm_is_categorized_blog() {
			//Preparing output
				if ( false === ( $all_the_cool_cats = get_transient( 'wm-all-categories' ) ) ) {

					//Create an array of all the categories that are attached to posts
						$all_the_cool_cats = get_categories( array(
								'fields'     => 'ids',
								'hide_empty' => 1,
								'number'     => 2, //we only need to know if there is more than one category
							) );

					//Count the number of categories that are attached to the posts
						$all_the_cool_cats = count( $all_the_cool_cats );

					set_transient( 'wm-all-categories', $all_the_cool_cats );

				}

			//Output
				if ( $all_the_cool_cats > 1 ) {
					//This blog has more than 1 category
						return true;
				} else {
					//This blog has only 1 category
						return false;
				}
		}
	} // /wm_is_categorized_blog



		/**
		 * Flush out the transients used in wm_is_categorized_blog
		 *
		 * @since    4.0
		 * @version  4.0
		 */
		if ( ! function_exists( 'wm_all_categories_transient_flusher' ) ) {
			function wm_all_categories_transient_flusher() {
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
					return;
				}
				//Like, beat it. Dig?
				delete_transient( 'wm-all-categories' );
			}
		} // /wm_all_categories_transient_flusher



	/**
	 * Modify home query
	 *
	 * @since    3.0
	 * @version  4.0
	 *
	 * @param  object $query WordPress posts query
	 */
	if ( ! function_exists( 'wm_home_query' ) ) {
		function wm_home_query( $query ) {
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

					//Change articles count
						if ( $article_count ) {
							$query->set( 'posts_per_page', absint( $article_count ) );
						}

					//Filter output by catagory
						if ( 0 < count( $cats ) ) {
							$query->set( $cats_action, $cats );
						}

					//Ignore sticky posts
						$query->set( 'ignore_sticky_posts', 1 );
			}
		}
	} // /wm_home_query



	/**
	 * Adds a Theme Options links to WordPress admin bar
	 *
	 * @since    3.0
	 * @version  4.0
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
						'title' => _x( 'Theme Options', 'WordPress admin bar theme options links group name.', 'wm_domain' ),
						'href'  => admin_url( 'customize.php' )
					) ) );

				//Submenu items
					if ( is_array( $submenu ) && ! empty( $submenu ) ) {
						foreach ( $submenu as $title => $url ) {
							$wp_admin_bar->add_menu( apply_filters( 'wmhook_wm_theme_options_admin_bar_child-' . sanitize_title( $title ), array(
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
	 * Shim for `the_archive_title()`.
	 *
	 * Display the archive title based on the queried object.
	 *
	 * @todo Remove this function when WordPress 4.3 is released.
	 *
	 * @since    4.0
	 * @version  4.0
	 *
	 * @param  string $before Optional. Content to prepend to the title. Default empty.
	 * @param  string $after  Optional. Content to append to the title. Default empty.
	 */
	if ( ! function_exists( 'the_archive_title' ) ) {
		function the_archive_title( $before = '', $after = '' ) {
			if ( is_category() ) {
				$title = sprintf( __( 'Category: %s', 'wm_domain' ), single_cat_title( '', false ) );
			} elseif ( is_tag() ) {
				$title = sprintf( __( 'Tag: %s', 'wm_domain' ), single_tag_title( '', false ) );
			} elseif ( is_author() ) {
				$title = sprintf( __( 'Author: %s', 'wm_domain' ), '<span class="vcard">' . get_the_author() . '</span>' );
			} elseif ( is_year() ) {
				$title = sprintf( __( 'Year: %s', 'wm_domain' ), get_the_date( 'Y' ) );
			} elseif ( is_month() ) {
				$title = sprintf( __( 'Month: %s', 'wm_domain' ), get_the_date( 'F Y' ) );
			} elseif ( is_day() ) {
				$title = sprintf( __( 'Day: %s', 'wm_domain' ), get_the_date() );
			} elseif ( is_tax( 'post_format' ) ) {
				if ( is_tax( 'post_format', 'post-format-aside' ) ) {
					$title = _x( 'Asides', 'post format archive title', 'wm_domain' );
				} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
					$title = _x( 'Galleries', 'post format archive title', 'wm_domain' );
				} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
					$title = _x( 'Images', 'post format archive title', 'wm_domain' );
				} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
					$title = _x( 'Videos', 'post format archive title', 'wm_domain' );
				} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
					$title = _x( 'Quotes', 'post format archive title', 'wm_domain' );
				} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
					$title = _x( 'Links', 'post format archive title', 'wm_domain' );
				} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
					$title = _x( 'Statuses', 'post format archive title', 'wm_domain' );
				} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
					$title = _x( 'Audio', 'post format archive title', 'wm_domain' );
				} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
					$title = _x( 'Chats', 'post format archive title', 'wm_domain' );
				}
			} elseif ( is_post_type_archive() ) {
				$title = sprintf( __( 'Archives: %s', 'wm_domain' ), post_type_archive_title( '', false ) );
			} elseif ( is_tax() ) {
				$tax = get_taxonomy( get_queried_object()->taxonomy );
				/* translators: 1: Taxonomy singular name, 2: Current taxonomy term */
				$title = sprintf( __( '%1$s: %2$s', 'wm_domain' ), $tax->labels->singular_name, single_term_title( '', false ) );
			} else {
				$title = __( 'Archives', 'wm_domain' );
			}

			/**
			 * Filter the archive title.
			 *
			 * @param string $title Archive title to be displayed.
			 */
			$title = apply_filters( 'get_the_archive_title', $title );

			if ( ! empty( $title ) ) {
				echo $before . $title . $after;
			}
		}
	} // /the_archive_title



	/**
	 * Shim for `the_archive_description()`.
	 *
	 * Display category, tag, or term description.
	 *
	 * @todo Remove this function when WordPress 4.3 is released.
	 *
	 * @since    4.0
	 * @version  4.0
	 *
	 * @param  string $before Optional. Content to prepend to the description. Default empty.
	 * @param  string $after  Optional. Content to append to the description. Default empty.
	 */
	if ( ! function_exists( 'the_archive_description' ) ) {
		function the_archive_description( $before = '', $after = '' ) {
			$description = apply_filters( 'get_the_archive_description', term_description() );

			if ( ! empty( $description ) ) {
				/**
				 * Filter the archive description.
				 *
				 * @see term_description()
				 *
				 * @param string $description Archive description to be displayed.
				 */
				echo $before . $description . $after;
			}
		}
	} // /the_archive_description

?>