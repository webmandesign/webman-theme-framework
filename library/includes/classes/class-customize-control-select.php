<?php defined( 'ABSPATH' ) || exit;
/**
 * Customizer custom controls
 *
 * Customizer select field (with optgroups).
 *
 * @subpackage  Customize
 *
 * @package    WebMan WordPress Theme Framework
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  2.8.0
 */
class Theme_Slug_Customize_Control_Select extends WP_Customize_Control {

	public $type = 'select';



	public function render_content() {

		// Output

			if ( ! empty( $this->choices ) && is_array( $this->choices ) ) :

				?>

				<label>
					<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php if ( $this->description ) : ?><span class="description customize-control-description"><?php echo wp_kses_post( force_balance_tags( $this->description ) ); ?></span><?php endif; ?>

					<select name="<?php echo esc_attr( $this->id ); ?>" <?php $this->link(); ?>>
						<?php

						foreach ( $this->choices as $value => $name ) {

							if ( 0 === strpos( $value, 'optgroup' ) ) {
								echo '<optgroup label="' . esc_attr( $name ) . '">';
							} elseif ( 0 === strpos( $value, '/optgroup' ) ) {
								echo '</optgroup>';
							} else {
								echo '<option value="' . esc_attr( $value ) . '" ' . selected( $this->value(), $value, false ) . '>' . esc_html( $name ) . '</option>';
							}

						}

						?>
					</select>
				</label>

				<?php

			endif;

	} // /render_content

} // /Theme_Slug_Customize_Control_Select
