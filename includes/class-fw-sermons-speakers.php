<?php 
/**
 * This class provides methods for manipulating the sermon speaker taxonomy.
 *
 */
class FW_Sermons_Speakers {
    
    function __construct()  {
        
        add_action( 'init', array( $this, 'register_meta' ) );

        // Display the 'Add Speakers' and 'Edit Speakers' pages.
        add_action( 'sermon_speaker_add_form_fields',  array( $this, 'add_speaker_fields' ) );
        add_action( 'sermon_speaker_edit_form_fields', array( $this, 'edit_speaker_fields' ) );

        // Save the form data submitted from the 'Add Speakers' and 'Edit Speakers' pages.
        add_action( 'edit_sermon_speaker',   array( $this, 'save_speaker_fields' ) );
        add_action( 'create_sermon_speaker', array( $this, 'save_speaker_fields' ) );

        // Add term column labels and populate the columns with data.
        add_filter( 'manage_edit-sermon_speaker_columns' , array( $this, 'speaker_columns') );
        add_filter( 'manage_sermon_speaker_custom_column' , array( $this, 'speaker_custom_columns' ), 10, 3 );

    }

    /**
     * Register the terms meta.
     */
    public function register_meta() {

         register_meta( 'term', 'speaker_url',   array( $this, 'sanitize_input' ) );
         register_meta( 'term', 'speaker_image', array( $this, 'sanitize_input' ) );

    }

    /*
     * Sanitize callback for our register_meta() method.
     *
     * @param  string   Unclean value.
     * @return string   Cleaned value.
     */
    public function sanitize_input( $input ) {

        $input = sanitize_text_field( $input );
        return $input;

    }

    /**
     * Add additional meta fields to our default sermon speaker fields. These fields only
     * appear on the Sermons -> Add Speakers taxonomy page. Be carefull with class names;
     * JavaScript event handlers are attached to some.
     */
    public function add_speaker_fields() {

        wp_nonce_field( basename( __FILE__ ), 'fw_sermons_speaker_meta_nonce' ); ?>

        <div class="form-field">
            <label for="fw_sermons_speaker_url">Sermon Speaker Url</label>
            <input type="text" name="fw_sermons_speaker_url" 
                   id="fw_sermons_speaker_url" value="" />
            <p class="description">Sermon speaker's biography page url</p>
        </div>
        <div class="form-field">
            <label for="fw_sermons_speaker_image_id">Sermon Speaker Image</label>
            <input type="hidden" name="fw_sermons_speaker_image_id"
                   id="fw_sermons_speaker_image_id" value="" />
            <input type="button"
                   class="button fw-sermons-image-upload-button"
                   value="Upload Image" />
            <input type="button" class="button fw-sermons-image-remove-button"
                   value="Remove Image" style="display:none;" />
            <div class="fw-sermons-image-upload-wrapper"><img 
                 class="fw-sermons-image-upload" src="" style="display:none;" /></div>
        </div>
        <?php 

    }

    /**
     * Add additional meta fields to our default sermon speaker fields. These fields only
     * appear on the Sermons -> Edit Speakers taxonomy page. Be carefull with class names;
     * JavaScript event handlers are attached to some.
     */
    public function edit_speaker_fields( $term ) {

        $speaker_url       = get_term_meta( $term->term_id, 'fw_sermons_speaker_url', true );
        $speaker_image_id  = get_term_meta( $term->term_id, 'fw_sermons_speaker_image_id', true );
        $speaker_image_url = empty( $speaker_image_id ) ? '' : wp_get_attachment_url( $speaker_image_id );

        if ( empty( $speaker_image_id ) ) {
            $upload_button_style = 'display:inline-block;';
            $remove_button_style = 'display:none;';
            $image_style = 'display:none;';
        }
        else {
            $upload_button_style = 'display:none;';
            $remove_button_style = 'display:inline-block;';
            $image_style = 'display:inline;';
        }

        ?>

        <tr class="form-field">
            <th scope="row"><label for="fw_sermons_speaker_url">Sermon Speaker Url</label></th>
            <td>
                <?php wp_nonce_field( basename( __FILE__ ), 'fw_sermons_speaker_meta_nonce' ); ?>
                <input type="text" name="fw_sermons_speaker_url" 
                       id="fw_sermons_speaker_url" 
                       value="<?php echo esc_attr( $speaker_url ); ?>" />
                <p class="description">Sermon speaker's biography page url</p>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row"><label for="fw_sermons_speaker_image_id">Sermon Speaker Image</label></th>
            <td>
                <input type="hidden" name="fw_sermons_speaker_image_id" 
                       id="fw_sermons_speaker_image_id"
                       value="<?php echo esc_attr( $speaker_image_id ); ?>" />
                <input type="button" class="button fw-sermons-image-upload-button" 
                       value="Upload Image" 
                       style="<?php echo $upload_button_style; ?>" />
                <input type="button" class="button fw-sermons-image-remove-button"
                       value="Remove Image"
                       style="<?php echo $remove_button_style; ?>" />
                <div class="fw-sermons-image-upload-wrapper"><img
                     class="fw-sermons-image-upload" 
                     src="<?php echo esc_attr($speaker_image_url); ?>"
                     style="<?php echo $image_style; ?>" /></div>
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
            ? $this->sanitize_input( trim( $_POST['fw_sermons_speaker_url'] ) )
            : '';

        $speaker_image_id = isset( $_POST['fw_sermons_speaker_image_id'] ) 
            ? $this->sanitize_input( trim( $_POST['fw_sermons_speaker_image_id'] ) )
            : '';

        // Allow the values to be empty.
        update_term_meta( $term_id, 'fw_sermons_speaker_url', $speaker_url );
        update_term_meta( $term_id, 'fw_sermons_speaker_image_id', $speaker_image_id );
    
    }

    /**
     * Override the given list of columns displayed in the speakers terms
     * table with our own.
     *
     * @param    array   List of column ids and labels.
     * @return   array   Same list.
     */
    public function speaker_columns( $columns ) {
  
        $columns = array(
            'cb'                       => '<input type="checkbox" />',
            'name'                     => 'Name',
            'sermon_speaker_thumbnail' => 'Photo',
            'slug'                     => 'Slug',
            'posts'                    => 'Sermon Count'
            // We won't include a 'Description' column as it's annoying.
        );

        return $columns;

    }

    /**
     * Switch on the given column id and return the string to be displayed
     * in our speakers table. 
     *
     * @param    null     Deprecated field.
     * @param    string   Column id.
     * @param    int      Term id.
     * @return   string   Value to display in the speakers table.
     */
    public function speaker_custom_columns( $out = null, $column, $term_id ) {

        switch ( $column ) {
        
           case 'sermon_speaker_thumbnail' :
               $out = $this->get_thumbnail_image_html( $term_id );
               break;

           default:
               $out = '';
               break;

        }

        return $out;

    }

    /**
     * Builds and returns an html string representing an image dom element. 
     *
     * @param    int      Term id.
     * @param    string   Space separated list of classes to attach to image html.
     * @return   string   Image html associated with the given term id or empty string.
     */
    public function get_thumbnail_image_html( $term_id, $classes = "" ) {

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-images.php';

        $image_id = get_term_meta( $term_id, 'fw_sermons_speaker_image_id', true );

        if ( !empty( $image_id ) ) {
            $img_html = FW_Sermons_Images::get_image_html( $image_id, $classes );
            return $img_html;
        }

        return '';

    }

}