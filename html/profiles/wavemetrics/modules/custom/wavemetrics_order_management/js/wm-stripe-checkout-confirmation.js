(function ($, Drupal, drupalSettings) {

  Drupal.behaviors.stripeconfirmation = {
    attach: function (context, settings) {


      //get the latest order id
      var orderID = drupalSettings.stripeWmOrderId;
      if(orderID) {
        //replace the span, if found, with the order id.
        var msg = "Your order number is: " + orderID + ".";
        $("span.stripe-wm-order-id").html(msg);
      }


    }
  };

})(jQuery, Drupal, drupalSettings);
