<?php
/**
 * Customizer custom controls
 *
 * Customizer text field (with datalist support).
 *
 * @package    WebMan WordPress Theme Framework
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    2.8.0
 * @version  2.8.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Theme_Slug_Customize_Control_Text extends WP_Customize_Control {

	public $type = 'text';



	public function render_content() {

		// Output

			if ( ! empty( $this->choices ) && is_array( $this->choices ) ) {
				ob_start();
				parent::render_content();

				echo str_replace(
					$this->get_link(),
					'list="datalist-' . esc_attr( $this->id ) . '"' . $this->get_link(),
					ob_get_clean()
				);

				echo '<datalist id="datalist-' . esc_attr( $this->id ) . '">';
				foreach ( $this->choices as $value ) {
					echo '<option value="' . esc_attr( $value ) . '">';
				}
				echo '</datalist>';
			} else {
				parent::render_content();
			}

	} // /render_content

} // /Theme_Slug_Customize_Control_Text
