<?php
/**
 * Visual Editor class
 *
 * This is a helper class and does not load automatically with the library.
 * Load it directly from within your theme's `functions.php` file.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Visual Editor
 *
 * @since    1.0
 * @version  1.9
 *
 * Contents:
 *
 *  0) Init
 * 10) Buttons
 * 20) Custom formats
 * 30) Body class
 */
final class {%= prefix_class %}_Theme_Framework_Visual_Editor {





	/**
	 * 0) Init
	 */

		private static $instance;



		/**
		 * Constructor
		 *
		 * @since    1.0
		 * @version  1.7.2
		 */
		private function __construct() {

			// Processing

				// Hooks

					// Actions

						// Editor body class on page template change

							if ( is_admin() ) {
								add_action( 'admin_enqueue_scripts', __CLASS__ . '::scripts_post_edit', 1000 );
							}

					// Filters

						// Editor body class

							if ( is_admin() ) {
								add_filter( 'tiny_mce_before_init', __CLASS__ . '::body_class' );
							}

						// Editor addons

							add_filter( 'mce_buttons', __CLASS__ . '::add_buttons_row1' );

							add_filter( 'mce_buttons_2', __CLASS__ . '::add_buttons_row2' );

							add_filter( 'tiny_mce_before_init', __CLASS__ . '::custom_mce_format' );

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
	 * 10) Buttons
	 */

		/**
		 * Add buttons to visual editor
		 *
		 * First row.
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  array $buttons
		 */
		public static function add_buttons_row1( $buttons ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_editor_add_buttons_row1_pre', false, $buttons );

				if ( false !== $pre ) {
					return $pre;
				}


			// Processing

				// Inserting buttons after "more" button

					$pos = array_search( 'wp_more', $buttons, true );

					if ( false !== $pos ) {
						$add     = array_slice( $buttons, 0, $pos + 1 );
						$add[]   = 'wp_page';
						$buttons = array_merge( $add, array_slice( $buttons, $pos + 1 ) );
					}


			// Output

				return $buttons;

		} // /add_buttons_row1



		/**
		 * Add buttons to visual editor
		 *
		 * Second row.
		 *
		 * @since    1.0
		 * @version  1.0
		 *
		 * @param  array $buttons
		 */
		public static function add_buttons_row2( $buttons ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_editor_add_buttons_row2_pre', false, $buttons );

				if ( false !== $pre ) {
					return $pre;
				}


			// Processing

				// Inserting buttons at the beginning of the row

					array_unshift( $buttons, 'styleselect' );


			// Output

				return $buttons;

		} // /add_buttons_row2





	/**
	 * 20) Custom formats
	 */

		/**
		 * Customizing format dropdown items
		 *
		 * @link  http://codex.wordpress.org/TinyMCE_Custom_Styles
		 * @link  http://www.tinymce.com/wiki.php/Configuration:style_formats
		 *
		 * @since    1.0
		 * @version  1.4
		 *
		 * @param  array $init
		 */
		public static function custom_mce_format( $init ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_tf_editor_custom_mce_format_pre', false, $init );

				if ( false !== $pre ) {
					return $pre;
				}


			// Processing

				// Add custom formats

					$style_formats = (array) apply_filters( 'wmhook_{%= prefix_hook %}_tf_editor_custom_mce_format', array(

							// Group: Text styles

								100 . 'text_styles' => array(
									'title' => esc_html__( 'Text styles', '{%= text_domain %}' ),
									'items' => array(

										100 . 'text_styles' . 100 => array(
											'title'    => esc_html__( 'Dropcap text', '{%= text_domain %}' ),
											'selector' => 'p',
											'classes'  => 'dropcap-text',
										),

										100 . 'text_styles' . 110 => array(
											'title'    => esc_html__( 'Uppercase heading or paragraph', '{%= text_domain %}' ),
											'selector' => 'h1, h2, h3, h4, h5, h6, p',
											'classes'  => 'uppercase',
										),

										100 . 'text_styles' . 120 => array(
											'title'  => esc_html__( 'Highlighted (marked) text', '{%= text_domain %}' ),
											'inline' => 'mark',
											'icon'   => 'backcolor',
										),

										100 . 'text_styles' . 130 => array(
											'title'  => esc_html__( 'Small text', '{%= text_domain %}' ),
											'inline' => 'small',
										),

										100 . 'text_styles' . 140 => array(
											'title'  => esc_html__( 'Superscript', '{%= text_domain %}' ),
											'icon'   => 'superscript',
											'format' => 'superscript',
										),

										100 . 'text_styles' . 150 => array(
											'title'  => esc_html__( 'Subscript', '{%= text_domain %}' ),
											'icon'   => 'subscript',
											'format' => 'subscript',
										),

										100 . 'text_styles' . 160 => array(
											'title'    => sprintf( esc_html_x( 'Heading %d text style', '%d = HTML heading size number.', '{%= text_domain %}' ), 1 ),
											'selector' => 'h2, h3, h4, h5, h6, p',
											'classes'  => 'h1',
										),

										100 . 'text_styles' . 170 => array(
											'title'    => sprintf( esc_html_x( 'Heading %d text style', '%d = HTML heading size number.', '{%= text_domain %}' ), 2 ),
											'selector' => 'h1, h3, h4, h5, h6, p',
											'classes'  => 'h2',
										),

										100 . 'text_styles' . 180 => array(
											'title'    => sprintf( esc_html_x( 'Heading %d text style', '%d = HTML heading size number.', '{%= text_domain %}' ), 3 ),
											'selector' => 'h1, h2, h4, h5, h6, p',
											'classes'  => 'h3',
										),

									),
								),

							// Group: Text size

								200 . 'text_sizes' => array(
									'title' => esc_html__( 'Text sizes', '{%= text_domain %}' ),
									'items' => array(

										200 . 'text_sizes' . 100 => array(
											'title'    => sprintf( esc_html_x( 'Display %d', '%d: Display text size number.', '{%= text_domain %}' ), 1 ),
											'selector' => 'h1, h2, h3, h4, h5, h6, p',
											'classes'  => 'display-1',
										),

										200 . 'text_sizes' . 110 => array(
											'title'    => sprintf( esc_html_x( 'Display %d', '%d: Display text size number.', '{%= text_domain %}' ), 2 ),
											'selector' => 'h1, h2, h3, h4, h5, h6, p',
											'classes'  => 'display-2',
										),

										200 . 'text_sizes' . 120 => array(
											'title'    => sprintf( esc_html_x( 'Display %d', '%d: Display text size number.', '{%= text_domain %}' ), 3 ),
											'selector' => 'h1, h2, h3, h4, h5, h6, p',
											'classes'  => 'display-3',
										),

										200 . 'text_sizes' . 130 => array(
											'title'    => sprintf( esc_html_x( 'Display %d', '%d: Display text size number.', '{%= text_domain %}' ), 4 ),
											'selector' => 'h1, h2, h3, h4, h5, h6, p',
											'classes'  => 'display-4',
										),

									),
								),

							// Group: Quotes

								300 . 'quotes' => array(
									'title' => esc_html_x( 'Quotes', 'Visual editor blockquote formats group title.', '{%= text_domain %}' ),
									'items' => array(

										300 . 'quotes' . 100 => array(
											'title' => esc_html__( 'Blockquote', '{%= text_domain %}' ),
											'block' => 'blockquote',
											'icon'  => 'blockquote',
										),

										300 . 'quotes' . 110 => array(
											'title'   => esc_html__( 'Pullquote - align left', '{%= text_domain %}' ),
											'block'   => 'blockquote',
											'classes' => 'pullquote alignleft',
											'icon'    => 'alignleft',
										),

										300 . 'quotes' . 120 => array(
											'title'   => esc_html__( 'Pullquote - align right', '{%= text_domain %}' ),
											'block'   => 'blockquote',
											'classes' => 'pullquote alignright',
											'icon'    => 'alignright',
										),

										300 . 'quotes' . 130 => array(
											'title'  => esc_html_x( 'Cite', 'Visual editor format label for HTML CITE tag used to set the blockquote source.', '{%= text_domain %}' ),
											'inline' => 'cite',
										),

									),
								),

						) );

					ksort( $style_formats );

						foreach ( $style_formats as $group_key => $group ) {
							if ( isset( $group['items'] ) ) {

								ksort( $group['items'] );
								$style_formats[ $group_key ]['items'] = $group['items'];

							}
						} // /foreach

					if ( ! empty( $style_formats ) ) {

						// Merge old & new formats

							$init['style_formats_merge'] = false;

						// New formats

							$init['style_formats'] = json_encode( $style_formats );

					}

				// Removing obsolete tags (this is localized already)

					$heading_1 = ( ! is_admin() ) ? ( 'Heading 1=h1;' ) : ( '' ); // Accounting for page builders front-end editing when page title is disabled

					$init['block_formats'] = 'Paragraph=p;' . $heading_1 . 'Heading 2=h2;Heading 3=h3;Heading 4=h4;Address=address;Preformatted=pre;Code=code';


			// Output

				return $init;

		} // /custom_mce_format





	/**
	 * 30) Body class
	 */

		/**
		 * Adding editor HTML body classes
		 *
		 * @since    1.7.2
		 * @version  1.7.2
		 *
		 * @param  array $init
		 */
		public static function body_class( $init ) {

			// Requirements check

				global $post;

				if ( ! isset( $post ) ) {
					return $init;
				}


			// Helper variables

				$class    = array();
				$template = get_page_template_slug( $post );


			// Processing

				// Setting custom classes

					// Adding `.entry-content` class for compatibility with `main.css` styles

						$class[] = 'entry-content';

					// Page template class

						if ( $template ) {
							$class[] = 'page-template-' . sanitize_html_class( basename( $template, '.php' ) );
						}

				// Adding custom classes

					$init['body_class'] = $init['body_class'] . ' ' . implode( ' ', $class );


			// Output

				return $init;

		} // /body_class



		/**
		 * Adding scripts to post edit screen
		 *
		 * @since    1.7.2
		 * @version  1.9
		 *
		 * @param  string $hook_suffix
		 */
		public static function scripts_post_edit( $hook_suffix = '' ) {

			// Requirements check

				$current_screen = get_current_screen();

				if ( isset( $current_screen->base ) && 'post' != $current_screen->base ){
					return;
				}


			// Processing

				// Scripts

					wp_enqueue_script(
							'{%= prefix_var %}-post-edit',
							{%= prefix_class %}_Theme_Framework::get_stylesheet_directory_uri( 'library/js/post.js' ),
							array( 'jquery' ),
							esc_attr( {%= prefix_constant %}_THEME_VERSION ),
							true
						);

		} // /scripts_post_edit





} // /{%= prefix_class %}_Theme_Framework_Visual_Editor

add_action( 'init', '{%= prefix_class %}_Theme_Framework_Visual_Editor::init' );
