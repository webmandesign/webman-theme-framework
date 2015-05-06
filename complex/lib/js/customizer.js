/**
 * Customizer scripts
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    3.0
 * @version  5.0
 */



( function( exports, $ ) {



	/**
	 * Custom radio select
	 *
	 * @since   3.1
	 * @version 3.1
	 */

	jQuery( '.custom-radio-container' ).on( 'change', 'input', function() {

			jQuery( this ).parent().addClass( 'active' ).siblings().removeClass( 'active' );

		} );



} )( wp, jQuery );