<?php defined( 'ABSPATH' ) || exit;
/**
 * Core class
 *
 * @subpackage  Core
 *
 * @package    WebMan WordPress Theme Framework
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  2.8.0
 *
 * Contents:
 *
 *   0) Init
 *  10) Theme upgrade action
 *  20) Post/page
 * 100) Helpers
 */
class Theme_Slug_Library {





	/**
	 * 0) Init
	 */

		/**
		 * Initialization.
		 *
		 * @since    1.0.0
		 * @version  2.8.0
		 */
		public static function init() {

			// Processing

				// Hooks

					// Actions

						add_action( 'init', __CLASS__ . '::theme_upgrade' );

						add_action( 'edit_category', __CLASS__ . '::all_categories_transient_flusher' );
						add_action( 'save_post',     __CLASS__ . '::all_categories_transient_flusher' );

					// Filters

						add_filter( 'show_recent_comments_widget_style', '__return_false' );

						add_filter( 'the_content', __CLASS__ . '::add_table_of_contents' );

		} // /init





	/**
	 * 10) Theme upgrade action
	 */

		/**
		 * Do action on theme version change
		 *
		 * @since    1.0.0
		 * @version  2.8.0
		 */
		public static function theme_upgrade() {

			// Variables

				$current_theme_version = get_transient( 'theme-slug_version' );
				$new_theme_version     = wp_get_theme( 'theme-slug' )->get( 'Version' );


			// Processing

				if (
					empty( $current_theme_version )
					|| $new_theme_version != $current_theme_version
				) {
					do_action( 'hook/theme_slug/Library/theme_upgrade', $new_theme_version, $current_theme_version );
					set_transient( 'theme-slug_version', $new_theme_version );
				}

		} // /theme_upgrade





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
		 * @since    1.0.0
		 * @version  2.7.0
		 *
		 * @param  string $content
		 */
		public static function add_table_of_contents( $content = '' ) {

			// Variables

				global $page, $numpages, $multipage, $post;

				// Requirements check

					if (
						! $multipage
						|| ! is_singular()
					) {
						return $content;
					}

				/**
				 * Filters post table of content title.
				 *
				 * @since  2.8.0
				 *
				 * @param  string $title_text
				 */
				$title_text = (string) apply_filters( 'hook/theme_slug/Library/add_table_of_contents/title_text', sprintf(
					esc_html_x( '"%s" table of contents', '%s: post title.', 'theme-slug' ),
					the_title_attribute( 'echo=0' )
				) );

				/**
				 * Filters post table of content setup arguments.
				 *
				 * @example
				 *   array(
				 *     'disable_first' => true,
				 *     'links'         => array(),
				 *     'post_content'  => ( isset( $post->post_content ) ) ? ( $post->post_content ) : ( '' ),
				 *     'tag'           => 'h2',
				 *   )
				 *
				 * @since  2.8.0
				 *
				 * @param  array $args
				 */
				$args = (array) apply_filters( 'hook/theme_slug/Library/add_table_of_contents/args', array(
					'disable_first' => true, // First part to have a title of the post (part title won't be parsed)?
					'links'         => array(), // The output HTML links.
					'post_content'  => ( isset( $post->post_content ) ) ? ( $post->post_content ) : ( '' ), // Get the whole post content.
					'tag'           => 'h2', // HTML heading tag to parse as a post part title.
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
									$part_title = sprintf( esc_html__( 'Page %s', 'theme-slug' ), number_format_i18n( $i ) );
								} else {
									$part_title = $matches[2];
								}

							}

						// Set post part class

							if ( $page === $i ) {
								$class = ' class="is-current"';
							} elseif ( $page > $i ) {
								$class = ' class="is-passed"';
							} else {
								$class = '';
							}

						// Post part item output

							/**
							 * Filters post table of content single part HTML output.
							 *
							 * @since  2.8.0
							 *
							 * @param  string $single_part
							 * @param  int    $i
							 * @param  string $part_title
							 * @param  string $class
							 * @param  array  $args
							 */
							$args['links'][$i] = (string) apply_filters( 'hook/theme_slug/Library/add_table_of_contents/part', '<li' . $class . '>' . _wp_link_page( $i ) . $part_title . '</a></li>', $i, $part_title, $class, $args );

					}

				// Add table of contents into the post/page content

					$args['links'] = implode( '', $args['links'] );

					/**
					 * Filters post table of content output array.
					 *
					 * @example
					 *   array(
					 *     'before' => '<nav class="post-table-of-contents before-content"><ol>' . $args['links'] . '</ol></nav>',
					 *     'after'  => '<nav class="post-table-of-contents after-content"><ol>' . $args['links'] . '</ol></nav>',
					 *   )
					 *
					 * @since  2.8.0
					 *
					 * @param  array $table_of_content
					 * @param  array $args
					 */
					$table_of_content = (array) apply_filters( 'hook/theme_slug/Library/add_table_of_contents/links', array(
						'before' => ( 1 === $page ) ? ( '<nav class="post-table-of-contents top" title="' . esc_attr( wp_strip_all_tags( $title_text ) ) . '" aria-label="' . esc_attr( wp_strip_all_tags( $title_text ) ) . '"><ol>' . $args['links'] . '</ol></nav>' ) : ( '' ),
						'after'  => '<nav class="post-table-of-contents bottom" title="' . esc_attr( wp_strip_all_tags( $title_text ) ) . '" aria-label="' . esc_attr( wp_strip_all_tags( $title_text ) ) . '"><ol>' . $args['links'] . '</ol></nav>',
					), $args );

					$content = $table_of_content['before'] . $content . $table_of_content['after'];


			// Output

				return $content;

		} // /add_table_of_contents



		/**
		 * Get the paginated heading suffix.
		 *
		 * @subpackage  Pagination
		 *
		 * @since    1.0.0
		 * @version  2.6.0
		 *
		 * @param  string $tag            Wrapper tag.
		 * @param  mixed  $singular_only  Display only on singular posts of specific type.
		 */
		public static function get_the_paginated_suffix( $tag = '', $singular_only = false ) {

			// Pre

				/**
				 * Bypass filter for Theme_Slug_Library::get_the_paginated_suffix().
				 *
				 * @since  2.8.0
				 *
				 * @param  mixed  $pre            Default: false. If not false, method returns this value.
				 * @param  string $tag            Wrapper tag.
				 * @param  mixed  $singular_only  Display only on singular posts of specific type.
				 */
				$pre = apply_filters( 'hook/theme_slug/Library/get_the_paginated_suffix/pre', false, $tag, $singular_only );

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


			// Variables

				global $page, $paged;

				$output    = '';
				$paginated = max( absint( $page ), absint( $paged ) );

				$tag = trim( $tag );
				if ( $tag ) {
					$tag = array( '<' . tag_escape( $tag ) . '>', '</' . tag_escape( $tag ) . '>' );
				} else {
					$tag = array( '', '' );
				}


			// Processing

				if ( 1 < $paginated ) {
					$output = ' ' . $tag[0] . sprintf( esc_html_x( '(page %s)', 'Paginated content title suffix, %s: page number.', 'theme-slug' ), number_format_i18n( $paginated ) ) . $tag[1];
				}


			// Output

				return $output;

		} // /get_the_paginated_suffix



			/**
			 * Display the paginated heading suffix
			 *
			 * @subpackage  Pagination
			 *
			 * @since    1.0.0
			 * @version  1.0.0
			 *
			 * @param  string $tag            Wrapper tag.
			 * @param  string $singular_only  Display only on singular posts of specific type.
			 */
			public static function the_paginated_suffix( $tag = '', $singular_only = false ) {

				// Variables

					$output = self::get_the_paginated_suffix( $tag, $singular_only );


				// Output

					if ( $output ) {
						echo $output; // WPCS: XSS OK.
					}

			} // /the_paginated_suffix



		/**
		 * Checks for more tag in post content.
		 *
		 * If more tag present, also retrieve its custom text value.
		 *
		 * @since    1.0.0
		 * @version  2.8.0
		 *
		 * @param  mixed $post
		 */
		public static function has_more_tag( $post = null ) {

			// Pre

				/**
				 * Bypass filter for Theme_Slug_Library::has_more_tag().
				 *
				 * @since  2.8.0
				 *
				 * @param  mixed $pre  Default: null. If not null, method returns the value.
				 */
				$pre = apply_filters( 'hook/theme_slug/Library/has_more_tag/pre', null, $post );

				if ( null !== $pre ) {
					return $pre;
				}


			// Variables

				$output = false;

				if ( empty( $post ) ) {
					$post = $GLOBALS['post'];
				} elseif ( is_numeric( $post ) ) {
					$post = get_post( $post );
				}


			// Requirements check

				if ( ! $post instanceof WP_Post ) {
					return;
				}


			// Processing

				if ( preg_match( '/<!--more(.*?)?-->/', $post->post_content, $matches ) ) {
					$output = true;
					if ( ! empty( $matches[1] ) ) {
						$output = strip_tags( wp_kses_no_null( trim( $matches[1] ) ) );
					}
				}


			// Output

				return $output;

		} // /has_more_tag





	/**
	 * 100) Helpers
	 */

		/**
		 * Fixing URLs according to `is_ssl()` function return.
		 *
		 * @since    1.3.0
		 * @version  2.8.0
		 *
		 * @param  string $content
		 */
		static public function fix_ssl_urls( $content ) {

			// Processing

				if ( is_ssl() ) {
					$content = str_ireplace( 'http:', 'https:', $content );
				} else {
					$content = str_ireplace( 'https:', 'http:', $content );
				}

				// Has to be `http:` only.
				$content = str_ireplace( 'xmlns="https:', 'xmlns="http:', $content );
				$content = str_ireplace( "xmlns='https:", "xmlns='http:", $content );


			// Output

				return $content;

		} // /fix_ssl_urls



		/**
		 * Remove shortcodes from string
		 *
		 * This function keeps the text between shortcodes,
		 * unlike WordPress native strip_shortcodes() function.
		 *
		 * @since    1.0.0
		 * @version  2.7.0
		 *
		 * @param  string $content
		 */
		public static function remove_shortcodes( $content ) {

			// Output

				return preg_replace( '|\[(.+?)\]|s', '', $content );

		} // /remove_shortcodes



		/**
		 * Accessibility skip links
		 *
		 * @since    1.0.0
		 * @version  2.8.0
		 *
		 * @param  string $id     Link target element ID.
		 * @param  string $text   Link text.
		 * @param  string $class  Additional link CSS classes.
		 * @param  string $html   Output html, use "%s" for actual link output.
		 */
		public static function link_skip_to( $id = 'content', $text = '', $class = '', $html = '%s' ) {

			// Pre

				/**
				 * Bypass filter for Theme_Slug_Library::link_skip_to().
				 *
				 * @since  2.8.0
				 *
				 * @param  mixed  $pre    Default: false. If not false, method returns this value.
				 * @param  string $id     Link target element ID.
				 * @param  string $text   Link text.
				 * @param  string $class  Additional link CSS classes.
				 * @param  string $html   Output html, use "%s" for actual link output.
				 */
				$pre = apply_filters( 'hook/theme_slug/Library/link_skip_to/pre', false, $id, $text, $class, $html );

				if ( false !== $pre ) {
					return $pre;
				}


			// Processing

				if ( empty( $text ) ) {
					$text = __( 'Skip to main content', 'theme-slug' );
				}


			// Output

				return sprintf(
					(string) $html,
					'<a class="' . esc_attr( trim( 'skip-link screen-reader-text ' . $class ) ) . '" href="#' . esc_attr( trim( $id ) ) . '">' . esc_html( $text ) . '</a>'
				);

		} // /link_skip_to



		/**
		 * Returns true if a blog has more than 1 category
		 *
		 * @since    1.0.0
		 * @version  2.7.0
		 */
		public static function is_categorized_blog() {

			// Pre

				/**
				 * Bypass filter for Theme_Slug_Library::is_categorized_blog().
				 *
				 * @since  2.8.0
				 *
				 * @param  mixed $pre  Default: null. If not null, method returns the value.
				 */
				$pre = apply_filters( 'hook/theme_slug/Library/is_categorized_blog/pre', null );

				if ( null !== $pre ) {
					return $pre;
				}


			// Processing

				if ( false === ( $all_cats = get_transient( 'theme_slug_all_categories' ) ) ) {

					$all_cats = get_categories( array(
						'fields'     => 'ids',
						'hide_empty' => true,
						'number'     => 2, // We only need to know if there is more than one category.
					) );

					$all_cats = count( $all_cats );

					set_transient( 'theme_slug_all_categories', $all_cats );

				}


			// Output

				if ( $all_cats > 1 ) {
					return true;
				} else {
					return false;
				}

		} // /is_categorized_blog



			/**
			 * Flush out the transients used in `is_categorized_blog`
			 *
			 * @since    1.0.0
			 * @version  1.0.0
			 */
			public static function all_categories_transient_flusher() {

				// Requirements check

					if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
						return;
					}


				// Processing

					delete_transient( 'theme_slug_all_categories' );

			} // /all_categories_transient_flusher





} // /Theme_Slug_Library

add_action( 'after_setup_theme', 'Theme_Slug_Library::init', -50 );
