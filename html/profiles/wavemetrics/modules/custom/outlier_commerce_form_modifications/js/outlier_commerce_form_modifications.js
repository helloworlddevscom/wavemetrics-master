/**
 * @file wm-instructions.js
 *
 * Provides JS adjustments site wide, targeting the admin panel and admin theme.
 */

(function ($, Drupal, drupalSettings) {
    /*
     * Helper functions
     */

    Drupal.behaviors.wmecommhelp = {
        attach: function(context, settings) {

            //Hide all help text body by default, only show on hover.
            $('.commerce-form-help-body').hide();

            $('.commerce-form-help').on('mouseover', function(e){
                //get the cursor position and help text body and set them to vars
                var left  = e.clientX  + 9;
                left  = left  + "px";
                var top  = e.clientY  + 9;
                top  = top + "px";
                var div = $(this).parent('label').parent('span.form-wrapper').children('.description').children(".commerce-form-help-body");

                //adjust the help text body css to position at the mouse cursor
                div.css( 'top', top );
                div.css( 'left', left );

                //reveal the help text body
                div.show();

            });

            //rehide the help text body
            $('.commerce-form-help').on('mouseout', function(){
                $('.commerce-form-help-body').hide();
            });

        }
    };
}) (jQuery, Drupal, drupalSettings);
