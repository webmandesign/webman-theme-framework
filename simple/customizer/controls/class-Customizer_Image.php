<?php
/**
 * Customizer custom controls
 *
 * Customizer image insert.
 *
 * @package    Receptar
 * @copyright  2015 WebMan - Oliver Juhas
 *
 * @since    1.0
 * @version  1.0
 */



/**
 * Add uploaded images tab to Image control
 */
class Receptar_Customizer_Image extends WP_Customize_Image_Control {

	/**
	 * Adding an .ico into supported image file formats
	 */
	public $extensions = array( 'ico', 'jpg', 'jpeg', 'gif', 'png' );

	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Search for images within the defined context
	 */
	public function tab_uploaded() {
		$context_uploads = get_posts( array(
				'post_type'  => 'attachment',
				'meta_key'   => '_wp_attachment_context',
				'meta_value' => $this->context,
				'orderby'    => 'post_date',
				'nopaging'   => true,
			) );
		?>

		<div class="uploaded-target"></div>

		<?php
		if ( empty( $context_uploads ) ) {
			return;
		}

		foreach ( (array) $context_uploads as $receptar_context_upload ) {
			$this->print_tab_image( esc_url_raw( $receptar_context_upload->guid ) );
		}
	}

} // /Receptar_Customizer_Image

?>