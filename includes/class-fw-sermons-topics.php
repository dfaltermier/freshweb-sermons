<?php 

/**
 * This class provides methods for manipulating the sermon topic taxonomy.
 *
 */
class FW_Sermons_Topics {
    
    function __construct()  {
        
        // Add term column labels and populate the columns with data.
        add_filter( 'manage_edit-sermon_topic_columns', array( $this, 'add_book_columns') );

    }

    /**
     * Configure the given list of table columns with our own.
     *
     * @param   array  $columns  List of column ids and labels.
     * @return  array            Same list.
     */
    public function add_book_columns( $columns ) {
            
        // We won't include a 'Description' column as it's unnecessary.
        unset( $columns['description'] );

        // Let's better describe what the 'post count' is.
        $columns['posts'] = 'Sermon Count';
        
        return $columns;

    }

}
