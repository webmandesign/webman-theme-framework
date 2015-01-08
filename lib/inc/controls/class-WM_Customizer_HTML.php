<?php
/**
 * Customizer custom controls
 *
 * Customizer custom HTML.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customizer
 * @copyright   2015 WebMan - Oliver Juhas
 *
 * @since    3.1
 * @version  4.0
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