<?php
/**
 * Admin functions
 *
 * @package     WebMan WordPress Theme Framework (Simple)
 * @subpackage  Admin
 *
 * @since    1.0
 * @version  2.0
 *
 * Contents:
 *
 *  1) Required files
 * 10) Hooks
 */





/**
 * 1) Required files
 */

	//Admin class

		locate_template( WMTF_LIBRARY_DIR . 'inc/classes/class-wm-theme-framework-admin.php', true );





/**
 * 10) Hooks
 */

	/**
	 * Actions
	 */

		//Styles and scripts

			add_action( 'admin_enqueue_scripts', 'WM_Theme_Framework_Admin::assets', 998 );

		//Posts list table

			//Posts

				add_action( 'manage_post_posts_columns',       'WM_Theme_Framework_Admin::post_columns_register', 10    );
				add_action( 'manage_post_posts_custom_column', 'WM_Theme_Framework_Admin::post_columns_render',   10, 2 );

			//Pages

				add_action( 'manage_pages_columns',       'WM_Theme_Framework_Admin::post_columns_register', 10    );
				add_action( 'manage_pages_custom_column', 'WM_Theme_Framework_Admin::post_columns_render',   10, 2 );

			//Jetpack Portfolio posts

				add_action( 'manage_edit-jetpack-portfolio_columns',        'WM_Theme_Framework_Admin::post_columns_register', 10    );
				add_action( 'manage_jetpack-portfolio_posts_custom_column', 'WM_Theme_Framework_Admin::post_columns_render',   10, 2 );
