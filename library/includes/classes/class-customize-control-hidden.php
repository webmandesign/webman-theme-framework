<?php
/**
 * Customizer custom controls
 *
 * Customizer hidden input field.
 *
 * @package    WebMan WordPress Theme Framework
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  2.7.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Theme_Slug_Customize_Control_Hidden extends WP_Customize_Control {

	public $type = 'hidden';



	public function render_content() {

		// Output

			?>

			<textarea <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>

			<?php

	} // /render_content

} // /Theme_Slug_Customize_Control_Hidden
