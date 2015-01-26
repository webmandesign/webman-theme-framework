/**
 * Customizer scripts
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customizer
 * @copyright   2015 WebMan - Oliver Juhas
 *
 * @since    3.0
 * @version  4.0
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



	/**
	 * Run actions after customizer saving
	 *
	 * @since    3.0
	 * @version  4.0
	 */

		exports.customize.bind( 'saved', function() {

			//When customizer saved and new skin/load skin set
				if (
						$( '[id$="skin-new"] input' ).val()
						|| $( '[id$="skin-load"] select' ).val()
					) {

					//Refresh the page when loading skin (will empty also the new skin/load skin fields)
						if ( $( '[id$="skin-load"] select' ).val() ) {
							document.location.reload( true );
						}

					//Empty the new skin field
						$( '[id$="skin-new"] input' ).val( '' );

				}

			} );



} )( wp, jQuery );