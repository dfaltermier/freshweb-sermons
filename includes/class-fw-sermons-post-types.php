<?php
 /** 
 * This class creates the Sermons custom post type and registers the associated
 * taxonomies.
 *
 * @package    FreshWeb_Church_Sermons
 * @subpackage Functions
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.0
 */
class FW_Sermons_Post_Types {
    
    function __construct()  {
        
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );

        // Add additional columns to our table.
        add_filter( 'manage_sermon_posts_columns' , array( $this, 'add_sermon_columns' ) );
        add_action( 'manage_sermon_posts_custom_column' , array( $this, 'populate_sermon_columns' ), 10, 2 );

        // Add a select menu at the top of the CPT table so posts can be filtered by taxonomies.
        add_action( 'restrict_manage_posts', array( $this, 'add_taxonomy_filters' ) );

        // Remove the [publish] date select menu from the 'All Sermons' page. Not needed
        // since we don't display the publish date.
        // TODO: remove this? Uneeded?
        //add_filter( 'months_dropdown_results', array( $this, 'remove_date_filter' ), 10, 2 );

    }

    /**
     * Register our Sermon post type.
     *
     * @since  1.1.0
     *
     */
    public function register_post_types() {

        $sermon_labels =  array(
            'name'               => 'Sermons',
            'singular_name'      => 'Sermon',
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New Sermon',
            'edit_item'          => 'Edit Sermon',
            'new_item'           => 'New Sermon',
            'all_items'          => 'All Sermons',
            'view_item'          => 'View Sermon',
            'search_items'       => 'Search Sermons',
            'not_found'          => 'No Sermons Found',
            'not_found_in_trash' => 'No Sermons Found In Trash',
            'menu_name'          => 'Sermons'
        );

        $sermon_args = array(
            'labels'             => $sermon_labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'menu_icon'          => 'dashicons-book',
            'rewrite'            => 'sermons',
            'has_archive'        => 'true',
            'hierarchical'       => true,
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'author' )
        );

        register_post_type( 'sermon', $sermon_args );
        
    }

    /**
     * Register taxonomies
     *
     * @since  1.1.0
     */
    public function register_taxonomies() {

        /** Series */
        $series_labels = array(
            'name'          => 'Series',
            'singular_name' => 'Series',
            'search_items'  => 'Search Series',
            'all_items'     => 'All Series',
            'parent_item'   => 'Parent Series',
            'edit_item'     => 'Edit Series',
            'update_item'   => 'Update Series',
            'add_new_item'  => 'Add New Series',
            'new_item_name' => 'New Series',
            'menu_name'     => 'Series',
            'not_found'     => 'No series found.'
        );

        $series_args = array(
            'hierarchical' => true,
            'labels'       => $series_labels,
            'show_ui'      => true,
            'query_var'    => 'sermon_series',
            'rewrite'      => array( 'slug' => 'sermons/series', 'with_front' => true, 'hierarchical' => true )
        );

        register_taxonomy( 'sermon_series', array( 'sermon' ), $series_args );

        /** Speaker */
        $speaker_labels = array(
            'name'          => 'Speakers',
            'singular_name' => 'Speaker',
            'search_items'  => 'Search Speakers',
            'all_items'     => 'All Speakers',
            'parent_item'   => 'Parent Speaker',
            'edit_item'     => 'Edit Speaker',
            'update_item'   => 'Update Speaker',
            'add_new_item'  => 'Add New Speaker',
            'new_item_name' => 'New Speaker',
            'menu_name'     => 'Speakers',
            'not_found'     => 'No speakers found.'
        );

        $speaker_args = array(
            'hierarchical' => true,
            'labels'       => $speaker_labels,
            'show_ui'      => true,
            'query_var'    => 'sermon_speaker',
            'rewrite'      => array( 'slug' => 'sermons/speaker', 'with_front' => true, 'hierarchical' => true )
        );

        register_taxonomy( 'sermon_speaker', array( 'sermon' ), $speaker_args );

        /** Topic */
        $topic_labels = array(
            'name'          => 'Topics',
            'singular_name' => 'Topic',
            'search_items'  => 'Search Topics',
            'all_items'     => 'All Topics',
            'parent_item'   => 'Parent Topic',
            'edit_item'     => 'Edit Topic',
            'update_item'   => 'Update Topic',
            'add_new_item'  => 'Add New Topic',
            'new_item_name' => 'New Topic',
            'menu_name'     => 'Topics',
            'not_found'     => 'No topics found.'
        );

        $topic_args = array(
            'hierarchical' => true,
            'labels'       => $topic_labels,
            'show_ui'      => true,
            'query_var'    => 'sermon_topic',
            'rewrite'      => array( 'slug' => 'sermons/topic', 'with_front' => true, 'hierarchical' => false )
        );

        register_taxonomy( 'sermon_topic', array( 'sermon' ), $topic_args );

        /** Book */
        $book_labels = array(
            'name'          => 'Books',
            'singular_name' => 'Book',
            'search_items'  => 'Search Books',
            'all_items'     => 'All Books',
            'parent_item'   => 'Parent Book',
            'edit_item'     => 'Edit Book',
            'update_item'   => 'Update Book',
            'add_new_item'  => 'Add New Book',
            'new_item_name' => 'New Book',
            'menu_name'     => 'Books',
            'not_found'     => 'No books found.'
        );

        $book_args = array(
            'hierarchical' => true,
            'labels'       => $book_labels,
            'show_ui'      => true,
            'query_var'    => 'sermon_book',
            'rewrite'      => array( 'slug' => 'sermons/book', 'with_front' => true, 'hierarchical' => false )
        );

        register_taxonomy( 'sermon_book', array( 'sermon' ), $book_args );

    }

    /**
     * Configure the given list of table columns with our own.
     *
     * @since   1.1.0
     *
     * @param   array  $columns  List of column ids and labels.
     * @return  array            Same list.
     */
    public function add_sermon_columns( $columns ) {
  
        unset( $columns['author'] );
        unset( $columns['date'] );

        $columns = array_merge(
            $columns,
            array(
                'sermon_players'   => 'Players',
                'sermon_downloads' => 'Downloads',
                'sermon_series'    => 'Series',
                'sermon_speaker'   => 'Speaker',
                'featured_image'   => 'Image',
                'date'             => 'Publish Date'
            )
        );

        return $columns;

    }

    /**
     * Switch on the given column id and display an appropriate string
     * in our Sermon table.
     *
     * @since  1.1.0
     *
     * @param  string  $column    Column id for the value to fetch. See add_sermon_columns().
     * @param  int     $post_id   Post id.
     */
    public function populate_sermon_columns( $column, $post_id  ) {

        switch ( $column ) {

            case 'sermon_players' :
                echo $this->get_sermon_players( $post_id );
                break;

            case 'sermon_downloads' :
                echo $this->get_sermon_downloads( $post_id );
                break;

            case 'sermon_series' :
                echo $this->get_sermon_series( $post_id );
                break;

            case 'sermon_speaker' :
                echo $this->get_sermon_speaker( $post_id );
                break;
            
            case 'featured_image' :
                echo $this->get_thumbnail_image_html( $post_id );
                break;

            default:
                echo '';
                break;

        }
    }

    /**
     * Returns the media players that are available for viewing/listening with the given Sermon post id. 
     *
     * @since   1.1.0
     *
     * @param   int     $post_id   Post id.
     * @return  string             Formats (e.g.: 'Audio, Video')
     */
    public function get_sermon_players( $post_id ) {

        $format = array();

        $audio_player_url = get_post_meta( $post_id, '_fw_sermons_audio_player_url', true );
        $video_player_url = get_post_meta( $post_id, '_fw_sermons_video_player_url', true );

        if ( ! empty( $audio_player_url ) ) {
            $format[] = 'Audio';
        }

        if ( ! empty( $video_player_url ) ) {
            $format[] = 'Video';
        }

        return join( ', ', $format );

    }

    /**
     * Returns the media formats that are available for download for the given Sermon post id. 
     *
     * @since   1.1.0
     *
     * @param   int     $post_id   Post id.
     * @return  string             Formats (e.g.: 'Audio, Video')
     */
    public function get_sermon_downloads( $post_id ) {

        $format = array();

        $audio_download_url = get_post_meta( $post_id, '_fw_sermons_audio_download_url', true );
        $video_download_url = get_post_meta( $post_id, '_fw_sermons_video_download_url', true );
        $document_links     = get_post_meta( $post_id, '_fw_sermons_document_links', true );
        
        if ( ! empty( $audio_download_url ) ) {
            $format[] = 'Audio';
        }

        if ( ! empty( $video_download_url ) ) {
            $format[] = 'Video';
        }

        if ( ! empty( $document_links ) ) {
            $format[] = 'Sermon Notes';
        }

        return join( ', ', $format );

    }

    /**
     * Returns the series name associated with the given Sermon post id. 
     *
     * @since   1.1.0
     *
     * @param   int     $post_id   Post id.
     * @return  string             Series name.
     */
    public function get_sermon_series( $post_id ) {

        $terms = get_the_terms( $post_id, 'sermon_series' );

        if ( !empty( $terms ) ) {
            foreach( $terms as $term ) {
                return $term->name;
            }
        } else {
            return '';
        }
        
    }

    /**
     * Returns the speaker name associated with the given Sermon post id. 
     *
     * @since   1.1.0
     *
     * @param   int      $post_id   Post id.
     * @return  string              Speaker name.
     */
    public function get_sermon_speaker( $post_id ) {

        $terms = get_the_terms( $post_id, 'sermon_speaker' );

        if ( !empty( $terms ) ) {
            foreach( $terms as $term ) {
                return $term->name;
            }
        } else {
            return '';
        }
        
    }

    /**
     * Builds and returns an image html string with a thumbnail view of the post's
     * featured image. 
     *
     * @since   1.1.0
     *
     * @param   int      $post_id  Post id.
     * @param   string   $classes  Optional. Space separated list of classes to attach to image html.
     * @return  string             Image html associated with the given post id or empty string.
     */
    public function get_thumbnail_image_html( $post_id, $classes = "" ) {

        $image_id = get_post_thumbnail_id( $post_id );

        if ( ! empty( $image_id ) ) {
            $img_html = wp_get_attachment_image(
                $image_id, 
                'thumbnail', 
                false, 
                array( 'class' => 'fw-sermons-featured-thumbnail ' . esc_attr( $classes ) )
            );
            return $img_html;
        }

        return '';

    }

    /**
     * Action for displaying one or more select menus on our 'All Sermons' page.
     * Each menu contains the list of terms for one taxonomy. The selected term
     * will act as a filter when the [WordPress] Filter button is clicked.
     *
     * Portions of code taken from Mike Hemberger's example at:
     * http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
     *
     * @since  1.1.0
     */
    public function add_taxonomy_filters() {
        global $typenow;

        if ( $typenow === 'sermon' ) {

            // An array of all the taxonomies you want to display. Use the taxonomy slug.
            $taxonomy_slugs = array( 'sermon_series', 'sermon_speaker' );

            foreach ( $taxonomy_slugs as $taxonomy_slug ) {

                $selected  = isset($_GET[$taxonomy_slug]) ? $_GET[$taxonomy_slug] : '';
                $taxonomy_obj   = get_taxonomy( $taxonomy_slug );
                $taxonomy_label = strtolower( $taxonomy_obj->label );

                wp_dropdown_categories(array(
                    'show_option_all' => __("All $taxonomy_label" ),
                    'taxonomy'        => $taxonomy_slug,
                    'name'            => $taxonomy_slug,
                    'orderby'         => 'name',
                    'selected'        => $selected,
                    'show_count'      => true,
                    'hide_empty'      => true,
                    'value_field'     => 'slug'
                ));

            }
        };
    }

    // TODO: remove this? Uneeded?
    /**
     * Action for removing the date select menu from the 'All Sermons' page.
     * It's not useful to us since we are not displaying the publishing dates.
     *
     * @since  1.1.0
     *
     * @param  array   $months      Array of month objects.
     * @param  string  $post_type   Post type of which we expect 'sermon'.
     * @return array                $months array.
     */
    /* 
    public function remove_date_filter( $months, $post_type ) {

        // Returning an empty array will remove the select menu.
        return ( $post_type === 'sermon' ) ? array() : $months;

    }
    */

}
