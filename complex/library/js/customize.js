/**
 * Customizer scripts
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    1.0
 * @version  1.5
 */





( function( exports, $ ) {





	/**
	 * Custom radio select
	 *
	 * @since    1.0
	 * @version  1.5
	 */
	jQuery( '.custom-radio-container' )
		.on( 'change', 'input', function() {

			// Processing

				jQuery( this )
					.parent()
						.addClass( 'active' )
						.siblings()
						.removeClass( 'active' );

		} );





} )( wp, jQuery );