<?php 

/**
* Load the base class
*/
class FW_Sermons_Meta_Box {
	
	function __construct()	{
		
		add_action( 'add_meta_boxes', array( $this, 'add_sermon_meta_box' ) );
		add_action( 'save_post', array( $this, 'sermon_meta_box_save' ), 10, 2 );

	}
	
	/**
	 * Load meta box
	 * 
	 */
	public function add_sermon_meta_box() {

		add_meta_box(
			'fw-sermons-details',
			'Sermon Details',
			array( $this, 'render_sermon_meta_box' ),
			'sermon',
			'normal',
			'high'
		);

	}

	public function render_sermon_meta_box() {

		global $post;

		$this->meta_box_detail_fields( $post->ID );

	}

	private function meta_box_detail_fields( $post_id ) {

		$audio_file     = get_post_meta( $post_id, '_fw_sermons_audio_file', true );
		$video_url      = get_post_meta( $post_id, '_fw_sermons_video_url', true );
        $document_links = get_post_meta( $post_id, '_fw_sermons_document_links', true );

        // Start with at least one document, even if empty. This will ensure we display one
        // note row at a minimum.
        if ( empty( $document_links ) ) {
            $document_links = array(
                array(
                    'label' => '',
                    'url'   => ''
                )
            );
        }

		?>
		<?php wp_nonce_field( 'fw_sermons_save', 'fw_sermons_meta_box_nonce' ); ?>

		<table class="form-table">
			<tr>
			<th><label>Audio File</label></th>
			<td><input type="text" class="widefat" id="fw_sermons_audio_file" name="_fw_sermons_audio_file" 
			           value="<?php echo $audio_file; ?>" />
	            <input type="button" class="button fw-sermons-audio-upload-button"
	                   value="Upload Audio" />
	            <p class="description">Url to an mp3 audio file</p></td>
	        </tr>
	        <tr>
	        	<th><label>Video URL</label></th>
				<td><input type="text" id="fw_sermons_video_url" class="widefat" name="_fw_sermons_video_url"
				     value="<?php echo $video_url; ?>" />
				     <p class="description">Url to a video file</p></td>
		     </tr>
	        <tr>
	        	<th><label>Sermon Notes</label></th>
				<td>
	                <!-- Don't change the class names in this table. JavaScript events are
	                     attached to them. -->
	                <table class="fw-sermons-document-meta-fields">
	                    <tbody>
	                        <tr>
	                            <th>Label</th>
	                            <th>URL</th>
	                            <th></th>
	                        </tr>

	                        <?php 
	                        foreach ($document_links as $document_link) : ?>
	                            <tr class="fw-sermons-document-row">
	                                <td><input type="text" class="fw-sermons-document-link-label"
	                                     name="fw_sermons_document_link_label[]" 
	                                     value="<?php echo $document_link['label'] ?>"
	                                      maxlength="300" />
							             <input type="button" class="button fw-sermons-document-upload-button"
							                    value="Upload Document" />
							             <p class="description">Document label and url</p>
	                                </td>
	                                <td><input type="text" class="fw-sermons-document-link-url"
	                                     name="fw_sermons_document_link_url[]"
	                                     value="<?php echo $document_link['url'] ?>"
	                                     maxlength="300" /></td>
	                                <td><a href="#" class="fw-sermons-document-delete-link" style="display:none;">Delete</a></td>
	                            </tr>
	                        <?php endforeach; ?>

	                        <tr>
	                            <td><a href="#" class="fw-sermons-document-add-link">Add New</a></td>
	                            <td></td>
	                            <td></td>
	                        </tr>
	                    </tbody>
	                </table>
			    </td>
		     </tr>
	    </table>
		<?php
	}

	
	public function sermon_meta_box_save( $post_id, $post ) {

		if ( ! isset( $_POST['fw_sermons_meta_box_nonce'] ) ||
		     ! wp_verify_nonce( $_POST['fw_sermons_meta_box_nonce'], 'fw_sermons_save' ) ) {
			return;
		}

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ||
		     ( defined( 'DOING_AJAX') && DOING_AJAX ) ||
		       isset( $_REQUEST['bulk_edit'] ) ) {
			return;
		}

		if ( isset( $post->post_type ) && 'revision' == $post->post_type ) {
			return;
		}

		if ( ! current_user_can( 'edit_posts', $post_id ) ) {
			return;
		}

		if ( isset( $_POST[ '_fw_sermons_audio_file' ] ) ) {
			$value = sanitize_text_field( trim( $_POST[ '_fw_sermons_audio_file' ] ) ); 
			update_post_meta( $post_id, '_fw_sermons_audio_file', $value );

        }

		if ( isset( $_POST[ '_fw_sermons_video_url' ] ) ) {
			$value = sanitize_text_field( trim( $_POST[ '_fw_sermons_video_url' ] ) ); 
			update_post_meta( $post_id, '_fw_sermons_video_url', $value );

        }

 		if ( isset( $_POST[ 'fw_sermons_document_link_label' ] ) &&
 			 isset( $_POST[ 'fw_sermons_document_link_url' ] ) ) {
	        $document_link_labels = array_map( 'sanitize_text_field', $_POST['fw_sermons_document_link_label'] );
	        $document_link_urls   = array_map( 'sanitize_text_field', $_POST['fw_sermons_document_link_url'] );

	        // We'll consolidate our label and url arrays into one for easier storage.
	        $document_links = array();

	        for ($i = 0, $link_count = count( $document_link_labels ); $i < $link_count; $i++ ) {
				$label = trim( $document_link_labels[$i] );
				$url   = trim( $document_link_urls[$i] );

	        	// If we're missing either label or url, then disregard this entry.
                if ( ! empty( $label ) && ! empty( $url ) ) {
		            $document_links[] = array(
		                'label' => $label,
		                'url'   => $url
		            );
		        }
	        }

	        update_post_meta( $post_id, '_fw_sermons_document_links', $document_links );
        }

	}

}