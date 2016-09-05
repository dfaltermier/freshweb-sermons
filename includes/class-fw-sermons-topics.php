<?php 

/**
 * This class provides methods for manipulating the sermon topic taxonomy.
 *
 */
class FW_Sermons_Topics {
    
    function __construct()  {
        
        // Add term column labels and populate the columns with data.
        add_filter( 'manage_edit-sermon_topic_columns', array( $this, 'topics_columns') );

    }

    /**
     * Override the given list of columns displayed in the topics terms
     * table with our own.
     *
     * @param    array   List of column ids and labels.
     * @return   array   Same list.
     */
    public function topics_columns( $columns ) {
            
        // We won't include a 'Description' column as it's annoying.
        unset( $columns['description'] );

        // Let's better describe what the 'count' is.
        $columns['posts'] = 'Sermon Count';
        
        return $columns;

    }

}
