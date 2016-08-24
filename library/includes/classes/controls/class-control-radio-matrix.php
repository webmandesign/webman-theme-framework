<?php
/**
 * Customizer custom controls
 *
 * Customizer matrix radio fields.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    1.0
 * @version  1.8
 */
class {%= prefix_class %}_Control_Radio_Matrix extends WP_Customize_Control {

	public $type = 'radiomatrix';

	public $class = '';



	public function enqueue() {

		// Processing

			// Scripts

				wp_enqueue_script(
						'{%= prefix_var %}-customize-control-radio-matrix',
						{%= prefix_class %}_Theme_Framework::get_stylesheet_directory_uri( {%= prefix_constant %}_LIBRARY_DIR . 'js/customize-control-radio-matrix.js' ),
						array( 'customize-controls' ),
						esc_attr( {%= prefix_constant %}_THEME_VERSION ),
						true
					);

	} // /enqueue



	public function render_content() {

		// Output

			if ( ! empty( $this->choices ) && is_array( $this->choices ) ) :

				?>

				<span class="customize-control-title"><?php echo $this->label; ?></span>
				<?php if ( $this->description ) : ?><span class="description customize-control-description"><?php echo $this->description; ?></span><?php endif; ?>

				<div class="<?php echo trim( 'custom-radio-container ' . $this->class ); ?>">
					<?php

					$i = 0;

					foreach ( $this->choices as $value => $name ) {

						$checked      = checked( $this->value(), $value, false );
						$active_class = ( $checked ) ? ( ' class="active"' ) : ( '' );

						if ( is_array( $name ) ) {
							$title = ' title="' . esc_attr( $name[0] ) . '"';
							$name  = $name[1];
						} else {
							$title = ' title="' . esc_attr( wp_strip_all_tags( $name ) ) . '"';
						}

						?>

						<label for="<?php echo $this->id . ++$i; ?>"<?php echo $active_class . $title; ?>>
							<?php echo $name; ?>
							<input class="custom-radio-item" type="radio" value="<?php echo $value; ?>" name="<?php echo $this->id; ?>" id="<?php echo $this->id . $i; ?>" <?php echo $this->get_link() . $checked; ?> />
						</label>

						<?php

					} // /foreach

					?>
				</div>

				<?php

			endif;

	} // /render_content

} // /{%= prefix_class %}_Control_Radio_Matrix
