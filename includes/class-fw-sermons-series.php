<?php 

/**
 * This class adds additonal fields to the sermon series taxonomy, allowing you to edit
 * and save their values.
 */
class FW_Sermons_Series {
	
	function __construct()	{
		
		add_action( 'init', array( $this, 'register_meta' ) );

		add_action( 'sermon_series_add_form_fields',  array( $this, 'add_sermon_fields' ) );
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

    /**
     * Add additional meta fields to our default sermon series fields. These fields only
     * appear on the Sermons -> Series page.
     */
	public function add_sermon_fields() {

		wp_nonce_field( basename( __FILE__ ), 'fw_sermons_series_meta_nonce' ); ?>

        <div class="form-field ">
            <label for="fw_sermons_series_date">Sermon Series Date</label>
            <input type="text" name="fw_sermons_series_date" 
                   id="fw_sermons_series_date"
                   class="fw-sermons-datepicker" value="" />
            <p class="description">Enter the sermon series start date</p>
        </div>
        <div class="form-field ">
            <label for="fw_sermon_series_image">Sermon Series Image</label>
            <input type="text" name="fw_sermons_series_image" 
                   id="fw_sermons_series_image" value="" />
            <input type="button" class="button fw-sermons-image-upload-button" value="Upload Image" />
            <p class="description">Enter the url of an image for this sermon series</p>
        </div>
	    <?php 

	}

    /**
     * Add additional meta fields to our default sermon series fields. These fields only
     * appear on the Sermons -> Series -> Edit page.
     */
	public function edit_sermon_fields( $term ) {

		$series_date  = get_term_meta( $term->term_id, 'fw_sermons_series_date', true );
		$series_image = get_term_meta( $term->term_id, 'fw_sermons_series_image', true );

	    ?>

	    <tr class="form-field">
	        <th scope="row"><label for="fw_sermons_series_date">Sermon Series Date</label></th>
	        <td>
	            <?php wp_nonce_field( basename( __FILE__ ), 'fw_sermons_series_meta_nonce' ); ?>
	            <input type="text" name="fw_sermons_series_date" id="fw_sermons_series_date" 
                       class="fw-sermons-datepicker"
                       value="<?php echo esc_attr( $series_date ); ?>" />
    	        <p class="description">Enter the sermon series start date</p>
	        </td>
	    </tr>
	    <tr class="form-field">
	        <th scope="row"><label for="fw_sermons_series_image">Sermon Series Image</label></th>
	        <td>
	            <input type="text" name="fw_sermons_series_image" id="fw_sermons_series_image" 
                       value="<?php echo esc_attr( $series_image ); ?>" />
                <input type="button" class="button fw-sermons-image-upload-button" value="Upload Image" />
                <p class="description">Enter the url of an image for this sermon series</p>
	        </td>
	    </tr>
	    <?php 

	}

    /**
     * Save the meta field values from both of the forms above.
     */
	public function save_sermon_fields( $term_id ) {

	    if ( ! isset( $_POST['fw_sermons_series_meta_nonce'] ) || 
	    	 ! wp_verify_nonce( $_POST['fw_sermons_series_meta_nonce'], basename( __FILE__ ) ) ) {
	        return;
		}

	    $series_date = isset( $_POST['fw_sermons_series_date'] ) 
	        ? $this->sanitize_input( ( $_POST['fw_sermons_series_date'] ) ) 
	        : '';

	    $series_image = isset( $_POST['fw_sermons_series_image'] ) 
	        ? $this->sanitize_input( ( $_POST['fw_sermons_series_image'] ) ) 
	        : '';

        // Allow the values to be empty.
    	update_term_meta( $term_id, 'fw_sermons_series_date',  $series_date );
    	update_term_meta( $term_id, 'fw_sermons_series_image', $series_image );
	
	}

}