<?php
/**
 * Skinning System
 *
 * Customizer number slider.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Skinning System
 * @copyright   2014 WebMan - Oliver Juhas
 *
 * @since       3.1
 */



/**
 * Number slider
 */
class WM_Customizer_Slider extends WP_Customize_Control {

	public function enqueue() {
		wp_enqueue_style( 'wm-theme-customizer' );
		wp_enqueue_script( 'jquery-ui-slider' );
	}

	public function render_content() {
		if ( empty( $this->json ) || ! is_array( $this->json ) ) {
			$this->json = array( 0, 10, 1 ); // [min, max, step]
		}
		?>
		<label>
			<span class="customize-control-title"><?php echo $this->label; ?></span>

			<span class="slide-number-wrapper">
				<span id="<?php echo sanitize_title( $this->id ); ?>-slider" class="number-slider"></span>
			</span>
			<input type="text" name="<?php echo $this->id; ?>" id="<?php echo sanitize_title( $this->id ); ?>" value="<?php echo esc_attr( $this->value() ); ?>" size="5" maxlength="5" readonly="readonly" <?php $this->link(); ?> />
		</label>

		<script><!--
			jQuery( function() {
				if ( jQuery().slider ) {
					jQuery( '#<?php echo sanitize_title( $this->id ); ?>-slider' ).slider( {
						value : <?php echo $this->value(); ?>,
						min   : <?php echo $this->json[0]; ?>,
						max   : <?php echo $this->json[1]; ?>,
						step  : <?php echo $this->json[2]; ?>,
						slide : function( event, ui ) {
							jQuery( '#<?php echo sanitize_title( $this->id ); ?>' ).val( ui.value ).change();
						}
					} );
				}

			} );
		//--></script>
		<?php
	}

} // /WM_Customizer_Slider

?>