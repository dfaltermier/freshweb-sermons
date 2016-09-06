<?php 

/**
 * This class creates a meta box for the Sermons custom post type.
 */
class FW_Sermons_Meta_Box {
    
    function __construct()  {
        
        add_action( 'add_meta_boxes', array( $this, 'add_sermon_meta_box' ) );
        add_action( 'save_post', array( $this, 'save_sermon_meta_box' ), 10, 2 );

    }
    
    /**
     * Load meta box.
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

    /**
     * Callback from add_meta_box() to render our meta box.
     */
    public function render_sermon_meta_box() {

        global $post;

        $this->meta_box_detail_fields( $post->ID );

    }

    /**
     * Display our meta box fields.
     *
     * @param   int   $post_id   Post id.
     */
    private function meta_box_detail_fields( $post_id ) {

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-date.php';

        // Convert the date string from the format that we save on the backend to
        // the format expected on the frontend.
        $date = get_post_meta( $post_id, '_fw_sermons_date', true );
        $date = FW_Sermons_Date::format_backend_to_frontend( $date );

        $audio_player_url   = get_post_meta( $post_id, '_fw_sermons_audio_player_url', true );
        $audio_download_url = get_post_meta( $post_id, '_fw_sermons_audio_download_url', true );
        $video_player_url   = get_post_meta( $post_id, '_fw_sermons_video_player_url', true );
        $video_download_url = get_post_meta( $post_id, '_fw_sermons_video_download_url', true );
        $document_links     = get_post_meta( $post_id, '_fw_sermons_document_links', true );

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
                <th><label>Sermon Date</label></th>
                <td><input type="text" class="widefat fw-sermons-datepicker" 
                           id="fw_sermons_date" name="fw_sermons_date"
                           value="<?php echo esc_attr( $date ); ?>" />
                    <p class="description">Date when sermon was given</p></td>
            </tr>
            <tr>
                <th><label>Audio Player Url</label></th>
                <td><input type="text" class="widefat" id="fw_sermons_audio_player_url"
                           name="fw_sermons_audio_player_url" 
                           value="<?php echo esc_attr( $audio_player_url ); ?>"
                           placeholder="<?php echo esc_attr('e.g. https://mydomain.com/sermon.mp3'); ?>" />
                    <input type="button" class="button fw-sermons-audio-upload-button"
                           value="Upload Audio" />
                    <p class="description">Url to playable sermon mp3 audio file</p></td>
            </tr> 
            <tr>
                <th><label>Audio Download Url</label></th>
                <td><input type="text" class="widefat" id="fw_sermons_audio_download_url"
                           name="fw_sermons_audio_download_url" 
                           value="<?php echo esc_attr( $audio_download_url ); ?>"
                           placeholder="<?php echo esc_attr('e.g. https://mydomain.com/sermon.mp3'); ?>" />
                    <input type="button" class="button fw-sermons-audio-upload-button"
                           value="Upload Audio" />
                    <p class="description">Url to downloadable sermon mp3 audio file (may be same url as above)</p></td>
            </tr>   
            <tr>
                <th><label>Video Player URL</label></th>
                <td><input type="text" id="fw_sermons_video_player_url" class="widefat" 
                           name="fw_sermons_video_player_url"
                           value="<?php echo esc_attr( $video_player_url ); ?>"
                           placeholder="<?php echo esc_attr('e.g. https://vimeo.com/123456789'); ?>" />
                    <input type="button" class="button fw-sermons-video-upload-button"
                           value="Upload Video" />
                    <p class="description">Url to playable sermon video file. <br />Ideally, you'll want to 
                           host your video files on Vimeo, YouTube, or equivalent video hosting service.
                           Your web hosting provider may not approve streaming videos from their web server
                           and may disrupt your service if this becomes abused.</p></td>
             </tr>
            <tr>
                <th><label>Video Download URL</label></th>
                <td><input type="text" id="fw_sermons_video_download_url" class="widefat" 
                           name="fw_sermons_video_download_url"
                           value="<?php echo esc_attr( $video_download_url ); ?>" 
                           placeholder="<?php echo esc_attr('e.g. https://player.vimeo.com/external/123456789.hd.mp4?download=1'); ?>" />
                    <input type="button" class="button fw-sermons-video-upload-button"
                           value="Upload Video" />
                    <p class="description">Url to downloadable sermon video file (may be same url as above)</p></td>
             </tr>
            <tr>
                <th><label>Sermon Documents</label></th>
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
                                               value="<?php echo esc_attr( $document_link['label'] ); ?>"
                                               placeholder="e.g. Notes on Romans"
                                               maxlength="300" />
                                        <input type="button" class="button fw-sermons-document-upload-button"
                                               value="Upload Document" />
                                        <p class="description">Document label and url</p>
                                    </td>
                                    <td><input type="text" class="fw-sermons-document-link-url"
                                               name="fw_sermons_document_link_url[]"
                                               value="<?php echo esc_attr( $document_link['url'] ); ?>"
                                               placeholder="e.g. https://mydomain.com/sermon.pdf"
                                               maxlength="300" /></td>
                                    <td><a href="#" class="fw-sermons-document-delete-link" style="display:none;">Delete</a></td>
                                </tr>
                            <?php endforeach; ?>

                            <tr>
                                <td colspan="3"><a href="#" class="fw-sermons-document-add-link">Add New</a></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
             </tr>
        </table>
        <?php
    }

    /**
     * Save our meta box fields.
     *
     * @param   int       $post_id   Post id.
     * @param   WP_Post   $post      Post object (https://developer.wordpress.org/reference/classes/wp_post/)
     */
    public function save_sermon_meta_box( $post_id, $post ) {
        
        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-date.php';

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

        $fields = array(
            'fw_sermons_date',
            'fw_sermons_audio_player_url',
            'fw_sermons_audio_download_url',
            'fw_sermons_video_player_url',
            'fw_sermons_video_download_url'
        );

        // Update each field. 
        foreach( $fields as $field ) {

            if ( isset( $_POST[ $field ] ) ) {
                $value = sanitize_text_field( trim( $_POST[ $field ] ) ); 

                // For the date field, convert the string format collected on
                // the frontend to the format we save on the backend.
                if ( $field === 'fw_sermons_date' ) {
                    $value = FW_Sermons_Date::format_frontend_to_backend( $value );
                }

                update_post_meta( $post_id, '_'.$field, $value );
            }

        }

        // Update the document links.
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