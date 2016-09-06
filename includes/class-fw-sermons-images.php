<?php 
/**
 * This class provides the interface for retrieving small thumbnail images
 * for use on the taxonomy and sermon pages.
 *
 * This class incorporates portions of code from the "Taxonomy Images"
 * WordPress plugin at https://github.com/benhuson/Taxonomy-Images.
 * Copyright 2010-2011, Michael Fields <michael@mfields.org>
 * Taxonomy Images is distributed under the terms of the GNU GPL v2.
 */
class FW_Sermons_Images {
    
    function __construct()  {
        
    }

    /**
     * Initialize ourselves.
     */
    public function init() {
        
        add_action( 'init', array( 'FW_Sermons_Images', 'add_image_size' ) );

    }

    /**
     * Return an array suitable for configuring WordPress's add_image_size().
     * This image size is intended to generate thumbnail images for our
     * taxonomy tables.
     *
     * @return   array  Configuration for 'small-thumbnail' image size.
     */
    private static function get_image_size() {

        return array(
            'name'   => 'small-thumbnail',
            'width'  => 75,
            'height' => 75,
            'crop'   => true
        );

    }

    /**
     * Register custom image size with WordPress.
     */
    public static function add_image_size() {

        $image_attrs = self::get_image_size();

        add_image_size(
            $image_attrs['name'],
            $image_attrs['width'],
            $image_attrs['height'],
            $image_attrs['crop']
        );

    }

    /**
     * Get image url.
     *
     * Return a uri to a custom image size.
     *
     * If size doesn't exist, attempt to create a resized version.
     * The output of this function should be escaped before printing to the browser.
     *
     * @param     int      $image_id   Image id.
     * @return    string               Url of custom image on success; empty string otherwise.   
     */
    public static function get_image_url( $image_id ) {
        $image_attrs = self::get_image_size();

        // Return url to custom intermediate size if it exists.
        $image_intermediate_attrs = image_get_intermediate_size( $image_id, $image_attrs['name'] );

        if ( isset( $image_intermediate_attrs['url'] ) ) {
            return $image_intermediate_attrs['url'];
        }

        // Detail image does not exist, attempt to create it.
        $wp_upload_dir = wp_upload_dir();

        if ( isset( $wp_upload_dir['basedir'] ) ) {

            // Create path to original uploaded image.
            $path = trailingslashit( $wp_upload_dir['basedir'] ) .
                    get_post_meta( $image_id, '_wp_attached_file', true );

            if ( is_file( $path ) ) {

                $image_editor = wp_get_image_editor( $path );

                if ( ! is_wp_error( $image_editor ) ) {

                    // Create a new downsized version of the original image.
                    $image_editor->resize(               
                        $image_attrs['width'],
                        $image_attrs['height'],
                        $image_attrs['crop']
                    );
                    // Modify the filename to include the image size suffix and save.
                    $new_path = $image_editor->generate_filename( null, $path );
                    $image_editor->save( $new_path );

                    // Generate and cache image metadata. Return url.
                    $meta = wp_generate_attachment_metadata( $image_id, $path );
                    wp_update_attachment_metadata( $image_id, $meta );
                    $image_intermediate_attrs = image_get_intermediate_size( $image_id, $image_attrs['name'] );
                    
                    if ( isset( $image_intermediate_attrs['url'] ) ) {
                        return $image_intermediate_attrs['url'];
                    }

                }

            }

        }

        // Custom intermediate size cannot be created, try for thumbnail.
        $image_intermediate_attrs = image_get_intermediate_size( $image_id, 'thumbnail' );

        if ( isset( $image_intermediate_attrs['url'] ) ) {
            return $image_intermediate_attrs['url'];
        }

        // Thumbnail cannot be found, try fullsize.
        $url = wp_get_attachment_url( $image_id );

        if ( ! empty( $url ) ) {
            return $url;
        }

        // We give up.
        return '';

    }

    /**
     * Get image html.
     *
     * Return the html for an image given the url and optional class names.
     * If the html cannot be constructed, the an empty string is returned.
     *
     * @param     int       Image id.
     * @param     string    String of class names.
     * @return    string    Image html on success; empty string otherwise.   
     */
    public static function get_image_html( $image_id, $classes = "" ) {
 
        $img = '';
        $image_url = self::get_image_url( $image_id );
        $alt_text = get_post_meta( $image_id , '_wp_attachment_image_alt', true );
        $classes  = 'fw-sermons-thumbnail ' . $classes;

        if ( !empty( $image_url ) ) {
            $img = '<img src="' . esc_attr( $image_url ) . '" ' .
                   'alt="' . esc_attr( $alt_text ) . '" ' .
                   'class="' . esc_attr( $classes ) . '" />';
        }

        return $img;

    }

}
