/**
 * Activate the behavior on our Post and Taxonomy form sections
 * where we are using document upload buttons to add documents
 * to the form.
 */
(function($) {

    'use strict';

    $(function() {
     
        /**
         * Callback function for the 'click' event on a [document] media upload button.
         *
         * @param   event.data.buttonText  Button text in media uploader window. 
         */
        function uploadMedia(event) {
            event.preventDefault();

            var $button = $(this);
            var $documentRow = $button.closest('.fw-sermons-document-row');
     
            var fileFrame = wp.media.frames.file_frame = wp.media({
                title: event.data.buttonText,
                button: {
                    text: event.data.buttonText
                },
                // There is currently no 'document' type to filter our media
                // frame by. Default to showing all files.
                library: {
                    type: ''
                },
                multiple: false
            });
     
            fileFrame.on('select', function() {
                var attachment = fileFrame.state().get('selection').first().toJSON();

                // Set the attachment title and URL in our input text fields.
                $('.fw-sermons-document-link-label', $documentRow)
                    .val(typeof attachment.title === 'string' ? (attachment.title).trim() : '');

                $('.fw-sermons-document-link-url', $documentRow)
                    .val(typeof attachment.url === 'string' ? (attachment.url).trim() : '');
            });
     
            // Open the file frame dialog.
            fileFrame.open();
        }

        function activateMediaUploadButtons() {
            $('.fw-sermons-document-upload-button').on(
                'click',
                {
                    buttonText: 'Choose Document'
                },
                uploadMedia
            );
        }

        /**
         * Adds another link row to the bottom of our current rows of links.
         */
        function addLink(event) {
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
