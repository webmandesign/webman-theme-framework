/**
 * Customizer custom controls scripts
 *
 * Customizer matrix radio fields.
 * WordPress customizer uses jQuery, so, go for it!
 *
 * @package    WebMan WordPress Theme Framework
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  2.8.0
 */
( function( exports, $ ) {

	'use strict';

	$( wp.customize ).on( 'ready', function() {

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
