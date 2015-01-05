<?php
/**
 * Customizer custom controls
 *
 * Customizer hidden input field.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customizer
 * @copyright   2015 WebMan - Oliver Juhas
 *
 * @since    3.1
 * @version  4.0
 */



/**
 * Hidden
 */
class WM_Customizer_Hidden extends WP_Customize_Control {

	public $type = 'hidden';

	public function render_content() {
		?>

		<textarea <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>

		<?php
	}

} // /WM_Customizer_Hidden

?>