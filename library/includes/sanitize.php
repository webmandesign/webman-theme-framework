<?php
/**
 * Skinning System
 *
 * Sanitizing functions.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Skinning System
 * @copyright   2014 WebMan - Oliver Juhas
 *
 * @since       3.1
 * @version     1.0
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
if ( ! function_exists( 'wm_sanitize_int' ) ) {
	function wm_sanitize_int( $value ) {
		//Helper variables
			$value = intval( $value );

		//Output
			return apply_filters( 'wmhook_wm_sanitize_int_output', $value );
	}
} // /wm_sanitize_int



/**
 * Sanitize absolute integer number
 *
 * @param  mixed $value WP customizer value to sanitize.
 */
if ( ! function_exists( 'wm_sanitize_absint' ) ) {
	function wm_sanitize_absint( $value ) {
		//Helper variables
			$value = absint( $value );

		//Output
			return apply_filters( 'wmhook_wm_sanitize_absint_output', $value );
	}
} // /wm_sanitize_absint

?>