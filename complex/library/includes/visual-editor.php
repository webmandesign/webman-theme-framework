<?php
/**
 * Visual editor addons
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Visual Editor
 *
 * @since    1.0
 * @version  1.1
 *
 * Contents:
 *
 *  1) Required files
 * 10) Init
 */





/**
 * 1) Required files
 */

	// Visual Editor class

		require_once( trailingslashit( get_template_directory() ) . {%= prefix_constant %}_LIBRARY_DIR . 'includes/classes/class-visual-editor.php' );





/**
 * 10) Init
 */

	add_action( 'init', array( '{%= prefix_class %}_Theme_Framework_Visual_Editor', 'init' ) );
