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
         * Intercept the click event for the Sermons Clean Up form submission button.
         *
         * When the user clicks the form submission button, prompt the user with a popup dialog
         * confirming whether they wish to continue with deleting Sermons.
         *
         * @since   1.1.0
         */
        function activateSermonSubmitButton() {

            $("input[name='submit_sermons_cleanup']").on('click', function(e) {
                var msg = 'You are about to permanently delete Sermons. Only do this if you are preparing to uninstall this plugin.' +
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

    });

})(jQuery);

