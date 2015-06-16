<?php
/**
 * Customizer custom controls
 *
 * Customizer image insert.
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Customize
 *
 * @since    3.1
 * @version  5.0
 */





class {%= prefix_class %}_Control_Image extends WP_Customize_Image_Control {

	public $type = 'image';

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

		foreach ( (array) $context_uploads as $context_upload ) {
			$this->print_tab_image( esc_url_raw( $context_upload->guid ) );
		}
	}

} // /{%= prefix_class %}_Control_Image
