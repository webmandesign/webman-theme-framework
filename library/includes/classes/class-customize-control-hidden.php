<?php
/**
 * Customizer custom controls
 *
 * Customizer hidden input field.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    1.0.0
 * @version  1.9.0
 */
class {%= prefix_class %}_Customize_Control_Hidden extends WP_Customize_Control {

	public $type = 'hidden';



	public function render_content() {

		// Output

			?>

			<textarea <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>

			<?php

	} // /render_content

} // /{%= prefix_class %}_Customize_Control_Hidden
