<?php
/**
 * Core class
 *
 * @uses  `wmhook_{%= prefix_hook %}_theme_options` global hook
 * @uses  `wmhook_{%= prefix_hook %}_enable_rtl` global hook
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Core
 *
 * @since    1.0
 * @version  2.0.2
 *
 * Contents:
 *
 *   0) Init
 *  10) Theme upgrade action
 *  20) Branding
 *  30) Post/page
 *  40) Path functions
 * 100) Helpers
 */
final class {%= prefix_class %}_Library {





	/**
	 * 0) Init
	 */

		private static $instance;



		/**
		 * Constructor
		 *
		 * @since    1.0
		 * @version  1.8
		 */
		private function __construct() {

			// Processing

				// Hooks

					// Actions

						// Theme upgrade action

							add_action( 'init', __CLASS__ . '::theme_upgrade' );

						// Flushing transients

							add_action( 'switch_theme', __CLASS__ . '::image_ids_transient_flusher' );

							add_action( 'edit_category', __CLASS__ . '::all_categories_transient_flusher' );

							add_action( 'save_post', __CLASS__ . '::all_categories_transient_flusher' );

						// Contextual help

							add_action( 'contextual_help', __CLASS__ . '::contextual_help', 10, 3 );

					// Filters

						// Widgets improvements

							add_filter( 'show_recent_comments_widget_style', '__return_false' );

							add_filter( 'widget_text', 'do_shortcode' );

						// Table of contents

							add_filter( 'the_content', __CLASS__ . '::add_table_of_contents' );

		} // /__construct



		/**
		 * Initialization (get instance)
		 *
		 * @since    1.0
		 * @version  1.0
		 */
		public static function init() {

			// Processing

				if ( null === self::$instance ) {
					self::$instance = new self;
				}


			// Output

				return self::$instance;

		} // /init





	/**
	 * 10) Theme upgrade action
	 */

		/**
		 * Do action on theme version change
		 *
		 * @since    1.0
		 * @version  2.0
		 */
		public static function theme_upgrade() {

			// Helper variables

				$current_theme_version = get_transient( '{%= theme_slug %}_version' );
				$new_theme_version     = wp_get_theme( '{%= theme_slug %}' )->get( 'Version' );


			// Processing

				if (
						empty( $current_theme_version )
						|| $new_theme_version != $current_theme_version
					) {

					do_action( 'wmhook_{%= prefix_hook %}_library_theme_upgrade', $current_theme_version, $new_theme_version );

					set_transient( '{%= theme_slug %}_version', $new_theme_version );

				}

		} // /theme_upgrade





	/**
	 * 20) Branding
	 */

		/**
		 * Get the logo
		 *
		 * Accessibility rules applied.
		 *
		 * @link  http://blog.rrwd.nl/2014/11/21/html5-headings-in-wordpress-lets-fight/
		 *
		 * @since    1.0
		 * @version  2.0
		 *
		 * @param  string $container_class  If empty, no container will be outputted.
		 */
		public static function get_the_logo( $container_class = 'site-branding' ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_logo_pre', false, $container_class );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				$output = array();

				$custom_logo = get_theme_mod( 'custom_logo' ); // Since WordPress 4.5

				// If we don't get WordPress 4.5+ custom logo, try Jetpack Site Logo

					if ( empty( $custom_logo ) && function_exists( 'jetpack_get_site_logo' ) ) {
						$custom_logo = get_option( 'site_logo', array() );
						$custom_logo = ( isset( $custom_logo['id'] ) && $custom_logo['id'] ) ? ( absint( $custom_logo['id'] ) ) : ( false );
					}

				$blog_info = apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_logo_blog_info', array(
						'name'        => trim( get_bloginfo( 'name' ) ),
						'description' => trim( get_bloginfo( 'description' ) ),
					), $container_class );

				$args = apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_logo_args', array(
						'logo_image' => ( ! empty( $custom_logo ) ) ? ( $custom_logo ) : ( false ),
						'logo_type'  => 'text',
						'title_att'  => ( $blog_info['description'] ) ? ( $blog_info['name'] . ' | ' . $blog_info['description'] ) : ( $blog_info['name'] ),
						'url'        => home_url( '/' ),
						'container'  => $container_class,
					), $blog_info );


			// Processing

				// Logo image

					if ( $args['logo_image'] ) {

						$img_id = ( is_numeric( $args['logo_image'] ) ) ? ( absint( $args['logo_image'] ) ) : ( self::get_image_id_from_url( $args['logo_image'] ) );

						if ( $img_id ) {

							$atts = (array) apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_logo_image_atts', array(
									'class' => '',
								), $img_id, $args, $blog_info );

							$args['logo_image'] = wp_get_attachment_image( absint( $img_id ), 'full', false, $atts );

						} else {

							$args['logo_image'] = '<img src="' . esc_url( $args['logo_image'] ) . '" alt="' . esc_attr( sprintf( esc_html_x( '%s logo', 'Site logo image "alt" HTML attribute text.', '{%= text_domain %}' ), $blog_info['name'] ) ) . '" title="' . esc_attr( $args['title_att'] ) . '" />';

						}

						$args['logo_type'] = 'img';

					}

					$args['logo_image'] = apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_logo_image', $args['logo_image'], $args, $blog_info );

				// Logo HTML

					$logo_class = apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_logo_class', 'site-title logo type-' . $args['logo_type'], $args, $blog_info );

					if ( $args['container'] ) {
						$output[10] = '<div class="' . esc_attr( trim( $args['container'] ) ) . '">';
					}

						if ( is_front_page() ) {
							$output[20] = '<h1 id="site-title" class="' . esc_attr( $logo_class ) . '">';
						} else {
							$output[20] = '<h2 class="screen-reader-text">' . wp_get_document_title() . '</h2>'; // To provide BODY heading on subpages
							$output[25] = '<a id="site-title" class="' . esc_attr( $logo_class ) . '" href="' . esc_url( $args['url'] ) . '" title="' . esc_attr( $args['title_att'] ) . '" rel="home">';
						}

							if ( 'text' === $args['logo_type'] ) {
								$output[30] = '<span class="text-logo">' . $blog_info['name'] . '</span>';
							} else {
								$output[30] = $args['logo_image'] . '<span class="screen-reader-text">' . $blog_info['name'] . '</span>';
							}

						if ( is_front_page() ) {
							$output[40] = '</h1>';
						} else {
							$output[40] = '</a>';
						}

							if ( $blog_info['description'] ) {
								$output[50] = '<div class="site-description">' . $blog_info['description'] . '</div>';
							}

					if ( $args['container'] ) {
						$output[60] = '</div>';
					}

					// Filter output array

						$output = (array) apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_logo_output', $output, $args, $blog_info );

						ksort( $output );


			// Output

				return implode( '', $output );

		} // /get_the_logo



			/**
			 * Display the logo
			 *
			 * @since    1.0
			 * @version  1.0
			 */
			public static function the_logo() {

				// Helper variables

					$output = self::get_the_logo();


				// Output

					if ( $output ) {
						echo $output;
					}

			} // /the_logo





	/**
	 * 30) Post/page
	 */

		/**
		 * Add table of contents generated from <!--nextpage--> tag
		 *
		 * Will create a table of content in multipage post from
		 * the first H2 heading in each post part.
		 * Appends the output at the top and bottom of post content.
		 *
		 * @since    1.0
		 * @version  2.0
		 *
		 * @param  string $content
		 */
		public static function add_table_of_contents( $content = '' ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_library_add_table_of_contents_pre', false, $content );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				global $page, $numpages, $multipage, $post;

				// Requirements check

					if (
							! $multipage
							|| ! is_singular()
						) {
						return $content;
					}

				$title_text = apply_filters( 'wmhook_{%= prefix_hook %}_library_add_table_of_contents_title_text', sprintf( esc_html_x( '"%s" table of contents', '%s: post title.', '{%= text_domain %}' ), the_title_attribute( 'echo=0' ) ) );
				$title      = apply_filters( 'wmhook_{%= prefix_hook %}_library_add_table_of_contents_title', '<h2 class="screen-reader-text">' . $title_text . '</h2>' );

				$args = apply_filters( 'wmhook_{%= prefix_hook %}_library_add_table_of_contents_args', array(
						'disable_first' => true, // First part to have a title of the post (part title won't be parsed)?
						'links'         => array(), // The output HTML links
						'post_content'  => ( isset( $post->post_content ) ) ? ( $post->post_content ) : ( '' ), // Get the whole post content
						'tag'           => 'h2', // HTML heading tag to parse as a post part title
					) );

				// Post part counter

					$i = 0;


			// Processing

				$args['post_content'] = explode( '<!--nextpage-->', (string) $args['post_content'] );

				// Get post parts titles

					foreach ( $args['post_content'] as $part ) {

						// Current post part number

							$i++;

						// Get the title for post part

							if ( $args['disable_first'] && 1 === $i ) {

								$part_title = get_the_title();

							} else {

								preg_match( '/<' . tag_escape( $args['tag'] ) . '(.*?)>(.*?)<\/' . tag_escape( $args['tag'] ) . '>/', $part, $matches );

								if ( ! isset( $matches[2] ) || ! $matches[2] ) {
									$part_title = sprintf( esc_html__( 'Page %s', '{%= text_domain %}' ), number_format_i18n( $i ) );
								} else {
									$part_title = $matches[2];
								}

							}

						// Set post part class

							if ( $page === $i ) {
								$class = ' class="current"';
							} elseif ( $page > $i ) {
								$class = ' class="passed"';
							} else {
								$class = '';
							}

						// Post part item output

							$args['links'][$i] = (string) apply_filters( 'wmhook_{%= prefix_hook %}_library_add_table_of_contents_part', '<li' . $class . '>' . _wp_link_page( $i ) . $part_title . '</a></li>', $i, $part_title, $class, $args );

					} // /foreach

				// Add table of contents into the post/page content

					$args['links'] = implode( '', $args['links'] );

					$links = apply_filters( 'wmhook_{%= prefix_hook %}_library_add_table_of_contents_links', array(
							// Display table of contents before the post content only in first post part
								'before' => ( 1 === $page ) ? ( '<div class="post-table-of-contents top" title="' . esc_attr( wp_strip_all_tags( $title_text ) ) . '">' . $title . '<ol>' . $args['links'] . '</ol></div>' ) : ( '' ),
							// Display table of cotnnets after the post cotnent on each post part
								'after'  => '<div class="post-table-of-contents bottom" title="' . esc_attr( wp_strip_all_tags( $title_text ) ) . '">' . $title . '<ol>' . $args['links'] . '</ol></div>',
						), $args );

					$content = $links['before'] . $content . $links['after'];

			// Output

				return $content;

		} // /add_table_of_contents



		/**
		 * Get the post meta info
		 *
		 * hAtom microformats compatible. @link http://goo.gl/LHi4Dy
		 * Supports WP ULike plugin. @link https://wordpress.org/plugins/wp-ulike/
		 * Supports ZillaLikes plugin. @link http://www.themezilla.com/plugins/zillalikes/
		 * Supports Post Views Count plugin. @link https://wordpress.org/plugins/baw-post-views-count/
		 *
		 * @since    1.0
		 * @version  2.0
		 *
		 * @param  array $args
		 */
		public static function get_the_post_meta_info( $args = array() ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_post_meta_info_pre', false, $args );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				$output = '';

				$args = wp_parse_args( $args, apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_post_meta_info_defaults', array(
						'class'       => 'entry-meta',
						'container'   => 'div',
						'date_format' => null,
						'html'        => '<span class="{class}"{attributes}>{description}{content}</span> ',
						'html_custom' => array(), // Example: array( 'date' => 'CUSTOM_HTML_WITH_{class}_{attributes}_{description}_AND_{content}_HERE' )
						'meta'        => array(), // Example: array( 'date', 'author', 'category', 'comments', 'permalink' )
						'post_id'     => null,
					) ) );
				$args = apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_post_meta_info_args', $args );

				$args['meta'] = array_filter( (array) $args['meta'] );

				if ( $args['post_id'] ) {
					$args['post_id'] = absint( $args['post_id'] );
				}


			// Requirements check

				if ( empty( $args['meta'] ) ) {
					return;
				}


			// Processing

				foreach ( $args['meta'] as $meta ) {

						$helper = '';

						$replacements  = (array) apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_post_meta_info_replacements', array(), $meta, $args );
						$output_single = apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_post_meta_info', '', $meta, $args );
						$output       .= $output_single;

					// Predefined metas

						switch ( $meta ) {

							case 'author':

								if ( apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_post_meta_info_enable_' . $meta, true, $args ) ) {
									$helper = ( is_callable( '{%= prefix_class %}_Schema::get' ) ) ? ( {%= prefix_class %}_Schema::get( 'name' ) ) : ( '' );

									$replacements = array(
											'{attributes}'  => ( is_callable( '{%= prefix_class %}_Schema::get' ) ) ? ( {%= prefix_class %}_Schema::get( 'author' ) . {%= prefix_class %}_Schema::get( 'Person' ) ) : ( '' ),
											'{class}'       => esc_attr( 'byline author vcard entry-meta-element' ),
											'{description}' => '<span class="entry-meta-description">' . esc_html_x( 'Written by:', 'Post meta info description: author name.', '{%= text_domain %}' ) . ' </span>',
											'{content}'     => '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" class="url fn n" rel="author"' . $helper . '>' . get_the_author() . '</a>',
										);
								}

							break;
							case 'category':

								if (
										apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_post_meta_info_enable_' . $meta, true, $args )
										&& self::is_categorized_blog()
										&& ( $helper = get_the_category_list( ', ', '', $args['post_id'] ) )
									) {
									$replacements = array(
											'{attributes}'  => '',
											'{class}'       => esc_attr( 'cat-links entry-meta-element' ),
											'{description}' => '<span class="entry-meta-description">' . esc_html_x( 'Categorized in:', 'Post meta info description: categories list.', '{%= text_domain %}' ) . ' </span>',
											'{content}'     => $helper,
										);
								}

							break;
							case 'comments':

								if (
										apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_post_meta_info_enable_' . $meta, true, $args )
										&& ! post_password_required()
										&& (
											comments_open( $args['post_id'] )
											|| get_comments_number( $args['post_id'] )
										)
									) {
									$helper       = absint( get_comments_number( $args['post_id'] ) );
									$element_id   = ( $helper ) ? ( '#comments' ) : ( '#respond' );
									$replacements = array(
											'{attributes}'  => '',
											'{class}'       => esc_attr( 'comments-link entry-meta-element' ),
											'{description}' => '<span class="entry-meta-description">' . esc_html_x( 'Comments:', 'Post meta info description: comments count.', '{%= text_domain %}' ) . ' </span>',
											'{content}'     => '<a href="' . esc_url( get_permalink( $args['post_id'] ) ) . $element_id . '" title="' . esc_attr( sprintf( esc_html_x( 'Comments: %s', '%s: number of comments.', '{%= text_domain %}' ), number_format_i18n( $helper ) ) ) . '"><span class="comments-title">' . esc_html_x( 'Comments:', 'Title for number of comments in post meta.', '{%= text_domain %}' ) . ' </span><span class="comments-count">' . $helper . '</span></a>',
										);
								}

							break;
							case 'date':

								if ( apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_post_meta_info_enable_' . $meta, true, $args ) ) {
									$helper = ( is_callable( '{%= prefix_class %}_Schema::get' ) ) ? ( {%= prefix_class %}_Schema::get( 'datePublished' ) ) : ( '' );

									$replacements = array(
											'{attributes}'  => '',
											'{class}'       => esc_attr( 'entry-date entry-meta-element' ),
											'{description}' => '<span class="entry-meta-description">' . esc_html_x( 'Posted on:', 'Post meta info description: publish date.', '{%= text_domain %}' ) . ' </span>',
											'{content}'     => '<a href="' . esc_url( get_permalink( $args['post_id'] ) ) . '" rel="bookmark"><time datetime="' . esc_attr( get_the_date( 'c' ) ) . '" class="published" title="' . esc_attr( get_the_date() ) . ' | ' . esc_attr( get_the_time( '', $args['post_id'] ) ) . '"' . $helper . '>' . esc_html( get_the_date( $args['date_format'] ) ) . '</time></a>',
										);

									if ( is_callable( '{%= prefix_class %}_Schema::get' ) ) {
										$replacements['{content}'] = $replacements['{content}'] . {%= prefix_class %}_Schema::get( 'dateModified', get_the_modified_date( 'c' ) );
									}
								}

							break;
							case 'edit':

								if (
										apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_post_meta_info_enable_' . $meta, true, $args )
										&& ( $helper = get_edit_post_link( $args['post_id'] ) )
									) {
									$the_title_attribute_args = array( 'echo' => false );
									if ( $args['post_id'] ) {
										$the_title_attribute_args['post'] = $args['post_id'];
									}

									$replacements = array(
											'{attributes}'  => '',
											'{class}'       => esc_attr( 'entry-edit entry-meta-element' ),
											'{description}' => '',
											'{content}'     => '<a href="' . esc_url( $helper ) . '" title="' . esc_attr( sprintf( esc_html__( 'Edit the "%s"', '{%= text_domain %}' ), the_title_attribute( $the_title_attribute_args ) ) ) . '"><span>' . esc_html_x( 'Edit', 'Edit post link.', '{%= text_domain %}' ) . '</span></a>',
										);
								}

							break;
							case 'likes':

								if ( apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_post_meta_info_enable_' . $meta, true, $args ) ) {

									if ( function_exists( 'wp_ulike' ) ) {
									// WP ULike first

										$replacements = array(
												'{attributes}'  => '',
												'{class}'       => esc_attr( 'entry-likes entry-meta-element' ),
												'{description}' => '',
												'{content}'     => wp_ulike( 'put' ),
											);

									} elseif ( function_exists( 'zilla_likes' ) ) {
									// ZillaLikes after

										global $zilla_likes;

										$replacements = array(
												'{attributes}'  => '',
												'{class}'       => esc_attr( 'entry-likes entry-meta-element' ),
												'{description}' => '',
												'{content}'     => $zilla_likes->do_likes(),
											);

									}

								}

							break;
							case 'permalink':

								if ( apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_post_meta_info_enable_' . $meta, true, $args ) ) {
									$the_title_attribute_args = array( 'echo' => false );
									if ( $args['post_id'] ) {
										$the_title_attribute_args['post'] = $args['post_id'];
									}

									$replacements = array(
											'{attributes}'  => ( is_callable( '{%= prefix_class %}_Schema::get' ) ) ? ( {%= prefix_class %}_Schema::get( 'url' ) ) : ( '' ),
											'{class}'       => esc_attr( 'entry-permalink entry-meta-element' ),
											'{description}' => '<span class="entry-meta-description">' . esc_html_x( 'Bookmark link:', 'Post meta info description: post bookmark link.', '{%= text_domain %}' ) . ' </span>',
											'{content}'     => '<a href="' . esc_url( get_permalink( $args['post_id'] ) ) . '" title="' . esc_attr( sprintf( esc_html__( 'Permalink to "%s"', '{%= text_domain %}' ), the_title_attribute( $the_title_attribute_args ) ) ) . '" rel="bookmark"><span>' . get_the_title( $args['post_id'] ) . '</span></a>',
										);
								}

							break;
							case 'tags':

								if (
										apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_post_meta_info_enable_' . $meta, true, $args )
										&& ( $helper = get_the_tag_list( '', ' ', '', $args['post_id'] ) )
									) {
									$replacements = array(
											'{attributes}'  => ( is_callable( '{%= prefix_class %}_Schema::get' ) ) ? ( {%= prefix_class %}_Schema::get( 'keywords' ) ) : ( '' ),
											'{class}'       => esc_attr( 'tags-links entry-meta-element' ),
											'{description}' => '<span class="entry-meta-description">' . esc_html_x( 'Tagged as:', 'Post meta info description: tags list.', '{%= text_domain %}' ) . ' </span>',
											'{content}'     => $helper,
										);
								}

							break;
							case 'views':

								if (
										apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_post_meta_info_enable_' . $meta, true, $args )
										&& function_exists( 'bawpvc_views_sc' )
										&& ( $helper = bawpvc_views_sc( array() ) )
									) {
									$replacements = array(
											'{attributes}'  => ' title="' . esc_attr__( 'Views count', '{%= text_domain %}' ) . '"',
											'{class}'       => esc_attr( 'entry-views entry-meta-element' ),
											'{description}' => '',
											'{content}'     => wp_strip_all_tags( $helper ),
										);
								}

							break;

							default:
							break;

						} // /switch

						// Single meta output

							$replacements = (array) apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_post_meta_info_replacements_' . $meta, $replacements, $args );

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
					$output = '<' . tag_escape( $args['container'] ) . ' class="' . esc_attr( $args['class'] ) . '">' . $output . '</' . tag_escape( $args['container'] ) . '>';
				}


			// Output

				return $output;

		} // /get_the_post_meta_info



			/**
			 * Display the post meta info
			 *
			 * @since    1.0
			 * @version  1.0
			 *
			 * @param  array $args
			 */
			public static function the_post_meta_info( $args = array() ) {

				// Helper variables

					$output = self::get_the_post_meta_info( $args );


				// Output

					if ( $output ) {
						echo $output;
					}

			} // /the_post_meta_info



		/**
		 * Get the paginated heading suffix
		 *
		 * @since    1.0
		 * @version  2.0
		 *
		 * @param  string $tag           Wrapper tag
		 * @param  string $singular_only Display only on singular posts of specific type
		 */
		public static function get_the_paginated_suffix( $tag = '', $singular_only = false ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_library_get_the_paginated_suffix_pre', false, $tag, $singular_only );

				if ( false !== $pre ) {
					return $pre;
				}


			// Requirements check

				if (
						$singular_only
						&& ! is_singular( $singular_only )
					) {
					return;
				}


			// Helper variables

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


			// Processing

				if ( 1 < $paged ) {
					$output = ' ' . $tag[0] . sprintf( esc_html_x( '(page %s)', 'Paginated content title suffix, %s: page number.', '{%= text_domain %}' ), number_format_i18n( $paged ) ) . $tag[1];
				}


			// Output

				return $output;

		} // /get_the_paginated_suffix



			/**
			 * Display the paginated heading suffix
			 *
			 * @since    1.0
			 * @version  1.0
			 *
			 * @param  string $tag           Wrapper tag
			 * @param  string $singular_only Display only on singular posts of specific type
			 */
			public static function the_paginated_suffix( $tag = '', $singular_only = false ) {

				// Helper variables

					$output = self::get_the_paginated_suffix( $tag, $singular_only );


				// Output

					if ( $output ) {
						echo $output;
					}

			} // /the_paginated_suffix



		/**
		 * Checks for <!--more--> tag in post content
		 *
		 * @since    1.0
		 * @version  2.0
		 *
		 * @param  mixed $post
		 */
		public static function has_more_tag( $post = null ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_library_has_more_tag_pre', false, $post );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				if ( empty( $post ) ) {
					global $post;
				} elseif ( is_numeric( $post ) ) {
					$post = get_post( absint( $post ) );
				}


			// Requirements check

				if (
						! is_object( $post )
						|| ! isset( $post->post_content )
					) {
					return;
				}


			// Output

				return strpos( $post->post_content, '<!--more-->' );

		} // /has_more_tag





	/**
	 * 40) Path functions
	 */

		/**
		 * Outputs URL to the specific file
		 *
		 * This function looks for the file in the child theme first.
		 * If the file is not located in child theme, output the URL from parent theme.
		 *
		 * Matching the WordPress 4.7+ native `get_theme_file_uri()` function.
		 *
		 * @todo  Remove with WordPress 4.9
		 *
		 * @since    1.0
		 * @version  2.0.2
		 *
		 * @param  string $file Optional. File to search for in the stylesheet directory.
		 *
		 * @return  string Actual URL to the file
		 */
		public static function get_theme_file_uri( $file = '' ) {

			// Helper variables

				$file = ltrim( $file, '/' );


			// Processing

				if ( empty( $file ) ) {
					$url = get_stylesheet_directory_uri();
				} elseif ( file_exists( get_stylesheet_directory() . '/' . $file ) ) {
					$url = get_stylesheet_directory_uri() . '/' . $file;
				} else {
					$url = get_template_directory_uri() . '/' . $file;
				}


			// Output

				return apply_filters( 'theme_file_uri', $url, $file );

		} // /get_theme_file_uri





	/**
	 * 100) Helpers
	 */

		/**
		 * Fixing URLs in `is_ssl()` returns TRUE
		 *
		 * @since    1.3
		 * @version  1.3.3
		 *
		 * @param  string $content
		 */
		static public function fix_ssl_urls( $content ) {

			// Processing

				if ( is_ssl() ) {
					$content = str_ireplace( 'http:', 'https:', $content );
					$content = str_ireplace( 'xmlns="https:', 'xmlns="http:', $content );
					$content = str_ireplace( "xmlns='https:", "xmlns='http:", $content );
				}


			// Output

				return $content;

		} // /fix_ssl_urls



		/**
		 * Remove shortcodes from string
		 *
		 * This function keeps the text between shortcodes,
		 * unlike WordPress native strip_shortcodes() function.
		 *
		 * @since    1.0
		 * @version  2.0
		 *
		 * @param  string $content
		 */
		public static function remove_shortcodes( $content ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_library_remove_shortcodes_pre', false, $content );

				if ( false !== $pre ) {
					return $pre;
				}


			// Output

				return preg_replace( '|\[(.+?)\]|s', '', $content );

		} // /remove_shortcodes



		/**
		 * Accessibility skip links
		 *
		 * @since    1.0
		 * @version  2.0
		 *
		 * @param  string $id     Link target element ID.
		 * @param  string $text   Link text.
		 * @param  string $class  Additional link CSS classes.
		 */
		public static function link_skip_to( $id = 'content', $text = '', $class = '' ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_library_link_skip_to_pre', false, $id, $text, $class );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				if ( empty( $text ) ) {
					$text = esc_html__( 'Skip to main content', '{%= text_domain %}' );
				}


			// Output

				return '<a class="' . esc_attr( trim( 'skip-link screen-reader-text ' . $class ) ) . '" href="#' . esc_attr( trim( $id ) ) . '">' . esc_html( $text ) . '</a>';

		} // /link_skip_to



		/**
		 * Contextual help text
		 *
		 * Hook onto `wmhook_{%= prefix_hook %}_library_contextual_help_texts_array` to add help texts.
		 *
		 * @example
		 *
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
		 * @since    1.0
		 * @version  2.0
		 *
		 * @param  string    $contextual_help  Help text that appears on the screen.
		 * @param  string    $screen_id        Screen ID.
		 * @param  WP_Screen $screen           Current WP_Screen instance.
		 */
		public static function contextual_help( $contextual_help, $screen_id, $screen ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_library_contextual_help_pre', false, $contextual_help, $screen_id, $screen );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				$texts_array = array_filter( (array) apply_filters( 'wmhook_{%= prefix_hook %}_library_contextual_help_texts_array', array() ) );


			// Requirements check

				if ( empty( $texts_array ) ) {
					return;
				}


			// Processing

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
		 * @since    1.0
		 * @version  2.0
		 *
		 * @link  http://pippinsplugins.com/retrieve-attachment-id-from-image-url/
		 * @link  http://make.wordpress.org/core/2012/12/12/php-warning-missing-argument-2-for-wpdb-prepare/
		 *
		 * @param  string $url
		 */
		public static function get_image_id_from_url( $url ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_library_get_image_id_from_url_pre', false, $url );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				global $wpdb;

				$output = null;

				$cache = array_filter( (array) get_transient( '{%= prefix_var %}_image_ids' ) );


			// Return cached result if found and if relevant

				if (
						! empty( $cache )
						&& isset( $cache[ $url ] )
						&& wp_get_attachment_url( absint( $cache[ $url ] ) )
						&& $url == wp_get_attachment_url( absint( $cache[ $url ] ) )
					) {

					return absint( $cache[ $url ] );

				}


			// Processing

				if (
						is_object( $wpdb )
						&& isset( $wpdb->posts )
					) {

					$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM $wpdb->posts WHERE guid='%s'", esc_url( $url ) ) );

					$output = ( isset( $attachment[0] ) ) ? ( $attachment[0] ) : ( null );

				}

				// Cache the new record

					$cache[ $url ] = $output;

					set_transient( '{%= prefix_var %}_image_ids', array_filter( (array) $cache ) );


			// Output

				return absint( $output );

		} // /get_image_id_from_url



			/**
			 * Flush out the transients used in `get_image_id_from_url`
			 *
			 * @since    1.0
			 * @version  1.0
			 */
			public static function image_ids_transient_flusher() {

				// Processing

					delete_transient( '{%= prefix_var %}_image_ids' );

			} // /image_ids_transient_flusher



		/**
		 * Returns true if a blog has more than 1 category
		 *
		 * @since    1.0
		 * @version  2.0
		 */
		public static function is_categorized_blog() {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_library_is_categorized_blog_pre', false );

				if ( false !== $pre ) {
					return $pre;
				}


			// Processing

				if ( false === ( $all_cats = get_transient( '{%= prefix_var %}_all_categories' ) ) ) {

					// Create an array of all the categories that are attached to posts

						$all_cats = get_categories( array(
								'fields'     => 'ids',
								'hide_empty' => 1,
								'number'     => 2, // We only need to know if there is more than one category
							) );

					// Count the number of categories that are attached to the posts

						$all_cats = count( $all_cats );

					set_transient( '{%= prefix_var %}_all_categories', $all_cats );

				}


			// Output

				if ( $all_cats > 1 ) {

					// This blog has more than 1 category

						return true;

				} else {

					// This blog has only 1 category

						return false;

				}

		} // /is_categorized_blog



			/**
			 * Flush out the transients used in `is_categorized_blog`
			 *
			 * @since    1.0
			 * @version  1.0
			 */
			public static function all_categories_transient_flusher() {

				// Requirements check

					if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
						return;
					}


				// Processing

					// Like, beat it. Dig?

						delete_transient( '{%= prefix_var %}_all_categories' );

			} // /all_categories_transient_flusher





} // /{%= prefix_class %}_Library

add_action( 'after_setup_theme', '{%= prefix_class %}_Library::init', -50 );





/**
 * Helper `get_theme_file_uri()` function declaration for WordPress 4.7-
 *
 * @todo  Remove with WordPress 4.9
 *
 * @since    2.0.2
 * @version  2.0.2
 */
if ( ! function_exists( 'get_theme_file_uri' ) ) {
	function get_theme_file_uri( $file = '' ) {

		// Output

			return {%= prefix_class %}_Library::get_theme_file_uri( $file );

	}
} // /get_theme_file_uri
