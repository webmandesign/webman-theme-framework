/**
 * Customizer custom controls scripts
 *
 * Customizer matrix radio fields.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    1.0
 * @version  1.8
 */
( function( exports, $ ) {





	jQuery( '.custom-radio-container' )
		.on( 'change', 'input', function() {

			// Processing

				jQuery( this )
					.parent()
						.addClass( 'is-active' )
						.siblings()
						.removeClass( 'is-active' );

		} );





} )( wp, jQuery );
