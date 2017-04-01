/**
 * Activate the behavior on our Post and Taxonomy form sections
 * where we are using media upload buttons to add audio, video,
 * and image files to the form.
 *
 * USE
 *
 * The html for image uploading must be wrapped in the shown parent <div> and
 * contain only the following elements:
 *
 * <div class="fw-sermons-image-upload-container">
 *    <label for="fw_sermons_speaker_image_id">Sermon Speaker Image</label>
 *    <input type="hidden" 
 *           name="fw_sermons_speaker_image_id"
 *           id="fw_sermons_speaker_image_id" value="" />
 *    <input type="button"
 *           class="button fw-sermons-image-upload-button"
 *           value="Upload Image" />
 *    <input type="button" 
 *           class="button fw-sermons-image-remove-button"
 *           value="Remove Image" style="display:none;" />
 *    <div class="fw-sermons-image-upload-wrapper"><img 
 *         class="fw-sermons-image-upload" src="" style="display:none;" alt="" /></div>
 * </div>
 *
 * The html for video (and audio) uploading must be wrapped in the shown parent <div> and
 * contain only the following elements:
 *
 * <div class="fw-sermons-media-upload-container">
 *    <label for="fw_sermons_video_download_url">Video Download Url</label>
 *    <input type="text"
 *           name="fw_sermons_video_download_url"
 *           id="fw_sermons_video_download_url" value="" />
 *    <input type="button"
 *           class="button fw-sermons-video-upload-button"
 *           value="Upload Video" />
 *    <p class="description">Url to downloadable sermon video file</p>
 * </div>
 *
 * @package    FreshWeb_Church_Sermons
 * @subpackage JavaScript
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.0
 */
(function($) {

    'use strict';

    $(function() {

        /**
         * Callback function for the 'click' event on a upload media button.
         *
         * @since   1.1.0
         * @todo    We should be caching the handle to the wp.media object so we're
         *          not recreating it before every open(). How?
         *
         * @param   Event object  event   Use: event.data.mediaType  ('image', 'audio', or 'video' media type).
         *                                     event.data.buttonText (Button text in media uploader window).
         */
        function uploadMedia(event) {
            event.preventDefault();

            var $uploadButton = $(this).show();

            // Get the parent element containing the upload/remove buttons and the
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
         * @since   1.1.0
         *
         * @param   Event object  event  Use: event.data.mediaType ('image', 'audio', or 'video' media type).
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
         *
         * @since 1.1.0
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
         *
         * @since 1.1.0
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
         *
         * @since 1.1.0
         */
        function activateClearMediaFormFieldsAfterSubmission() {
            var $form = $('form#addtag', 'body.post-type-sermon');

            if ($form && $form.length === 1) {
                // The WordPress JavaScript for submitting the Add Taxonomy
                // form does not propagate the button's click event. We'll
                // attach to the ajaxComplete hook instead so we're notified
                // when the submission is complete.
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

        // Initialize.
        activateMediaUploadButtons();
        activateMediaRemoveButtons();
        activateClearMediaFormFieldsAfterSubmission();

    });

})( jQuery );
