<?php
 /** 
 * Bootstrapping class.
 *
 * All of our plubin dependencies are initalized here.
 *
 * @package    FreshWeb_Church_Sermons
 * @subpackage Functions
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.0
 */
class FW_Sermons {
    
    function __construct()  { 
    }

    /**
     * Run our initialization.
     *
     * @since 1.1.0
     */
    public function run() {

        $this->setup_constants();
        $this->includes();

        add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
    
    }

    /**
     * Determines if we're on one of our plugin's admin pages.
     *
     * @since 1.1.0
     *
     * @return  bool  Returns true if this is so.
     */
    public function is_plugin_admin_page() {

        global $typenow;

        return ( 'sermon' === $typenow ? true : false );

    }

    /**
     * Enqueue our scripts and stylesheets.
     *
     * @since 1.1.0
     *
     */
    public function load_admin_scripts() {

        // Only enqueue if we're in our plugin pages.
        if ( ! $this->is_plugin_admin_page() ) {
            return;
        }

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

        wp_enqueue_script(
            'fw_sermons_admin_page',
            FW_SERMONS_PLUGIN_URL . 'admin/js/admin-page.js',
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
     *
     * @since  1.1.0
     * @access private
     */
    private function setup_constants() {

        /*
         * Set true if plugin is to be detected by theme writers as activated.
         *
         * Theme writers: Use this defined variable to determine if plugin is installed
         * and activated. False means No, True means yes.
         */
        if ( ! defined( 'FW_SERMONS_IS_ACTIVATED' ) ) {
            define( 'FW_SERMONS_IS_ACTIVATED', true );
        }     

        // Plugin version.
        if ( ! defined( 'FW_SERMONS_VERSION' ) ) {
            define( 'FW_SERMONS_VERSION', '1.1.1' );
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
     *
     * @since  1.1.0
     * @access private
     */
    private function includes() {

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-post-types.php';
        $post_types = new FW_Sermons_Post_Types;

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-meta-box.php';
        $meta_boxes = new FW_Sermons_Meta_Box;

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-series.php';
        $sermon_series = new FW_Sermons_Series;

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-speakers.php';
        $sermon_speakers = new FW_Sermons_Speakers;

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-topics.php';
        $sermon_topics = new FW_Sermons_Topics;

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-books.php';
        $sermon_books = new FW_Sermons_Books;

        require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-admin-page.php';
        $sermons_admin_page = new FW_Sermons_Admin_Page;

    }

}