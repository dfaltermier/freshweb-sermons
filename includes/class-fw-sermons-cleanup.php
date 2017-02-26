<?php
/**
 * Provide utility methods for deleting Sermons from the database.
 *
 * This class is provided to remove all Sermons from the database before the plugin
 * is deactivated and uninstalled.
 *
 * @package    FreshWeb_Church
 * @subpackage Functions
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.0
 *
 * This file incorporates portions of code from Kees Meijer's 
 * 'Custom Post Type Cleanup' plugin
 * (https://github.com/keesiemeijer/custom-post-type-cleanup). The original code is 
 * copyright (c) 2015, Kees Meijer  (email : keesie.meijer@gmail.com) and is 
 * distributed under the terms of the GNU GPL license 2.0.
 * (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version. You may NOT assume that you can use 
 * any other version of the GPL.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class FW_Sermons_Cleanup {

    /**
     * We are only concerned about the Sermon post type.
     *
     * @since 1.1.0
     */
    const SERMON_POST_TYPE = 'sermon';

    /**
     * Because we don't want to timeout our UI, limit our deletion of Sermons to this maximum count.
     *
     * @since 1.1.0
     */
    const MAX_SERMONS_TO_DELETE_AT_ONCE = 50;

    /**
     * Do we wish to permanently delete the Sermons, or just move them to the trash?
     *   False: Move them to trash
     *   True:  Delete permanently
     *
     * @since 1.1.0
     */
    const IS_SERMONS_DELETED_PERMANENTLY = false;

    /**
     * Define our sermon nonce name.
     *
     * @since 1.1.0
     */
    const SERMON_NONCE_NAME = 'sermon_cleanup_nonce';


	public function __construct() {

        add_action( 'admin_menu', array( $this, 'admin_menu' ) );

	}

	/**
	 * Add a settings page.
	 *
     * @since 1.1.0
	 */
	public function admin_menu() {

		$page_hook = add_submenu_page(
			'edit.php?post_type=' . self::SERMON_POST_TYPE,
			'Sermons Clean Up',
			'Sermons Clean Up',
            'delete_plugins',
			'sermons-cleanup',
			array( $this, 'admin_page' )
        );

		//add_action( 'admin_print_scripts-' . $page_hook, array( $this, 'enqueue_script' ) );

	}

	/**
	 * Displays the admin settings page for this plugin.
	 *
	 * @since 1.1.0
	 * @return void
	 */
	public function admin_page() {

        $number_of_sermons_deleted = -1; // Start with less than zero.

        // If the user clicked our button to delete Sermons, then do so.
        if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {

            check_admin_referer( self::SERMON_NONCE_NAME );
            $number_of_sermons_deleted = $this->delete_sermons();

        }

        // Okay, how many are now left?
        $number_of_remaining_sermons = $this->get_number_of_remaining_sermons();

        // Start admin settings page.
        echo '<div class="wrap rpbt_cache">';
        echo '<h1>Sermons Clean Up</h1>';
        echo '<p>Before permanently uninstalling this plugin, it would be wise to delete all of the Sermons that were ' .
             'created. By doing so, you\'ll leave the WordPress database nice and clean!</p>' .
             '<p>Because it is time consuming and process intensive to delete hundreds or more Sermons at once, ' .
             'we\'ll need to manually delete them in batches of no more than ' . self::MAX_SERMONS_TO_DELETE_AT_ONCE . 
             ' Sermons at a time. Delete Sermons until zero remain.</p>';

        if ( $number_of_sermons_deleted >= 0 ) {

            echo '<div class="notice notice-success"><p>Deleted ' . $number_of_sermons_deleted . ' Sermons.</p></div>';

        }

        if ( $number_of_remaining_sermons > 1 ) {

            echo '<div class="notice notice-warning"><p>There are a total of ' . $number_of_remaining_sermons . ' Sermons in our database.</p></div>';

        }
        else if ( $number_of_remaining_sermons === 1 ) {

            echo '<div class="notice notice-warning"><p>There is a total of ' . $number_of_remaining_sermons . ' Sermon in our database.</p></div>';

        }
        else if ( $number_of_remaining_sermons === 0 ) { // zero

            echo '<div class="notice notice-success"><p>There are ' . $number_of_remaining_sermons . ' Sermons remaining. You may safely ' .
                 '<a href="' . admin_url( 'plugins.php' ) . '">deactivate and uninstall this plugin</a>.</p></div>';

        }
        else {

            echo '<div class="notice notice-error"><p>An error occured while deleting Sermons. ' .
                 'Please try again. Contact the plugin author if the problem persists.</p></div>';

        }

        /*
         * Display our [form] button to delete Sermons as long as the number of remaining sermons 
         * is greater than zero or -1 (which indicates a query failure).
         */
        if ( $number_of_remaining_sermons !== 0 ) {

            $number_of_sermons_to_delete = min( $number_of_remaining_sermons, self::MAX_SERMONS_TO_DELETE_AT_ONCE );

            $button_label = ( $number_of_remaining_sermons > 0 ) 
                ? 'Delete ' . sprintf( _n( '%d Sermon', '%d Sermons', $number_of_sermons_to_delete ), $number_of_sermons_to_delete )
                : 'Delete Sermons';

            echo '<form method="post" action="">';
            wp_nonce_field( self::SERMON_NONCE_NAME );
            submit_button( 
                $button_label, 
                'primary', 
                'submit_sermons_cleanup' // If you change this, update the corresponding JS file.
            );
            echo '</form>';

        }

        echo '</div>';

	}

    /**
     * Delete Sermon posts from the database.
     *
     * @since  1.1.0
     * @access private
     * @return int     Number of posts deleted.
     */
    private function delete_sermons() {


        // TODO: Remove this stub:
        return 2;


        global $wpdb;

        $number_of_deleted_sermons = 0;

        // Get a max of MAX_SERMONS_TO_DELETE_AT_ONCE Sermon post ids.
        $post_ids = $this->get_sermon_post_ids( self::MAX_SERMONS_TO_DELETE_AT_ONCE );

        // Return if we have deleted them all.
        if ( empty( $post_ids ) ) {
            return $number_of_deleted_sermons;
        }

        // Delete a max of MAX_SERMONS_TO_DELETE_AT_ONCE Sermons.
        foreach ( $post_ids as $post_id ) {

            $status = wp_delete_post( $post_id, self::IS_SERMONS_DELETED_PERMANENTLY );

            if ( false !== $status ) {
                $number_of_deleted_sermons++;
            }

        }

        return $number_of_deleted_sermons;

    }

    /**
     * Returns sermon ids.
     *
     * @since  1.1.0
     *
     * @param  int   $limit  Optional. Limit how many ids are returned.
     * @return array         Array with post ids.
     */
    private function get_sermon_post_ids( $limit = 0 ) {

        global $wpdb;

        $limit    = $limit ? " LIMIT {$limit}" : '';
        $query    = "SELECT p.ID FROM $wpdb->posts AS p WHERE p.post_type IN (%s){$limit}";
        $post_ids = $wpdb->get_col( $wpdb->prepare( $query, self::SERMON_POST_TYPE ) );

        return $post_ids;

    }

    /**
     * Returns number of sermons remaining in database.
     *
     * @since  1.1.0
     *
     * @return int     Number of sermons. -1 returned on error.
     */
    private function get_number_of_remaining_sermons() {

        global $wpdb;

        $query = "SELECT COUNT(p.ID) FROM $wpdb->posts AS p WHERE p.post_type = %s";
        $count = $wpdb->get_var( $wpdb->prepare( $query, self::SERMON_POST_TYPE ) );

        return ( $count !== null ) ? (int) $count : -1;

    }

}

