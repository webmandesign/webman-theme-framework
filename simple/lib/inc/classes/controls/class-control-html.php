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





if ( ! class_exists( '{%= prefix_class %}_Control_HTML' ) ) {
	class {%= prefix_class %}_Control_HTML extends WP_Customize_Control {

		public function render_content() {
			echo $this->label;
		}

	}
} // /{%= prefix_class %}_Control_HTML
