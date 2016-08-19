<?php 

/**
* Load the base class
*/
class FWS_Term_Meta {
	
	function __construct()	{
		
		add_action( 'init', array( $this, 'register_meta' ) );

		add_action( 'sermon_series_add_form_fields', array( $this, 'new_sermon_fields' ) );
		add_action( 'sermon_series_edit_form_fields', array( $this, 'edit_sermon_fields' ) );

		add_action( 'edit_sermon_series',   array( $this, 'save_sermon_fields' ) );
		add_action( 'create_sermon_series', array( $this, 'save_sermon_fields' ) );

	}

	public function register_meta() {

		 register_meta( 'term', 'series_dates', array( $this, 'sanitize_input' ) );
		 register_meta( 'term', 'series_image', array( $this, 'sanitize_input' ) );

	}

	public function sanitize_input( $input ) {

	    $input = sanitize_text_field( $input );

	    return $input;

	}

	public function new_sermon_fields() {

		wp_nonce_field( basename( __FILE__ ), 'fws_series_meta_nonce' ); ?>

	    <div class="form-field ">
	        <label for="sermon_series_dates">Sermon Series Dates</label>
	        <input type="text" name="sermon_series_dates" id="sermon_series_dates" value="" />

	        <label for="sermon_series_image">Sermon Image</label>
	        <input type="text" name="sermon_series_image" id="sermon_series_image" value="" /> <input id="upload_sermon_image_button" type="button" class="button" value="Upload Image" />
	    </div>
	    <?php 
	}

	public function edit_sermon_fields( $term ) {

		$sermon_dates = get_term_meta( $term->term_id, 'sermon_series_dates', true );
		$sermon_image = get_term_meta( $term->term_id, 'sermon_series_image', true );

	    ?>

	    <tr class="form-field">
	        <th scope="row"><label for="sermon_series_dates">Sermon Series Dates</label></th>
	        <td>
	            <?php wp_nonce_field( basename( __FILE__ ), 'fws_series_meta_nonce' ); ?>
	            <input type="text" name="sermon_series_dates" id="sermon_series_dates" value="<?php echo esc_attr( $sermon_dates ); ?>" " />
	        </td>
	    </tr>
	    <tr class="form-field">
	        <th scope="row"><label for="sermon_series_image">Sermon Series Image</label></th>
	        <td>
	            <input type="text" name="sermon_series_image" id="sermon_series_image" value="<?php echo esc_attr( $sermon_image ); ?>" " /> <input id="upload_sermon_image_button" type="button" class="button" value="Upload Image" />
	        </td>
	    </tr>
	    <?php 
	}


	public function save_sermon_fields( $term_id ) {

	    if ( ! isset( $_POST['fws_series_meta_nonce'] ) || ! wp_verify_nonce( $_POST['fws_series_meta_nonce'], basename( __FILE__ ) ) )
	        return;

	    $series_dates = isset( $_POST['sermon_series_dates'] ) ? $this->sanitize_input( ( $_POST['sermon_series_dates'] ) ) : '';
	    $series_image = isset( $_POST['sermon_series_image'] ) ? $this->sanitize_input( ( $_POST['sermon_series_image'] ) ) : '';

	    if ( ! empty( $series_dates ) ) {
	    	update_term_meta( $term_id, 'sermon_series_dates', $series_dates );
	    }

	    if ( ! empty( $series_image ) ) {
	    	update_term_meta( $term_id, 'sermon_series_image', $series_image );
	    }
	
	}

}