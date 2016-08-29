/**
 * Add a datepicker to post and taxonomy date form fields.
 * There is no limit to the number of datepickers you may have per page.
 * Just attach the class name 'fw-sermons-datepicker' to a input[type="text"]
 * field.
 *
 * @Dependencies
 *   1. jquery-ui-datepicker
 */
(function($) {
    
    'use strict';

    $(function() {

        // Regexp for date format. Example: January 1, 2016
        var dateRegexp = /^(January|February|March|April|May|June|July|August|September|October|November|December) ([1-9]|[12]\d|3[01]), \d{4}$/;

        /**
         * Converts an input[type="text"] field to a jquery ui datepicker.
         * If the user enters a date that is invalid (empty or does not
         * match the date regexp above), then we set the input field to
         * an empty string.
         */
        function activateDatepicker() {
            $('.fw-sermons-datepicker')
                .attr('placeholder', 'e.g. January 12, 2016')
                .datepicker({
                    dateFormat: 'MM d, yy', // Example: January 1, 2016
                    changeMonth: true,
                    changeYear: true,
                    onClose: function(dateString, datepicker) {
                        var value = $(this).val().trim();
                        if ((value === '') || (value.match(dateRegexp) === null)) {
                            $(this).val('');
                        }
                    }
                });
        }

        activateDatepicker();
    });

})( jQuery );
