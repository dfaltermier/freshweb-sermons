<?php 

/**
* Load the base class
*/
class Freshweb_Sermons {
	
	function __construct()	{
		
	}

	/**
	 * Kick it off
	 * 
	 */
	public function run() {

		self::setup_constants();
		self::includes();

		add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
	
	}

	/**
	 * Load scripts for admin
	 * 
	 */
	public function load_admin_scripts() {

		wp_enqueue_script( 'fw-file-import', FWS_PLUGIN_URL . 'js/file-import.js' );

	}

	/**
	 * Setup plugin constants.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function setup_constants() {

		// Plugin version.
		if ( ! defined( 'FWS_VERSION' ) ) {
			define( 'FWS_VERSION', '1.0.0' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'FWS_PLUGIN_DIR' ) ) {
			define( 'FWS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'FWS_PLUGIN_URL' ) ) {
			define( 'FWS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'FWS_PLUGIN_FILE' ) ) {
			define( 'FWS_PLUGIN_FILE', __FILE__ );
		}

	}

	/**
	 * Include required files.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function includes() {

		require_once FWS_PLUGIN_DIR . 'class-post-types.php';
		$post_types = new FWS_Post_Types;

		require_once FWS_PLUGIN_DIR . 'class-meta-box.php';
		$meta_boxes = new FWS_Meta_Box;

		require_once FWS_PLUGIN_DIR . 'class-series.php';
		$term_meta = new FreshWeb_Sermons_Series;

	}

}