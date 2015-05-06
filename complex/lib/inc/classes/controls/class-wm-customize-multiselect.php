<?php
/**
 * Customizer custom controls
 *
 * Customizer multi select field.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    3.1
 * @version  5.0
 */




if ( ! class_exists( 'WM_Customize_Multiselect' ) ) {
	class WM_Customize_Multiselect extends WP_Customize_Control {

		public function render_content() {
			if ( ! empty( $this->choices ) && is_array( $this->choices ) ) {
				?>

				<label>
					<span class="customize-control-title"><?php echo $this->label; ?></span>
					<?php if ( $this->description ) : ?><span class="description customize-control-description"><?php echo $this->description; ?></span><?php endif; ?>

					<select name="<?php echo $this->id; ?>" multiple="multiple" <?php $this->link(); ?>>

						<?php

						foreach ( $this->choices as $value => $name ) {

							echo '<option value="' . $value . '" ' . selected( $this->value(), $value, false ) . '>' . $name . '</option>';

						}

						?>

					</select>
					<em><?php _e( 'Press CTRL key for multiple selection.', 'wm_domain' ); ?></em>
				</label>

				<?php
			}
		}

	}
} // /WM_Customize_Multiselect
