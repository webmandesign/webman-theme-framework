<?php
/**
 * Theme framework hooks
 *
 * Compatible with Theme Hook Alliance (v1.0-draft)
 *
 * @link  https://github.com/zamoose/themehookalliance
 *
 * @package     WebMan WordPress Theme Framework (Simple)
 * @subpackage  Hooks
 *
 * @since    1.0
 * @version  2.0
 */






/**
 * Theme Hook Alliance hook stub list.
 *
 * @package  themehookalliance
 * @version  1.0-draft
 * @since    1.0-draft
 * @license  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License, v2 (or newer)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */



	/**
	 * Define the version of THA support, in case that becomes useful down the road.
	 */
	define( 'THA_HOOKS_VERSION', '1.0-draft' );



	/**
	 * Themes and Plugins can check for tha_hooks using current_theme_supports( 'tha_hooks', $hook )
	 * to determine whether a theme declares itself to support this specific hook type.
	 *
	 * Example:
	 * <code>
	 * 		// Declare support for all hook types
	 * 		add_theme_support( 'tha_hooks', array( 'all' ) );
	 *
	 * 		// Declare support for certain hook types only
	 * 		add_theme_support( 'tha_hooks', array( 'header', 'content', 'footer' ) );
	 * </code>
	 */
	add_theme_support( 'tha_hooks', array( 'all' ) );



	/**
	 * Determines, whether the specific hook type is actually supported.
	 *
	 * Plugin developers should always check for the support of a <strong>specific</strong>
	 * hook type before hooking a callback function to a hook of this type.
	 *
	 * Example:
	 * <code>
	 * 		if ( current_theme_supports( 'tha_hooks', 'header' ) )
	 * 	  		add_action( 'tha_head_top', 'prefix_header_top' );
	 * </code>
	 *
	 * @param   bool  $bool       True
	 * @param   array $args       The hook type being checked
	 * @param   array $registered All registered hook types
	 *
	 * @return  bool
	 */
	function tha_current_theme_supports( $bool, $args, $registered ) {
		return in_array( $args[0], $registered[0] ) || in_array( 'all', $registered[0] );
	} // /tha_current_theme_supports

	add_filter( 'current_theme_supports-tha_hooks', 'tha_current_theme_supports', 10, 3 );





/**
 * WebMan Theme Hooks
 */

	/**
	 * HTML <html> hook
	 *
	 * Special case, useful for <DOCTYPE>, etc.
	 *
	 * $tha_supports[] = 'html';
	 */

		function wmhook_{%= prefix_hook %}_tha_html_before() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_html_before' );
			do_action( 'tha_html_before' );
		} // /wmhook_{%= prefix_hook %}_tha_html_before



	/**
	 * HTML <body> hooks
	 *
	 * $tha_supports[] = 'body';
	 */

		function wmhook_{%= prefix_hook %}_tha_body_top() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_body_top' );
			do_action( 'tha_body_top' );
		} // /wmhook_{%= prefix_hook %}_tha_body_top

		function wmhook_{%= prefix_hook %}_tha_body_bottom() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_body_bottom' );
			do_action( 'tha_body_bottom' );
		} // /wmhook_{%= prefix_hook %}_tha_body_bottom



	/**
	 * HTML <head> hooks
	 *
	 * $tha_supports[] = 'head';
	 */

		function wmhook_{%= prefix_hook %}_tha_head_top() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_head_top' );
			do_action( 'tha_head_top' );
		} // /wmhook_{%= prefix_hook %}_tha_head_top

		function wmhook_{%= prefix_hook %}_tha_head_bottom() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_head_bottom' );
			do_action( 'tha_head_bottom' );
		} // /wmhook_{%= prefix_hook %}_tha_head_bottom



	/**
	 * Semantic <header> hooks
	 *
	 * $tha_supports[] = 'header';
	 */

		function wmhook_{%= prefix_hook %}_tha_header_before() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_header_before' );
			do_action( 'tha_header_before' );
		} // /wmhook_{%= prefix_hook %}_tha_header_before

		function wmhook_{%= prefix_hook %}_tha_header_after() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_header_after' );
			do_action( 'tha_header_after' );
		} // /wmhook_{%= prefix_hook %}_tha_header_after

		function wmhook_{%= prefix_hook %}_tha_header() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_header' );
		} // /wmhook_{%= prefix_hook %}_tha_header

		function wmhook_{%= prefix_hook %}_tha_header_top() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_header_top' );
			do_action( 'tha_header_top' );
		} // /wmhook_{%= prefix_hook %}_tha_header_top

		function wmhook_{%= prefix_hook %}_tha_header_bottom() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_header_bottom' );
			do_action( 'tha_header_bottom' );
		} // /wmhook_{%= prefix_hook %}_tha_header_bottom



	/**
	 * Semantic <content> hooks
	 *
	 * $tha_supports[] = 'content';
	 */

		function wmhook_{%= prefix_hook %}_tha_content_before() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_content_before' );
			do_action( 'tha_content_before' );
		} // /wmhook_{%= prefix_hook %}_tha_content_before

		function wmhook_{%= prefix_hook %}_tha_content_after() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_content_after' );
			do_action( 'tha_content_after' );
		} // /wmhook_{%= prefix_hook %}_tha_content_after

		function wmhook_{%= prefix_hook %}_tha_content_top() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_content_top' );
			do_action( 'tha_content_top' );
		} // /wmhook_{%= prefix_hook %}_tha_content_top

		function wmhook_{%= prefix_hook %}_tha_content_bottom() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_content_bottom' );
			do_action( 'tha_content_bottom' );
		} // /wmhook_{%= prefix_hook %}_tha_content_bottom



	/**
	 * Posts list (loop) hooks
	 *
	 * WebMan custom hooks.
	 * Not part of Theme Hook Alliance hooks.
	 */

		function wmhook_{%= prefix_hook %}_tha_postslist_before() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_postslist_before' );
		} // /wmhook_{%= prefix_hook %}_tha_postslist_before

		function wmhook_{%= prefix_hook %}_tha_postslist_after() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_postslist_after' );
		} // /wmhook_{%= prefix_hook %}_tha_postslist_after

		function wmhook_{%= prefix_hook %}_tha_postslist_top() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_postslist_top' );
		} // /wmhook_{%= prefix_hook %}_tha_postslist_top

		function wmhook_{%= prefix_hook %}_tha_postslist_bottom() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_postslist_bottom' );
		} // /wmhook_{%= prefix_hook %}_tha_postslist_bottom



	/**
	 * Semantic <entry> hooks
	 *
	 * $tha_supports[] = 'entry';
	 */

		function wmhook_{%= prefix_hook %}_tha_entry_before() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_entry_before' );
			do_action( 'tha_entry_before' );
		} // /wmhook_{%= prefix_hook %}_tha_entry_before

		function wmhook_{%= prefix_hook %}_tha_entry_after() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_entry_after' );
			do_action( 'tha_entry_after' );
		} // /wmhook_{%= prefix_hook %}_tha_entry_after

		function wmhook_{%= prefix_hook %}_tha_entry_top() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_entry_top' );
			do_action( 'tha_entry_top' );
		} // /wmhook_{%= prefix_hook %}_tha_entry_top

		function wmhook_{%= prefix_hook %}_tha_entry_bottom() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_entry_bottom' );
			do_action( 'tha_entry_bottom' );
		} // /wmhook_{%= prefix_hook %}_tha_entry_bottom



	/**
	 * Comments block hooks
	 *
	 * $tha_supports[] = 'comments';
	 */

		function wmhook_{%= prefix_hook %}_tha_comments_before() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_comments_before' );
			do_action( 'tha_comments_before' );
		} // /wmhook_{%= prefix_hook %}_tha_comments_before

		function wmhook_{%= prefix_hook %}_tha_comments_after() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_comments_after' );
			do_action( 'tha_comments_after' );
		} // /wmhook_{%= prefix_hook %}_tha_comments_after



	/**
	 * Semantic <sidebar> hooks
	 *
	 * $tha_supports[] = 'sidebar';
	 */

		function wmhook_{%= prefix_hook %}_tha_sidebars_before() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_sidebars_before' );
			do_action( 'tha_sidebars_before' );
		} // /wmhook_{%= prefix_hook %}_tha_sidebars_before

		function wmhook_{%= prefix_hook %}_tha_sidebars_after() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_sidebars_after' );
			do_action( 'tha_sidebars_after' );
		} // /wmhook_{%= prefix_hook %}_tha_sidebars_after

		function wmhook_{%= prefix_hook %}_tha_sidebar_top() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_sidebar_top' );
			do_action( 'tha_sidebar_top' );
		} // /wmhook_{%= prefix_hook %}_tha_sidebar_top

		function wmhook_{%= prefix_hook %}_tha_sidebar_bottom() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_sidebar_bottom' );
			do_action( 'tha_sidebar_bottom' );
		} // /wmhook_{%= prefix_hook %}_tha_sidebar_bottom



	/**
	 * Semantic <footer> hooks
	 *
	 * $tha_supports[] = 'footer';
	 */

		function wmhook_{%= prefix_hook %}_tha_footer_before() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_footer_before' );
			do_action( 'tha_footer_before' );
		} // wmhook_{%= prefix_hook %}_tha_footer_before

		function wmhook_{%= prefix_hook %}_tha_footer_after() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_footer_after' );
			do_action( 'tha_footer_after' );
		} // /wmhook_{%= prefix_hook %}_tha_footer_after

		function wmhook_{%= prefix_hook %}_tha_footer() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_footer' );
		} // /wmhook_{%= prefix_hook %}_tha_footer

		function wmhook_{%= prefix_hook %}_tha_footer_top() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_footer_top' );
			do_action( 'tha_footer_top' );
		} // /wmhook_{%= prefix_hook %}_tha_footer_top

		function wmhook_{%= prefix_hook %}_tha_footer_bottom() {
			do_action( 'wmhook_{%= prefix_hook %}_tha_footer_bottom' );
			do_action( 'tha_footer_bottom' );
		} // /wmhook_{%= prefix_hook %}_tha_footer_bottom

?>