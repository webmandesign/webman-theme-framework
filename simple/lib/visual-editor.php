<?php
/**
 * Visual editor addons
 *
 * @package    Receptar
 * @copyright  2015 WebMan - Oliver Juhas
 *
 * @since    1.0
 * @version  1.0
 *
 * CONTENT:
 * - 10) Actions and filters
 * - 20) Visual editor addons
 */





/**
 * 10) Actions and filters
 */

	/**
	 * Filters
	 */

		//Visual Editor addons
			add_filter( 'mce_buttons',          'receptar_add_buttons_row1'  );
			add_filter( 'mce_buttons_2',        'receptar_add_buttons_row2'  );
			add_filter( 'tiny_mce_before_init', 'receptar_custom_mce_format' );





/**
 * 20) Visual editor addons
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
	if ( ! function_exists( 'receptar_add_buttons_row1' ) ) {
		function receptar_add_buttons_row1( $buttons ) {
			//Inserting buttons after "more" button
				$pos = array_search( 'wp_more', $buttons, true );
				if ( false !== $pos ) {
					$add     = array_slice( $buttons, 0, $pos + 1 );
					$add[]   = 'wp_page';
					$buttons = array_merge( $add, array_slice( $buttons, $pos + 1 ) );
				}

			//Output
				return $buttons;
		}
	} // /receptar_add_buttons_row1



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
		if ( ! function_exists( 'receptar_add_buttons_row2' ) ) {
			function receptar_add_buttons_row2( $buttons ) {
				//Inserting buttons at the beginning of the row
					array_unshift( $buttons, 'styleselect' );

				//Output
					return $buttons;
			}
		} // /receptar_add_buttons_row2



	/**
	 * Customizing format dropdown items
	 *
	 * @link  http://codex.wordpress.org/TinyMCE_Custom_Styles
	 *
	 * @since    1.0
	 * @version  1.0
	 *
	 * @param  array $init
	 */
	if ( ! function_exists( 'receptar_custom_mce_format' ) ) {
		function receptar_custom_mce_format( $init ) {
			//Preparing output
				//Merge old & new formats
					$init['style_formats_merge'] = true;

				//Add custom formats
					$init['style_formats'] = json_encode( apply_filters( 'wmhook_receptar_custom_mce_format_style_formats', array(

							//Group: Quotes
								array(
									'title' => _x( 'Quotes', 'Visual editor blockquote formats group title.', 'receptar' ),
									'items' => array(

										array(
											'title' => __( 'Blockquote', 'receptar' ),
											'block' => 'blockquote',
										),
										array(
											'title'   => __( 'Pullquote - align left', 'receptar' ),
											'block'   => 'blockquote',
											'classes' => 'pullquote alignleft',
										),
										array(
											'title'   => __( 'Pullquote - align right', 'receptar' ),
											'block'   => 'blockquote',
											'classes' => 'pullquote alignright',
										),
										array(
											'title' => _x( 'Cite', 'Visual editor format label for HTML CITE tag used to set the blockquote source.', 'receptar' ),
											'block' => 'cite',
										),

									),
								),

							//Group: Text styles
								array(
									'title' => __( 'Text styles', 'receptar' ),
									'items' => array(

										array(
											'title'    => __( 'Uppercase heading or paragraph', 'receptar' ),
											'selector' => 'h1, h2, h3, h4, h5, h6, p',
											'classes'  => 'uppercase',
										),

										array(
											'title'  => __( 'Highlighted (marked) text', 'receptar' ),
											'inline' => 'mark',
										),

										array(
											'title'    => __( 'Button link', 'receptar' ),
											'selector' => 'a',
											'classes'  => 'button',
										),

									),
								),

						) ) );

			//Output
				return apply_filters( 'wmhook_receptar_custom_mce_format_output', $init );
		}
	} // /receptar_custom_mce_format

?>