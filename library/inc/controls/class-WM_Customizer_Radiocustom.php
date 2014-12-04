<?php
/**
 * Theme Customizer Input Fields
 *
 * Customizer image radio fields.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Theme Customizer
 * @copyright   2014 WebMan - Oliver Juhas
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
		if ( ! empty( $this->choices ) && is_array( $this->choices ) ) {

			echo '<span class="customize-control-title">' . $this->label . '</span>';
			if ( $this->description ) {
				echo '<span class="description customize-control-description">' . $this->description . '</span>';
			}

			echo '<div class="' . trim( 'custom-radio-container ' . $this->class ) . '">';

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

					echo '<label for="' . $this->id . ++$i . '"' . $active_class . $title . '>';
						echo $name;
						echo '<input class="custom-radio-item" type="radio" value="' . $value . '" name="' . $this->id . '" id="' . $this->id . $i . '" ' . $this->get_link() . $checked . ' />';
					echo '</label>';

				}

			echo '</div>';

		}
	}

} // /WM_Customizer_Radiocustom

?>