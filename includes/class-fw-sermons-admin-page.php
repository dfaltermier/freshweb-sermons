<?php
/**
 * Provide utility methods for deleting Sermons from the database.
 *
 * This class is provided to remove all Sermons from the database before the plugin
 * is deactivated and uninstalled. Before permanently uninstalling this plugin,
 * it would be wise to delete all of the Sermons that were created. By doing so,
 * we'll leave the WordPress database nice and clean!
 *
 * Because it is time consuming and process intensive to delete hundreds or more Sermons
 * at once, we'll need to ask the administrator to manually delete them in batches of no
 * more than MAX_SERMONS_TO_DELETE_AT_ONCE Sermons at a time. We also give the administrator
 * form buttons for deleting the Sermon taxonomies also.
 *
 * @package    FreshWeb_Church
 * @subpackage Functions
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9.1
 *
 * This file incorporates portions of code from Kees Meijer's 
 * 'Custom Post Type Cleanup' and 'Custom Taxonomy Cleanup' plugins
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

class FW_Sermons_Admin_Page {

    /**
     * We are only concerned about the Sermon post type.
     *
     * @since 0.9.1
     */
    const SERMON_POST_TYPE = 'sermon';

    /**
     * Because we don't want to timeout our UI, limit our deletion of Sermons to this maximum count.
     *
     * @since 0.9.1
     */
    const MAX_SERMONS_TO_DELETE_AT_ONCE = 50;

    /**
     * Do we wish to permanently delete the Sermons, or just move them to the trash?
     *   False: Move them to trash
     *   True:  Delete permanently
     *
     * @since 0.9.1
     */
    const IS_SERMONS_DELETED_PERMANENTLY = false;

    /**
     * Define our sermon nonce name.
     *
     * @since 0.9.1
     */
    const SERMON_NONCE_NAME = 'sermon_cleanup_nonce';

    /**
     * Kick things off.
     *
     * @since 0.9.1
     */
    public function __construct() {

        add_action( 'admin_menu', array( $this, 'admin_menu' ) );

    }

    /**
     * Add an administrative page.
     *
     * @since 0.9.1
     */
    public function admin_menu() {

        add_submenu_page(
            'edit.php?post_type=' . self::SERMON_POST_TYPE,
            'Prepare To Uninstall',
            'Prepare To Uninstall',
            'delete_plugins',
            'sermons-cleanup',
            array( $this, 'show_settings_page' )
        );

    }

    /**
     * Callback to render the administrative page.
     *
     * @since 0.9.1
     */
    public function show_settings_page() {

        // Start administrative page.
        echo '<div class="wrap fw-sermons-cleanup">' .
             '<h1>Prepare To Uninstall</h1>' .
             '<p>Before permanently uninstalling this plugin, it would be wise to delete all of the Sermons that were ' .
             'created. By doing so, you\'ll leave the WordPress database nice and clean!</p>';

        // Display Sermons section.
        $all_sermons_are_deleted = $this->show_sermons_section();

        // Display Taxonomies section. Enable the forms only if all sermons have been deleted.
        $all_taxonomies_are_deleted = $this->show_taxonomies_section( $all_sermons_are_deleted );

        $this->show_unistall_section( $all_taxonomies_are_deleted );

        echo '</div>';

    }

    /**
     * Displays the 'Sermons' form section.
     *
     * @since  0.9.1
     * @access private
     *
     * @return bool  True if all sermons are deleted. False if some still remain.
     */
    private function show_sermons_section() {

        $num_of_deleted_sermons = -1;

        // If the user clicked our button to delete Sermons, then do so.
        if ( ( 'POST' === $_SERVER['REQUEST_METHOD'] ) &&
             ( ! empty( $_POST['fw-sermons-cleanup-sermons'] ) ) &&
             ( 'true' === $_POST['fw-sermons-cleanup-sermons'] ) ) {

            check_admin_referer( self::SERMON_NONCE_NAME );
            $num_of_deleted_sermons = $this->delete_sermons();

        }

        // How many sermons remain?
        $num_of_remaining_sermons = $this->get_number_of_sermons(); // Careful, could be -1.

        echo '<h2>Step 1. Delete Sermons</h2>' .
             '<p>Because it is time consuming and process intensive to delete hundreds or more Sermons at once, ' .
             'we\'ll need to manually delete them in batches of no more than ' . self::MAX_SERMONS_TO_DELETE_AT_ONCE . 
             ' Sermons at a time. Delete Sermons until zero remain.</p>';

        // Display an inline message with deletion status.
        if ( $num_of_deleted_sermons > 0 ) {
            echo '<div class="fw-sermons-notice fw-sermons-notice-success"><p>' .
                 'Deleted ' . $num_of_deleted_sermons . _n( ' Sermon.', ' Sermons.', $num_of_deleted_sermons ) .
                 '</p></div>';
        }
        else if ( $num_of_deleted_sermons === 0 ) {
            echo '<div class="fw-sermons-notice fw-sermons-notice-warning"><p>' .
                 'Deleted ' . $num_of_deleted_sermons . ' Sermons.' .
                 '</p></div>';
        }
        else { // ($num_of_deleted_sermons === -1 )
            // We didn't delete any sermons because we never took action to do so.
        }

        // Display an inline message with the number of remaining sermons.
        if ( $num_of_remaining_sermons > 0 ) {
            echo '<div class="fw-sermons-notice fw-sermons-notice-warning"><p>' .
                 'There is a total of ' . $num_of_remaining_sermons .
                  _n( ' Sermon ', ' Sermons ', $num_of_remaining_sermons ) .
                 ' in our database.' .
                 '</p></div>';
        }
        else if ( $num_of_remaining_sermons === 0 ) { // zero
            echo '<div class="fw-sermons-notice fw-sermons-notice-success"><p>' .
                 'There are ' . $num_of_remaining_sermons . ' Sermons in our database.' .
                 '</p></div>';
        }
        else { // -1, which is a db query error.
            echo '<div class="notice notice-error"><p>An error occured while deleting Sermons. ' .
                 'Please try again. Contact the plugin author if the problem persists.</p></div>';
        }

        /*
         * Display our [form] submit button to delete Sermons as long as the number of remaining sermons 
         * is greater than zero or -1 (which indicates a query failure and for which we'll try again).
         * Disable the button if we don't have any remaining sermons to delete.
         */
        $button_attr_disabled = ( $num_of_remaining_sermons > 0 ) ? '' : 'disabled';
        $button_label         = 'Delete Sermons';

        if ( $num_of_remaining_sermons > 0 ) {

            $number_of_sermons_to_delete = min( $num_of_remaining_sermons, self::MAX_SERMONS_TO_DELETE_AT_ONCE );
            $button_label = 'Delete ' . 
                sprintf( _n( '%d Sermon', '%d Sermons', $number_of_sermons_to_delete ), $number_of_sermons_to_delete );

        }

        echo '<form name="fw-sermons-cleanup-sermons-form" ' .
             'class="fw-sermons-cleanup-sermons-form" method="post" action="">'; // See JS/CSS.
        wp_nonce_field( self::SERMON_NONCE_NAME );
        echo '<input type="hidden" name="fw-sermons-cleanup-sermons" value="true" />';
        submit_button( 
            $button_label, 
            'primary', 
            'fw-sermons-cleanup-sermons-form-submit', // See JS/CSS.
            false,                                    // Don't wrap button in <p> tags.
            $button_attr_disabled
        );
        echo '</form>';

        return ( $num_of_remaining_sermons <= 0 ) ? true : false;

    }

    /**
     * Delete Sermon posts from the database.
     *
     * @since  0.9.1
     * @access private
     *
     * @return int     Number of posts deleted.
     */
    private function delete_sermons() {

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
     * @since  0.9.1
     * @access private
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
     * @since  0.9.1
     * @access private
     *
     * @return int     Number of sermons. -1 returned on error.
     */
    private function get_number_of_sermons() {

        global $wpdb;

        $query = "SELECT COUNT(p.ID) FROM $wpdb->posts AS p WHERE p.post_type = %s";
        $count = $wpdb->get_var( $wpdb->prepare( $query, self::SERMON_POST_TYPE ) );

        return ( $count !== null ) ? (int) $count : -1;

    }

    /**
     * Displays the 'Taxonomies' section.
     *
     * @since  0.9.1
     * @access private
     *
     * @param  bool   $enable_taxonomies  If true, enable the forms in all taxonomy sections. This
     *                                    means that all of the sermons have been previously deleted.
     * @return bool                       True if all taxonomies have been deleted.
     */
    private function show_taxonomies_section( $enable_taxonomies ) {

        $are_all_taxonomies_deleted = true;
        $taxonomies_names = array();

        // Get a list of all sermon taxonomies.
        $taxonomies = get_object_taxonomies( self::SERMON_POST_TYPE, 'objects' );

        // Build a comma deliniated string of taxonomy names for our header below.
        foreach( $taxonomies as $taxonomy ) {
            $taxonomies_names[] = $taxonomy->labels->name;
        }

        // Start our section header.
        echo '<h2>Step 2. Delete Sermon ' . join( ', ', $taxonomies_names ) . '</h2>' .
             '<p>Along with Sermons, we need to delete Sermon ' . join( ', ', $taxonomies_names ) . '. ' .
             'Delete each below.</p>';

        foreach( $taxonomies as $taxonomy ) {

            $is_this_taxonomy_deleted = $this->show_taxonomy_section( $taxonomy, $enable_taxonomies );

            if ( ! $is_this_taxonomy_deleted ) {
                $are_all_taxonomies_deleted = false;
            }

        }

        return $are_all_taxonomies_deleted;

    }

    /**
     * Display the form for the given taxonomy.
     *
     * @since  0.9.1
     * @access private
     *
     * @param  string  $taxonomy       Taxonomy object returned by get_object_taxonomies().
     * @param  bool    $is_enabled     Optional. Should this section's submit button be enabled.
     *                                 This will be the case when the user has deleted
     *                                 all of the Sermons, but no sooner. Default: false.
     * @return bool                    True if all taxonomy terms have been deleted.
     */
    private function show_taxonomy_section( $taxonomy, $is_enabled = false ) {

        $num_of_deleted_terms = -1;

        $form_name         = 'fw-sermons-cleanup-taxonomy-form-' . $taxonomy->name;
        $form_class        = 'fw-sermons-cleanup-taxonomy-form-' . $taxonomy->name;
        $hidden_field_name = 'fw-sermons-cleanup-taxonomy-' . $taxonomy->name;
        $form_submit_id    = 'fw-sermons-cleanup-taxonomy-form-submit-' . $taxonomy->name;

        // If the user clicked our button to delete the taxonomy, then do so.
        if ( ( 'POST' === $_SERVER['REQUEST_METHOD'] ) &&
             ( ! empty( $_POST[$hidden_field_name] ) ) &&
             ( 'true' === $_POST[$hidden_field_name] ) && 
             ( $is_enabled ) ) {

            check_admin_referer( self::SERMON_NONCE_NAME );
            $num_of_deleted_terms = $this->delete_taxonomy_terms( $taxonomy );

        }

        // How many terms remain?
        $num_of_remaining_terms = wp_count_terms( $taxonomy->name, array( 'hide_empty' => false ) );

        // Display an inline message with deletion status.
        if ( $num_of_deleted_terms > 0 ) {
            echo '<div class="fw-sermons-notice fw-sermons-notice-success"><p>' .
                 'Deleted ' . $num_of_deleted_terms . ' Sermon ' .
                 _n( $taxonomy->labels->singular_name, $taxonomy->labels->name, $num_of_deleted_terms ) .
                 '</p></div>';
        }
        else if ( $num_of_deleted_terms === 0 ) {
            echo '<div class="fw-sermons-notice fw-sermons-notice-warning"><p>' .
                 'Deleted ' . $num_of_deleted_terms . ' Sermon ' . $taxonomy->labels->name .
                 '</p></div>';
        }
        else { // ($num_of_deleted_terms === -1 )
            // We didn't delete any terms because we never took action to do so.
        }

        // Display an inline message with the number of remaining taxonomy terms.
        if ( $num_of_remaining_terms > 0 ) {
            echo '<div class="fw-sermons-notice fw-sermons-notice-warning"><p>' .
                 'There is a total of ' . $num_of_remaining_terms . ' Sermon ' .
                 _n( $taxonomy->labels->singular_name, $taxonomy->labels->name, $num_of_remaining_terms ) .
                 ' in our database.' .
                 '</p></div>';
        }
        else { // Zero terms remaining.
            echo '<div class="fw-sermons-notice fw-sermons-notice-success"><p>' .
                 'There are ' . $num_of_remaining_terms . ' Sermon ' . $taxonomy->labels->name . ' in our database.' .
                 '</p></div>';
        }

        /*
         * Display our [form] submit button to delete terms as long as the number of remaining terms 
         * is greater than zero. Disable the button if we don't have any remaining terms to delete
         * or $is_enabled is false.
         */
        $button_attr_disabled = ( ( $num_of_remaining_terms > 0 ) && $is_enabled ) ? '' : 'disabled';

        $button_label = ( $num_of_remaining_terms > 0 )
            ? 'Delete ' . $num_of_remaining_terms . ' Sermon ' . 
               _n( $taxonomy->labels->singular_name, $taxonomy->labels->name, $num_of_remaining_terms )
            : 'Delete Sermon ' . $taxonomy->labels->name;

        // Now it's time to display our form.
        echo '<form name="' . $form_name . '" class="' . $form_class . '" method="post" action="">'; // See JS/CSS.
        wp_nonce_field( self::SERMON_NONCE_NAME );
        echo '<input type="hidden" name="'. $hidden_field_name . '" value="true" />';
        submit_button( 
            $button_label, 
            'primary', 
            $form_submit_id,       // See JS/CSS.
            false,                 // Don't wrap button in <p> tags.
            $button_attr_disabled
        );
        echo '</form>';

        return ( $num_of_remaining_terms <= 0 ) ? true : false;

    }

    /**
     * Delete Sermon taxonomy terms from the database.
     *
     * @since  0.9.1
     * @access private
     *
     * @param  string  $taxonomy   Taxonomy object returned by get_object_taxonomies().
     * @return int                 Number of terms deleted.    
     */
    private function delete_taxonomy_terms( $taxonomy ) {

        $number_of_deleted_terms = 0;

        // Retrieve the terms for this taxonomy.
        $terms = get_terms( array(
            'taxonomy'   => $taxonomy->name,
            'hide_empty' => false
        ) );

        // Delete each term in our list.
        foreach ( $terms as $term ) {

            $status = wp_delete_term( $term->term_id, $taxonomy->name );

            if ( true === $status ) {
                $number_of_deleted_terms++;
            }

        }

        return $number_of_deleted_terms;

    }

    /**
     * Display final user instructions for uninstalling the plugin.
     *
     * @since  0.9.1
     * @access private
     *
     * @param  bool  $is_enabled     Optional. Should this section's submit button be enabled.
     *                               This will be the case when the user has deleted
     *                               all of the Sermons and taxonomies, but no sooner. Default: false.
     * @return bool                  True if the form button is activated.
     */
    private function show_unistall_section( $is_enabled = false ) {

        $button_attr_disabled = $is_enabled ? '' : 'disabled';
        $button_label         = 'Continue to Plugins Page';
        $form_submit_id       = 'fw-sermons-cleanup-uninstall-form-submit';

        // Start our section header.
        echo '<h2>Step 3. Uninstall Sermon Plugin</h2>' .
             '<p>Continue to the Pluigins page to deactivate and uninstall this plugin.</p>';
        
        // Display our form.
        echo '<form method="get" action="' . admin_url( 'plugins.php' ) . '">';
        submit_button( 
            $button_label, 
            'primary', 
            $form_submit_id,       // See JS/CSS.
            false,                 // Don't wrap button in <p> tags.
            $button_attr_disabled
        );
        echo '</form>';

        return $is_enabled;

    }

}
