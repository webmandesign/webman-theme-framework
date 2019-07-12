/**
 * Customizer custom controls scripts
 *
 * Customizer multiple checkboxes.
 * WordPress customizer uses jQuery, so, go for it!
 *
 * @package    WebMan WordPress Theme Framework
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    2.1.0
 * @version  2.8.0
 */
( function( exports, $ ) {

	'use strict';

	$( wp.customize ).on( 'ready', function() {

		$( '.customize-control-multicheckbox' )
			.on( 'change', 'input[type="checkbox"]', function() {

				// Variables

					var
						$this   = $( this ),
						$values = $this
							.closest( '.customize-control' )
							.find( 'input[type="checkbox"]:checked' )
								.map( function() {

									// Output

										return this.value;

								} )
								.get()
								.join( ',' );


				// Processing

					$this
						.closest( '.customize-control' )
						.find( 'input[type="hidden"]' )
							.val( $values )
							.trigger( 'change' );

			} );

	} );
} )( wp, jQuery );
