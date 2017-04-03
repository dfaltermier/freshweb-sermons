<?php
 /** 
 * This class provides methods for manipulating the sermon book taxonomy.
 *
 * @package    FreshWeb_Church_Sermons
 * @subpackage Functions
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9.1
 */
class FW_Sermons_Books {
    
    function __construct()  {
        
        // Add term column labels and populate the columns with data.
        add_filter( 'manage_edit-sermon_book_columns', array( $this, 'add_book_columns') );

    }

    /**
     * Configure the given list of table columns with our own.
     *
     * @since   0.9.1
     *
     * @param   array  $columns  List of column ids and labels.
     * @return  array            Same list.
     */
    public function add_book_columns( $columns ) {
            
        // We won't include a 'Description' column as it's annoying.
        unset( $columns['description'] );

        // Let's better describe what the 'post count' is.
        $columns['posts'] = 'Sermon Count';

        return $columns;

    }

}
