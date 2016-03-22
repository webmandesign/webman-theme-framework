<?php
/**
 * Core class
 *
 * @uses  `wmhook_{%= prefix_hook %}_theme_options` global hook
 * @uses  `wmhook_{%= prefix_hook %}_enable_rtl` global hook
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Core
 */





/**
 * Core class
 *
 * @since    1.0
 * @version  1.3.1
 *
 * Contents:
 *
 *   0) Init
 *  10) Theme upgrade action
 *  20) Branding
 *  30) Post/page
 *  40) CSS functions
 *  50) Options
 *  60) Filesystem
 * 100) Helpers
 */
final class {%= prefix_class %}_Theme_Framework {





	/**
	 * 0) Init
	 */

		private static $instance;



		/**
		 * Constructor
		 *
		 * @since    1.0
		 * @version  1.3
		 */
		private function __construct() {

			// Processing

				// Hooks

					// Actions

						// Theme upgrade action

							add_action( 'init', array( $this, 'theme_upgrade' ) );

						// Flushing transients

							add_action( 'switch_theme',  array( $this, 'image_ids_transient_flusher' )      );
							add_action( 'edit_category', array( $this, 'all_categories_transient_flusher' ) );
							add_action( 'save_post',     array( $this, 'all_categories_transient_flusher' ) );

						// Contextual help

							add_action( 'contextual_help', array( $this, 'contextual_help' ), 10, 3 );

						// Customizer saving

							add_action( 'customize_save_after', array( $this, 'generate_main_css_all' ), 98 );

					// Filters

						// Escape inline CSS

							add_filter( 'wmhook_{%= prefix_hook %}_esc_css', 'wp_strip_all_tags' ); // https://github.com/WPTRT/code-examples/blob/master/customizer/sanitization-callbacks.php#L43

						// Widgets improvements

							// add_filter( 'widget_title', array( $this, 'html_widget_title' ) );

							add_filter( 'show_recent_comments_widget_style', '__return_false' );
							add_filter( 'widget_text', 'do_shortcode' );

							remove_filter( 'widget_title', 'esc_html' );

						// Table of contents

							add_filter( 'the_content', array( $this, 'add_table_of_contents' ), 10 );

						// Minify CSS

							add_filter( 'wmhook_{%= prefix_hook %}_tf_generate_main_css_output_min', array( $this, 'minify_css' ), 10 );

						// SSL ready URLs

							add_filter( 'wmhook_{%= prefix_hook %}_tf_generate_main_css_output', array( $this, 'fix_ssl_urls' ), 9999 );

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
		 * @version  1.1
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

					do_action( 'wmhook_{%= prefix_hook %}_tf_theme_upgrade', $current_theme_version, $new_theme_version );

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
		 * @version  1.3.1
		 *
		 * @param  string $container_class  If empty, no container will be outputted.
		 */
		public static function get_the_logo( $container_class = 'site-branding' ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_logo_pre', false, $container_class );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				$output = array();

				// @todo Remove `wp_title` with WordPress 4.6
				$document_title = ( 0 > version_compare( $GLOBALS['wp_version'], '4.4' ) ) ? ( wp_title( '|', false, 'right' ) ) : ( wp_get_document_title() ); // Since WordPress 4.4

				$custom_logo = get_theme_mod( 'custom_logo' ); // Since WordPress 4.5

				// If we don't get WordPress 4.5+ custom logo, try Jetpack Site Logo

					if ( empty( $custom_logo ) && function_exists( 'jetpack_get_site_logo' ) ) {
						$custom_logo = get_option( 'site_logo', array() );
						$custom_logo = ( isset( $custom_logo['id'] ) && $custom_logo['id'] ) ? ( absint( $custom_logo['id'] ) ) : ( false );
					}

				$blog_info = apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_logo_blog_info', array(
						'name'        => trim( get_bloginfo( 'name' ) ),
						'description' => trim( get_bloginfo( 'description' ) ),
					), $container_class );

				$args = apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_logo_args', array(
						'logo_image' => ( ! empty( $custom_logo ) ) ? ( array( $custom_logo ) ) : ( array( self::get_theme_mod( 'logo' ), self::get_theme_mod( 'logo-hidpi' ) ) ),
						'logo_type'  => 'text',
						'title_att'  => ( $blog_info['description'] ) ? ( $blog_info['name'] . ' | ' . $blog_info['description'] ) : ( $blog_info['name'] ),
						'url'        => home_url( '/' ),
						'container'  => $container_class,
					) );


			// Processing

				// Logo image

					if ( ! empty( $args['logo_image'] ) && $args['logo_image'][0] ) {

						$img_id = ( is_numeric( $args['logo_image'][0] ) ) ? ( absint( $args['logo_image'][0] ) ) : ( self::get_image_id_from_url( $args['logo_image'][0] ) );

						// High resolution support

							if ( isset( $args['logo_image'][1] ) && is_numeric( $args['logo_image'][1] ) ) {
								$img_id_hidpi = absint( $args['logo_image'][1] );
							} elseif ( isset( $args['logo_image'][1] ) ) {
								$img_id_hidpi = self::get_image_id_from_url( $args['logo_image'][1] );
							} else {
								$img_id_hidpi = false;
							}

						if ( $img_id ) {

							$atts = array(
									'alt'   => esc_attr( sprintf( esc_html_x( '%s logo', 'Site logo image "alt" HTML attribute text.', '{%= text_domain %}' ), $blog_info['name'] ) ),
									'title' => esc_attr( $args['title_att'] ),
									'class' => '',
								);

							if ( $img_id_hidpi ) {
								$logo_url_hidpi     = wp_get_attachment_image_src( $img_id_hidpi, 'full' );
								$atts['data-hidpi'] = $logo_url_hidpi[0];
							}

							$atts = (array) apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_logo_image_atts', $atts, $img_id, $img_id_hidpi );

							$args['logo_image'] = wp_get_attachment_image( absint( $img_id ), 'full', false, $atts );

						}

						$args['logo_type'] = 'img';

					}

					$args['logo_image'] = apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_logo_image', $args['logo_image'] );

				// Logo HTML

					$logo_class = apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_logo_class', 'site-title logo type-' . $args['logo_type'], $args );

					if ( $args['container'] ) {
						$output[1] = '<div class="' . esc_attr( trim( $args['container'] ) ) . '">';
					}

						if ( is_front_page() ) {
							$output[10] = '<h1 id="site-title" class="' . esc_attr( $logo_class ) . '">';
						} else {
							$output[10] = '<h2 class="screen-reader-text">' . $document_title . '</h2>'; // To provide BODY heading on subpages
							$output[15] = '<a id="site-title" class="' . esc_attr( $logo_class ) . '" href="' . esc_url( $args['url'] ) . '" title="' . esc_attr( $args['title_att'] ) . '" rel="home">';
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
						$output[100] = '</div>';
					}

					// Filter output array

						$output = (array) apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_logo_output', $output, $args );

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
		 * @version  1.3
		 *
		 * @param  string $content
		 */
		public static function add_table_of_contents( $content = '' ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_add_table_of_contents_pre', false, $content );

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

				$title_text = apply_filters( 'wmhook_{%= prefix_hook %}_tf_add_table_of_contents_title_text', sprintf( esc_html_x( '"%s" table of contents', '%s: post title.', '{%= text_domain %}' ), the_title_attribute( 'echo=0' ) ) );
				$title      = apply_filters( 'wmhook_{%= prefix_hook %}_tf_add_table_of_contents_title', '<h2 class="screen-reader-text">' . $title_text . '</h2>' );

				$args = apply_filters( 'wmhook_{%= prefix_hook %}_tf_add_table_of_contents_args', array(
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

							$args['links'][$i] = (string) apply_filters( 'wmhook_{%= prefix_hook %}_tf_add_table_of_contents_part', '<li' . $class . '>' . _wp_link_page( $i ) . $part_title . '</a></li>', $i, $part_title, $class, $args );

					} // /foreach

				// Add table of contents into the post/page content

					$args['links'] = implode( '', $args['links'] );

					$links = apply_filters( 'wmhook_{%= prefix_hook %}_tf_add_table_of_contents_links', array(
							// Display table of contents before the post content only in first post part
								'before' => ( 1 === $page ) ? ( '<div class="post-table-of-contents top" title="' . esc_attr( strip_tags( $title_text ) ) . '">' . $title . '<ol>' . $args['links'] . '</ol></div>' ) : ( '' ),
							// Display table of cotnnets after the post cotnent on each post part
								'after'  => '<div class="post-table-of-contents bottom" title="' . esc_attr( strip_tags( $title_text ) ) . '">' . $title . '<ol>' . $args['links'] . '</ol></div>',
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
		 * @version  1.3
		 *
		 * @param  array $args
		 */
		public static function get_the_post_meta_info( $args = array() ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_post_meta_info_pre', false, $args );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				$output = '';

				$args = wp_parse_args( $args, apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_post_meta_info_defaults', array(
						'class'       => 'entry-meta',
						'container'   => 'div',
						'date_format' => null,
						'html'        => '<span class="{class}"{attributes}>{description}{content}</span>',
						'html_custom' => array(), // Example: array( 'date' => 'CUSTOM_HTML_WITH_{class}_{attributes}_{description}_AND_{content}_HERE' )
						'meta'        => array(), // Example: array( 'date', 'author', 'category', 'comments', 'permalink' )
						'post_id'     => null,
					) ) );
				$args = apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_post_meta_info_args', $args );

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

						$replacements  = (array) apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_post_meta_info_replacements', array(), $meta, $args );
						$output_single = apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_post_meta_info', '', $meta, $args );
						$output       .= $output_single;

					// Predefined metas

						switch ( $meta ) {

							case 'author':

								if ( apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_post_meta_info_enable_' . $meta, true, $args ) ) {
									$helper = ( function_exists( '{%= prefix_fn %}_schema_org' ) ) ? ( {%= prefix_fn %}_schema_org( 'name' ) ) : ( '' );

									$replacements = array(
											'{attributes}'  => ( function_exists( '{%= prefix_fn %}_schema_org' ) ) ? ( {%= prefix_fn %}_schema_org( 'author' ) . {%= prefix_fn %}_schema_org( 'Person' ) ) : ( '' ),
											'{class}'       => esc_attr( 'byline author vcard entry-meta-element' ),
											'{description}' => '<span class="entry-meta-description">' . esc_html_x( 'Written by:', 'Post meta info description: author name.', '{%= text_domain %}' ) . ' </span>',
											'{content}'     => '<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" class="url fn n" rel="author"' . $helper . '>' . get_the_author() . '</a>',
										);
								}

							break;
							case 'category':

								if (
										apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_post_meta_info_enable_' . $meta, true, $args )
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
										apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_post_meta_info_enable_' . $meta, true, $args )
										&& ! post_password_required()
										&& (
											comments_open( $args['post_id'] )
											|| get_comments_number( $args['post_id'] )
										)
									) {
									$helper       = get_comments_number( $args['post_id'] );
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

								if ( apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_post_meta_info_enable_' . $meta, true, $args ) ) {
									$helper = ( function_exists( '{%= prefix_fn %}_schema_org' ) ) ? ( {%= prefix_fn %}_schema_org( 'datePublished' ) ) : ( '' );

									$replacements = array(
											'{attributes}'  => '',
											'{class}'       => esc_attr( 'entry-date entry-meta-element' ),
											'{description}' => '<span class="entry-meta-description">' . esc_html_x( 'Posted on:', 'Post meta info description: publish date.', '{%= text_domain %}' ) . ' </span>',
											'{content}'     => '<a href="' . esc_url( get_permalink( $args['post_id'] ) ) . '" rel="bookmark"><time datetime="' . esc_attr( get_the_date( 'c' ) ) . '" class="published" title="' . esc_attr( get_the_date() ) . ' | ' . esc_attr( get_the_time( '', $args['post_id'] ) ) . '"' . $helper . '>' . esc_html( get_the_date( $args['date_format'] ) ) . '</time></a>',
										);

									if ( function_exists( '{%= prefix_fn %}_schema_org' ) ) {
										$replacements['{content}'] = $replacements['{content}'] . {%= prefix_fn %}_schema_org( 'dateModified', get_the_modified_date( 'c' ) );
									}
								}

							break;
							case 'edit':

								if (
										apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_post_meta_info_enable_' . $meta, true, $args )
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

								if ( apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_post_meta_info_enable_' . $meta, true, $args ) ) {

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

								if ( apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_post_meta_info_enable_' . $meta, true, $args ) ) {
									$the_title_attribute_args = array( 'echo' => false );
									if ( $args['post_id'] ) {
										$the_title_attribute_args['post'] = $args['post_id'];
									}

									$replacements = array(
											'{attributes}'  => ( function_exists( '{%= prefix_fn %}_schema_org' ) ) ? ( {%= prefix_fn %}_schema_org( 'url' ) ) : ( '' ),
											'{class}'       => esc_attr( 'entry-permalink entry-meta-element' ),
											'{description}' => '<span class="entry-meta-description">' . esc_html_x( 'Bookmark link:', 'Post meta info description: post bookmark link.', '{%= text_domain %}' ) . ' </span>',
											'{content}'     => '<a href="' . esc_url( get_permalink( $args['post_id'] ) ) . '" title="' . esc_attr( sprintf( esc_html__( 'Permalink to "%s"', '{%= text_domain %}' ), the_title_attribute( $the_title_attribute_args ) ) ) . '" rel="bookmark"><span>' . get_the_title( $args['post_id'] ) . '</span></a>',
										);
								}

							break;
							case 'tags':

								if (
										apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_post_meta_info_enable_' . $meta, true, $args )
										&& ( $helper = get_the_tag_list( '', ' ', '', $args['post_id'] ) )
									) {
									$replacements = array(
											'{attributes}'  => ( function_exists( '{%= prefix_fn %}_schema_org' ) ) ? ( {%= prefix_fn %}_schema_org( 'keywords' ) ) : ( '' ),
											'{class}'       => esc_attr( 'tags-links entry-meta-element' ),
											'{description}' => '<span class="entry-meta-description">' . esc_html_x( 'Tagged as:', 'Post meta info description: tags list.', '{%= text_domain %}' ) . ' </span>',
											'{content}'     => $helper,
										);
								}

							break;
							case 'views':

								if (
										apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_post_meta_info_enable_' . $meta, true, $args )
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

							$replacements = (array) apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_post_meta_info_replacements_' . $meta, $replacements, $args );

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
		 * @version  1.0.2
		 *
		 * @param  string $tag           Wrapper tag
		 * @param  string $singular_only Display only on singular posts of specific type
		 */
		public static function get_the_paginated_suffix( $tag = '', $singular_only = false ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_the_paginated_suffix_pre', false, $tag, $singular_only );

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
		 * @version  1.0
		 *
		 * @param  mixed $post
		 */
		public static function has_more_tag( $post = null ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_has_more_tag_pre', false, $post );

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
	 * 40) CSS functions
	 */

		/**
		 * Outputs path to the specific file
		 *
		 * This function looks for the file in the child theme first.
		 * If the file is not located in child theme, outputs the path from parent theme.
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  string $file_relative_path File to look for (insert also the theme structure relative path)
		 *
		 * @return  string Actual path to the file
		 */
		public static function get_stylesheet_directory( $file_relative_path ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_stylesheet_directory_pre', false, $file_relative_path );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				$output = '';

				$file_relative_path = trim( $file_relative_path );


			// Requirements chek

				if ( ! $file_relative_path ) {
					return;
				}


			// Processing

				if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $file_relative_path ) ) {
					$output = trailingslashit( get_stylesheet_directory() ) . $file_relative_path;
				} else {
					$output = trailingslashit( get_template_directory() ) . $file_relative_path;
				}


			// Output

				return $output;

		} // /get_stylesheet_directory



		/**
		 * Outputs URL to the specific file
		 *
		 * This function looks for the file in the child theme first.
		 * If the file is not located in child theme, output the URL from parent theme.
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  string $file_relative_path File to look for (insert also the theme structure relative path)
		 *
		 * @return  string Actual URL to the file
		 */
		public static function get_stylesheet_directory_uri( $file_relative_path ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_stylesheet_directory_uri_pre', false, $file_relative_path );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				$output = '';

				$file_relative_path = trim( $file_relative_path );


			// Requirements chek

				if ( ! $file_relative_path ) {
					return;
				}


			// Processing

				if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $file_relative_path ) ) {
					$output = trailingslashit( get_stylesheet_directory_uri() ) . $file_relative_path;
				} else {
					$output = trailingslashit( get_template_directory_uri() ) . $file_relative_path;
				}


			// Output

				return $output;

		} // /get_stylesheet_directory_uri



		/**
		 * CSS minifier
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  string $css Code to minimize
		 */
		public static function minify_css( $css ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_minify_css_pre', false, $css );

				if ( false !== $pre ) {
					return $pre;
				}


			// Requirements check

				if ( ! is_string( $css ) ) {
					return $css;
				}


			// Processing

				// Remove CSS comments

					$css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );

				// Remove tabs, spaces, line breaks, etc.

					$css = str_replace( array( "\r\n", "\r", "\n", "\t" ), '', $css );
					$css = str_replace( array( '  ', '   ', '    ', '     ' ), ' ', $css );
					$css = str_replace( array( ' { ', ': ', '; }' ), array( '{', ':', '}' ), $css );


			// Output

				return $css;

		} // /minify_css



		/**
		 * Hex color to RGBA
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @link  http://php.net/manual/en/function.hexdec.php
		 *
		 * @param  string $hex
		 * @param  absint $alpha [0-100]
		 *
		 * @return  string Color in rgb() or rgba() format to use in CSS.
		 */
		public static function color_hex_to_rgba( $hex, $alpha = 100 ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_color_hex_to_rgba_pre', false, $hex, $alpha );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				$alpha = absint( $alpha );

				$output = ( 100 === $alpha ) ? ( 'rgb(' ) : ( 'rgba(' );

				$rgb = array();

				$hex = preg_replace( '/[^0-9A-Fa-f]/', '', $hex );
				$hex = substr( $hex, 0, 6 );


			// Processing

				// Converting hex color into rgb

					$color = (int) hexdec( $hex );

					$rgb['r'] = (int) 0xFF & ( $color >> 0x10 );
					$rgb['g'] = (int) 0xFF & ( $color >> 0x8 );
					$rgb['b'] = (int) 0xFF & $color;

					$output .= implode( ',', $rgb );

				// Using alpha (rgba)?

					if ( 100 > $alpha ) {
						$output .= ',' . ( $alpha / 100 );
					}

				// Closing opening bracket

					$output .= ')';


			// Output

				return $output;

		} // /color_hex_to_rgba



		/**
		 * Duplicating WordPress native function in case it does not exist yet
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @link  https://developer.wordpress.org/reference/functions/maybe_hash_hex_color/
		 * @link  https://developer.wordpress.org/reference/functions/sanitize_hex_color_no_hash/
		 *
		 * @param  string $color
		 */
		public static function maybe_hash_hex_color( $color ) {

			// Requirements check

				if (
						function_exists( 'maybe_hash_hex_color' )
						&& function_exists( 'sanitize_hex_color_no_hash' )
					) {
					return maybe_hash_hex_color( $color );
				}


			// Helper variables

				// 3 or 6 hex digits, or the empty string.

					if ( preg_match( '|([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
						$color = ltrim( $color, '#' );
					} else {
						$color = '';
					}


			// Processing

				if ( $color ) {
					$color = '#' . $color;
				}


			// Output

				return $color;

		} // /maybe_hash_hex_color



		/**
		 * Replace variables in the custom CSS
		 *
		 * Just use a '___customizer_option_id' tags in your custom CSS
		 * styles string where the specific option value should be used.
		 *
		 * @uses  `wmhook_{%= prefix_hook %}_theme_options` global hook
		 *
		 * @since    1.0
		 * @version  1.3
		 *
		 * @param  string $css CSS string with variables to replace.
		 */
		public static function custom_styles( $css ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_custom_styles_pre', false, $css );

				if ( false !== $pre ) {
					return $pre;
				}


			// Requirements check

				if ( ! ( $css = trim( (string) $css ) ) ) {
					return;
				}


			// Helper variables

				$output = $css;

				$theme_options = (array) apply_filters( 'wmhook_{%= prefix_hook %}_theme_options', array() );
				$alphas        = array_filter( (array) apply_filters( 'wmhook_{%= prefix_hook %}_tf_custom_styles_alphas', array() ) );

				$replacements = array();


			// Processing

				// Setting up replacements array

					if ( ! empty( $theme_options ) ) {
						foreach ( $theme_options as $option ) {

							// Reset variables

								$option_id = $value = '';

							// Set option ID

								if ( isset( $option['id'] ) ) {
									$option_id = $option['id'];
								}

							// If no option ID set, jump to next option

								if ( empty( $option_id ) ) {
									continue;
								}

							// If we have an ID, get the default value if set

								if ( isset( $option['default'] ) ) {
									$value = $option['default'];
								}

							// Get the option value saved in database and apply it when exists

								if ( $mod = self::get_theme_mod( $option_id ) ) {
									$value = $mod;
								}

							// Make sure the color value contains '#'

								if ( 'color' === $option['type'] ) {
									$value = self::maybe_hash_hex_color( $value );
								}

							// Make sure the image URL is used in CSS format

								if ( 'image' === $option['type'] ) {
									if ( is_array( $value ) && isset( $value['id'] ) ) {
										$value = absint( $value['id'] );
									}
									if ( is_numeric( $value ) ) {
										$value = wp_get_attachment_image_src( absint( $value ), 'full' );
										$value = $value[0];
									}
									if ( ! empty( $value ) ) {
										$value = "url('" . esc_url( $value ) . "')";
									} else {
										$value = 'none';
									}
								}

							// Value filtering

								$value = apply_filters( 'wmhook_{%= prefix_hook %}_tf_custom_styles_value', $value, $option );

							// Convert array to string as otherwise the strtr() function throws error

								if ( is_array( $value ) ) {
									$value = (string) implode( ',', (array) $value );
								}

							// Finally, modify the output string

								$replacements[ '___' . str_replace( '-', '_', $option_id ) ] = $value;

								// Add also rgba() color interpratation

									if ( 'color' === $option['type'] && ! empty( $alphas ) ) {
										foreach ( $alphas as $alpha ) {
											$replacements[ '___' . str_replace( '-', '_', $option_id ) . '|alpha=' . absint( $alpha ) ] = self::color_hex_to_rgba( $value, absint( $alpha ) );
										} // /foreach
									}

						} // /foreach
					}

					// Add WordPress Custom Background and Header support

						// Background color

							if ( $value = get_background_color() ) {
								$replacements['___background_color'] = self::maybe_hash_hex_color( $value );

								if ( ! empty( $alphas ) ) {
									foreach ( $alphas as $alpha ) {
										$replacements[ '___background_color|alpha=' . absint( $alpha ) ] = self::color_hex_to_rgba( $value, absint( $alpha ) );
									} // /foreach
								}
							}

						// Background image

							if ( $value = esc_url( get_background_image() ) ) {
								$replacements['___background_image'] = "url('" . esc_url( $value ) . "')";
							} else {
								$replacements['___background_image'] = 'none';
							}

						// Header text color

							if ( $value = get_header_textcolor() ) {
								$replacements['___header_textcolor'] = self::maybe_hash_hex_color( $value );

								if ( ! empty( $alphas ) ) {
									foreach ( $alphas as $alpha ) {
										$replacements[ '___header_textcolor|alpha=' . absint( $alpha ) ] = self::color_hex_to_rgba( $value, absint( $alpha ) );
									} // /foreach
								}
							}

						// Header image

							if ( $value = esc_url( get_header_image() ) ) {
								$replacements['___header_image'] = "url('" . esc_url( $value ) . "')";
							} else {
								$replacements['___header_image'] = 'none';
							}

					$replacements = (array) apply_filters( 'wmhook_{%= prefix_hook %}_tf_custom_styles_replacements', $replacements, $theme_options, $output );

				// Replace tags in custom CSS strings with actual values

					$output = strtr( $output, $replacements );


			// Output

				$output = (string) apply_filters( 'wmhook_{%= prefix_hook %}_tf_custom_styles_output', $output );

				return trim( (string) $output );

		} // /custom_styles



		/**
		 * Generate main CSS file
		 *
		 * @since    1.0
		 * @version  1.3
		 *
		 * @param  array $args
		 */
		public static function generate_main_css( $args = array() ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_generate_main_css_pre', false, $args );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper viariables

				$filesystem = self::get_filesystem();

				$args = wp_parse_args( $args, apply_filters( 'wmhook_{%= prefix_hook %}_tf_generate_main_css_defaults', array(
						'message'        => '<strong>' . esc_html__( "The main theme CSS stylesheet was regenerated. Please refresh your web browser's and server's cache (if you are using a website server caching solution).", '{%= text_domain %}' ) . '</strong>',
						'message_after'  => '',
						'message_before' => '',
						'type'           => '',
					) ) );
				$args = apply_filters( 'wmhook_{%= prefix_hook %}_tf_generate_main_css_args', $args );

				$output = $output_min = '';

				$args['type'] = trim( $args['type'] );


			// Requirements check

				if (
						! $filesystem
						|| ! is_object( $filesystem )
						|| ! is_callable( array( $filesystem, 'mkdir' ) )
						|| ! is_callable( array( $filesystem, 'put_contents' ) )
					) {
					return;
				}


			// Processing

				// Get the file content with output buffering

					ob_start();

					require_once( get_template_directory() . '/assets/css-generate/generate-css' . $args['type'] . '.php' );

					$output = trim( ob_get_clean() );

				// Filter output

					$output = apply_filters( 'wmhook_{%= prefix_hook %}_tf_generate_main_css_output', $output, $args );

				// Requirements check

					if ( ! $output ) {
						return;
					}

				// Minify output if set

					$output_min = apply_filters( 'wmhook_{%= prefix_hook %}_tf_generate_main_css_output_min', $output, $args );

				// Create the theme CSS folder

					$wp_upload_dir = wp_upload_dir();

					$theme_css_url = trailingslashit( $wp_upload_dir['baseurl'] ) . 'wmtheme-{%= theme_slug %}';
					$theme_css_dir = trailingslashit( $wp_upload_dir['basedir'] ) . 'wmtheme-{%= theme_slug %}';

					if (
							! ( file_exists( $theme_css_dir ) && is_dir( $theme_css_dir ) )
							&& ! $filesystem->mkdir( $theme_css_dir, FS_CHMOD_DIR )
						) {

						set_transient(
								'{%= prefix_var %}_admin_notice',
								array(
									'<strong>' . esc_html__( "ERROR: Wasn't able to create a theme CSS folder! Contact the theme support.", '{%= text_domain %}' ) . '</strong>',
									'notice-error',
									'switch_themes',
									2
								),
								( 60 * 60 * 48 )
							);

						remove_theme_mod( '__url_css' . $args['type'] );
						remove_theme_mod( '__path_theme_generated_files' . $args['type'] );

						return false;

					}

				$file_name           = apply_filters( 'wmhook_{%= prefix_hook %}_tf_generate_main_css_file_name',           'global' . $args['type'],                                    $args             );
				$global_css_path     = apply_filters( 'wmhook_{%= prefix_hook %}_tf_generate_main_css_global_css_path',     trailingslashit( $theme_css_dir ) . $file_name . '.css',     $args, $file_name );
				$global_css_url      = apply_filters( 'wmhook_{%= prefix_hook %}_tf_generate_main_css_global_css_url',      trailingslashit( $theme_css_url ) . $file_name . '.css',     $args, $file_name );
				$global_css_path_dev = apply_filters( 'wmhook_{%= prefix_hook %}_tf_generate_main_css_global_css_path_dev', trailingslashit( $theme_css_dir ) . $file_name . '.dev.css', $args, $file_name );

				if (
						$output
						&& $filesystem->put_contents( $global_css_path, $output_min, FS_CHMOD_FILE )
					) {

					$filesystem->put_contents( $global_css_path_dev, $output, FS_CHMOD_FILE );

					// Store the CSS files paths and urls in DB

						set_theme_mod( '__url_css' . $args['type'], $global_css_url );
						set_theme_mod( '__path_theme_generated_files' . $args['type'], str_replace( $wp_upload_dir['basedir'], '', $theme_css_dir ) );
						set_theme_mod( '__theme_installed', true );

					// Admin notice

						set_transient(
								'{%= prefix_var %}_admin_notice',
								array(
									$args['message_before'] . $args['message'] . $args['message_after'],
									'notice-info',
									'switch_themes'
								),
								( 60 * 60 * 24 )
							);

					// Run custom actions

						do_action( 'wmhook_{%= prefix_hook %}_tf_generate_main_css', $args );

					return true;

				}

				remove_theme_mod( '__url_css' . $args['type'] );
				remove_theme_mod( '__path_theme_generated_files' . $args['type'] );

				return false;

		} // /generate_main_css



			/**
			 * Generate editor CSS file
			 *
			 * @since    1.3
			 * @version  1.3
			 */
			public static function generate_main_css_editor() {

				// Output

					self::generate_main_css( array( 'type' => '-editor' ) );

			} // /generate_main_css_editor



			/**
			 * Generate RTL CSS file
			 *
			 * @since    1.3
			 * @version  1.3
			 */
			public static function generate_main_css_rtl() {

				// Output

					if ( apply_filters( 'wmhook_{%= prefix_hook %}_enable_rtl', false ) ) {
						self::generate_main_css( array( 'type' => '-rtl' ) );
						self::generate_main_css( array( 'type' => '-editor-rtl' ) );
					}

			} // /generate_main_css_rtl



			/**
			 * Generate all CSS files
			 *
			 * @since    1.3
			 * @version  1.3
			 */
			public static function generate_main_css_all() {

				// Output

					if ( self::generate_main_css() ) {
						self::generate_main_css_editor();
						self::generate_main_css_rtl();
					}

			} // /generate_main_css_all





	/**
	 * 50) Options
	 */

		/**
		 * Get the theme option
		 *
		 * Note: Do not use get_theme_mod() as it is not
		 * transferable from "lite" to "pro" themes.
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  string $id     Option ID
		 * @param  string $type   Output format
		 * @param  string $suffix
		 *
		 * @return  mixed Option value.
		 */
		public static function get_theme_mod( $id, $type = '', $suffix = '' ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_theme_mod_pre', false, $id, $type, $suffix );

				if ( false !== $pre ) {
					return $pre;
				}


			// Requirements check

				$id = trim( $id );

				if ( ! $id ) {
					return;
				}


			// Helper variables

				global ${%= prefix_var %}_theme_options, $wp_customize;

				$output = '';

				if (
						! isset( ${%= prefix_var %}_theme_options )
						|| (
								isset( $wp_customize )
								&& $wp_customize->is_preview()
							)
					) {
					${%= prefix_var %}_theme_options = null;
				}

				$options = ( ${%= prefix_var %}_theme_options ) ? ( ${%= prefix_var %}_theme_options ) : ( (array) get_option( {%= prefix_constant %}_OPTION_CUSTOMIZER ) );


			// Processing

				if (
						! isset( $options[ $id ] )
						|| ! $options[ $id ]
					) {
					return;
				}

				// Output formatter

					if ( 'css_image_url' === $type ) {
						$output = "url('" . esc_url( stripslashes( $options[ $id ] ) ) . "')";
					} elseif ( 'color_hex' === $type ) {
						$output = '#' . trim( stripslashes( $options[ $id ] ), '#' );
					} else {
						$output = ( is_array( $options[ $id ] ) ) ? ( $options[ $id ] ) : ( stripslashes( $options[ $id ] ) );
					}

				// Add suffix

					if ( is_string( $output ) && $output ) {
						$output .= (string) $suffix;
					}


			// Output

				return apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_theme_mod_output', $output, $id, $type, $suffix );

		} // /get_theme_mod





	/**
	 * 60) Filesystem
	 */

		/**
		 * Get WP_Filesystem
		 *
		 * Possible filesystem methods: 'direct', 'ssh2', 'ftpext' or 'ftpsockets'.
		 *
		 * No need to use `request_filesystem_credentials()` if using 'direct' method.
		 * @see  http://aquagraphite.com/2012/11/using-wp_filesystem-to-generate-dynamic-css/
		 *
		 * If not using 'direct' method, display an admin notice about setting up
		 * the FTP credentials in `wp-config.php`.
		 * @see  http://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants
		 *
		 * @see  https://codex.wordpress.org/Filesystem_API
		 * @see  http://ottopress.com/2011/tutorial-using-the-wp_filesystem/
		 * @see  http://wordpress.findincity.net/view/63538464303732726692954/using-wpfilesystem-in-plugins-to-store-customizer-settings
		 *
		 * @since    1.0
		 * @version  1.2.1
		 */
		public static function get_filesystem() {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_filesystem_pre', false );

				if ( false !== $pre ) {
					return $pre;
				}


			// Requirements check

				// Require the WordPress filesystem functionality if not found

					if (
							! function_exists( 'get_filesystem_method' )
							&& ABSPATH
						) {
						require_once( ABSPATH . '/wp-admin/includes/file.php' );
					}

				// Check the filesystem method

					if (
							'direct' !== get_filesystem_method()
							&& ! defined( 'FTP_USER' )
						) {

						// If we don't have filesystem access, display an admin notice

							set_transient(
									'{%= prefix_var %}_admin_notice',
									array(
										esc_html__( 'The theme writes a files to your server. You do not appear to have your FTP credentials set up in "wp-config.php" file.', '{%= text_domain %}' ) . ' <a href="http://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants" target="_blank">' . esc_html__( 'Please set your FTP credentials first.', '{%= text_domain %}' ) . '</a>',
										'notice-error',
										'switch_themes'
									),
									( 60 * 60 * 24 )
								);

						return false;

					}


			// Processing

				WP_Filesystem();

				global $wp_filesystem;


			// Output

				return $wp_filesystem;

		} // /get_filesystem





	/**
	 * 100) Helpers
	 */

		/**
		 * Fixing URLs in `is_ssl()` returns TRUE
		 *
		 * @since    1.3
		 * @version  1.3
		 *
		 * @param  string $content
		 */
		static public function fix_ssl_urls( $content ) {

			// Processing

				if ( is_ssl() ) {
					$content = str_ireplace( 'http:', 'https:', $content );
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
		 * @version  1.0
		 *
		 * @param  string $content
		 */
		public static function remove_shortcodes( $content ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_remove_shortcodes_pre', false, $content );

				if ( false !== $pre ) {
					return $pre;
				}


			// Output

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
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  string $title
		 */
		public static function html_widget_title( $title ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_html_widget_title_pre', false, $title );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				$replacements = array(
						'[' => '<',
						']' => '>',
					);

				$allowed_tags = array(
						'a'      => array( 'href' => array() ),
						'abbr'   => array(),
						'br'     => array(),
						'code'   => array(),
						'del'    => array(),
						'em'     => array(),
						'ins'    => array(),
						'mark'   => array(),
						'q'      => array(),
						's'      => array(),
						'small'  => array(),
						'span'   => array( 'class' => array() ),
						'strong' => array(),
						'sub'    => array(),
						'sup'    => array(),
					);


			// Output

				return wp_kses( strtr( $title, $replacements ), $allowed_tags );

		} // /html_widget_title



		/**
		 * Accessibility skip links
		 *
		 * @since    1.0
		 * @version  1.0.9
		 *
		 * @param  string $id     Link target element ID.
		 * @param  string $text   Link text.
		 * @param  string $class  Additional link CSS classes.
		 */
		public static function link_skip_to( $id = 'content', $text = '', $class = '' ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_link_skip_to_pre', false, $id, $text, $class );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				if ( empty( $text ) ) {
					$text = __( 'Skip to content', '{%= text_domain %}' );
				}


			// Output

				return '<a class="' . esc_attr( trim( 'skip-link screen-reader-text ' . $class ) ) . '" href="#' . esc_attr( trim( $id ) ) . '">' . esc_html( $text ) . '</a>';

		} // /link_skip_to



		/**
		 * Contextual help text
		 *
		 * Hook onto `wmhook_{%= prefix_hook %}_tf_contextual_help_texts_array` to add help texts.
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
		 * @version  1.0
		 *
		 * @param  string    $contextual_help  Help text that appears on the screen.
		 * @param  string    $screen_id        Screen ID.
		 * @param  WP_Screen $screen           Current WP_Screen instance.
		 */
		public static function contextual_help( $contextual_help, $screen_id, $screen ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_contextual_help_pre', false, $contextual_help, $screen_id, $screen );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				$texts_array = array_filter( (array) apply_filters( 'wmhook_{%= prefix_hook %}_tf_contextual_help_texts_array', array() ) );


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
		 * @version  1.0
		 *
		 * @link  http://pippinsplugins.com/retrieve-attachment-id-from-image-url/
		 * @link  http://make.wordpress.org/core/2012/12/12/php-warning-missing-argument-2-for-wpdb-prepare/
		 *
		 * @param  string $url
		 */
		public static function get_image_id_from_url( $url ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_get_image_id_from_url_pre', false, $url );

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
		 * @version  1.3
		 */
		public static function is_categorized_blog() {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_is_categorized_blog_pre', false );

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





} // /{%= prefix_class %}_Theme_Framework
