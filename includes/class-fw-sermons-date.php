<?php 
/**
 * This class provides date utilities.
 *
 */
class FW_Sermons_Date {
    
    const FRONTEND_FORMAT = 'F j, Y'; // e.g. September 3, 2016
    const BACKEND_FORMAT  = 'Y-m-d';  // e.g. 2016-09-03

    function __construct()  {
        
    }

    /**
     * Converts a formatted date string from one format to another. This is
     * useful for converting the date string received from the frontend and
     * converting it to the format we desire on the backend before saving.
     * Or, vice-versa.
     *
     * @param   string  $date_string  Date string to convert.
     * @param   string  $in_format    Date format string expected by PHP DateTime::createFromFormat().
     * @param   string  $out_format   Date format string expected by PHP DateTime::createFromFormat().
     * @return  string                Formatted date string on success; empty string on error.
     */
    public static function createFromFormat( $date_string, $in_format, $out_format ) {

        // Note: '!' Resets all fields (year, month, day, hour, minute, second, etc.) to
        // the Unix Epoch. Without !, all fields will be set to the current date and time.
        // We don't want that.
        $in_format = '!' . $in_format;

        $date_obj = DateTime::createFromFormat( $in_format, $date_string );

        if ( is_object( $date_obj ) ) {

            $date_string = $date_obj->format( $out_format );

            if ( is_string( $date_string ) ) {

                return $date_string;

            }

        }

        return '';
 
    }

    /**
     * Converts a date string format used on the frontend to the format
     * used on the backend. This is just a wrapper around the createFromFormat()
     * class method above.
     *
     * @param   string  $date_string  Date string to convert.
     * @return  string                Formatted date string on success; empty string on error.
     */
    public static function format_frontend_to_backend( $date_string ) {

        $date_string = self::createFromFormat(
            $date_string,
            self::FRONTEND_FORMAT,
            self::BACKEND_FORMAT  
        );

        return $date_string;

    }

    /**
     * Converts a date string format used on the backend to the format
     * used on the frontend. This is just a wrapper around the createFromFormat()
     * class method above.
     *
     * @param   string  $date_string  Date string to convert.
     * @return  string                Formatted date string on success; empty string on error.
     */
    public static function format_backend_to_frontend( $date_string ) {

        $date_string = self::createFromFormat(
            $date_string,
            self::BACKEND_FORMAT,
            self::FRONTEND_FORMAT 
        );

        return $date_string;

    }

}
