<?php 

/**
 * This class provides methods for manipulating the sermon series taxonomy.
 *
 */
class FW_Sermons_Series {
    
    function __construct()  {
        
        add_action( 'init', array( $this, 'register_meta' ) );

        // Display the 'Add Series' and 'Edit Series' pages.
        add_action( 'sermon_series_add_form_fields',  array( $this, 'add_series_fields' ) );
        add_action( 'sermon_series_edit_form_fields', array( $this, 'edit_series_fields' ) );

        // Save the form data submitted from the 'Add Series' and 'Edit Series' pages.
        add_action( 'edit_sermon_series',   array( $this, 'save_series_fields' ) );
        add_action( 'create_sermon_series', array( $this, 'save_series_fields' ) );

        // Add term column labels and populate the columns with data.
        add_filter( 'manage_edit-sermon_series_columns' , array( $this, 'series_columns') );
        add_filter( 'manage_sermon_series_custom_column' , array( $this, 'series_custom_columns' ), 10, 3 );

    }

    /**
     * Register the terms meta.
     */
    public function register_meta() {

         register_meta( 'term', 'series_date',  array( $this, 'sanitize_input' ) );
         register_meta( 'term', 'series_image', array( $this, 'sanitize_input' ) );

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
     * Add additional meta fields to our default sermon series fields. These fields only
     * appear on the Sermons -> Add Series taxomony page. Be carefull with class names;
     * JavaScript event handlers are attached to some.
     */
    public function add_series_fields() {

        wp_nonce_field( basename( __FILE__ ), 'fw_sermons_series_meta_nonce' ); ?>

        <div class="form-field ">
            <label for="fw_sermons_series_date">Sermon Series Date</label>
            <input type="text" name="fw_sermons_series_date" 
                   id="fw_sermons_series_date"
                   class="fw-sermons-datepicker" value="" />
            <p class="description">Sermon series start date</p>
        </div>
        <div class="form-field">
            <label for="fw_sermons_series_image_id">Sermon Series Image</label>
            <input type="hidden" name="fw_sermons_series_image_id"
                   id="fw_sermons_series_image_id" value="" />
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
     * Add additional meta fields to our default sermon series fields. These fields only
     * appear on the Sermons -> Edit Series taxonomy page. Be carefull with class names;
     * JavaScript event handlers are attached to some.
     */
    public function edit_series_fields( $term ) {

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-date.php';

        // Convert the date string from the format that we save on the backend to
        // the format expected on the frontend.
        $series_date = get_term_meta( $term->term_id, 'fw_sermons_series_date', true );
        $series_date = FW_Sermons_Date::format_backend_to_frontend( $series_date );

        $series_image_id  = get_term_meta( $term->term_id, 'fw_sermons_series_image_id', true );
        $series_image_url = empty( $series_image_id ) ? '' : wp_get_attachment_url( $series_image_id );

        if ( empty( $series_image_id ) ) {
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
            <th scope="row"><label for="fw_sermons_series_date">Sermon Series Date</label></th>
            <td>
                <?php wp_nonce_field( basename( __FILE__ ), 'fw_sermons_series_meta_nonce' ); ?>
                <input type="text" name="fw_sermons_series_date" id="fw_sermons_series_date" 
                       class="fw-sermons-datepicker"
                       value="<?php echo esc_attr( $series_date ); ?>" />
                <p class="description">Sermon series start date</p>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row"><label for="fw_sermons_series_image">Sermon Series Image</label></th>
            <td>
                <input type="hidden" name="fw_sermons_series_image_id" 
                       id="fw_sermons_series_image_id"
                       value="<?php echo esc_attr( $series_image_id ); ?>" />
                <input type="button" class="button fw-sermons-image-upload-button" 
                       value="Upload Image" 
                       style="<?php echo $upload_button_style; ?>" />
                <input type="button" class="button fw-sermons-image-remove-button"
                       value="Remove Image"
                       style="<?php echo $remove_button_style; ?>" />
                <div class="fw-sermons-image-upload-wrapper"><img
                     class="fw-sermons-image-upload" 
                     src="<?php echo esc_attr($series_image_url); ?>"
                     style="<?php echo $image_style; ?>" /></div>
            </td>
        </tr>
        <?php 

    }

    /**
     * Save the meta field values from both of the forms above.
     */
    public function save_series_fields( $term_id ) {

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-date.php';

        if ( ! isset( $_POST['fw_sermons_series_meta_nonce'] ) ||
             ! wp_verify_nonce( $_POST['fw_sermons_series_meta_nonce'], basename( __FILE__ ) ) ) {
            return;
        }

        // For the date field, convert the string format collected on the frontend
        // to the format we save on the backend.
        $series_date = '';
        if ( isset( $_POST['fw_sermons_series_date'] ) ) {

            $series_date = $this->sanitize_input( trim( $_POST['fw_sermons_series_date'] ) );
            $series_date = $series_date ? FW_Sermons_Date::format_frontend_to_backend( $series_date ) : '';

        }

        $series_image_id = isset( $_POST['fw_sermons_series_image_id'] )
            ? $this->sanitize_input( trim( $_POST['fw_sermons_series_image_id'] ) )
            : '';

        // Allow the values to be empty.
        update_term_meta( $term_id, 'fw_sermons_series_date', $series_date );
        update_term_meta( $term_id, 'fw_sermons_series_image_id', $series_image_id );
    
    }

    /**
     * Override the given list of columns displayed in the series terms
     * table with our own.
     *
     * @param    array   List of column ids and labels.
     * @return   array   Same list.
     */
    public function series_columns( $columns ) {
  
        $columns = array(
            'cb'                      => '<input type="checkbox" />',
            'name'                    => 'Name',
            'sermon_series_date'      => 'Start Date',
            'sermon_series_thumbnail' => 'Image',
            'slug'                    => 'Slug',
            'posts'                   => 'Sermon Count'
            // We won't include a 'Description' column as it's annoying.
        );

        return $columns;

    }

    /**
     * Switch on the given column id and return the string to be displayed
     * in our series table. 
     *
     * @param    null     Deprecated field.
     * @param    string   Column id.
     * @param    int      Term id.
     * @return   string   Value to display in the series table.
     */
    public function series_custom_columns( $out = null, $column, $term_id  ) {

        switch ( $column ) {
        
           case 'sermon_series_date' :
               $out = $this->get_start_date( $term_id );
               break;

           case 'sermon_series_thumbnail' :
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

        $image_id = get_term_meta( $term_id, 'fw_sermons_series_image_id', true );

        if ( !empty( $image_id ) ) {
            $img_html = FW_Sermons_Images::get_image_html( $image_id, $classes );
            return $img_html;
        }

        return '';

    }

    /**
     * Returns a date string suitable for display in our series table.
     *
     * @param    int      Term id.
     * @return   string   Date string or empty string.
     */
    public function get_start_date( $term_id ) {

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-date.php';

        $date = get_term_meta( $term_id, 'fw_sermons_series_date', true );

        // For the date field, convert the string format saved on the backend
        // to the format we display on the frontend.
        $date = $date ? FW_Sermons_Date::format_backend_to_frontend( $date ) : '';

        return $date;

    }

}
