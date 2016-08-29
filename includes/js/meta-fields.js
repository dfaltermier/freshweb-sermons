/**
 * This script adds the dynamic behavior we need for our post and
 * taxonomy form fields.
 *
 * @Dependencies
 *   1. jquery
 *   2. jquery-ui-datepicker
 */
(function($) {

    'use strict';

    /**
     * Add a datepicker to post and taxonomy date form fields.
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
     * Activate the media upload buttons for audio, video, or images. These
     * are form blocks where a media upload button accompanies a text input
     * field (for audio and video urls) and images and hidden text fields
     * for images.
     */
    $(function() {

        /**
         * Callback function for the 'click' event on a upload media button.
         *
         * @param   event.data.mediaType   'image', 'audio', or 'video' media type.
         * @param   event.data.buttonText  Button text in media uploader window. 
         */
        function uploadMedia(event) {
            event.preventDefault();

            var $uploadButton = $(this).show();

            // Get the element containing the upload/remove buttons and the
            // associatedd media form fields.
            var $container = $uploadButton.parent();

            var $removeButton = $(
                '.fw-sermons-'+ event.data.mediaType +'-remove-button',
                 $container
            )

            if ($removeButton.length) {
                $removeButton.hide();
            }

            // Create a new media file frame.
            var fileFrame = wp.media.frames.file_frame = wp.media({
                title: event.data.buttonText,
                button: {
                    text: event.data.buttonText
                },
                library: {
                    type: event.data.mediaType // Filter library by mediaType
                },
                multiple: false
            });
     
            fileFrame.on('select', function() {
                var attachment = fileFrame.state().get('selection').first().toJSON();

                switch (event.data.mediaType) {
                    case 'audio':
                    case 'video':
                        // We expect an input text form field for these media types
                        // since the audio and video urls can be to external sites.
                        // Set the text field value with the attachment url.
                        var $input = $('input[type="text"]', $container);
                        if ($input.length) {
                            $input.val(attachment.url);
                        }
                        break;

                    case 'image':
                        // Display our image.
                        var $image = $('img', $container);
                        if ($image.length) {
                            $image.attr('src', attachment.url).fadeIn();
                        }

                        // We expect a hidden form field to store our image attachment id.
                        var $hidden = $('input[type="hidden"]', $container);
                        if ($hidden.length) {
                            $hidden.val(attachment.id);
                        }
                        break;

                    default:
                        return;
                        break;
                }

                if ($removeButton.length) {
                    $uploadButton.hide();
                    $removeButton.show();
                }
            });

            fileFrame.open();
        }

        /**
         * Callback function for the 'click' event on a remove media button.
         *
         * @param   event.data.mediaType   'image', 'audio', or 'video' media type.
         */
        function removeMedia(event) {
            event.preventDefault();

            var $removeButton = $(this);
            var $container = $removeButton.parent();
            var $uploadButton = $(
                '.fw-sermons-'+ event.data.mediaType +'-upload-button',
                $container
            );

            // Clear any input text and hidden form field values
            var $input = $('input[type="text"], input[type="hidden"]', $container);
            if ($input.length) {
                $input.val('');
            }

            // Clear our image.
            var $image = $('img', $container);
            if ($image.length) {
                $image.fadeOut(800, function() {
                    $(this).attr('src', '');
                });
            }

            if ($uploadButton.length) {
                $removeButton.hide();
                $uploadButton.show();
            }
        }

        /**
         * Activate the media upload buttons for audio, video, and images.
         * There is no limit to the number of media buttons you may have per page.
         * Just attach the class names shown below to input[type="button"] fields.
         * See the Speaker taxonomy and Add Sermon page for html layout.
         */
        function activateMediaUploadButtons() {
            $('.fw-sermons-audio-upload-button').on(
                'click',
                {
                    mediaType:  'audio',
                    buttonText: 'Choose Audio'
                },
                uploadMedia
            );

            $('.fw-sermons-video-upload-button').on(
                'click', 
                {
                    mediaType:  'video',
                    buttonText: 'Choose Video'  
                },
                uploadMedia
            );

            $('.fw-sermons-image-upload-button').on(
                'click', 
                {
                    mediaType:  'image',
                    buttonText: 'Choose Image'  
                },
                uploadMedia
            );
        }

        /**
         * Activate the media remove buttons for audio, video, and images.
         * There is no limit to the number of media buttons you may have per page.
         * Just attach the class names shown below to input[type="button"] fields.
         * See the Speaker taxonomy and Add Sermon page for html layout.
         */
        function activateMediaRemoveButtons() {
            $('.fw-sermons-audio-remove-button').on(
                'click',
                {
                    mediaType: 'audio'
                },
                removeMedia
            );

            $('.fw-sermons-video-remove-button').on(
                'click', 
                {
                    mediaType: 'video'  
                },
                removeMedia
            );

            $('.fw-sermons-image-remove-button').on(
                'click', 
                {
                    mediaType: 'image'
                },
                removeMedia
            );
        }

        /**
         * On the 'Add New Taxonomy' pages (e.g. Add New Speaker, etc.), clicking 
         * WordPress's submit button does not refresh the page when it submits
         * the form fields. It submits the fields via an Ajax call and then displays
         * the newly created taxonomy in the table to the right of the form.
         * After the form is submitted, WordPress only clears the text and text 
         * area form fields. Our image and hidden form fields are not cleared as
         * a result. We must do this ourselves. Again, this only applies to the 
         * 'Add New Taxonomy' page.
         */
        function activateClearMediaFormFieldsAfterSubmission() {
            var $form = $('form#addtag', 'body.post-type-sermon');

            if ($form && $form.length === 1) {
                // Note: WordPress does not allow the click event to propagate
                // when the submit button is clicked. This was the only way I
                // could find to get notified when the form was submitted so I
                // could then reset the image/hidden fields. Perhaps there is
                // a better way, but I couldn't find it.
                $(document).ajaxComplete(function(event, jqXHR, obj) {
                    if (event &&
                        event.currentTarget &&
                        event.currentTarget.activeElement &&
                        event.currentTarget.activeElement.id === 'submit') {
                        $('.fw-sermons-audio-remove-button, ' +
                          '.fw-sermons-video-remove-button, ' +
                          '.fw-sermons-image-remove-button', $form).trigger('click');
                    }
                });
            }
        }

        /*
         * The below functions activate the behavior on our Post and Taxonomy form
         * sections where we are using media upload buttons to add audio, video,
         * and image files to the form.
         *
         * We assume the html for image uploading looks something like this:
         *
         *  <label for="fw_sermons_speaker_image">Sermon Speaker Image</label>
         *  <input type="hidden" name="fw_sermons_speaker_image"
         *         id="fw_sermons_speaker_image" value="" />
         *  <input type="button"
         *         class="button fw-sermons-image-upload-button"
         *         value="Upload Image" />
         *  <input type="button" class="button fw-sermons-image-remove-button"
         *         value="Remove Image" style="display:none;" />
         *  <div class="fw-sermons-image-upload-wrapper"><img 
         *       class="fw-sermons-image-upload" src="" style="display:none;" /></div>
         *
         * We assume the html for video (and audio) uploading looks something like this:
         *
         *  <label for="fw_sermons_video_download_url">Video Download Url</label>
         *  <input type="text" name="fw_sermons_video_download_url"
         *         id="fw_sermons_video_download_url" value="" />
         *  <input type="button"
         *         class="button fw-sermons-video-upload-button"
         *         value="Upload Video" />
         *  <p class="description">Url to downloadable sermon video file</p>
         */
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
            $clonedLinkRow.hide();

            // Append the cloned link after the last link.
            $lastLinkRow.after($clonedLinkRow);

            $clonedLinkRow.fadeIn();

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

            // if we have two rows remaining, hide the 'Delete' link for the row
            // we're now removing AND the row that will remain.
            var $linkRows = $('.fw-sermons-document-row',  $table);
            if ($linkRows.length <= 2) {
                $('.fw-sermons-document-delete-link', $table).hide();
            }

            // Delete the selected row.
            $(this).closest('.fw-sermons-document-row').fadeOut(800, function() {
                $(this).remove();
            });
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
