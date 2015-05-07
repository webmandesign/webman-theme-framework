<?php
/**
 * Customizer custom controls
 *
 * Customizer custom HTML.
 *
 * @package    Receptar
 * @copyright  2015 WebMan - Oliver Juhas
 *
 * @since    1.0
 * @version  1.0
 */



/**
 * Custom HTML (set as label)
 */
class Receptar_Customizer_HTML extends WP_Customize_Control {

	public function render_content() {
		echo $this->label;
	}

} // /Receptar_Customizer_HTML

?>