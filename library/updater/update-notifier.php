<?php
/**
 * Theme Update Notifier
 *
 * Provides a notification to the user everytime the WordPress theme is updated.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Theme Update Notifier
 * @author      Modifications by WebMan - Oliver Juhas
 * @author      Joao Araujo
 * @link        http://themeforest.net/user/unisphere
 * @link        http://twitter.com/unispheredesign
 *
 * @since       3.0
 * @version     3.1
 *
 * CONTENT:
 * - 1) Constants
 * - 10) Actions and filters
 * - 20) Dashboard menu and admin bar
 * - 30) Update notifier page
 * - 40) Remote XML data
 *****************************************************
 */





/**
 * 1) Constants
 */

	//The remote notifier XML file containing the latest version of the theme and changelog
		define( 'NOTIFIER_XML_FILE', WM_DEVELOPER_URL . '/updates/' . WM_THEME_SHORTNAME . '/' . WM_THEME_SHORTNAME . '-version.xml' );
	//The time interval for the remote XML cache in the database (86400 seconds = 24 hours)
		define( 'NOTIFIER_CACHE_INTERVAL', 86400 );





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
	 */
	function update_notifier_menu() {
		if ( function_exists( 'simplexml_load_string' ) ) { //Stop if simplexml_load_string funtion isn't available
			if ( ! is_super_admin() ) {
			//Don't display notification if the current user isn't an administrator
				return;
			}

			$xml = get_latest_theme_version( NOTIFIER_CACHE_INTERVAL ); //Get the latest remote XML file on our server

			//support for nested point releases (such as v1.0.1) - works for versions of up to 9.9
			$latestVersion    = sanitize_title( str_replace( '.', '', $xml->latest ) ); //1.0.1 => 101
			$latestVersion    = substr( $latestVersion, 0, 1 ) . '.' . substr( $latestVersion, 1 ); //101 => 1.01
			$installedVersion = sanitize_title( str_replace( '.', '', WM_THEME_VERSION ) ); //1.0.1 => 101
			$installedVersion = substr( $installedVersion, 0, 1 ) . '.' . substr( $installedVersion, 1 ); //101 => 1.01

			if ( (float) $latestVersion > (float) $installedVersion ) { //Compare current theme version with the remote XML version
				add_theme_page(
					//page_title
					sprintf( __( '%s Theme Updates', 'wm_domain' ), WM_THEME_NAME ),
					//menu_title
					__( 'Theme Updates', 'wm_domain' ) . ' <span class="update-plugins count-1"><span class="update-count">1</span></span>',
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
	 */
	function update_notifier_bar_menu() {
		if ( function_exists( 'simplexml_load_string' ) ) { //Stop if simplexml_load_string funtion isn't available
			if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
			//Don't display notification in admin bar if it's disabled or the current user isn't an administrator
				return;
			}

			global $wp_admin_bar, $wpdb;

			$xml = get_latest_theme_version( NOTIFIER_CACHE_INTERVAL ); //Get the latest remote XML file on our server

			//support for nested point releases (such as v1.0.1) - works for versions of up to 9.9
			$latestVersion    = sanitize_title( str_replace( '.', '', $xml->latest ) ); //1.0.1 => 101
			$latestVersion    = substr( $latestVersion, 0, 1 ) . '.' . substr( $latestVersion, 1 ); //101 => 1.01
			$installedVersion = sanitize_title( str_replace( '.', '', WM_THEME_VERSION ) ); //1.0.1 => 101
			$installedVersion = substr( $installedVersion, 0, 1 ) . '.' . substr( $installedVersion, 1 ); //101 => 1.01

			if( (float) $latestVersion > (float) $installedVersion ) { //Compare current theme version with the remote XML version
				$adminURL = get_admin_url() . 'themes.php?page=theme-update-notifier';
				if ( class_exists( 'Envato_WP_Toolkit' ) && ! isset( $xml->noenvato ) ) {
					$adminURL = network_admin_url( 'admin.php?page=envato-wordpress-toolkit' );
				}
				$wp_admin_bar->add_menu( array(
						'id'    => 'update_notifier',
						'title' => sprintf( __( '%s update', 'wm_domain' ), WM_THEME_NAME ) . ' <span id="ab-updates">1</span>',
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
	 */
	function update_notifier() {
		//Requirements check
			if ( ! is_super_admin() ) {
				return;
			}

		$xml = get_latest_theme_version( NOTIFIER_CACHE_INTERVAL ); // Get the latest remote XML file on our server
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

				<p>To update the theme you need to <strong>re-download it from the marketplace</strong> you have bought it on.</p>

				<p>Use one of these options to update the theme:</p>

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
						<h4>Easier, but might take longer time:</h4>
						<ol>
							<li>Unzip the zipped theme file (you have just downloaded) on your computer.</li>
							<li>Upload the unzipped theme folder using FTP client to your server (into <code>YOUR_WORDPRESS_INSTALLATION/wp-content/themes/</code>) overwriting all the current theme files.</li>
						</ol>
					</li>
					<li>
						<h4>More advanced, quicker:</h4>
						<ol>
							<li>Upload the theme installation ZIP file using FTP client to your server (into <code>YOUR_WORDPRESS_INSTALLATION/wp-content/themes/</code>).</li>
							<li>Using your FTP client, rename the old theme folder (for example from <code><?php echo WM_THEME_SHORTNAME; ?></code> to <code><?php echo WM_THEME_SHORTNAME; ?>-old</code>).</li>
							<li>When the old theme folder is renamed, unzip the theme installation zip file directly on the server (you might need to use a web-based FTP tool for this; hosting companies provides such tools).</li>
							<li>After checking whether the theme works fine, delete the renamed old theme folder from the server.</li>
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
	 * @param  integer $interval
	 */
	function get_latest_theme_version( $interval ) {
		//Requirements check
			if ( ! is_super_admin() ) {
				return;
			}

		$db_cache_field              = 'notifier-cache-' . WM_THEME_SHORTNAME;
		$db_cache_field_last_updated = 'notifier-cache-' . WM_THEME_SHORTNAME . '-last-updated';
		$last                        = get_option( $db_cache_field_last_updated );
		$now                         = time();
		$interval                    = absint( $interval );

		//check the cache
		if ( ! $last || ( ( $now - $last ) > $interval ) || 3 > absint( get_option( WM_THEME_SETTINGS_INSTALL ) ) ) {

			//cache doesn't exist, or is old, so refresh it
			$response = wp_remote_get( NOTIFIER_XML_FILE );
			if ( is_wp_error( $response ) ) {
				$error = $response->get_error_message();
				$cache = '<?xml version="1.0" encoding="UTF-8"?><notifier><latest>1.0</latest><message><![CDATA[<span style="font-size:125%;color:#f33">Something went wrong: ' . $error . '</span>]]></message><changelog></changelog><changefiles></changefiles></notifier>';
			} else {
				$cache = $response['body'];
			}

			if ( $cache ) {
				//we got good results
				update_option( $db_cache_field, $cache );
				update_option( $db_cache_field_last_updated, time() );
			}

			//read from the cache file
			$notifier_data = get_option( $db_cache_field );

		} else {

			//cache file is fresh enough, so read from it
			$notifier_data = get_option( $db_cache_field );

		}

		//Let's see if the $xml data was returned as we expected it to.
		//If it didn't, use the default 1.0 as the latest version so that we don't have problems when the remote server hosting the XML file is down
		if ( strpos( (string) $notifier_data, '<notifier>' ) === false ) {
			$notifier_data = '<?xml version="1.0" encoding="UTF-8"?><notifier><latest>1.0</latest><message></message><changelog></changelog><changefiles></changefiles></notifier>';
		}

		//Load the remote XML data into a variable and return it
		$xml = simplexml_load_string( $notifier_data );

		return $xml;
	} // /get_latest_theme_version

?>