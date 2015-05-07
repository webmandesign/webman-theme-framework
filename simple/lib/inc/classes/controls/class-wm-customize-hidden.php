<?php
/**
 * Customizer custom controls
 *
 * Customizer hidden input field.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    3.1
 * @version  5.0
 */





if ( ! class_exists( 'WM_Customize_Hidden' ) ) {
	class WM_Customize_Hidden extends WP_Customize_Control {

		public $type = 'hidden';

		public function render_content() {
			?>

			<textarea <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>

			<?php
		}

	}
} // /WM_Customize_Hidden
