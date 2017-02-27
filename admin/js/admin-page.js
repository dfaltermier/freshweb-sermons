/**
 * Adds JavaSccript behavior to our 'Sermons Clean Up' admin page.
 *
 * @package    FreshWeb_Church_Sermons
 * @subpackage JavaScript
 * @copyright  Copyright (c) 2017, freshwebstudio.com
 * @link       https://freshwebstudio.com
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.0
 */

(function($) {

    "use strict";

    $(function() {

        /**
         * Intercept the click event for the Sermons Clean Up form submission buttons.
         *
         * When the user clicks the form submission buttons, prompt the user with a popup dialog
         * confirming whether they wish to continue with deleting Sermons and the taxonomies.
         *
         * @since 1.1.0
         */
        function activateSermonSubmitButton() {

            $('#fw-sermons-cleanup-sermons-form-submit', '.fw-sermons-cleanup-sermons-form').on('click', function(e) {
                var msg = 'You are about to permanently delete Sermons. ' +
                          'Only do this if you are preparing to uninstall this plugin.' +
                          '\n\nDo you wish to continue?';

                var reply = confirm(msg);
                
                if (!reply) {
                    e.preventDefault();
                    return false;
                }

                return true;
            });

        }

        function activateSermonSeriesSubmitButton() {

            $('#fw-sermons-cleanup-taxonomy-form-submit-sermon_series', '.fw-sermons-cleanup-taxonomy-form-sermon_series').on('click', function(e) {
                var msg = 'You are about to permanently delete the Sermon Series. ' +
                          'Only do this if you are preparing to uninstall this plugin.' +
                          '\n\nDo you wish to continue?';

                var reply = confirm(msg);
                
                if (!reply) {
                    e.preventDefault();
                    return false;
                }

                return true;
            });

        }

        function activateSermonSpeakersSubmitButton() {

            $('#fw-sermons-cleanup-taxonomy-form-submit-sermon_speaker', '.fw-sermons-cleanup-taxonomy-form-sermon_speaker').on('click', function(e) {
                var msg = 'You are about to permanently delete the Sermon Speakers. ' +
                          'Only do this if you are preparing to uninstall this plugin.' +
                          '\n\nDo you wish to continue?';

                var reply = confirm(msg);
                
                if (!reply) {
                    e.preventDefault();
                    return false;
                }

                return true;
            });

        }

        function activateSermonTopicsSubmitButton() {

            $('#fw-sermons-cleanup-taxonomy-form-submit-sermon_topic', '.fw-sermons-cleanup-taxonomy-form-sermon_topic').on('click', function(e) {
                var msg = 'You are about to permanently delete the Sermon Topics. ' +
                          'Only do this if you are preparing to uninstall this plugin.' +
                          '\n\nDo you wish to continue?';

                var reply = confirm(msg);
                
                if (!reply) {
                    e.preventDefault();
                    return false;
                }

                return true;
            });

        }

        function activateSermonBooksSubmitButton() {

            $('#fw-sermons-cleanup-taxonomy-form-submit-sermon_book', '.fw-sermons-cleanup-taxonomy-form-sermon_book').on('click', function(e) {
                var msg = 'You are about to permanently delete the Sermon Books. ' +
                          'Only do this if you are preparing to uninstall this plugin.' +
                          '\n\nDo you wish to continue?';

                var reply = confirm(msg);
                
                if (!reply) {
                    e.preventDefault();
                    return false;
                }

                return true;
            });

        }

        activateSermonSubmitButton();
        activateSermonSeriesSubmitButton();
        activateSermonSpeakersSubmitButton();
        activateSermonTopicsSubmitButton();
        activateSermonBooksSubmitButton();

    });

})(jQuery);

