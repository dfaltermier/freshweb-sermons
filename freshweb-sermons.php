<?php
/**
 * Plugin Name: Freshweb Studio Sermons
 * Plugin URI: https://github.com/dfaltermier/freshweb-sermons
 * Description: Create sermon series containing video, audio, and other associated materials.
 * Version: 1.0.1
 * Author: Freshweb Studio and Endo Creative
 * Author URI: https://freshwebstudio.com/
 * Text Domain: fw-sermons
 * License: GPL3
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
 * When adding custom post types and taxonomies, we must flush the 
 * rewrite rules or else the user may see a "Page Not Found" error.
 * Be sure to register the CPT and taxonomies before flushing!
 * Why do this? See https://codex.wordpress.org/Function_Reference/flush_rewrite_rules
 */
register_activation_hook( __FILE__, 'fw_sermons_flush_rewrites' );

function fw_sermons_flush_rewrites() {
    
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
 */
function run_freshweb_sermons() {

	$plugin = new FW_Sermons();
	$plugin->run();

}
run_freshweb_sermons();