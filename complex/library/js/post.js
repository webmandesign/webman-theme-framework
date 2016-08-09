/**
 * Post edit scripts
 *
 * @see  wp-admin/js/post.js
 *
 * @package     WebMan WordPress Theme Framework
 * @subpackage  Visual Editor
 *
 * @since    1.7.2
 * @version  1.7.2
 */





jQuery( document ).ready( function( $ ) {





	/**
	 * Adding page template class on TinyMCE editor HTML body
	 *
	 * @since    1.7.2
	 * @version  1.7.2
	 */
	if ( typeof tinymce !== 'undefined' ) {

		jQuery( '#page_template' )
			.on( 'change.set-editor-class', function() {

				// Helper variables

					var editor,
					    body,
					    template = jQuery( this ).val();

					template = template.replace( '.php', '' ).substr( template.lastIndexOf( '/' ) + 1, template.length );


				// Processing

					if ( template && ( editor = tinymce.get( 'content' ) ) ) {

						body = editor.getBody();

						body.className = body.className.replace( /\bpage-template-[^ ]+/, '' );

						editor.dom.addClass( body, template == 'page-template-0' ? 'page-template-default' : 'page-template-' + template );

						jQuery( document ).trigger( 'editor-classchange' );

					}

			} );

	}





} );
