<?php 

/**
 * This class adds additonal fields to the sermon speakers taxonomy, allowing you to edit
 * and save their values.
 */
class FW_Sermons_Speakers {
    
    function __construct()  {
        
        add_action( 'init', array( $this, 'register_meta' ) );

        add_action( 'sermon_speaker_add_form_fields',  array( $this, 'add_speaker_fields' ) );
        add_action( 'sermon_speaker_edit_form_fields', array( $this, 'edit_speaker_fields' ) );

        add_action( 'edit_sermon_speaker',   array( $this, 'save_speaker_fields' ) );
        add_action( 'create_sermon_speaker', array( $this, 'save_speaker_fields' ) );

    }

    public function register_meta() {

         register_meta( 'term', 'speaker_url',   array( $this, 'sanitize_input' ) );
         register_meta( 'term', 'speaker_image', array( $this, 'sanitize_input' ) );

    }

    public function sanitize_input( $input ) {

        $input = sanitize_text_field( $input );

        return $input;

    }

    /**
     * Add additional meta fields to our default sermon speaker fields. These fields only
     * appear on the Sermons -> Add Speakers taxonomy page.
     */
    public function add_speaker_fields() {

        wp_nonce_field( basename( __FILE__ ), 'fw_sermons_speaker_meta_nonce' ); ?>

        <div class="form-field ">
            <label for="fw_sermons_speaker_url">Sermon Speaker Url</label>
            <input type="text" name="fw_sermons_speaker_url" 
                   id="fw_sermons_speaker_url" value="" />
            <p class="description">Sermon speaker's biography page url</p>
        </div>
        <div class="form-field ">
            <label for="fw_sermons_speaker_image">Sermon Speaker Image</label>
            <input type="text" name="fw_sermons_speaker_image" 
                   id="fw_sermons_speaker_image" value="" />
            <input type="button" class="button fw-sermons-image-upload-button" value="Upload Image" />
            <p class="description">Sermon speaker's image url</p>
        </div>
        <?php 

    }

    /**
     * Add additional meta fields to our default sermon speaker fields. These fields only
     * appear on the Sermons -> Edit Speakers taxonomy page.
     */
    public function edit_speaker_fields( $term ) {

        $speaker_url   = get_term_meta( $term->term_id, 'fw_sermons_speaker_url', true );
        $speaker_image = get_term_meta( $term->term_id, 'fw_sermons_speaker_image', true );

        ?>

        <tr class="form-field">
            <th scope="row"><label for="fw_sermons_speaker_url">Sermon Speaker Url</label></th>
            <td>
                <?php wp_nonce_field( basename( __FILE__ ), 'fw_sermons_speaker_meta_nonce' ); ?>
                <input type="text" name="fw_sermons_speaker_url" id="fw_sermons_speaker_url" 
                       value="<?php echo esc_attr( $speaker_url ); ?>" />
                <p class="description">Sermon speaker's biography page url</p>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row"><label for="fw_sermons_speaker_image">Sermon Speaker Image</label></th>
            <td>
                <input type="text" name="fw_sermons_speaker_image" id="fw_sermons_speaker_image" 
                       value="<?php echo esc_attr( $speaker_image ); ?>" />
                <input type="button" class="button fw-sermons-image-upload-button" value="Upload Image" />
                <p class="description">Sermon speaker's image url</p>
            </td>
        </tr>
        <?php 

    }

    /**
     * Save the meta field values from both of the forms above.
     */
    public function save_speaker_fields( $term_id ) {

        if ( ! isset( $_POST['fw_sermons_speaker_meta_nonce'] ) || 
             ! wp_verify_nonce( $_POST['fw_sermons_speaker_meta_nonce'], basename( __FILE__ ) ) ) {
            return;
        }

        $speaker_url = isset( $_POST['fw_sermons_speaker_url'] ) 
            ? $this->sanitize_input( ( $_POST['fw_sermons_speaker_url'] ) ) 
            : '';

        $speaker_image = isset( $_POST['fw_sermons_speaker_image'] ) 
            ? $this->sanitize_input( ( $_POST['fw_sermons_speaker_image'] ) ) 
            : '';

        // Allow the values to be empty.
        update_term_meta( $term_id, 'fw_sermons_speaker_url',  $speaker_url );
        update_term_meta( $term_id, 'fw_sermons_speaker_image', $speaker_image );
    
    }

}