<?php
/**
 * Skinning System
 *
 * Customizer textarea field.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Skinning System
 * @copyright   2014 WebMan - Oliver Juhas
 *
 * @since       3.1
 */



/**
 * Textarea
 */
class WM_Customizer_Textarea extends WP_Customize_Control {

	public $type = 'textarea';

	public function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo $this->label; ?></span>
			<textarea cols="20" rows="4" <?php $this->link(); ?>>
				<?php echo esc_textarea( $this->value() ); ?>
			</textarea>
		</label>
		<?php
	}

} // /WM_Customizer_Textarea

?>