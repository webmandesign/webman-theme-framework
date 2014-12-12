<?php
/**
 * Sanitizing Functions
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Theme Customizer
 * @copyright   2014 WebMan - Oliver Juhas
 *
 * @since    3.1
 * @version  4.0
 */



/**
 * Sanitize email
 *
 * @param  mixed $value WP customizer value to sanitize.
 */
if ( ! function_exists( 'wm_sanitize_email' ) ) {
	function wm_sanitize_email( $value ) {
		//Helper variables
			$value = ( is_email( trim( $value ) ) ) ? ( trim( $value ) ) : ( null );

		//Output
			return apply_filters( 'wmhook_wm_sanitize_email_output', $value );
	}
} // /wm_sanitize_email



/**
 * Sanitize integer number
 *
 * @param  mixed $value WP customizer value to sanitize.
 */
if ( ! function_exists( 'wm_sanitize_intval' ) ) {
	function wm_sanitize_intval( $value ) {
		return apply_filters( 'wmhook_wm_sanitize_intval_output', intval( $value ) );
	}
} // /wm_sanitize_intval



/**
 * Sanitize texts
 *
 * @since  4.0
 *
 * @param  mixed $value WP customizer value to sanitize.
 */
if ( ! function_exists( 'wm_sanitize_text' ) ) {
	function wm_sanitize_text( $value ) {
		return apply_filters( 'wmhook_wm_sanitize_text_output', wp_kses_post( force_balance_tags( $value ) ) );
	}
} // /wm_sanitize_text



/**
 * No sanitization at all, simply return the value
 *
 * @param  mixed $value WP customizer value to sanitize.
 */
if ( ! function_exists( 'wm_sanitize_return_value' ) ) {
	function wm_sanitize_return_value( $value ) {
		return apply_filters( 'wmhook_wm_sanitize_return_value_output', $value );
	}
} // /wm_sanitize_return_value

?>