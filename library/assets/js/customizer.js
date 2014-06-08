/**
 * Theme Customizer Scripts
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Skinning System
 * @copyright   2014 WebMan - Oliver Juhas
 * @version     1.0
 */



( function( exports, $ ) {



	/**
	 * Run actions after customizer saving
	 */

		exports.customize.bind( 'saved', function() {

				if (
						$( '#customize-control-wm-trigger-skin-wm-skin-new input' ).val()
						|| $( '#customize-control-wm-trigger-skin-wm-skin-load select' ).val()
					) {
					//Trigger action when customizer saved and new skin/load skin set

					//Refresh the page when loading skin (will empty also the new skin/load skin fields)
						if ( $( '#customize-control-wm-trigger-skin-wm-skin-load select' ).val() ) {
							document.location.reload( true );
						}

					//Empty the new skin field
						$( '#customize-control-wm-trigger-skin-wm-skin-new input' ).val( '' );

				}

			} );



} )( wp, jQuery );