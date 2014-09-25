/**
 * Theme Customizer Scripts
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Skinning System
 * @copyright   2014 WebMan - Oliver Juhas
 *
 * @since       3.0
 * @version     3.1
 */



( function( exports, $ ) {



	/**
	 * Custom radio select
	 *
	 * @since 3.1
	 */

	jQuery( '.custom-radio-container' ).on( 'change', 'input', function() {
			jQuery( this ).parent().addClass( 'active' ).siblings().removeClass( 'active' );
		} );



	/**
	 * Run actions after customizer saving
	 *
	 * @since    3.0
	 * @version  3.1
	 */

		exports.customize.bind( 'saved', function() {

				if (
						$( '#customize-control-wm-' + wmCustomizerHelper.wmThemeShortname + '-skin-wm-skin-new input' ).val()
						|| $( '#customize-control-wm-' + wmCustomizerHelper.wmThemeShortname + '-skin-wm-skin-load select' ).val()
					) {
					//Trigger action when customizer saved and new skin/load skin set

					//Refresh the page when loading skin (will empty also the new skin/load skin fields)
						if ( $( '#customize-control-wm-' + wmCustomizerHelper.wmThemeShortname + '-skin-wm-skin-load select' ).val() ) {
							document.location.reload( true );
						}

					//Empty the new skin field
						$( '#customize-control-wm-' + wmCustomizerHelper.wmThemeShortname + '-skin-wm-skin-new input' ).val( '' );

				}

			} );



} )( wp, jQuery );