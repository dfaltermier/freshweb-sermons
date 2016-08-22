<?php
/**
 * Plugin Name: Freshweb Studio Sermons
 * Plugin URI: http://www.endocreative.com/
 * Description: Create sermon series containing video, audio, and other related materials.
 * Version: 1.0.1
 * Author: Freshweb Studio
 * Author URI: http://freshwebstudio.com/
 * Text Domain: mytextdomain
 * License: GPL2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-fw-sermons.php';

/* 
 * If the site uses custom permalinks, we will need to flush the permalink 
 * structure after making changes to our taxonomies, or else the user may
 * see a "Page Not Found" error.
 */
register_activation_hook( __FILE__, 'fw_sermons_flush_rewrites' );

function fw_sermons_flush_rewrites() {
    
	// call your CPT registration function here (it should also be hooked into 'init')
	require_once FW_SERMONS_PLUGIN_DIR . 'class-fw-sermons-post-types.php';
	$post_types = new FW_Sermons_Post_Types;

	flush_rewrite_rules();

}

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_freshweb_sermons() {

	$plugin = new FW_Sermons();
	$plugin->run();

}
run_freshweb_sermons();