/**
 * @file wm-instructions.js
 *
 * Provides JS adjustments site wide, targeting the admin panel and admin theme.
 */

(function ($, Drupal, drupalSettings) {
    /*
     * Helper functions
     */

    Drupal.behaviors.wminstructions = {
        attach: function(context, settings) {

            ////A collapsible div used on content creation pages (node/add/code_snippet and node/add/forums but can be used on others)
            $('#wm-instructions').hide();
            $('.wm-instruction-toggle').on('click', function(){
                $('#wm-instructions').slideToggle();
            })

        }
    };
}) (jQuery, Drupal, drupalSettings);
