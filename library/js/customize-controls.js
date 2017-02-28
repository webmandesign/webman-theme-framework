/**
 * Customizer custom controls scripts
 *
 * Customizer background image controls conditional hiding:
 * If control with the ID ending on "_image" is found in theme options,
 * we conditionally hide corresponding "_attachment", "_position",
 * "_repeat", "_size" and also "_opacity" controls.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    2.2.0
 * @version  2.2.2
 */
( function( exports, $ ) {
	$( wp.customize ).on( 'ready', function() {





		// Range inputs

			$( 'input[type="range"]' )
				.wrap( '<div class="range-container" />' )
				.after( '<span class="range-value" />' )
				.on( 'input change', function() {

					// Helper variables

						var
							$this       = $( this ),
							value       = $this.val() * $this.data( 'multiply' ),
							valuePrefix = $this.data( 'prefix' ),
							valueSuffix = $this.data( 'suffix' );


					// Processing

						$this
							.next()
								.text( valuePrefix + Math.round( value ) + valueSuffix );

				} );

			$( '.range-value' )
				.each( function() {

					// Helper variables

						var
							$this       = $( this ),
							$inputField = $this.prev(),
							value       = $inputField.val() * $inputField.data( 'multiply' ),
							valuePrefix = $inputField.data( 'prefix' ),
							valueSuffix = $inputField.data( 'suffix' );


					// Processing

						$this
							.text( valuePrefix + Math.round( value ) + valueSuffix );

				} );



		// Background images

			var
				backgroundImages = [];

			// Get all image control under theme options

				$.each( $( '.control-section-theme-options [id$="_image"]' ), function( i, o ) {
					backgroundImages.push( $( o ).attr( 'id' ).replace( 'customize-control-', '' ) );
				} );

			// Hide additional background image controls if no image set

				$.each( backgroundImages, function( i, settingId ) {
					wp.customize( settingId, function( value ) {

						var
							selectors = [
								'[id$="' + settingId + '_attachment"]',
								'[id$="' + settingId + '_opacity"]',
								'[id$="' + settingId + '_position"]',
								'[id$="' + settingId + '_repeat"]',
								'[id$="' + settingId + '_size"]',
							];

						if ( ! _wpCustomizeSettings.settings[ settingId ].value ) {
							$( selectors.join() )
								.hide();
						}

						value
							.bind( function( to ) {

								if ( ! to ) {
									$( selectors.join() )
										.hide();
								} else {
									$( selectors.join() )
										.show();
								}

							} );
					} );
				} );





	} );
} )( wp, jQuery );

