(function($, Drupal, drupalSettings) {

    Drupal.behaviors.fscheckout = {
        attach: function (context, settings) {

            //Copy the GET variable value for wm-fs-order-reference into the span for display.
            if($('#wm-fs-order-reference')) {
                var urlParams = new URLSearchParams(window.location.search);
                var orderRef = urlParams.get('fsOrderID');
                //add and clean this value to the span
                if(orderRef) {
                    var orderRefStr = "Order Reference: "+orderRef;
                    $('#wm-fs-order-reference').text(orderRefStr);
                }
                else {
                    var orderRefStr = "";
                    $('#wm-fs-order-reference').text(orderRefStr);
                }
            }

        }
    };

})(jQuery, Drupal, drupalSettings);

//This is a callback for the Fast Spring JS
function onFSPopupClosed(orderReference) {
    if (orderReference)
    {
        window.location.replace("/order/confirmation/fastspring?fsOrderID=" + orderReference.reference);
    } else {
        //console.log("no order ID");
    }
}
