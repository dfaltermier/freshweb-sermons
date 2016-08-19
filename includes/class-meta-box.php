<?php 

/**
* Load the base class
*/
class FWS_Meta_Box {
	
	function __construct()	{
		
		add_action( 'add_meta_boxes', array( $this, 'add_sermon_meta_box' ) );
		add_action( 'save_post', array( $this, 'sermon_meta_box_save' ), 10, 2 );

	}
	
	/**
	 * Load meta box
	 * 
	 */
	public function add_sermon_meta_box() {

		add_meta_box( 'fws_details', 'Sermon Details', array( $this, 'render_sermon_meta_box' ), 'sermon', 'normal', 'high' );

	}

	public function render_sermon_meta_box() {

		global $post;

		$this->meta_box_detail_fields( $post->ID );

	}

	public function sermon_meta_box_fields() {

		$fields = array(
			'_fws_audio_file',
			'_fws_video_url'
		);

		return $fields;

	}

	private function meta_box_detail_fields( $post_id ) {

		$audio_file = get_post_meta( $post_id, '_fws_audio_file', true );
		$video_url = get_post_meta( $post_id, '_fws_video_url', true );

		?>
		<?php wp_nonce_field( 'fws_sermon_save', 'fws_sermon_meta_box_nonce' ); ?>

		<table class="form-table">
			<tr>
			<td><label>Audio File</label></td>
			<td><input type="text" id="fws_audio_file" class="widefat" name="_fws_audio_file" value="<?php echo $audio_file; ?>" />
	    <input id="upload_audio_file_button" type="button" class="button" value="Upload Audio" /><br>
	        <span class="description">Upload an mp3 file</span></td>
	        </tr>
	        <tr>
	        	<td><label>Video URL</label></td>
				<td><input type="text" id="fws_video_url" class="widefat" name="_fws_video_url" value="<?php echo $video_url; ?>" /></td>
	    </table>
		<?php 
	}

	
	public function sermon_meta_box_save( $post_id, $post ) {

		if ( ! isset( $_POST['fws_sermon_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['fws_sermon_meta_box_nonce'], 'fws_sermon_save' ) ) {
			return;
		}

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX') && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) ) {
			return;
		}

		if ( isset( $post->post_type ) && 'revision' == $post->post_type ) {
			return;
		}

		if ( ! current_user_can( 'edit_posts', $post_id ) ) {
			return;
		}

		$fields = $this->sermon_meta_box_fields();

		foreach( $fields as $field ) {
			
			if ( ! empty( $_POST[ $field ] ) ) {
				$value = sanitize_text_field( $_POST[ $field ] ); 
				update_post_meta( $post_id, $field, $value );
			}

		}




	}
}