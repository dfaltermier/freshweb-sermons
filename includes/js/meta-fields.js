/**
 * This script adds the dynamic behavior we need for meta input fields.  
 *
 * @Dependencies
 *   1. jquery-ui-datepicker.js.
 */
(function($) {

    'use strict';

    /**
     * Add a datepicker to date meta fields.
     * There is no limit to the number of datepickers you may have per page.
     * Just attach the class name 'fw-sermons-datepicker' to a input[type="text"]
     * field.
     */
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
                .attr('placeholder', 'Example: January 12, 2016')
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

    /**
     * Activate the media upload buttons for audio, video, or image urls.
     */
    $(function() {
     
        /**
         * Returns a callback function for the 'click' event on a media upload button.
         * We wrap the callback function within a closure to ensure that each function
         * instance has access to it's own fileFrame handle. The fileFrame handle
         * contains a reference to an active media dialog from which the media file
         * may be uploaded and selected.
         *
         * @param   object.string  args.mediaType   'image', 'audio', or 'video'
         * @param   object.string  args.buttonText  Button text in media uploader window. 
         * @return  Callback function.
         */
        function uploadMedia(args) {
            var fileFrame = null;

            return function(event) {
                var $button = $(this);

                event.preventDefault();
         
                // If the frame object has already been created, reopen the dialog.
                if (fileFrame) {
                    fileFrame.open();
                    return;
                }
         
                // Create a new media file frame.
                fileFrame = wp.media.frames.file_frame = wp.media({
                    title: args.buttonText,
                    button: {
                        text: args.buttonText
                    },
                    library: {
                        type: args.mediaType
                    },
                    multiple: false
                });
         
                // When a file is selected, grab the URL and set it as the text
                // field's value. 
                fileFrame.on('select', function() {
                    // Get media attachment details from the fileFrame state.
                    var attachment = fileFrame.state().get('selection').first().toJSON();

                    // Set the attachment URL in our input text field. We make the 
                    // assumption that the previous sibling is the input text field
                    // we want to stuff. Ensure this is the case in our html!
                    $button.prev().val(attachment.url);
                });
         
                // Open the file frame dialog.
                fileFrame.open();
            };
        }

        /**
         * Activate the media upload buttons for audio, video, or image urls, as needed.
         * There is no limit to the number of media buttons you may have per page.
         * Just attach the class names shown below to input[type="button"] fields.
         * This button must follow an input[type="text"] field.
         */
        function activateMediaUploadButtons() {
            // Activate audio upload buttons.
            $('.fw-sermons-audio-upload-button').on('click', uploadMedia({
                mediaType: 'audio',
                buttonText: 'Choose Audio File'  
            }));

            // Activate image upload buttons.
            $('.fw-sermons-image-upload-button').on('click', uploadMedia({
                mediaType: 'image',
                buttonText: 'Choose Image'  
            }));
        }

        activateMediaUploadButtons();

    });

})( jQuery );
