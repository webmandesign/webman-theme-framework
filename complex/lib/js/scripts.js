/**
 * Admin scripts
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Admin
 *
 * @since    3.0
 * @version  5.0
 */



jQuery( function() {



	/**
	 * WooSidebars specific
	 *
	 * @since    3.0
	 * @version  5.0
	 */

		//Remove line breaks in sidebars admin list

			jQuery( '.post-type-sidebar .column-condition br:not(:last-child)' )
				.replaceWith( ' | ' );



} );