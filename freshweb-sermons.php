<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * Plugin Name:    Freshweb Studio Sermons
 * Plugin URI:     https://github.com/dfaltermier/freshweb-sermons
 * Description:    Create and manage sermons containing video, audio, sermon notes, and other details.
 * Version:        1.1.0
 * Author:         Freshweb Studio
 * Author URI:     https://github.com/dfaltermier
 * Text Domain:    fw-sermons
 * License:        GNU General Public License v2 or later
 * License URI:    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * 
 * @package    FreshWeb_Church_Sermons
 * @subpackage Functions
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.0
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
 * Activate Sermon plugin.
 *
 * When adding custom post types and taxonomies, we must flush the 
 * rewrite rules or else the user may see a "Page Not Found" error.
 * Be sure to register the CPT and taxonomies before flushing!
 * Why do this? See https://codex.wordpress.org/Function_Reference/flush_rewrite_rules
 *
 * @since 1.1.0
 */
function fw_sermons_activation() {
    
    // Register the Sermon post type.
    require_once FW_SERMONS_PLUGIN_DIR . '/includes/class-fw-sermons-post-types.php';
    $post_types = new FW_Sermons_Post_Types;
    $post_types->register_post_types();
    $post_types->register_taxonomies(); // Necessary? Does this add rewrite rules?

    flush_rewrite_rules();

}
register_activation_hook( __FILE__, 'fw_sermons_activation' );

/* 
 * Deactivate Sermon plugin.
 *
 * @since 1.1.0
 */
function fw_sermons_deactivation() {

    flush_rewrite_rules();

}
register_deactivation_hook( __FILE__, 'fw_sermons_deactivation' );

/**
 * Begin execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks, then kicking off the
 * plugin from this point in the file does not affect the page life cycle.
 *
 * @since 1.1.0
 */
function run_freshweb_sermons() {

    $plugin = new FW_Sermons();
    $plugin->run();

}
run_freshweb_sermons();
