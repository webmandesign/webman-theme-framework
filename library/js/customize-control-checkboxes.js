/**
 * Customizer custom controls scripts
 *
 * Customizer multiple checkboxes.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    2.1.0
 * @version  2.1.0
 */
( function( exports, $ ) {

	$( document ).ready( function() {





		$( '.customize-control-checkboxes' )
			.on( 'change', 'input[type="checkbox"]', function() {

				// Helper variables

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
