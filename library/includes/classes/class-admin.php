<?php
/**
 * Admin class
 *
 * @subpackage  Admin
 *
 * @package    WebMan WordPress Theme Framework
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  2.7.1
 *
 * Contents:
 *
 *  0) Init
 * 10) Assets
 * 20) Messages
 */
final class {%= prefix_class %}_Library_Admin {





	/**
	 * 0) Init
	 */

		private static $instance;



		/**
		 * Constructor
		 *
		 * @since    1.0.0
		 * @version  2.7.1
		 */
		private function __construct() {

			// Processing

				// Hooks

					// Actions

						// Styles and scripts

							add_action( 'admin_enqueue_scripts', __CLASS__ . '::assets' );

		} // /__construct



		/**
		 * Initialization (get instance)
		 *
		 * @since    1.0.0
		 * @version  1.0.0
		 */
		public static function init() {

			// Processing

				if ( null === self::$instance ) {
					self::$instance = new self;
				}


			// Output

				return self::$instance;

		} // /init





	/**
	 * 10) Assets
	 */

		/**
		 * Admin assets
		 *
		 * @since    1.0.0
		 * @version  2.7.1
		 */
		public static function assets() {

			// Processing

				// Register

					// Styles

						wp_register_style(
							'{%= prefix_var %}-welcome',
							get_theme_file_uri( {%= prefix_constant %}_LIBRARY_DIR . 'css/welcome.css' ),
							false,
							{%= prefix_constant %}_THEME_VERSION,
							'screen'
						);

					// RTL setup

						wp_style_add_data( '{%= prefix_var %}-welcome', 'rtl', 'replace' );

		} // /assets





} // /{%= prefix_class %}_Library_Admin

add_action( 'admin_init', '{%= prefix_class %}_Library_Admin::init' );
