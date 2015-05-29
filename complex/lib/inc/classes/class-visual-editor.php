<?php
/**
 * Visual Editor class
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Visual Editor
 *
 * @since    5.0
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
		 * @since    4.0
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
		 * @since    4.0
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
		 *
		 * @since    4.0
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

					$init['style_formats_merge'] = true;

				// Add custom formats

					$init['style_formats'] = json_encode( apply_filters( 'wmhook_{%= prefix_hook %}_tf_editor_custom_mce_format', array(

							// Group: Quotes

								array(
									'title' => esc_html_x( 'Quotes', 'Visual editor blockquote formats group title.', '{%= text_domain %}' ),
									'items' => array(

										array(
											'title' => esc_html__( 'Blockquote', '{%= text_domain %}' ),
											'block' => 'blockquote',
										),
										array(
											'title'   => esc_html__( 'Pullquote - align left', '{%= text_domain %}' ),
											'block'   => 'blockquote',
											'classes' => 'pullquote alignleft',
										),
										array(
											'title'   => esc_html__( 'Pullquote - align right', '{%= text_domain %}' ),
											'block'   => 'blockquote',
											'classes' => 'pullquote alignright',
										),
										array(
											'title' => esc_html_x( 'Cite', 'Visual editor format label for HTML CITE tag used to set the blockquote source.', '{%= text_domain %}' ),
											'block' => 'cite',
										),

									),
								),

							// Group: Text styles

								array(
									'title' => esc_html__( 'Text styles', '{%= text_domain %}' ),
									'items' => array(

										array(
											'title'    => esc_html__( 'Uppercase heading or paragraph', '{%= text_domain %}' ),
											'selector' => 'h1, h2, h3, h4, h5, h6, p',
											'classes'  => 'uppercase',
										),

										array(
											'title'  => esc_html__( 'Highlighted (marked) text', '{%= text_domain %}' ),
											'inline' => 'mark',
										),

									),
								),

						) ) );


			// Output

				return $init;

		} // /custom_mce_format





} // /{%= prefix_class %}_Theme_Framework_Visual_Editor
