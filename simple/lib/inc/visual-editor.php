<?php
/**
 * Visual editor addons
 *
 * @package     WebMan WordPress Theme Framework (Simple)
 * @subpackage  Visual Editor
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

	// Visual Editor class

		locate_template( {%= prefix_constant %}_LIBRARY_DIR . 'inc/classes/class-visual-editor.php', true );





/**
 * 10) Hooks
 */

	/**
	 * Filters
	 */

		// Visual Editor addons

			add_filter( 'mce_buttons',          '{%= prefix_class %}_Theme_Framework_Visual_Editor::add_buttons_row1'  );
			add_filter( 'mce_buttons_2',        '{%= prefix_class %}_Theme_Framework_Visual_Editor::add_buttons_row2'  );
			add_filter( 'tiny_mce_before_init', '{%= prefix_class %}_Theme_Framework_Visual_Editor::custom_mce_format' );
