<?php
/**
 * Visual editor addons
 *
 * @package     WebMan WordPress Theme Framework (Simple)
 * @subpackage  Visual Editor
 *
 * @since    1.0
 * @version  1.0.8
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

		locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'include/classes/class-visual-editor.php', true );





/**
 * 10) Init
 */

	add_action( 'init', array( '{%= prefix_class %}_Theme_Framework_Visual_Editor', 'init' ) );
