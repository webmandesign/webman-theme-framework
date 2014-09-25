/**
 * Admin Scripts
 *
 * @package    WebMan WordPress Theme Framework
 * @copyright  2014 WebMan - Oliver Juhas
 *
 * @since      3.0
 * @version    3.1
 */



jQuery( function() {



	/**
	 * Global scripts
	 *
	 * @since  3.0
	 */

		//Open "View" button in new window
			jQuery( '#view-post-btn a, .view a' ).attr( 'target', '_blank' );



	/**
	 * WooSidebars specific
	 *
	 * @since  3.0
	 */

		//Remove line breaks in sidebars admin list
			jQuery( '.post-type-sidebar .column-condition br:not(:last-child)' ).replaceWith( ' | ' );



	/**
	 * WooCommerce specific
	 *
	 * @since    3.0
	 * @version  3.1
	 */

		//Remove secondary color picker
			jQuery( '.woocommerce_page_wc-settings.wc-remove-frontend_css_secondary #woocommerce_frontend_css_secondary, .woocommerce_page_wc-settings.wc-remove-frontend_css_content_bg #woocommerce_frontend_css_content_bg' ).parent().hide();



} );