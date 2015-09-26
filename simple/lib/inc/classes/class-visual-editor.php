<?php
/**
 * Visual Editor class
 *
 * @package     WebMan WordPress Theme Framework (Simple)
 * @subpackage  Visual Editor
 *
 * @since    2.0
 * @version  5.0
 */





/**
 * Visual Editor class
 *
 * @since    5.0
 * @version  5.0
 *
 * Contents:
 *
 *  0) Init
 * 10) Buttons
 * 20) Custom formats
 */
final class {%= prefix_class %}_Theme_Framework_Visual_Editor {





	/**
	 * 0) Init
	 */

		private static $instance;



		/**
		 * Constructor
		 *
		 * @since    5.0
		 * @version  5.0
		 */
		private function __construct() {

			// Processing

				/**
				 * Hooks
				 */

					/**
					 * Filters
					 */

						// Visual Editor addons

							add_filter( 'mce_buttons',          array( $this, 'add_buttons_row1' )  );
							add_filter( 'mce_buttons_2',        array( $this, 'add_buttons_row2' )  );
							add_filter( 'tiny_mce_before_init', array( $this, 'custom_mce_format' ) );

		} // /__construct



		/**
		 * Initialization (get instance)
		 *
		 * @since    5.0
		 * @version  5.0
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
		 * @version  5.0
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
		 * @version  5.0
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
		 * @version  5.0
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

				// Merge old & new formats

					$init['style_formats_merge'] = false;

				// Add custom formats

					$init['style_formats'] = json_encode( apply_filters( 'wmhook_{%= prefix_hook %}_tf_editor_custom_mce_format', array(

							// Group: Text styles

								array(
									'title' => esc_html__( 'Text styles', '{%= text_domain %}' ),
									'items' => array(

										array(
											'title'    => __( 'Dropcap text', '{%= text_domain %}' ),
											'selector' => 'p',
											'classes'  => 'dropcap-text',
										),

										array(
											'title'    => esc_html__( 'Uppercase heading or paragraph', '{%= text_domain %}' ),
											'selector' => 'h2, h3, h4, h5, h6, p',
											'classes'  => 'uppercase',
										),

										array(
											'title'  => esc_html__( 'Highlighted (marked) text', '{%= text_domain %}' ),
											'inline' => 'mark',
											'icon'   => 'backcolor',
										),

										array(
											'title'  => esc_html__( 'Small text', '{%= text_domain %}' ),
											'inline' => 'small',
										),

										array(
											'title'  => esc_html__( 'Superscript', '{%= text_domain %}' ),
											'icon'   => 'superscript',
											'format' => 'superscript',
										),

										array(
											'title'  => esc_html__( 'Subscript', '{%= text_domain %}' ),
											'icon'   => 'subscript',
											'format' => 'subscript',
										),

										array(
											'title'    => sprintf( esc_html_x( 'Heading %d text style', '%d = HTML heading size number.', '{%= text_domain %}' ), 1 ),
											'selector' => 'h2, h3, h4, h5, h6, p',
											'classes'  => 'h1',
										),

										array(
											'title'    => sprintf( esc_html_x( 'Heading %d text style', '%d = HTML heading size number.', '{%= text_domain %}' ), 2 ),
											'selector' => 'h3, h4, h5, h6, p',
											'classes'  => 'h2',
										),

										array(
											'title'    => sprintf( esc_html_x( 'Heading %d text style', '%d = HTML heading size number.', '{%= text_domain %}' ), 3 ),
											'selector' => 'h2, h4, h5, h6, p',
											'classes'  => 'h3',
										),

									),
								),

							// Group: Text size

								array(
									'title' => esc_html__( 'Text sizes', '{%= text_domain %}' ),
									'items' => array(

										array(
											'title'    => sprintf( esc_html_x( 'Display %d', '%d: Display text size number.', '{%= text_domain %}' ), 1 ),
											'selector' => 'h2, h3, h4, h5, h6, p',
											'classes'  => 'display-1',
										),

										array(
											'title'    => sprintf( esc_html_x( 'Display %d', '%d: Display text size number.', '{%= text_domain %}' ), 2 ),
											'selector' => 'h2, h3, h4, h5, h6, p',
											'classes'  => 'display-2',
										),

										array(
											'title'    => sprintf( esc_html_x( 'Display %d', '%d: Display text size number.', '{%= text_domain %}' ), 3 ),
											'selector' => 'h2, h3, h4, h5, h6, p',
											'classes'  => 'display-3',
										),

										array(
											'title'    => sprintf( esc_html_x( 'Display %d', '%d: Display text size number.', '{%= text_domain %}' ), 4 ),
											'selector' => 'h2, h3, h4, h5, h6, p',
											'classes'  => 'display-4',
										),

									),
								),

							// Group: Quotes

								array(
									'title' => esc_html_x( 'Quotes', 'Visual editor blockquote formats group title.', '{%= text_domain %}' ),
									'items' => array(

										array(
											'title' => esc_html__( 'Blockquote', '{%= text_domain %}' ),
											'block' => 'blockquote',
											'icon'  => 'blockquote',
										),

										array(
											'title'   => esc_html__( 'Pullquote - align left', '{%= text_domain %}' ),
											'block'   => 'blockquote',
											'classes' => 'pullquote alignleft',
											'icon'    => 'alignleft',
										),

										array(
											'title'   => esc_html__( 'Pullquote - align right', '{%= text_domain %}' ),
											'block'   => 'blockquote',
											'classes' => 'pullquote alignright',
											'icon'    => 'alignright',
										),

										array(
											'title'  => esc_html_x( 'Cite', 'Visual editor format label for HTML CITE tag used to set the blockquote source.', '{%= text_domain %}' ),
											'inline' => 'cite',
										),

									),
								),

						) ) );

				// Removing obsolete tags (this is localized already)

					$heading_1 = ( ! is_admin() ) ? ( 'Heading 1=h1;' ) : ( '' ); // Accounting for page builders front-end editing when page title is disabled

					$init['block_formats'] = 'Paragraph=p;' . $heading_1 . 'Heading 2=h2;Heading 3=h3;Heading 4=h4;Address=address;Preformatted=pre;Code=code';


			// Output

				return $init;

		} // /custom_mce_format





} // /{%= prefix_class %}_Theme_Framework_Visual_Editor
