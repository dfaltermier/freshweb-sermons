<?php 
/**
 * This class initializes the environment in which we'll run.
 */
class FW_Sermons {
    
    function __construct()  {
        
    }

    /**
     * Kick it off
     */
    public function run() {

        self::setup_constants();
        self::includes();

        add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
    
    }

    /**
     * @return    bool    Returns true if we're on one of our plugin's admin pages. 
     */
    public function is_plugin_admin_page() {

        global $typenow;

        /* This is what we can expect:
        Page: Sermons
        pagenow: "edit.php"
        typenow: "sermon"

        Page: Add New Sermon
        pagenow: "post-new.php"
        typenow: "sermon"

        Page: Edit Sermon
        pagenow: "post.php"
        typenow: "sermon"

        Page: Series
        pagenow: "edit.tags"
        typenow: "sermon"

        Page: Edit Series
        pagenow: "term.php"
        typenow: "sermon"
        */
        return ( 'sermon' === $typenow ? true : false );

    }

    /**
     * Enqueue our scripts and stylesheets.
     */
    public function load_admin_scripts() {

        // Only enqueue if we're in our plugin pages.
        if ( ! $this->is_plugin_admin_page() ) {
            return;
        }

        wp_enqueue_script(
            'fw_sermons_datepicker',
            FW_SERMONS_PLUGIN_URL . 'admin/js/datepicker.js',
            array( 'jquery', 'jquery-ui-datepicker' ),
            FW_SERMONS_VERSION
        );

        wp_enqueue_script(
            'fw_sermons_media_uploader',
            FW_SERMONS_PLUGIN_URL . 'admin/js/media-uploader.js',
            array( 'jquery' ),
            FW_SERMONS_VERSION
        );

        wp_enqueue_script(
            'fw_sermons_document_uploader',
            FW_SERMONS_PLUGIN_URL . 'admin/js/document-uploader.js',
            array( 'jquery' ),
            FW_SERMONS_VERSION
        );

        wp_enqueue_style(
            'fw_sermons_styles',
            FW_SERMONS_PLUGIN_URL . 'admin/css/style.css', 
            array(), 
            FW_SERMONS_VERSION
        );

    }

    /**
     * Setup plugin constants.
     */
    private function setup_constants() {

        // Plugin version.
        if ( ! defined( 'FW_SERMONS_VERSION' ) ) {
            define( 'FW_SERMONS_VERSION', '1.0.0' );
        }

        // Plugin Folder Path (without trailing slash)
        if ( ! defined( 'FW_SERMONS_PLUGIN_DIR' ) ) {
            define( 'FW_SERMONS_PLUGIN_DIR', dirname( __DIR__ ) );
        }

        // Plugin Folder URL (with trailing slash)
        if ( ! defined( 'FW_SERMONS_PLUGIN_URL' ) ) {
            define( 'FW_SERMONS_PLUGIN_URL', plugin_dir_url( __DIR__ ) );
        }

    }

    /**
     * Include required files.
     */
    private function includes() {

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-post-types.php';
        $post_types = new FW_Sermons_Post_Types;

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-meta-box.php';
        $meta_boxes = new FW_Sermons_Meta_Box;

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-series.php';
        $term_meta = new FW_Sermons_Series;

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-speakers.php';
        $term_meta = new FW_Sermons_Speakers;

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-images.php';
        $images = new FW_Sermons_Images;
        $images->init();

    }

}