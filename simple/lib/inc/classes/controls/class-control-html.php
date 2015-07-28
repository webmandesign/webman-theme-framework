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





class {%= prefix_class %}_Control_HTML extends WP_Customize_Control {

	public $type = 'html';

	public $content = '';

	public function render_content() {
		if ( isset( $this->label ) ) {
			echo '<span class="customize-control-title">' . $this->label . '</span>';
		}

		if ( isset( $this->content ) ) {
			echo $this->content;
		}

		if ( isset( $this->description ) ) {
			echo '<span class="description customize-control-description">' . $this->description . '</span>';
		}
	}

} // /{%= prefix_class %}_Control_HTML
