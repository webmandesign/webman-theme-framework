<?php
/**
 * Customizer custom controls
 *
 * Customizer multi select field.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    1.0
 * @version  1.9
 */
class {%= prefix_class %}_Customize_Control_Multiselect extends WP_Customize_Control {

	public $type = 'multiselect';



	public function render_content() {

		// Output

			if ( ! empty( $this->choices ) && is_array( $this->choices ) ) :

				?>

				<label>
					<span class="customize-control-title"><?php echo $this->label; ?></span>
					<?php if ( $this->description ) : ?><span class="description customize-control-description"><?php echo $this->description; ?></span><?php endif; ?>

					<select name="<?php echo $this->id; ?>" multiple="multiple" <?php $this->link(); ?>>
						<?php

						foreach ( $this->choices as $value => $name ) {

							echo '<option value="' . $value . '" ' . selected( $this->value(), $value, false ) . '>' . $name . '</option>';

						} // /foreach

						?>
					</select>

					<em><?php esc_html_e( 'Press CTRL key for multiple selection.', '{%= text_domain %}' ); ?></em>
				</label>

				<?php

			endif;

	} // /render_content

} // /{%= prefix_class %}_Customize_Control_Multiselect
