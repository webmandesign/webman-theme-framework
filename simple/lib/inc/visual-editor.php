<?php
/**
 * Visual editor addons
 *
 * @package     WebMan WordPress Theme Framework (Simple)
 * @subpackage  Visual Editor
 *
 * @since    1.0
 * @version  5.0
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

		locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/classes/class-visual-editor.php', true );





/**
 * 10) Init
 */

	add_action( 'init', array( '{%= prefix_class %}_Theme_Framework_Visual_Editor', 'init' ), -100 );
