<?php 
/**
 * This class creates the Sermons custom post type and registers the associated
 * taxonomies.
 */
class FW_Sermons_Post_Types {
    
    function __construct()  {
        
        add_action( 'init', array( $this, 'register_post_types' ) );
        add_action( 'init', array( $this, 'register_taxonomies' ) );

        // Add additional columns to our table.
        add_filter( 'manage_sermon_posts_columns' , array( $this, 'sermon_columns') );
        add_action( 'manage_sermon_posts_custom_column' , array( $this, 'sermon_custom_columns' ), 10, 2 );

        // Make some columns sortable.
        add_filter( 'manage_edit-sermon_sortable_columns' , array( $this, 'sermon_sort_columns') );
        add_filter( 'request', array( $this, 'sermon_sort_columns_orderby' ) );

        // Add a select menu at the top of the CPT table so posts can be filtered by taxonomies.
        add_action( 'restrict_manage_posts', array( $this, 'add_taxonomy_filters' ) );
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
            'query_var'    => 'sermon_series',
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
            'query_var'    => 'sermon_speaker',
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
            'query_var'    => 'sermon_topic',
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
            'query_var'    => 'sermon_book',
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

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-date.php';

        // Convert the date string from the format that we save on the backend to
        // the format expected on the frontend.
        $date = get_post_meta( $post_id, '_fw_sermons_date', true );
        $date = FW_Sermons_Date::format_backend_to_frontend( $date );

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

    /**
     * Filter for sorting the date column. Add our column id and the associated 
     * meta key name to the given list of columns. The method that will actually
     * sort these columns is sermon_sort_columns_orderby() and will be called
     * later by WordPress.
     *
     * @param    array   List of column ids and the query 'orderby' value.
     * @return   array   Same list.
     */
    public function sermon_sort_columns( $columns ) {

        $columns['sermon_date'] = '_fw_sermons_date';
        return $columns;

    }

    /**
     * Filter for making some columns sortable. Here we receive query parameters
     * and we'll manipulate them so our columns will sort.
     *
     * Using the date column as an example, we'll modify this incoming $vars structure:
     *
     * Array (
     *     'order' => 'asc',
     *     'orderby' => '_fw_sermons_date',
     *     'post_type' => 'sermon',
     *     'posts_per_page' => 20
     * )
     *
     * to this:
     *
     * Array (
     *     'order' => 'asc',
     *     'orderby' => 'meta_value',
     *     'post_type' => 'sermon',
     *     'posts_per_page' => 20,
     *     'meta_key' => '_fw_sermons_date'
     * )
     *
     * @param    array  $vars  WordPress query parameters
     * @return   array         Modified query parameters.
     */
    public function sermon_sort_columns_orderby( $vars ) {

        if ( isset( $vars['orderby'] ) ) {

            switch( $vars['orderby'] ) {

                case '_fw_sermons_date':
                    $vars = array_merge( $vars, array(
                        'meta_key' => '_fw_sermons_date',
                        'orderby'  => 'meta_value' // Sort alphanumerically!
                    ) );
                    break;

                default:
                    break;

            }

        }

        return $vars;

    }

    /**
     * Action for displaying one or more select menus on our 'All Sermons' page.
     * Each menu contains the list of terms for one taxonomy. The selected term
     * will act as a filter when the [WordPress] Filter button is clicked.
     *
     * Portions of code taken from Mike Hemberger's example at:
     * http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
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

}
