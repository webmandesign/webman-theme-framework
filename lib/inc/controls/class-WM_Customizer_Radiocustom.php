<?php
/**
 * Customizer custom controls
 *
 * Customizer image radio fields.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customizer
 * @copyright   2015 WebMan - Oliver Juhas
 *
 * @since    3.1
 * @version  4.0
 */



/**
 * Textarea
 */
class WM_Customizer_Radiocustom extends WP_Customize_Control {

	public $class = '';

	public function render_content() {
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
						$title = ' title="' . esc_attr( strip_tags( $name ) ) . '"';
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
	}

} // /WM_Customizer_Radiocustom

?>