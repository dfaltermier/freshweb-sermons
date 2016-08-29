/**
 * This script adds the dynamic behavior we need for meta input fields.  
 *
 * @Dependencies
 *   1. jquery
 *   2. jquery-ui-datepicker
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

    /**
     * Activate the media upload buttons for audio, video, or image urls.
     */
    $(function() {

        /**
         * Callback function for the 'click' event on a media upload button.
         *
         * @param   event.data.mediaType   'image', 'audio', or 'video' media type.
         * @param   event.data.buttonText  Button text in media uploader window. 
         */
        function uploadMedia(event) {
            event.preventDefault();

            var $uploadButton = $(this);

            // Create a new media file frame.
            var fileFrame = wp.media.frames.file_frame = wp.media({
                title: event.data.buttonText,
                button: {
                    text: event.data.buttonText
                },
                library: {
                    type: event.data.mediaType
                },
                multiple: false
            });
     
            // When a file is selected, grab the URL and set it as the text
            // field's value. 
            fileFrame.on('select', function() {
                var attachment = fileFrame.state().get('selection').first().toJSON();

                // If we have an input text field as a sibling of our upload button,
                // then set the text field value with the attachment url.
                var $input = $uploadButton.siblings('input[type="text"]');

                if ($input.length) {
                    $input.val(attachment.url);
                }

                // If we have an img tag as a sibling of our upload button,
                // then set the src attribute value with the attachment url.
                // We only do this for images since it makes no sense for 
                // other media types.
                var $image = null;
                if (event.data.mediaType === 'image') {
                    $image = $uploadButton.siblings('img');

                    if ($image.length) {
                        $image.attr('src', attachment.url).fadeIn();
                    }
                }

                // If we have a hidden field as a sibling of our upload button,
                // then set the hidden input field value with the attachment url.
                var $hidden = $uploadButton.siblings('input[type="hidden"]');

                if ($hidden.length) {
                    $hidden.val(attachment.url);
                }

                // Now that we have the values set, replace the 'upload' button
                // with the 'remove' button.
                var $removeButton = $uploadButton.siblings(
                    '.fw-sermons-'+ event.data.mediaType +'-remove-button'
                );

                if ($removeButton.length) {
                    $uploadButton.hide();
                    $removeButton.show();
                }
            });
     
            // Open the file frame dialog.
            fileFrame.open();
        }


        function removeMedia(event) {
            event.preventDefault();

            var $removeButton = $(this);
            console.log("removeMedia(): ", $removeButton);

            // If we have an input text field as a sibling of our remove button,
            // then remove the text field value.
            var $input = $removeButton.siblings('input[type="text"]');

            if ($input.length) {
                $input.val('');
            }

            // If we have an img tag as a sibling of our remove button,
            // then remove the src attribute value. 
            var $image = $removeButton.siblings('img');

            if ($image.length) {
                $image.fadeOut(800, function() {
                    $(this).attr('src', '');
                });
            }

            // If we have a hidden field as a sibling of our remove button,
            // then remove the hidden input field value.
            var $hidden = $removeButton.siblings('input[type="hidden"]');

            if ($hidden.length) {
                $hidden.val('');
            }

            // Now that we have the values removed, replace the 'remove' button
            // with the 'upload' button.
            var $uploadButton = $removeButton.siblings(
                '.fw-sermons-'+ event.data.mediaType +'-upload-button'
            );

            if ($uploadButton.length) {
                $removeButton.hide();
                $uploadButton.show();
            }
        }

        /**
         * Activate the media upload buttons for audio, video, or image urls, as needed.
         * There is no limit to the number of media buttons you may have per page.
         * Just attach the class names shown below to input[type="button"] fields.
         * This button must follow an input[type="text"] field.
         */
        function activateMediaUploadButtons() {
            // Activate audio upload buttons.
            $('.fw-sermons-audio-upload-button').on(
                'click',
                {
                    mediaType:  'audio',
                    buttonText: 'Choose Audio File'
                },
                uploadMedia
            );

            // Activate video upload buttons.
            $('.fw-sermons-video-upload-button').on(
                'click', 
                {
                    mediaType:  'video',
                    buttonText: 'Choose Video File'  
                },
                uploadMedia
            );

            // Activate image upload buttons.
            $('.fw-sermons-image-upload-button').on(
                'click', 
                {
                    mediaType:  'image',
                    buttonText: 'Choose Image File'  
                },
                uploadMedia
            );
        }

        function activateMediaRemoveButtons() {
            // Activate audio remove buttons.
            $('.fw-sermons-audio-remove-button').on(
                'click',
                {
                    mediaType: 'audio'
                },
                removeMedia
            );

            // Activate video remove buttons.
            $('.fw-sermons-video-remove-button').on(
                'click', 
                {
                    mediaType: 'video'  
                },
                removeMedia
            );

            // Activate image remove buttons.
            $('.fw-sermons-image-remove-button').on(
                'click', 
                {
                    mediaType: 'image'
                },
                removeMedia
            );
        }

        function clearMediaFormFieldsAfterSubmission($form, submitButtonId) {
            console.log('clearMediaFormFieldsAfterSubmission() ', $form, ' submitButtonId: ' + submitButtonId);

            if ($form && $form.length === 1) {
                $(document).ajaxComplete(function(event, jqXHR, obj) {
                    console.log('clearMediaFormFieldsAfterSubmission(): ajaxcomplete(): ', event);

                    if (event &&
                        event.currentTarget &&
                        event.currentTarget.activeElement &&
                        event.currentTarget.activeElement.id === submitButtonId) {

                        $('.fw-sermons-audio-remove-button', $form).trigger('click');
                        $('.fw-sermons-video-remove-button', $form).trigger('click');
                        $('.fw-sermons-image-remove-button', $form).trigger('click');
                    }
                });

                return true;
            }

            return false;
        }

        function activateClearMediaFormFieldsAfterSubmission() {
            return (
                clearMediaFormFieldsAfterSubmission( $('form#addtag', 'body.post-type-sermon'), 'submit' )
            );   
        }

        activateMediaUploadButtons();
        activateMediaRemoveButtons();
        activateClearMediaFormFieldsAfterSubmission();
    });

    /**
     * Activate the media upload button for [multiple] documents.
     */
    $(function() {
     
        /**
         * Callback function for the 'click' event on a [document] media upload button
         */
        function uploadMedia(args) {
            event.preventDefault();

            var $button = $(this);
     
            // Create a new media file frame.
            var fileFrame = wp.media.frames.file_frame = wp.media({
                title: args.buttonText,
                button: {
                    text: 'Choose Document File'
                },
                library: {
                    type: '' // There is currently no 'document' type to filter by.
                },
                multiple: false
            });
     
            // When a file is selected, grab the URL and set the text field value. 
            fileFrame.on('select', function() {
                // Get media attachment details from the fileFrame state.
                var attachment = fileFrame.state().get('selection').first().toJSON();

                // Set the attachment URL in our input text field. We make the 
                // assumption that the previous sibling is the input text field
                // we want to stuff. Ensure this is the case in our html!
                $button.closest('.fw-sermons-document-row')
                    .find('.fw-sermons-document-link-label')
                    .val(typeof attachment.url === 'string' ? (attachment.title).trim() : '');

                $button.closest('.fw-sermons-document-row')
                    .find('.fw-sermons-document-link-url')
                    .val(typeof attachment.url === 'string' ? (attachment.url).trim() : '');
            });
     
            // Open the file frame dialog.
            fileFrame.open();
        }

        /**
         * Activate the media upload buttons for audio, video, or image urls, as needed.
         * There is no limit to the number of media buttons you may have per page.
         * Just attach the class names shown below to input[type="button"] fields.
         * This button must follow an input[type="text"] field.
         */
        function activateMediaUploadButtons() {
            $('.fw-sermons-document-upload-button').on('click', uploadMedia);
        }

        /**
         * Adds another link row to the bottom of our current rows of links.
         */
        function addLink(event) {
            // Get enclosing table.
            var $table = $(this).closest('.fw-sermons-document-meta-fields');
            var $linkRows = $('.fw-sermons-document-row', $table);

            // Sanity. This should never happen.
            if ($linkRows.length <= 0) {
                return;
            }

            // Clone the last link row.
            var $lastLinkRow   = $linkRows.last();
            var $clonedLinkRow = $lastLinkRow.clone(true, true); // 'true' will clone attached events also.

            // Clear the cloned inputs.
            $clonedLinkRow.find('input[type="text"]').val('');

            // Append the cloned link after the last link.
            $lastLinkRow.after($clonedLinkRow);

            // Now that we have two or more links, show the 'Delete' link for all of them.
            // Be sure to fetch our new set of links and not use the previous set.
            $('.fw-sermons-document-delete-link', $table).show();
        }

        /**
         * Deletes a link when the user clicks the associated 'Delete' link.
         */
        function deleteLink(event) {
            // Get enclosing table.
            var $table = $(this).closest('.fw-sermons-document-meta-fields');

            // Delete the selected row.
            $(this).closest('.fw-sermons-document-row').remove();

            // if we have only one row remaining, hide the 'Delete' link.
            var $linkRows = $('.fw-sermons-document-row',  $table);

            if ($linkRows.length <= 1) {
                $('.fw-sermons-document-delete-link', $table).hide();
            }
        }

        /**
         * Initializes our links. Only one link is created to begin with.
         * The user is not allowed to delete a link if it the only one
         * remaining.
         */
        function activateAddDeleteLinks() {
            // Fetch our list of current links.
            var $table    = $('.fw-sermons-document-meta-fields');
            var $linkRows = $('.fw-sermons-document-row',  $table);

            // Activate the Add Link.
            $('.fw-sermons-document-add-link', $table).on('click', addLink);

            // Activate all Delete Links.
            $('.fw-sermons-document-delete-link', $table).on('click', deleteLink);

            // if we have only one row (which we should), hide the 'Delete' link.
            // Otherwise, show them all.
            if ($linkRows.length <= 1) {
                $('.fw-sermons-document-delete-link', $table).hide();
            }
            else {
                $('.fw-sermons-document-delete-link', $table).show();
            }
        }

        activateMediaUploadButtons();
        activateAddDeleteLinks();

    });

})( jQuery );
