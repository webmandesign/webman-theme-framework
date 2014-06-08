/**
 * Admin Scripts
 *
 * @package    WebMan WordPress Theme Framework
 * @copyright  2014 WebMan - Oliver Juhas
 */



jQuery( function() {



	/**
	 * Global scripts
	 */

		//Open "View" button in new window
			jQuery( '#view-post-btn a, .view a' ).attr( 'target', '_blank' );



	/**
	 * WooSidebars specific
	 */

		//Remove line breaks in sidebars admin list
			jQuery( '.post-type-sidebar .column-condition br:not(:last-child)' ).replaceWith( ' | ' );



	/**
	 * WooCommerce specific
	 */

		//Remove secondary color picker
			jQuery( '.woocommerce_page_wc-settings #woocommerce_frontend_css_secondary, .woocommerce_page_wc-settings #woocommerce_frontend_css_content_bg' ).parent().remove();



} );