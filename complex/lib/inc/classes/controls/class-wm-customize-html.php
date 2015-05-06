<?php
/**
 * Customizer custom controls
 *
 * Customizer custom HTML.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    3.1
 * @version  5.0
 */





if ( ! class_exists( 'WM_Customize_HTML' ) ) {
	class WM_Customize_HTML extends WP_Customize_Control {

		public function render_content() {
			echo $this->label;
		}

	}
} // /WM_Customize_HTML
