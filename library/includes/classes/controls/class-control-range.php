<?php
/**
 * Customizer custom controls
 *
 * Customizer range / number slider.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    1.0
 * @version  1.8
 */
class {%= prefix_class %}_Control_Range extends WP_Customize_Control {

	public $type = 'range';

	public $multiplier = 1; // Value display alteration



	public function enqueue() {

		// Processing

			// Scripts

				wp_enqueue_script( 'jquery-ui-slider' );

	} // /enqueue



	public function render_content() {

		// Helper variables

			$round_precision = 0;

			// JSON [min, max, step]

				if ( empty( $this->json ) || ! is_array( $this->json ) ) {
					$this->json = array( 0, 10, 1 );
				}

				if ( is_float( $this->json[2] ) ) {
					$round_precision = explode( '.', $this->json[2] );
					$round_precision = strlen( $round_precision[1] );
				}


		// Output

			?>

			<label>
				<span class="customize-control-title"><?php echo $this->label; ?></span>
				<?php if ( $this->description ) : ?><span class="description customize-control-description"><?php echo $this->description; ?></span><?php endif; ?>

				<span class="slide-number-wrapper">
					<span id="<?php echo sanitize_title( $this->id ); ?>-slider" class="number-slider"></span>
				</span>
				<input type="number" name="<?php echo $this->id; ?>" id="<?php echo sanitize_title( $this->id ); ?>" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
			</label>

			<script><!--
				jQuery( function() {
					if ( jQuery().slider ) {

						jQuery( '#<?php echo sanitize_title( $this->id ); ?>' )
							.attr( 'type', 'hidden' );

						jQuery( '#<?php echo sanitize_title( $this->id ); ?>-slider' )
							.slider( {
								value  : <?php echo $this->value(); ?>,
								min    : <?php echo $this->json[0]; ?>,
								max    : <?php echo $this->json[1]; ?>,
								step   : <?php echo $this->json[2]; ?>,
								create : function( e, ui ) {

									jQuery( this )
										.find( '.ui-slider-handle' )
											.text( <?php echo esc_attr( round( $this->value() * floatval( $this->multiplier ), $round_precision ) ); ?> );

								},
								slide  : function( e, ui ) {

									jQuery( this )
										.find( '.ui-slider-handle' )
											.text( Math.round( ui.value * <?php echo floatval( $this->multiplier ); ?> * <?php echo pow( 10, $round_precision ); ?> ) / <?php echo pow( 10, $round_precision ); ?> );

									jQuery( '#<?php echo sanitize_title( $this->id ); ?>' )
										.val( ui.value )
											.change();

								}
							} );

					}
				} );
			//--></script>

			<?php

	} // /render_content

} // /{%= prefix_class %}_Control_Range
