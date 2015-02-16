<?php
/**
 * Theme update notifier
 *
 * Provides a notification to the user everytime the WordPress theme is updated.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Theme update notifier
 * @copyright   Modifications: 2015 WebMan - Oliver Juhas
 *
 * @author  Joao Araujo, modified by WebMan
 * @link    https://github.com/unisphere/unisphere_notifier
 *
 * @since    3.0
 * @version  4.0
 *
 * CONTENT:
 * -  0) Constants
 * - 10) Actions and filters
 * - 20) Dashboard menu and admin bar
 * - 30) Update notifier page
 * - 40) Remote XML data
 *****************************************************
 */



/**
 * License:
 *
 * Copyright (c) 2011 João Araújo (http://themeforest.net/user/unisphere)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */





/**
 * 0) Constants
 */

	//The remote notifier XML file containing the latest version of the theme and changelog
		if ( ! defined( 'NOTIFIER_XML_FILE' ) )       define( 'NOTIFIER_XML_FILE',       trailingslashit( WM_THEME_AUTHOR_URI ) . 'updates/' . WM_THEME_SHORTNAME . '/' . WM_THEME_SHORTNAME . '-version.xml' );
	//The time interval for the remote XML cache in the database (86400 seconds = 24 hours)
		if ( ! defined( 'NOTIFIER_CACHE_INTERVAL' ) ) define( 'NOTIFIER_CACHE_INTERVAL', 86400 );





/**
 * 10) Actions and filters
 */

	/**
	 * Actions
	 */

		//Admin menu and menu bar
			if ( ! apply_filters( 'wmhook_disable_update_notifier_menu', false ) ) {
				add_action( 'admin_menu', 'update_notifier_menu', 1000 );
			}
			if ( ! apply_filters( 'wmhook_disable_update_notifier_bar_menu', false ) ) {
				add_action( 'admin_bar_menu', 'update_notifier_bar_menu', 1000 );
			}





/**
 * 20) Dashboard menu and admin bar
 */

	/**
	 * Adds an update notification to the WordPress Dashboard menu
	 *
	 * @since    3.0
	 * @version  4.0
	 */
	function update_notifier_menu() {
		if ( function_exists( 'simplexml_load_string' ) ) {
		//Stop if simplexml_load_string funtion isn't available

			//Don't display notification if the current user isn't an administrator
				if ( ! is_super_admin() ) {
					return;
				}

			//Get the latest remote XML file on our server
				$xml = get_latest_theme_version( NOTIFIER_CACHE_INTERVAL );

			//Compare current theme version with the remote XML version
				if ( version_compare( $xml->latest, WM_THEME_VERSION, '>' ) ) {

					add_theme_page(
						//page_title
						sprintf( _x( '%s Theme Updates', '%s stands for the theme name. Just copy it.', 'wm_domain' ), WM_THEME_NAME ),
						//menu_title
						_x( 'Theme Updates', 'Admin menu title.', 'wm_domain' ) . ' <span class="update-plugins count-1"><span class="update-count">1</span></span>',
						//capability
						'switch_themes',
						//menu_slug
						'theme-update-notifier',
						//function
						'update_notifier'
					);

				}

		}
	} // /update_notifier_menu



	/**
	 * Adds an update notification to the WordPress 3.1+ Admin Bar
	 *
	 * @since    3.0
	 * @version  4.0
	 */
	function update_notifier_bar_menu() {
		if ( function_exists( 'simplexml_load_string' ) ) {
		//Stop if simplexml_load_string funtion isn't available

			//Don't display notification in admin bar if it's disabled or the current user isn't an administrator
				if (
						! is_super_admin()
						|| ! is_admin_bar_showing()
					) {
					return;
				}

			global $wp_admin_bar, $wpdb;

			//Get the latest remote XML file on our server
				$xml = get_latest_theme_version( NOTIFIER_CACHE_INTERVAL );

			//Compare current theme version with the remote XML version
				if ( version_compare( $xml->latest, WM_THEME_VERSION, '>' ) ) {

					$adminURL = get_admin_url() . 'themes.php?page=theme-update-notifier';

					if (
							class_exists( 'Envato_WP_Toolkit' )
							&& ! isset( $xml->noenvato )
						) {
						$adminURL = network_admin_url( 'admin.php?page=envato-wordpress-toolkit' );
					}

					$wp_admin_bar->add_menu( array(
							'id'    => 'update_notifier',
							'title' => sprintf( _x( '%s update', 'Admin bar notification link. %s stands for the theme name. Just copy it.', 'wm_domain' ), WM_THEME_NAME ) . ' <span id="ab-updates">1</span>',
							'href'  => $adminURL
						) );

				}

		}
	} // /update_notifier_bar_menu





/**
 * 30) Update notifier page
 */

	/**
	 * Notifier page renderer
	 *
	 * @since    3.0
	 * @version  4.0
	 */
	function update_notifier() {
		//Requirements check
			if ( ! is_super_admin() ) {
				return;
			}

		//Get the latest remote XML file on our server
			$xml = get_latest_theme_version( NOTIFIER_CACHE_INTERVAL );

		/**
		 * No need for translations, english only.
		 */
		?>
		<div class="wrap update-notifier">

			<div id="icon-tools" class="icon32"></div>
			<h2><strong><?php echo WM_THEME_NAME; ?></strong> Theme Updates</h2>

			<br />

			<div id="message" class="error">
				<p>
				<?php
				if ( isset( $xml->message ) && trim( $xml->message ) ) {
					echo '<strong>' . trim( $xml->message ) . '</strong><br />';
				}
				echo 'You have version ' . WM_THEME_VERSION . ' installed. <strong>Update to version ' . trim( $xml->latest ) . ' now.</strong>';
				?>
				</p>
			</div>

			<div id="instructions">
				<img src="<?php echo get_template_directory_uri(); ?>/screenshot.png" alt="" class="theme-img" />

				<h3>Update Download and Instructions</h3>

				<p>First, please, re-download the new theme update from the source where you've originally obtained the theme.</p>

				<p>Use one of these options to update your theme:</p>

				<?php
				if ( isset( $xml->important ) ) {
					echo '<div class="important-note">' . $xml->important . '</div>';
				}
				?>

				<ul>
					<?php
					if ( class_exists( 'Envato_WP_Toolkit' ) && ! isset( $xml->noenvato ) ) {
						echo '<li><h4>Automatic theme update:</h4><a href="' . network_admin_url( 'admin.php?page=envato-wordpress-toolkit' ) . '" class="button button-primary button-hero">Update the Theme Automatically &raquo;</a></li>';
					}
					?>
					<li>
						<h4>Preffered, safer, quicker procedure:</h4>
						<ol>
							<li>Upload the theme installation ZIP file using FTP client to your server (into <code>YOUR_WORDPRESS_INSTALLATION/wp-content/themes/</code>).</li>
							<li>Using your FTP client, rename the old theme folder (for example from <code><?php echo WM_THEME_SHORTNAME; ?></code> to <code><?php echo WM_THEME_SHORTNAME; ?>-bak</code>).</li>
							<li>When the old theme folder is renamed, unzip the theme installation zip file directly on the server (you might need to use a web-based FTP tool for this - hosting companies provides such tools).</li>
							<li>After checking whether the theme works fine, delete the renamed old theme folder from the server (the <code><?php echo WM_THEME_SHORTNAME; ?>-bak</code> folder in our case).</li>
						</ol>
					</li>
					<li>
						<h4>Easier, slower procedure:</h4>
						<ol>
							<li>Unzip the zipped theme file (you have just downloaded) on your computer.</li>
							<li>Upload the unzipped theme folder using FTP client to your server (into <code>YOUR_WORDPRESS_INSTALLATION/wp-content/themes/</code>) overwriting all the current theme files. Please note that if some files were removed from the theme in the new update, you will have to delete these files additionally from your server. For removed files please check the changelog on the right.</li>
						</ol>
					</li>
				</ul>
			</div>

			<div id="changelog" class="note">
				<div class="icon32 icon32-posts-page" id="icon-edit-pages"><br /></div><h2>Changelog</h2>
				<?php echo $xml->changelog; ?>
				<hr />
				<h3>Files changed:</h3>
				<code><?php echo str_replace( ', ', '</code><br /><code>', $xml->changefiles ); ?></code>
			</div>
		</div>
		<?php
	} // /update_notifier





/**
 * 40) Remote XML data
 */

	/**
	 * Remove XML file processing
	 *
	 * Get the remote XML file contents and return its data (Version and Changelog).
	 * Uses the cached version if available and inside the time interval defined.
	 *
	 * @since    3.0
	 * @version  4.0
	 *
	 * @param  integer $interval
	 */
	function get_latest_theme_version( $interval ) {
		//Requirements check
			if ( ! is_super_admin() ) {
				return;
			}

		$db_cache_field              = 'notifier-cache-' . WM_THEME_SHORTNAME;
		$db_cache_field_last_updated = 'notifier-cache-' . WM_THEME_SHORTNAME . '-last-updated';
		$last                        = get_transient( $db_cache_field_last_updated );
		$now                         = time();
		$interval                    = absint( $interval );

		//Check the cache
			if (
					! $last
					|| ( $now - $last ) > $interval
				) {

				//Cache doesn't exist, or is old, so refresh it
					$response = wp_remote_get( NOTIFIER_XML_FILE );

					if ( is_wp_error( $response ) ) {

						$error = $response->get_error_message();

						$cache  = '<?xml version="1.0" encoding="UTF-8"?>';
						$cache .= '<notifier>';
							$cache .= '<latest>1.0</latest>';
							$cache .= '<message><![CDATA[<span style="font-size:125%;color:#f33">Something went wrong: ' . strip_tags( $error, '<a><span><strong>' ) . '</span>]]></message>';
							$cache .= '<changelog></changelog>';
							$cache .= '<changefiles></changefiles>';
						$cache .= '</notifier>';

					} else {

						$cache = $response['body'];

					}

				//If we've got good results, cache them
					if ( $cache ) {
						set_transient( $db_cache_field, $cache );
						set_transient( $db_cache_field_last_updated, time() );
					}

				//Read from the cache
					$notifier_data = get_transient( $db_cache_field );

			} else {

				//Cache is fresh enough, so read from it
					$notifier_data = get_transient( $db_cache_field );

			}

		/**
		 * Let's see if the $xml data was returned as we expected it to.
		 * If it wasn't, use the default 1.0 as the latest version so that we
		 * don't have problems when the remote server hosting the XML file is down
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

		//Load the remote XML data into a variable and return it
			$xml = simplexml_load_string( $notifier_data );

		//Output
			return $xml;
	} // /get_latest_theme_version

?>