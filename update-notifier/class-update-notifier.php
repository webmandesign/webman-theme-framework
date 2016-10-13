<?php
/**
 * Update Notifier class
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Update Notifier
 *
 * @since    1.9
 * @version  2.0
 *
 * Contents:
 *
 *  0) Init
 * 10) Links
 * 20) Page
 * 30) Get remote data
 */
final class {%= prefix_class %}_Library_Update_Notifier {





	/**
	 * 0) Init
	 */

		private static $instance;

		private static $xml;



		/**
		 * Constructor
		 *
		 * @since    1.0
		 * @version  1.5
		 */
		private function __construct() {

			// Requirements check

				if (
						! function_exists( 'simplexml_load_string' )
						|| ! is_super_admin()
					) {
					return;
				}


			// Helper variables

				self::$xml = self::get_remote_xml_data( {%= prefix_constant %}_UPDATE_NOTIFIER_CACHE_INTERVAL );


			// Processing

				// Hooks

					// Actions

						// Admin menu

							add_action( 'admin_menu', __CLASS__ . '::menu', 998 );

						// Toolbar

							add_action( 'admin_bar_menu', __CLASS__ . '::toolbar', 998 );

		} // /__construct



		/**
		 * Initialization (get instance)
		 *
		 * @since    1.0
		 * @version  1.0
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
	 * 10) Dashboard links
	 */

		/**
		 * Admin menu link
		 *
		 * @since    1.0
		 * @version  1.9
		 */
		public static function menu() {

			// Processing

				if (
						isset( self::$xml->latest )
						&& version_compare( self::$xml->latest, {%= prefix_constant %}_THEME_VERSION, '>' )
					) {

					add_theme_page(
						// page_title
						sprintf(
							esc_html_x( '%s Theme Updates', '%s stands for the theme name. Just copy it.', '{%= text_domain %}' ),
							wp_get_theme( '{%= theme_slug %}' )->get( 'Name' )
						),
						// menu_title
						esc_html_x( 'Theme Updates', 'Admin menu title.', '{%= text_domain %}' ) . ' <span class="update-plugins count-1"><span class="update-count">1</span></span>',
						// capability
						'switch_themes',
						// menu_slug
						'theme-update-notifier',
						// function
						'{%= prefix_class %}_Library_Update_Notifier::page'
					);

				}

		} // /menu



		/**
		 * Toolbar link
		 *
		 * @since    1.0
		 * @version  1.9
		 */
		public static function toolbar() {

			// Requirements check

				if ( ! is_admin_bar_showing() ) {
					return;
				}


			// Helper variables

				global $wp_admin_bar;


			// Processing

				if (
						isset( self::$xml->latest )
						&& version_compare( self::$xml->latest, {%= prefix_constant %}_THEME_VERSION, '>' )
					) {

					$wp_admin_bar->add_menu( array(
							'id'    => 'update_notifier',
							'title' => sprintf( esc_html_x( '%s update', 'Admin bar notification link. %s: theme name.', '{%= text_domain %}' ), wp_get_theme( '{%= theme_slug %}' )->get( 'Name' ) ) . ' <span id="ab-updates">1</span>',
							'href'  => esc_url( get_admin_url() . 'themes.php?page=theme-update-notifier' )
						) );

				}

		} // /toolbar





	/**
	 * 20) Page
	 */

		/**
		 * Notifier page renderer
		 *
		 * @since    1.0
		 * @version  1.9
		 */
		public static function page() {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_library_updater_page_pre', false );

				if ( false !== $pre ) {
					return $pre;
				}


			// Processing

				/**
				 * No need for translations, English only.
				 */
				?>

				<div class="wrap update-notifier-wrap about-wrap">

					<h1><strong><?php echo wp_get_theme( '{%= theme_slug %}' )->get( 'Name' ); ?></strong> Theme Updates</h1>

					<br />

					<div class="about-text">
						<?php

						if ( isset( self::$xml->message ) && trim( self::$xml->message ) ) {
							echo '<strong>' . trim( self::$xml->message ) . '</strong><br />';
						}

						echo 'You have version ' . {%= prefix_constant %}_THEME_VERSION . ' installed. <strong>Update to version ' . trim( self::$xml->latest ) . ' now.</strong>';

						?>
					</div>

					<div class="instructions">

						<?php

						if ( isset( self::$xml->instructions ) && trim( self::$xml->instructions ) ) {

							echo trim( self::$xml->instructions );

						} else {

						?>

							<h2 style="text-align: inherit; font-weight: 600;">Update Download and Instructions</h2>

							<p>First, please, re-download the new theme update from the source where you've originally obtained the theme.</p>

							<p>Use one of these options to update your theme:</p>

							<?php

							if ( isset( self::$xml->important ) ) {

								echo '<div class="important-note">' . self::$xml->important . '</div>';

							}

							?>

							<ul>

								<li>

									<h3>Preferred, safer, quicker procedure:</h3>

									<ol>
										<li>Upload the theme installation ZIP file using FTP client to your server (into <code>YOUR_WORDPRESS_INSTALLATION/wp-content/themes/</code>).</li>
										<li>Using your FTP client, rename the old theme folder (for example from <code>{%= theme_slug %}</code> to <code>{%= theme_slug %}-bak</code>).</li>
										<li>When the old theme folder is renamed, unzip the theme installation zip file directly on the server (you might need to use a web-based FTP tool for this - hosting companies provides such tools).</li>
										<li>After checking whether the theme works fine, delete the renamed old theme folder from the server (the <code>{%= theme_slug %}-bak</code> folder in our case).</li>
									</ol>

								</li>

								<li>

									<h3>Easier, slower procedure:</h3>

									<ol>
										<li>Unzip the zipped theme file (you have just downloaded) on your computer.</li>
										<li>Upload the unzipped theme folder using FTP client to your server (into <code>YOUR_WORDPRESS_INSTALLATION/wp-content/themes/</code>) overwriting all the current theme files. Please note that if some files were removed from the theme in the new update, you will have to delete these files additionally from your server. For removed files please check the changelog on the right.</li>
									</ol>

								</li>

							</ul>

						<?php

						} // /Custom instructions check

						?>

					</div>

					<hr style="margin: 2.62em 0;">

					<div class="changelog">

						<h2 style="text-align: inherit; font-weight: 600;">Changelog</h2>

						<?php

						if ( isset( self::$xml->changelog ) ) {
							echo self::$xml->changelog;
						}

						?>

						<h3>Files changed:</h3>

						<code><?php

						if ( isset( self::$xml->changefiles ) ) {
							echo str_replace( ', ', '</code><br /><code>', self::$xml->changefiles );
						}

						?></code>

					</div>

				</div>

				<?php

		} // /page





	/**
	 * 30) Get remote data
	 */

		/**
		 * Remote XML file processing
		 *
		 * Get the remote XML file contents and return its data.
		 * Uses the cached version if available, inside the time interval defined.
		 *
		 * @since    1.0
		 * @version  1.9
		 *
		 * @param  int $interval
		 */
		public static function get_remote_xml_data( $interval ) {

			// Pre

				$pre = apply_filters( 'wmhook_{%= prefix_hook %}_library_updater_get_remote_xml_data_pre', false, $interval );

				if ( false !== $pre ) {
					return $pre;
				}


			// Helper variables

				$db_cache_field              = '{%= prefix_var %}_notifier_cache';
				$db_cache_field_last_updated = '{%= prefix_var %}_notifier_cache_last_updated';
				$last                        = get_transient( $db_cache_field_last_updated );

				// Check the cache

					if (
							! $last
							|| ( time() - $last ) > absint( $interval )
						) {

						// Cache doesn't exist, or is old, so refresh it

							$response = wp_remote_get( esc_url( trailingslashit( wp_get_theme( '{%= theme_slug %}' )->get( 'AuthorURI' ) ) . 'updates/{%= theme_slug %}/{%= theme_slug %}-version.xml' ) );

							if ( is_wp_error( $response ) ) {

								$error = $response->get_error_message();

								$cache  = '<?xml version="1.0" encoding="UTF-8"?>';
								$cache .= '<notifier>';
									$cache .= '<latest>1.0</latest>';
									$cache .= '<message><![CDATA[<span style="font-size:125%;color:#f33">Something went wrong: ' . wp_kses(
											$error,
											array(
												'a' => array(
														'href' => true,
														'class' => true,
													),
												'span' => array(
														'class' => true,
													),
												'strong' => array(
														'class' => true,
													),
											)
										) . '</span>]]></message>';
									$cache .= '<changelog></changelog>';
									$cache .= '<changefiles></changefiles>';
								$cache .= '</notifier>';

							} else {

								$cache = $response['body'];

							}

						// If we've got good results, cache them

							if ( $cache ) {
								set_transient( $db_cache_field, $cache );
								set_transient( $db_cache_field_last_updated, time() );
							}

						// Read from the cache

							$notifier_data = get_transient( $db_cache_field );

					} else {

						// Cache is fresh enough, so read from it

							$notifier_data = get_transient( $db_cache_field );

					}

				/**
				 * Let's see if the XML data were returned as we expected them.
				 * If they weren't, use the default 1.0 as the latest version so that we
				 * don't have problems when the remote server hosting the XML file is down.
				 */
				if ( strpos( (string) $notifier_data, '<notifier>' ) === false ) {

					$notifier_data  = '<?xml version="1.0" encoding="UTF-8"?>';
					$notifier_data .= '<notifier>';
						$notifier_data .= '<latest>1.0</latest>';
						$notifier_data .= '<message></message>';
						$notifier_data .= '<changelog></changelog>';
						$notifier_data .= '<changefiles></changefiles>';
					$notifier_data .= '</notifier>';

				}

				// Load the remote XML data into a variable and return it

					$xml = simplexml_load_string( $notifier_data );


			// Output

				return $xml;

		} // /get_remote_xml_data





} // /{%= prefix_class %}_Library_Update_Notifier

add_action( 'init', '{%= prefix_class %}_Library_Update_Notifier::init' );
