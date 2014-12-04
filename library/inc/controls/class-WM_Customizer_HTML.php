<?php
/**
 * Theme Customizer Input Fields
 *
 * Customizer custom HTML.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Theme Customizer
 * @copyright   2014 WebMan - Oliver Juhas
 *
 * @since  3.1
 */



/**
 * Custom HTML (set as label)
 */
class WM_Customizer_HTML extends WP_Customize_Control {

	public function render_content() {
		echo $this->label;
	}

} // /WM_Customizer_HTML

?>