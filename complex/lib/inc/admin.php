<?php
/**
 * Admin functions
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Admin
 *
 * @since    3.0
 * @version  5.0
 *
 * Contents:
 *
 *  1) Required files
 * 10) Hooks
 */





/**
 * 1) Required files
 */

	//Load the theme About page

		locate_template( WM_SETUP_DIR . 'about/about.php', true );

	//Theme Updater

		if ( apply_filters( 'wmhook_enable_update_notifier', false ) ) {
			locate_template( WM_LIBRARY_DIR . 'inc/update-notifier.php', true );
		}

	//Admin class

		locate_template( WM_LIBRARY_DIR . 'inc/classes/class-wm-theme-framework-admin.php', true );





/**
 * 10) Hooks
 */

	/**
	 * Actions
	 */

		//Styles and scripts

			add_action( 'admin_enqueue_scripts', 'WM_Theme_Framework_Admin::assets', 998 );

		//Admin notices

			add_action( 'admin_notices', 'WM_Theme_Framework_Admin::message', 998 );

		//Posts list table

			//Posts

				add_action( 'manage_post_posts_columns',       'WM_Theme_Framework_Admin::post_columns_register', 10    );
				add_action( 'manage_post_posts_custom_column', 'WM_Theme_Framework_Admin::post_columns_render',   10, 2 );

			//Pages

				add_action( 'manage_pages_columns',       'WM_Theme_Framework_Admin::post_columns_register', 10    );
				add_action( 'manage_pages_custom_column', 'WM_Theme_Framework_Admin::post_columns_render',   10, 2 );
