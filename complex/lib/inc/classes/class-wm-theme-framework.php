<?php
/**
 * Core class
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Core
 *
 * @since    5.0
 * @version  5.0
 */





if ( ! class_exists( 'WM_Theme_Framework' ) ) {
	final class WM_Theme_Framework {

		/**
		 * Contents:
		 *
		 *   0) Theme upgrade action
		 *  10) Branding
		 *  20) Post/page
		 *  30) CSS functions
		 *  40) Options
		 * 100) Helpers
		 */





		/**
		 * 0) Theme upgrade action
		 */

			/**
			 * Do action on theme version change
			 *
			 * @since    4.0
			 * @version  5.0
			 */
			static public function theme_upgrade() {

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

			} // /theme_upgrade





		/**
		 * 10) Branding
		 */

			/**
			 * Get the logo
			 *
			 * Supports Jetpack Site Logo module.
			 *
			 * @since    3.0
			 * @version  5.0
			 */
			static public function get_the_logo() {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_get_the_logo_pre', false );

					if ( false !== $pre ) {
						echo $pre;
						return;
					}


				//Helper variables

					$output = '';

					$blog_info = apply_filters( 'wmhook_wmtf_get_the_logo_blog_info', array(
							'name'        => trim( get_bloginfo( 'name' ) ),
							'description' => trim( get_bloginfo( 'description' ) ),
						) );

					$args = apply_filters( 'wmhook_wmtf_get_the_logo_args', array(
							'logo_image' => ( function_exists( 'jetpack_get_site_logo' ) ) ? ( array( absint( jetpack_get_site_logo( 'id' ) ) ) ) : ( array( self::get_theme_mod( 'logo' ), self::get_theme_mod( 'logo-hidpi' ) ) ),
							'logo_type'  => 'text',
							'title_att'  => ( $blog_info['description'] ) ? ( $blog_info['name'] . ' | ' . $blog_info['description'] ) : ( $blog_info['name'] ),
							'url'        => home_url( '/' ),
						) );


				//Processing

					//Logo image

						if ( $args['logo_image'][0] ) {

							$img_id = ( is_numeric( $args['logo_image'][0] ) ) ? ( absint( $args['logo_image'][0] ) ) : ( self::get_image_id_from_url( $args['logo_image'][0] ) );

							//High resolution support

								if ( isset( $args['logo_image'][1] ) && is_numeric( $args['logo_image'][1] ) ) {
									$img_id_hidpi = absint( $args['logo_image'][1] );
								} elseif ( isset( $args['logo_image'][1] ) ) {
									$img_id_hidpi = self::get_image_id_from_url( $args['logo_image'][1] );
								} else {
									$img_id_hidpi = false;
								}

							if ( $img_id ) {

								$logo_url = wp_get_attachment_image_src( $img_id, 'full' );

								$atts = array(
										'alt'   => esc_attr( sprintf( _x( '%s logo', 'Site logo image "alt" HTML attribute text.', 'wmtf_domain' ), $blog_info['name'] ) ),
										'title' => esc_attr( $args['title_att'] ),
										'class' => '',
									);

								if ( $img_id_hidpi ) {
									$logo_url_hidpi     = wp_get_attachment_image_src( $img_id_hidpi, 'full' );
									$atts['data-hidpi'] = $logo_url_hidpi[0];
								}

								$atts = (array) apply_filters( 'wmhook_wmtf_get_the_logo_image_atts', $atts, $img_id, $img_id_hidpi );

								$args['logo_image'] = wp_get_attachment_image( absint( $img_id ), 'full', false, $atts );

							}

							$args['logo_type'] = 'img';

						}

						$args['logo_image'] = apply_filters( 'wmhook_wmtf_get_the_logo_image', $args['logo_image'] );

					//Logo HTML

						$output .= '<div class="site-branding">';
							$output .= '<h1 class="' . esc_attr( apply_filters( 'wmhook_wmtf_get_the_logo_class', 'site-title logo type-' . $args['logo_type'], $args ) ) . '">';
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

					echo $output;

			} // /get_the_logo



				/**
				 * Display the logo
				 *
				 * @since    5.0
				 * @version  5.0
				 */
				static public function the_logo() {

					//Helper variables

						$output = self::get_the_logo();


					//Output

						if ( $output ) {
							echo $output;
						}

				} // /the_logo





		/**
		 * 20) Post/page
		 */

			/**
			 * Add table of contents generated from <!--nextpage--> tag
			 *
			 * Will create a table of content in multipage post from
			 * the first H2 heading in each post part.
			 * Appends the output at the top and bottom of post content.
			 *
			 * @since    3.0
			 * @version  5.0
			 *
			 * @param  string $content
			 */
			static public function add_table_of_contents( $content = '' ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_add_table_of_contents_pre', false, $content );

					if ( false !== $pre ) {
						return $pre;
					}


				//Helper variables

					global $page, $numpages, $multipage, $post;

					//Requirements check

						if (
								! $multipage
								|| ! is_singular()
							) {
							return $content;
						}

					//translators: %s will be replaced with parted post title. Copy it, do not translate.
					$title_text = apply_filters( 'wmhook_wmtf_add_table_of_contents_title_text', sprintf( _x( '"%s" table of contents', 'Parted/paginated post table of content title. %s = post title.', 'wm_domain' ), the_title_attribute( 'echo=0' ) ) );
					$title      = apply_filters( 'wmhook_wmtf_add_table_of_contents_title', '<h2 class="screen-reader-text">' . $title_text . '</h2>' );

					$args = apply_filters( 'wmhook_wmtf_add_table_of_contents_args', array(
							'disable_first' => true, //First part to have a title of the post (part title won't be parsed)?
							'links'         => array(), //The output HTML links
							'post_content'  => ( isset( $post->post_content ) ) ? ( $post->post_content ) : ( '' ), //Get the whole post content
							'tag'           => 'h2', //HTML heading tag to parse as a post part title
						) );

					//Post part counter

						$i = 0;


				//Processing

					$args['post_content'] = explode( '<!--nextpage-->', (string) $args['post_content'] );

					//Get post parts titles

						foreach ( $args['post_content'] as $part ) {

							//Current post part number

								$i++;

							//Get the title for post part

								if ( $args['disable_first'] && 1 === $i ) {

									$part_title = the_title_attribute( 'echo=0' );

								} else {

									preg_match( '/<' . tag_escape( $args['tag'] ) . '(.*?)>(.*?)<\/' . tag_escape( $args['tag'] ) . '>/', $part, $matches );

									if ( ! isset( $matches[2] ) || ! $matches[2] ) {
										$part_title = sprintf( __( 'Page %d', 'wm_domain' ), $i );
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

								$args['links'][$i] = (string) apply_filters( 'wmhook_wmtf_add_table_of_contents_part', '<li' . $class . '>' . _wp_link_page( $i ) . $part_title . '</a></li>', $i, $part_title, $class, $args );

						} // /foreach

					//Add table of contents into the post/page content

						$args['links'] = implode( '', $args['links'] );

						$links = apply_filters( 'wmhook_wmtf_add_table_of_contents_links', array(
								//Display table of contents before the post content only in first post part
									'before' => ( 1 === $page ) ? ( '<div class="post-table-of-contents top" title="' . esc_attr( strip_tags( $title_text ) ) . '">' . $title . '<ol>' . $args['links'] . '</ol></div>' ) : ( '' ),
								//Display table of cotnnets after the post cotnent on each post part
									'after'  => '<div class="post-table-of-contents bottom" title="' . esc_attr( strip_tags( $title_text ) ) . '">' . $title . '<ol>' . $args['links'] . '</ol></div>',
							), $args );

						$content = $links['before'] . $content . $links['after'];

				//Output

					return $content;

			} // /add_table_of_contents



			/**
			 * Get the post meta info
			 *
			 * hAtom microformats compatible. @link http://goo.gl/LHi4Dy
			 * Supports ZillaLikes plugin. @link http://www.themezilla.com/plugins/zillalikes/
			 * Supports Post Views Count plugin. @link https://wordpress.org/plugins/baw-post-views-count/
			 *
			 * @since    3.0
			 * @version  5.0
			 *
			 * @param  array $args
			 */
			static public function get_the_post_meta_info( $args = array() ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_get_the_post_meta_info_pre', false, $args );

					if ( false !== $pre ) {
						return $pre;
					}


				//Helper variables

					$output = '';

					$args = wp_parse_args( $args, apply_filters( 'wmhook_wmtf_get_the_post_meta_info_defaults', array(
							'class'       => 'entry-meta clearfix',
							'date_format' => null,
							'html'        => '<span class="{class}"{attributes}>{content}</span>',
							'html_custom' => array(
									'date' => '<time datetime="{datetime}" class="{class}"{attributes}>{content}</time>',
								),
							'meta'        => array(), //Example: array( 'date', 'author', 'category', 'comments', 'permalink' )
							'post_id'     => null,
						) ) );
					$args = apply_filters( 'wmhook_wmtf_get_the_post_meta_info_args', $args );

					$args['meta'] = array_filter( (array) $args['meta'] );

					if ( $args['post_id'] ) {
						$args['post_id'] = absint( $args['post_id'] );
					}


				//Requirements check

					if ( empty( $args['meta'] ) ) {
						return;
					}


				//Processing

					foreach ( $args['meta'] as $meta ) {

							$helper = '';

							$replacements  = (array) apply_filters( 'wmhook_wmtf_get_the_post_meta_info_replacements', array(), $meta, $args );
							$output_single = apply_filters( 'wmhook_wmtf_get_the_post_meta_info', '', $meta, $args );
							$output       .= $output_single;

						//Predefined metas

							switch ( $meta ) {

								case 'author':

									if ( apply_filters( 'wmhook_wmtf_get_the_post_meta_info_enable_' . $meta, true, $args ) ) {
										$helper = ( function_exists( 'wmtf_schema_org' ) ) ? ( wmtf_schema_org( 'author' ) ) : ( '' );

										$replacements = array(
												'{attributes}' => ( function_exists( 'wmtf_schema_org' ) ) ? ( wmtf_schema_org( 'Person' ) ) : ( '' ),
												'{class}'      => esc_attr( 'author vcard entry-meta-element' ),
												'{content}'    => '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" class="url fn n" rel="author"' . $helper . '>' . get_the_author() . '</a>',
											);
									}

								break;
								case 'category':

									if (
											apply_filters( 'wmhook_wmtf_get_the_post_meta_info_enable_' . $meta, true, $args )
											&& self::is_categorized_blog()
											&& ( $helper = get_the_category_list( ', ', '', $args['post_id'] ) )
										) {
										$replacements = array(
												'{attributes}' => '',
												'{class}'      => esc_attr( 'cat-links entry-meta-element' ),
												'{content}'    => $helper,
											);
									}

								break;
								case 'comments':

									if (
											apply_filters( 'wmhook_wmtf_get_the_post_meta_info_enable_' . $meta, true, $args )
											&& ! post_password_required()
											&& (
												comments_open( $args['post_id'] )
												|| get_comments_number( $args['post_id'] )
											)
										) {
										$helper       = get_comments_number( $args['post_id'] );
										$element_id   = ( $helper ) ? ( '#comments' ) : ( '#respond' );
										$replacements = array(
												'{attributes}' => '',
												'{class}'      => esc_attr( 'comments-link entry-meta-element' ),
												'{content}'    => '<a href="' . esc_url( get_permalink( $args['post_id'] ) ) . $element_id . '" title="' . esc_attr( sprintf( _x( 'Comments: %s', 'Number of comments in post meta.', 'wm_domain' ), $helper ) ) . '">' . sprintf( _x( '<span class="comments-title">Comments: </span>%s', 'Number of comments in post meta (keep the HTML tags).', 'wm_domain' ), '<span class="comments-count">' . $helper . '</span>' ) . '</a>',
											);
									}

								break;
								case 'date':

									if ( apply_filters( 'wmhook_wmtf_get_the_post_meta_info_enable_' . $meta, true, $args ) ) {
										$helper = ( function_exists( 'wmtf_schema_org' ) ) ? ( wmtf_schema_org( 'datePublished' ) ) : ( '' );

										$replacements = array(
												'{attributes}' => ' title="' . esc_attr( get_the_date() ) . ' | ' . esc_attr( get_the_time( '', $args['post_id'] ) ) . '"' . $helper,
												'{class}'      => esc_attr( 'entry-date entry-meta-element published' ),
												'{content}'    => esc_html( get_the_date( $args['date_format'] ) ),
												'{datetime}'   => esc_attr( get_the_date( 'c' ) ),
											);
									}

								break;
								case 'edit':

									if (
											apply_filters( 'wmhook_wmtf_get_the_post_meta_info_enable_' . $meta, true, $args )
											&& ( $helper = get_edit_post_link( $args['post_id'] ) )
										) {
										$the_title_attribute_args = array( 'echo' => false );
										if ( $args['post_id'] ) {
											$the_title_attribute_args['post'] = $args['post_id'];
										}

										$replacements = array(
												'{attributes}' => '',
												'{class}'      => esc_attr( 'entry-edit entry-meta-element' ),
												'{content}'    => '<a href="' . esc_url( $helper ) . '" title="' . esc_attr( sprintf( __( 'Edit the "%s"', 'wm_domain' ), the_title_attribute( $the_title_attribute_args ) ) ) . '"><span>' . _x( 'Edit', 'Edit post link.', 'wm_domain' ) . '</span></a>',
											);
									}

								break;
								case 'likes':

									if (
											apply_filters( 'wmhook_wmtf_get_the_post_meta_info_enable_' . $meta, true, $args )
											&& function_exists( 'zilla_likes' )
										) {
										global $zilla_likes;
										$helper = $zilla_likes->do_likes();

										$replacements = array(
												'{attributes}' => '',
												'{class}'      => esc_attr( 'entry-likes entry-meta-element' ),
												'{content}'    => $helper,
											);
									}

								break;
								case 'permalink':

									if ( apply_filters( 'wmhook_wmtf_get_the_post_meta_info_enable_' . $meta, true, $args ) ) {
										$the_title_attribute_args = array( 'echo' => false );
										if ( $args['post_id'] ) {
											$the_title_attribute_args['post'] = $args['post_id'];
										}

										$replacements = array(
												'{attributes}' => ( function_exists( 'wmtf_schema_org' ) ) ? ( wmtf_schema_org( 'url' ) ) : ( '' ),
												'{class}'      => esc_attr( 'entry-permalink entry-meta-element' ),
												'{content}'    => '<a href="' . esc_url( get_permalink( $args['post_id'] ) ) . '" title="' . esc_attr( sprintf( __( 'Permalink to "%s"', 'wm_domain' ), the_title_attribute( $the_title_attribute_args ) ) ) . '" rel="bookmark"><span>' . get_the_title( $args['post_id'] ) . '</span></a>',
											);
									}

								break;
								case 'tags':

									if (
											apply_filters( 'wmhook_wmtf_get_the_post_meta_info_enable_' . $meta, true, $args )
											&& ( $helper = get_the_tag_list( '', ' ', '', $args['post_id'] ) )
										) {
										$replacements = array(
												'{attributes}' => ( function_exists( 'wmtf_schema_org' ) ) ? ( wmtf_schema_org( 'keywords' ) ) : ( '' ),
												'{class}'      => esc_attr( 'tags-links entry-meta-element' ),
												'{content}'    => $helper,
											);
									}

								break;
								case 'views':

									if (
											apply_filters( 'wmhook_wmtf_get_the_post_meta_info_enable_' . $meta, true, $args )
											&& function_exists( 'bawpvc_views_sc' )
											&& ( $helper = bawpvc_views_sc( array() ) )
										) {
										$replacements = array(
												'{attributes}' => ' title="' . __( 'Views count', 'wm_domain' ) . '"',
												'{class}'      => esc_attr( 'entry-views entry-meta-element' ),
												'{content}'    => $helper,
											);
									}

								break;

								default:
								break;

							} // /switch

							//Single meta output

								$replacements = (array) apply_filters( 'wmhook_wmtf_get_the_post_meta_info_replacements_' . $meta, $replacements, $args );

								if (
										empty( $output_single )
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

					return $output;

			} // /get_the_post_meta_info



				/**
				 * Display the post meta info
				 *
				 * @since    5.0
				 * @version  5.0
				 *
				 * @param  array $args
				 */
				static public function the_post_meta_info( $args = array() ) {

					//Helper variables

						$output = self::get_the_post_meta_info( $args );


					//Output

						if ( $output ) {
							echo $output;
						}

				} // /the_post_meta_info



			/**
			 * Get the paginated heading suffix
			 *
			 * @since    3.0
			 * @version  5.0
			 *
			 * @param  string $tag           Wrapper tag
			 * @param  string $singular_only Display only on singular posts of specific type
			 */
			static public function get_the_paginated_suffix( $tag = '', $singular_only = false ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_get_the_paginated_suffix_pre', false, $tag, $singular_only );

					if ( false !== $pre ) {
						return $pre;
					}


				//Requirements check

					if (
							$singular_only
							&& ! is_singular( $singular_only )
						) {
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
						$tag = array( '<' . tag_escape( $tag ) . '>', '</' . tag_escape( $tag ) . '>' );
					} else {
						$tag = array( '', '' );
					}


				//Processing

					if ( 1 < $paged ) {
						$output = ' ' . $tag[0] . sprintf( _x( '(page %s)', 'Paginated content title suffix.', 'wm_domain' ), $paged ) . $tag[1];
					}


				//Output

					return $output;

			} // /paginated_suffix



				/**
				 * Display the paginated heading suffix
				 *
				 * @since    5.0
				 * @version  5.0
				 *
				 * @param  string $tag           Wrapper tag
				 * @param  string $singular_only Display only on singular posts of specific type
				 */
				static public function the_paginated_suffix( $tag = '', $singular_only = false ) {

					//Helper variables

						$output = self::get_the_paginated_suffix( $tag, $singular_only );


					//Output

						if ( $output ) {
							echo $output;
						}

				} // /the_paginated_suffix



			/**
			 * Checks for <!--more--> tag in post content
			 *
			 * @since    4.0
			 * @version  5.0
			 *
			 * @param  mixed $post
			 */
			static public function has_more_tag( $post = null ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_has_more_tag_pre', false, $post );

					if ( false !== $pre ) {
						return $pre;
					}


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

			} // /has_more_tag





		/**
		 * 30) CSS functions
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
			 * @version  5.0
			 *
			 * @param  string $css Code to escape
			 */
			static public function esc_css( $css ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_esc_css_pre', false, $css );

					if ( false !== $pre ) {
						return $pre;
					}


				//Output

					return str_replace( array( '&gt;', '&quot;', '&#039;' ), array( '>', '"', '\'' ), esc_attr( (string) $css ) );

			} // /esc_css



			/**
			 * Outputs path to the specific file
			 *
			 * This function looks for the file in the child theme first.
			 * If the file is not located in child theme, outputs the path from parent theme.
			 *
			 * @since    3.1
			 * @version  5.0
			 *
			 * @param  string $file_relative_path File to look for (insert also the theme structure relative path)
			 *
			 * @return  string Actual path to the file
			 */
			static public function get_stylesheet_directory( $file_relative_path ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_get_stylesheet_directory_pre', false, $file_relative_path );

					if ( false !== $pre ) {
						return $pre;
					}


				//Helper variables

					$output = '';

					$file_relative_path = trim( $file_relative_path );


				//Requirements chek

					if ( ! $file_relative_path ) {
						return;
					}


				//Processing

					if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $file_relative_path ) ) {
						$output = trailingslashit( get_stylesheet_directory() ) . $file_relative_path;
					} else {
						$output = trailingslashit( get_template_directory() ) . $file_relative_path;
					}


				//Output

					return $output;

			} // /get_stylesheet_directory



			/**
			 * Outputs URL to the specific file
			 *
			 * This function looks for the file in the child theme first.
			 * If the file is not located in child theme, output the URL from parent theme.
			 *
			 * @since    3.0
			 * @version  5.0
			 *
			 * @param  string $file_relative_path File to look for (insert also the theme structure relative path)
			 *
			 * @return  string Actual URL to the file
			 */
			static public function get_stylesheet_directory_uri( $file_relative_path ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_get_stylesheet_directory_uri_pre', false, $file_relative_path );

					if ( false !== $pre ) {
						return $pre;
					}


				//Helper variables

					$output = '';

					$file_relative_path = trim( $file_relative_path );


				//Requirements chek

					if ( ! $file_relative_path ) {
						return;
					}


				//Processing

					if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $file_relative_path ) ) {
						$output = trailingslashit( get_stylesheet_directory_uri() ) . $file_relative_path;
					} else {
						$output = trailingslashit( get_template_directory_uri() ) . $file_relative_path;
					}


				//Output

					return $output;

			} // /get_stylesheet_directory_uri



			/**
			 * CSS minifier
			 *
			 * @since    3.0
			 * @version  5.0
			 *
			 * @param  string $css Code to minimize
			 */
			static public function minify_css( $css ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_minify_css_pre', false, $css );

					if ( false !== $pre ) {
						return $pre;
					}


				//Requirements check

					if ( ! is_string( $css ) ) {
						return $css;
					}


				//Processing

					//Remove CSS comments

						$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );

					//Remove tabs, spaces, line breaks, etc.

						$css = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $css );
						$css = str_replace( array( '  ', '   ', '    ', '     ' ), ' ', $css );
						$css = str_replace( array( ' { ', ': ', '; }' ), array( '{', ':', '}' ), $css );


				//Output

					return $css;

			} // /minify_css



			/**
			 * Hex color to RGBA
			 *
			 * @since    4.0
			 * @version  5.0
			 *
			 * @link  http://php.net/manual/en/function.hexdec.php
			 *
			 * @param  string $hex
			 * @param  absint $alpha [0-100]
			 *
			 * @return  string Color in rgb() or rgba() format to use in CSS.
			 */
			static public function color_hex_to_rgba( $hex, $alpha = 100 ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_color_hex_to_rgba_pre', false, $hex, $alpha );

					if ( false !== $pre ) {
						return $pre;
					}


				//Helper variables

					$alpha = absint( $alpha );

					$output = ( 100 === $alpha ) ? ( 'rgb(' ) : ( 'rgba(' );

					$rgb = array();

					$hex = preg_replace( '/[^0-9A-Fa-f]/', '', $hex );
					$hex = substr( $hex, 0, 6 );


				//Processing

					//Converting hex color into rgb

						$color = (int) hexdec( $hex );

						$rgb['r'] = (int) 0xFF & ( $color >> 0x10 );
						$rgb['g'] = (int) 0xFF & ( $color >> 0x8 );
						$rgb['b'] = (int) 0xFF & $color;

						$output .= implode( ',', $rgb );

					//Using alpha (rgba)?

						if ( 100 > $alpha ) {
							$output .= ',' . ( $alpha / 100 );
						}


				//Output

					return $output;

			} // /color_hex_to_rgba



			/**
			 * Replace variables in the custom CSS
			 *
			 * Just use a '[[customizer-option-id]]' tags in your custom CSS
			 * styles string where the specific option value should be used.
			 *
			 * @since    4.0
			 * @version  5.0
			 *
			 * @param  string $css CSS string with variables to replace.
			 */
			static public function custom_styles_replace( $css ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_custom_styles_replace_pre', false, $css );

					if ( false !== $pre ) {
						return $pre;
					}


				//Requirement check

					if ( ! ( $css = trim( (string) $css ) ) ) {
						return;
					}


				//Helper variables

					$output = $css;

					$theme_options = (array) apply_filters( 'wmhook_theme_options', array() );
					$alphas        = (array) apply_filters( 'wmhook_wmtf_custom_styles_replace_alpha', array( 0 ), $option );

					$replacements = array();


				//Processing

					//Setting up replacements array

						if ( ! empty( $theme_options ) ) {
							foreach ( $theme_options as $option ) {

								//Reset variables

									$option_id = $value = '';

								//Set option ID

									if ( isset( $option['id'] ) ) {
										$option_id = $option['id'];
									}

								//If no option ID set, jump to next option

									if ( empty( $option_id ) ) {
										continue;
									}

								//If we have an ID, get the default value if set

									if ( isset( $option['default'] ) ) {
										$value = $option['default'];
									}

								//Get the option value saved in database and apply it when exists

									if ( $mod = self::get_theme_mod( $option_id ) ) {
										$value = $mod;
									}

								//Make sure the color value contains '#'

									if ( 'color' === $option['type'] ) {
										$value = '#' . trim( $value, '#' );
									}

								//Make sure the image URL is used in CSS format

									if ( 'image' === $option['type'] ) {
										if ( is_array( $value ) && isset( $value['id'] ) ) {
											$value = absint( $value['id'] );
										}
										if ( is_numeric( $value ) ) {
											$value = wp_get_attachment_image_src( $value, 'full' );
											$value = $value[0];
										}
										if ( ! empty( $value ) ) {
											$value = "url('" . esc_url( $value ) . "')";
										} else {
											$value = 'none';
										}
									}

								//Value filtering

									$value = apply_filters( 'wmhook_wmtf_custom_styles_replace_value', $value, $option );

								//Convert array to string as otherwise the strtr() function throws error

									if ( is_array( $value ) ) {
										$value = (string) implode( ',', (array) $value );
									}

								//Finally, modify the output string

									$replacements['[[' . $option_id . ']]'] = $value;

									/**
									 * Add also rgba() color interpratation
									 *
									 * Other alpha values has to be added via custom function
									 * hooked onto the $replacements filter below.
									 */
									if ( 'color' === $option['type'] ) {
										foreach ( $alphas as $alpha ) {
											$replacements['[[' . $option_id . '|alpha=' . absint( $alpha ) . ']]'] = wm_color_hex_to_rgba( $value, absint( $alpha ) );
										} // /foreach
									}

							} // /foreach
						}

						//Add WordPress Custom Background and Header support

							//Background color

								if ( $value = get_background_color() ) {
									$replacements['[[background_color]]'] = '#' . trim( $value, '#' );

									foreach ( $alphas as $alpha ) {
										$replacements['[[background_color|alpha=' . absint( $alpha ) . ']]'] = wm_color_hex_to_rgba( $value, absint( $alpha ) );
									} // /foreach
								}

							//Background image

								if ( $value = esc_url( get_background_image() ) ) {
									$replacements['[[background_image]]'] = "url('" . $value . "')";
								} else {
									$replacements['[[background_image]]'] = 'none';
								}

							//Header text color

								if ( $value = get_header_textcolor() ) {
									$replacements['[[header_textcolor]]'] = '#' . trim( $value, '#' );

									foreach ( $alphas as $alpha ) {
										$replacements['[[header_textcolor|alpha=' . absint( $alpha ) . ']]'] = wm_color_hex_to_rgba( $value, absint( $alpha ) );
									} // /foreach
								}

							//Header image

								if ( $value = esc_url( get_header_image() ) ) {
									$replacements['[[header_image]]'] = "url('" . $value . "')";
								} else {
									$replacements['[[header_image]]'] = 'none';
								}

						$replacements = apply_filters( 'wmhook_wmtf_custom_styles_replace_replacements', $replacements, $theme_options, $output );

					//Replace tags in custom CSS strings with actual values

						$output = strtr( $output, $replacements );


				//Output

					return trim( (string) $output );

			} // /custom_styles_replace





		/**
		 * 40) Options
		 */

			/**
			 * Get the theme option
			 *
			 * Note: Do not use get_theme_mod() as it is not
			 * transferable from "lite" to "pro" themes.
			 *
			 * @since    3.0
			 * @version  5.0
			 *
			 * @param  string $id     Option ID
			 * @param  string $type   Output format
			 * @param  string $suffix
			 *
			 * @return  mixed Option value.
			 */
			static public function get_theme_mod( $id, $type = '', $suffix = '' ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_get_theme_mod_pre', false, $id, $type, $suffix );

					if ( false !== $pre ) {
						return $pre;
					}


				//Requirements check

					$id = trim( $id );

					if ( ! $id ) {
						return;
					}


				//Helper variables

					global $wmtf_theme_options, $wp_customize;

					$output = '';

					if (
							! isset( $wmtf_theme_options )
							|| (
									isset( $wp_customize )
									&& $wp_customize->is_preview()
								)
						) {
						$wmtf_theme_options = null;
					}

					$options = ( $wmtf_theme_options ) ? ( $wmtf_theme_options ) : ( (array) get_option( WM_OPTION_CUSTOMIZER ) );

					$id = WM_OPTION_PREFIX . $id;


				//Processing

					if (
							! isset( $options[ $id ] )
							|| ! $options[ $id ]
						) {
						return;
					}

					//Output formatter

						if ( 'css_image_url' === $type ) {
							$output = "url('" . esc_url( stripslashes( $options[ $id ] ) ) . "')";
						} elseif ( 'color_hex' === $type ) {
							$output = '#' . trim( stripslashes( $options[ $id ] ), '#' );
						} else {
							$output = ( is_array( $options[ $id ] ) ) ? ( $options[ $id ] ) : ( stripslashes( $options[ $id ] ) );
						}

					//Add suffix

						if ( is_string( $output ) && $output ) {
							$output .= (string) $suffix;
						}


				//Output

					return apply_filters( 'wmhook_wmtf_get_theme_mod_output', $output, $id, $type, $suffix );

			} // /get_theme_mod



			/**
			 * Adds a Theme Options links to WordPress toolbar (admin bar)
			 *
			 * @since    3.0
			 * @version  5.0
			 */
			static public function toolbar() {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_toolbar_pre', false );

					if ( false !== $pre ) {
						return $pre;
					}


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

					$submenu = apply_filters( 'wmhook_wmtf_toolbar_submenu', array() );


				//Processing

					$wp_admin_bar->add_menu( apply_filters( 'wmhook_wmtf_toolbar_parent', array(
							'id'    => 'theme_options_links',
							'title' => _x( 'Theme Options', 'WordPress toolbar (admin bar) theme options links group name.', 'wm_domain' ),
							'href'  => esc_url( admin_url( 'customize.php' ) )
						) ) );

					//Submenu items

						if ( is_array( $submenu ) && ! empty( $submenu ) ) {
							foreach ( $submenu as $title => $url ) {

								$wp_admin_bar->add_menu( apply_filters( 'wmhook_wmtf_toolbar_child-' . sanitize_title( $title ), array(
										'parent' => 'theme_options_links',
										'id'     => WM_THEME_SHORTNAME . '_theme_options-' . sanitize_title( $title ),
										'title'  => $title,
										'href'   => esc_url( $url ),
									) ) );

							} // /foreach
						}

			} // /toolbar





		/**
		 * 100) Helpers
		 */

			/**
			 * Remove shortcodes from string
			 *
			 * This function keeps the text between shortcodes,
			 * unlike WordPress native strip_shortcodes() function.
			 *
			 * @since    3.0
			 * @version  5.0
			 *
			 * @param  string $content
			 */
			static public function remove_shortcodes( $content ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_remove_shortcodes_pre', false, $content );

					if ( false !== $pre ) {
						return $pre;
					}


				//Output

					return preg_replace( '|\[(.+?)\]|s', '', $content );

			} // /remove_shortcodes



			/**
			 * HTML in widget titles
			 *
			 * Just replace the "<" and ">" in HTML tag with "[" and "]".
			 * Examples:
			 * "[em][/em]" will output "<em></em>"
			 * "[br /]" will output "<br />"
			 *
			 * @since    3.0
			 * @version  5.0
			 *
			 * @param  string $title
			 */
			static public function html_widget_title( $title ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_html_widget_title_pre', false, $title );

					if ( false !== $pre ) {
						return $pre;
					}


				//Helper variables

					$replacements = array(
							'[' => '<',
							']' => '>',
						);


				//Output

					return wp_kses_post( strtr( $title, $replacements ) );

			} // /html_widget_title



			/**
			 * Accessibility skip links
			 *
			 * @since    3.0
			 * @version  5.0
			 *
			 * @param  string $type
			 */
			static public function accessibility_skip_link( $type ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_accessibility_skip_link_pre', false, $type );

					if ( false !== $pre ) {
						return $pre;
					}


				//Helper variables

					$links = apply_filters( 'wmhook_wmtf_accessibility_skip_links', array(
						'to_content'    => '<a class="skip-link screen-reader-text" href="#content">' . __( 'Skip to content', 'wm_domain' ) . '</a>',
						'to_navigation' => '<a class="skip-link screen-reader-text" href="#site-navigation">' . __( 'Skip to navigation', 'wm_domain' ) . '</a>',
					) );


				//Output

					if ( isset( $links[ $type ] ) ) {
						return $links[ $type ];
					}

			} // /accessibility_skip_link



			/**
			 * Contextual help text
			 *
			 * Hook onto `wmhook_contextual_help_texts_array` to add help texts.
			 *
			 * @example
			 *   $texts_array = array(
			 *     $screen_id => array(
			 *       array(
			 *         'tab-id'      => 'TAB_ID_1',
			 *         'tab-title'   => 'TAB_TITLE_1',
			 *         'tab-content' => 'TAB_CONTENT_1',
			 *       ),
			 *       array(
			 *         'tab-id'      => 'TAB_ID_2',
			 *         'tab-title'   => 'TAB_TITLE_2',
			 *         'tab-content' => 'TAB_CONTENT_2',
			 *       )
			 *     )
			 *   );
			 *
			 * @since    3.0
			 * @version  5.0
			 *
			 * @param  string    $contextual_help  Help text that appears on the screen.
			 * @param  string    $screen_id        Screen ID.
			 * @param  WP_Screen $screen           Current WP_Screen instance.
			 */
			static public function contextual_help( $contextual_help, $screen_id, $screen ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_contextual_help_pre', false, $contextual_help, $screen_id, $screen );

					if ( false !== $pre ) {
						return $pre;
					}


				//Helper variables

					$texts_array = array_filter( (array) apply_filters( 'wmhook_contextual_help_texts_array', array() ) );


				//Requirements check

					if ( empty( $texts_array ) ) {
						return;
					}


				//Processing

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

						} // /foreach

					}

			} // /contextual_help



			/**
			 * Get image ID from its URL
			 *
			 * @since    3.0
			 * @version  5.0
			 *
			 * @link  http://pippinsplugins.com/retrieve-attachment-id-from-image-url/
			 * @link  http://make.wordpress.org/core/2012/12/12/php-warning-missing-argument-2-for-wpdb-prepare/
			 *
			 * @param  string $url
			 */
			static public function get_image_id_from_url( $url ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_get_image_id_from_url_pre', false, $url );

					if ( false !== $pre ) {
						return $pre;
					}


				//Helper variables

					global $wpdb;

					$output = null;

					$cache = array_filter( (array) get_transient( 'wmtf_image_ids' ) );


				//Return cached result if found and if relevant

					if (
							! empty( $cache )
							&& isset( $cache[ $url ] )
							&& wp_get_attachment_url( absint( $cache[ $url ] ) )
							&& $url == wp_get_attachment_url( absint( $cache[ $url ] ) )
						) {

						return absint( $cache[ $url ] );

					}


				//Processing

					if (
							is_object( $wpdb )
							&& isset( $wpdb->prefix )
						) {

						$prefix = $wpdb->prefix;

						$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM " . $prefix . "posts" . " WHERE guid = %s", esc_url( $url ) ) );

						$output = ( isset( $attachment[0] ) ) ? ( $attachment[0] ) : ( null );

					}

					//Cache the new record

						$cache[ $url ] = $output;

						set_transient( 'wmtf_image_ids', array_filter( (array) $cache ) );


				//Output

					return absint( $output );

			} // /get_image_id_from_url



				/**
				 * Flush out the transients used in wm_get_image_id_from_url
				 *
				 * @since    4.0
				 * @version  5.0
				 */
				static public function image_ids_transient_flusher() {

					//Processing

						delete_transient( 'wmtf_image_ids' );

				} // /image_ids_transient_flusher



			/**
			 * Returns true if a blog has more than 1 category
			 *
			 * @since    4.0
			 * @version  5.0
			 */
			static public function is_categorized_blog() {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_is_categorized_blog_pre', false );

					if ( false !== $pre ) {
						return $pre;
					}


				//Processing

					if ( false === ( $all_cats = get_transient( 'wmtf_all_categories' ) ) ) {

						//Create an array of all the categories that are attached to posts

							$all_cats = get_categories( array(
									'fields'     => 'ids',
									'hide_empty' => 1,
									'number'     => 2, //we only need to know if there is more than one category
								) );

						//Count the number of categories that are attached to the posts

							$all_cats = count( $all_cats );

						set_transient( 'wmtf_all_categories', $all_cats );

					}


				//Output

					if ( $all_cats > 1 ) {

						//This blog has more than 1 category

							return true;

					} else {

						//This blog has only 1 category

							return false;

					}

			} // /is_categorized_blog



				/**
				 * Flush out the transients used in wm_is_categorized_blog
				 *
				 * @since    4.0
				 * @version  5.0
				 */
				static public function all_categories_transient_flusher() {

					//Requirements check

						if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
							return;
						}


					//Processing

						//Like, beat it. Dig?

							delete_transient( 'wmtf_all_categories' );

				} // /all_categories_transient_flusher

	}
} // /WM_Theme_Framework