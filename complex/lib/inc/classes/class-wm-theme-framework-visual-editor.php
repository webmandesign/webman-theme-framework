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





if ( ! class_exists( 'WM_Theme_Framework_Visual_Editor' ) ) {
	final class WM_Theme_Framework_Visual_Editor {

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
			 * @since    4.0
			 * @version  5.0
			 *
			 * @param  array $buttons
			 */
			public static function add_buttons_row1( $buttons ) {

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_editor_add_buttons_row1_pre', false, $buttons );

					if ( false !== $pre ) {
						return $pre;
					}


				//Processing

					//Inserting buttons after "more" button

						$pos = array_search( 'wp_more', $buttons, true );

						if ( false !== $pos ) {
							$add     = array_slice( $buttons, 0, $pos + 1 );
							$add[]   = 'wp_page';
							$buttons = array_merge( $add, array_slice( $buttons, $pos + 1 ) );
						}


				//Output

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

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_editor_add_buttons_row2_pre', false, $buttons );

					if ( false !== $pre ) {
						return $pre;
					}


				//Processing

					//Inserting buttons at the beginning of the row

						array_unshift( $buttons, 'styleselect' );


				//Output

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

				//Pre

					$pre = apply_filters( 'wmhook_wmtf_editor_custom_mce_format_pre', false, $init );

					if ( false !== $pre ) {
						return $pre;
					}


				//Processing

					//Merge old & new formats

						$init['style_formats_merge'] = true;

					//Add custom formats

						$init['style_formats'] = json_encode( apply_filters( 'wmhook_wmtf_editor_custom_mce_format', array(

								//Group: Quotes

									array(
										'title' => _x( 'Quotes', 'Visual editor blockquote formats group title.', 'wmtf_domain' ),
										'items' => array(

											array(
												'title' => __( 'Blockquote', 'wmtf_domain' ),
												'block' => 'blockquote',
											),
											array(
												'title'   => __( 'Pullquote - align left', 'wmtf_domain' ),
												'block'   => 'blockquote',
												'classes' => 'pullquote alignleft',
											),
											array(
												'title'   => __( 'Pullquote - align right', 'wmtf_domain' ),
												'block'   => 'blockquote',
												'classes' => 'pullquote alignright',
											),
											array(
												'title' => _x( 'Cite', 'Visual editor format label for HTML CITE tag used to set the blockquote source.', 'wmtf_domain' ),
												'block' => 'cite',
											),

										),
									),

								//Group: Text styles

									array(
										'title' => __( 'Text styles', 'wmtf_domain' ),
										'items' => array(

											array(
												'title'    => __( 'Uppercase heading or paragraph', 'wmtf_domain' ),
												'selector' => 'h1, h2, h3, h4, h5, h6, p',
												'classes'  => 'uppercase',
											),

											array(
												'title'  => __( 'Highlighted (marked) text', 'wmtf_domain' ),
												'inline' => 'mark',
											),

										),
									),

							) ) );


				//Output

					return $init;

			} // /custom_mce_format

	}
} // /WM_Theme_Framework_Visual_Editor
