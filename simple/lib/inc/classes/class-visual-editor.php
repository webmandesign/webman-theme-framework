<?php
/**
 * Visual Editor class
 *
 * @package     WebMan WordPress Theme Framework (Simple)
 * @subpackage  Visual Editor
 *
 * @since    2.0
 * @version  2.0
 */





if ( ! class_exists( '{%= prefix_class %}_Theme_Framework_Visual_Editor' ) ) {
	final class {%= prefix_class %}_Theme_Framework_Visual_Editor {

		/**
		 * Contents:
		 *
		 * 10) Buttons
		 * 20) Custom formats
		 */





		/**
		 * 10) Buttons
		 */

			/**
			 * Add buttons to visual editor
			 *
			 * First row.
			 *
			 * @since    1.0
			 * @version  2.0
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
			 * @version  2.0
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
			 * @since    1.0
			 * @version  2.0
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

											array(
												'title'    => esc_html__( 'Button link', '{%= text_domain %}' ),
												'selector' => 'a',
												'classes'  => 'button',
											),

										),
									),

							) ) );


				// Output

					return $init;

			} // /custom_mce_format

	}
} // /{%= prefix_class %}_Theme_Framework_Visual_Editor
