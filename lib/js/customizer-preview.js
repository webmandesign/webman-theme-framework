/**
 * Customizer preview scripts
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customizer
 * @copyright   2015 WebMan - Oliver Juhas
 *
 * @since    4.0
 * @version  4.0
 */



jQuery( function() {



	/**
	 * Site title and description.
	 */

		wp.customize( 'blogname', function( value ) {

			value.bind( function( to ) {
				jQuery( '.site-title a' ).text( to );
			} );

		} ); // /blogname

		wp.customize( 'blogdescription', function( value ) {

			value.bind( function( to ) {
				jQuery( '.site-description' ).text( to );
			} );

		} ); // /blogdescription



} );