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
require plugin_dir_path( __FILE__ ) . 'includes/class-freshweb-sermons.php';

/* 
 * If the site uses custom permalinks, we will need to flush the permalink 
 * structure after making changes to our taxonomies, or else the user may
 * see a "Page Not Found" error.
 */
register_activation_hook( __FILE__, 'fws_flush_rewrites' );
function fws_flush_rewrites() {
	// call your CPT registration function here (it should also be hooked into 'init')
	require_once FWS_PLUGIN_DIR . 'class-post-types.php';
	$post_types = new FWS_Post_Types;

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
	$plugin = new Freshweb_Sermons();
	$plugin->run();
}
run_freshweb_sermons();