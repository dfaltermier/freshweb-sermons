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
 * Description:    Create sermon series containing video, audio, and other associated materials.
 * Version:        0.1.5
 * Author:         Freshweb Studio
 * Author URI:     https://github.com/dfaltermier
 * Text Domain:    fw-sermons
 * License:        GNU General Public License v3 or later
 * License URI:    http://www.gnu.org/licenses/gpl-3.0.html
 * 
 * Copyright 2016  David Faltermier <david@freshwebstudio.com>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 3 as published by
 * the Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
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
 * Begin execution of the plugin.
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
