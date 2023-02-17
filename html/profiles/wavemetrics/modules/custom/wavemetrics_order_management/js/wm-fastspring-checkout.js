(function($, Drupal, drupalSettings) {

    Drupal.behaviors.fscheckout = {
        attach: function (context, settings) {

            //get the latest order info
            var orderData = drupalSettings.orderData;
            //format the product string
            var productString;            // check the country code to make sure it is a valid destination NOT 'IR' or 'SY' or 'IQ' or 'KP':
            if(!orderData ||orderData.paymentContact.country=='IR' || orderData.paymentContact.country=='SY' || orderData.paymentContact.country=='IQ' || orderData.paymentContact.country=='KP'){
                return;
            }
            // save order items into session tags
            productString=orderData.products.toString();
            orderData.tags=productString;
            fastspring.builder.push(orderData);
            fastspring.builder.checkout();

        }
    };

})(jQuery, Drupal, drupalSettings);