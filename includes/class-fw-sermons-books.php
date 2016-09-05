<?php 

/**
 * This class provides methods for manipulating the sermon book taxonomy.
 *
 */
class FW_Sermons_Books {
    
    function __construct()  {
        
        // Add term column labels and populate the columns with data.
        add_filter( 'manage_edit-sermon_book_columns', array( $this, 'book_columns') );

    }

    /**
     * Override the given list of columns displayed in the books terms
     * table with our own.
     *
     * @param    array   List of column ids and labels.
     * @return   array   Same list.
     */
    public function book_columns( $columns ) {
            
        // We won't include a 'Description' column as it's annoying.
        unset( $columns['description'] );

        // Let's better describe what the 'count' is.
        $columns['posts'] = 'Sermon Count';

        return $columns;

    }

}
