<?php
/**
 * Skinning System
 *
 * Customizer multi checkbox fields.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Skinning System
 * @copyright   2014 WebMan - Oliver Juhas
 *
 * @since       3.1
 */



/**
 * Multicheckbox
 */
class WM_Customizer_Multiselect extends WP_Customize_Control {

	public function render_content() {
		if ( ! empty( $this->choices ) && is_array( $this->choices ) ) {
			echo '<label><span class="customize-control-title">' . $this->label . '</span>';
			echo '<select name="' . $this->id . '" multiple="multiple" ' . $this->get_link() . '>';
			foreach ( $this->choices as $value => $name ) {
				echo '<option value="' . $value . '" ' . selected( $this->value(), $value, false ) . '>' . $name . '</option>';
			}
			echo '</select>';
			echo '<em>' . __( 'Press CTRL key for multiple selection.', 'wm_domain' ) . '</em>';
			echo '</label>';
		}
	}

} // /WM_Customizer_Multicheckbox

?>