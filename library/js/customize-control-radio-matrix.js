/**
 * Customizer custom controls scripts
 *
 * Customizer matrix radio fields.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    1.0.0
 * @version  2.1.0
 */
( function( exports, $ ) {

	$( document ).ready( function() {





		$( '.custom-radio-container' )
			.on( 'change', 'input', function() {

				// Processing

					$( this )
						.parent()
							.addClass( 'is-active' )
							.siblings()
							.removeClass( 'is-active' );

			} );





	} );

} )( wp, jQuery );
