<?php 
/**
 * This class creates the Sermons custom post type and registers the associated
 * taxonomies.
 */
class FW_Sermons_Post_Types {
    
    function __construct()  {
        
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );

        add_filter( 'manage_sermon_posts_columns' , array( $this, 'sermon_columns') );
        add_action( 'manage_sermon_posts_custom_column' , array( $this, 'sermon_custom_columns' ), 10, 2 );

    }

    /**
     * Register post types.
     * 
     */
    public function register_post_types() {

        $sermon_labels =  array(
            'name'                  => 'Sermons',
            'singular_name'         => 'Sermon',
            'add_new'               => 'Add New',
            'add_new_item'          => 'Add New Sermon',
            'edit_item'             => 'Edit Sermon',
            'new_item'              => 'New Sermon',
            'all_items'             => 'All Sermons',
            'view_item'             => 'View Sermon',
            'search_items'          => 'Search Sermons',
            'not_found'             => 'No Sermons Found',
            'not_found_in_trash'    => 'No Sermons Found In Trash',
            'menu_name'             => 'Sermons',
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
            'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'author' ),
        );

        register_post_type( 'sermon', $sermon_args );
        
    }

    /**
     * Register taxonomies
     * 
     */
    public function register_taxonomies() {

        /** Series */
        $series_labels = array(
            'name'              => 'Series',
            'singular_name'     => 'Series',
            'search_items'      => 'Search Series',
            'all_items'         => 'All Series',
            'parent_item'       => 'Parent Series',
            'edit_item'         => 'Edit Series',
            'update_item'       => 'Update Series',
            'add_new_item'      => 'Add New Series',
            'new_item_name'     => 'New Series',
            'menu_name'         => 'Series',
            'not_found'         => 'No series found.'
        );

        $series_args = array(
            'hierarchical' => true,
            'labels'       => $series_labels,
            'show_ui'      => true,
            'query_var'    => 'series',
            'rewrite'      => array('slug' => 'series', 'with_front' => false, 'hierarchical' => true ),
        );
        register_taxonomy( 'sermon_series', array('sermon'), $series_args );


        /** Speaker */
        $speaker_labels = array(
            'name'              => 'Speakers',
            'singular_name'     => 'Speaker',
            'search_items'      => 'Search Speakers',
            'all_items'         => 'All Speakers',
            'parent_item'       => 'Parent Speaker',
            'edit_item'         => 'Edit Speaker',
            'update_item'       => 'Update Speaker',
            'add_new_item'      => 'Add New Speaker',
            'new_item_name'     => 'New Speaker',
            'menu_name'         => 'Speakers',
            'not_found'         => 'No speakers found.'
        );

        $speaker_args = array(
            'hierarchical' => true,
            'labels'       => $speaker_labels,
            'show_ui'      => true,
            'query_var'    => 'speaker',
            'rewrite'      => array('slug' => 'speaker', 'with_front' => false, 'hierarchical' => true ),
        );
        register_taxonomy( 'sermon_speaker', array('sermon'), $speaker_args );


        /** Topic */
        $topic_labels = array(
            'name'              => 'Topics',
            'singular_name'     => 'Topic',
            'search_items'      => 'Search Topics',
            'all_items'         => 'All Topics',
            'parent_item'       => 'Parent Topic',
            'edit_item'         => 'Edit Topic',
            'update_item'       => 'Update Topic',
            'add_new_item'      => 'Add New Topic',
            'new_item_name'     => 'New Topic',
            'menu_name'         => 'Topics',
            'not_found'         => 'No topics found.'
        );

        $topic_args = array(
            'hierarchical' => true,
            'labels'       => $topic_labels,
            'show_ui'      => true,
            'query_var'    => 'topic',
            'rewrite'      => array('slug' => 'topic', 'with_front' => false, 'hierarchical' => false ),
        );
        register_taxonomy( 'sermon_topic', array('sermon'), $topic_args );


        /** Book */
        $book_labels = array(
            'name'              => 'Books',
            'singular_name'     => 'Book',
            'search_items'      => 'Search Books',
            'all_items'         => 'All Books',
            'parent_item'       => 'Parent Book',
            'edit_item'         => 'Edit Book',
            'update_item'       => 'Update Book',
            'add_new_item'      => 'Add New Book',
            'new_item_name'     => 'New Book',
            'menu_name'         => 'Books',
            'not_found'         => 'No books found.'
        );

        $book_args = array(
            'hierarchical' => true,
            'labels'       => $book_labels,
            'show_ui'      => true,
            'query_var'    => 'topic',
            'rewrite'      => array('slug' => 'book', 'with_front' => false, 'hierarchical' => false ),
        );
        register_taxonomy( 'sermon_book', array('sermon'), $book_args );

    }

    /**
     * Override the given list of columns displayed in the sermon
     * table with our own.
     *
     * @param    array   List of column ids and labels.
     * @return   array   Same list.
     */
    public function sermon_columns( $columns ) {
  
        unset( $columns['author'] );
        unset( $columns['date'] );

        $columns = array_merge(
            $columns,
            array(
                'sermon_date'    => 'Date',
                'sermon_series'  => 'Series',
                'sermon_speaker' => 'Speaker',
                'featured_image' => 'Image'
            )
        );

        return $columns;

    }

    /**
     * Switch on the given column id and display an appropriate string
     * in our Sermon table.
     *
     * @param    string   Column id.
     * @param    int      Post id.
     */
    public function sermon_custom_columns( $column, $post_id  ) {

        switch ( $column ) {

            case 'sermon_date' :
                echo $this->get_sermon_date( $post_id );
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
     * Returns the date associated with the given Sermon post id. 
     *
     * @param    int      Post id.
     * @return   string   Date string.
     */
    public function get_sermon_date( $post_id ) {

        $date = get_post_meta( $post_id, '_fw_sermons_date', true );
        return $date;

    }

    /**
     * Returns the series name associated with the given Sermon post id. 
     *
     * @param    int      Post id.
     * @return   string   Series name.
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
     * @param    int      Post id.
     * @return   string   Speaker name.
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
     * @param    int      Post id.
     * @param    string   Space separated list of classes to attach to image html.
     * @return   string   Image html associated with the given post id or empty string.
     */
    public function get_thumbnail_image_html( $post_id, $classes = "" ) {

        $image_id = get_post_thumbnail_id( $post_id );

        if ( !empty( $image_id ) ) {

            require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-images.php';
            $img_html = FW_Sermons_Images::get_image_html( $image_id, $classes );
            return $img_html;

        }

        return '';

    }

}