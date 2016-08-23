<?php 

/**
 * This class initializes the environment in which we'll run.
 */
class FW_Sermons {
	
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

		wp_enqueue_script(
			'fw_sermons_meta_fields',
			FW_SERMONS_PLUGIN_URL . 'js/meta-fields.js',
			array( 'jquery-ui-datepicker' ),
			FW_SERMONS_VERSION
		);

        wp_enqueue_style(
			'fw_sermons_styles',
            FW_SERMONS_PLUGIN_URL . 'css/style.css', 
            array(), 
            FW_SERMONS_VERSION
        );

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
		if ( ! defined( 'FW_SERMONS_VERSION' ) ) {
			define( 'FW_SERMONS_VERSION', '1.0.0' );
		}

		// Plugin Folder Path.
		if ( ! defined( 'FW_SERMONS_PLUGIN_DIR' ) ) {
			define( 'FW_SERMONS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL.
		if ( ! defined( 'FW_SERMONS_PLUGIN_URL' ) ) {
			define( 'FW_SERMONS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File.
		if ( ! defined( 'FW_SERMONS_PLUGIN_FILE' ) ) {
			define( 'FW_SERMONS_PLUGIN_FILE', __FILE__ );
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

		require_once FW_SERMONS_PLUGIN_DIR . 'class-fw-sermons-post-types.php';
		$post_types = new FW_Sermons_Post_Types;

		require_once FW_SERMONS_PLUGIN_DIR . 'class-fw-sermons-meta-box.php';
		$meta_boxes = new FW_Sermons_Meta_Box;

		require_once FW_SERMONS_PLUGIN_DIR . 'class-fw-sermons-series.php';
		$term_meta = new FW_Sermons_Series;

	}

}