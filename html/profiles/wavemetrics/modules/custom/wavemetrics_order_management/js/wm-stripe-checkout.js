(function ($, Drupal, drupalSettings) {

  Drupal.behaviors.stripecheckout = {
    attach: function (context, settings) {

      //get the latest order info
      var orderData = drupalSettings.orderData;
      // A reference to Stripe.js initialized with your real test publishable API key.
      var stripe = Stripe(orderData.pk);

      // The items the customer wants to buy
      var purchase = {
        items: [{id: "paybyorderid"}]
      };

      //Display the order summary
      var orderPrice = orderData.price;
      orderPrice = orderPrice / 100;
      orderPrice = orderPrice.toFixed(2);
      var orderSummaryHTML = "<p>Order Total: $"+orderPrice+"</p>";
      $("#stripe-wm-order-summary").html(orderSummaryHTML);

      // Disable the button until we have Stripe set up on the page
      document.querySelector("button").disabled = true;
      fetch("/order/checkout/stripe/json", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify(purchase)
      })
        .then(function (result) {
          return result.json();
        })
        .then(function (data) {
          var elements = stripe.elements();

          var style = {
            base: {
              color: "#32325d",
              fontFamily: 'Arial, sans-serif',
              fontSmoothing: "antialiased",
              fontSize: "16px",
              "::placeholder": {
                color: "#32325d"
              }
            },
            invalid: {
              fontFamily: 'Arial, sans-serif',
              color: "#fa755a",
              iconColor: "#fa755a"
            }
          };

          var card = elements.create("card", {style: style});
          // Stripe injects an iframe into the DOM
          card.mount("#card-element");

          card.on("change", function (event) {
            // Disable the Pay button if there are no card details in the Element
            document.querySelector("button").disabled = event.empty;
            document.querySelector("#card-error").textContent = event.error ? event.error.message : "";
          });

          var form = document.getElementById("payment-form");
          form.addEventListener("submit", function (event) {
            event.preventDefault();
            // Complete payment when the submit button is clicked
            payWithCard(stripe, card, data.clientSecret);
          });
        });

      // Calls stripe.confirmCardPayment
      // If the card requires authentication Stripe shows a pop-up modal to
      // prompt the user to enter authentication details without leaving your page.
      var payWithCard = function (stripe, card, clientSecret) {
        loading(true);
        stripe
          .confirmCardPayment(clientSecret, {
            payment_method: {
              card: card
            }
          })
          .then(function (result) {
            if (result.error) {
              // Show error to your customer
              showError(result.error.message);
            } else {
              // The payment succeeded!
              orderComplete(result.paymentIntent.id);
            }
          });
      };

      /* ------- UI helpers ------- */

      // Shows a success message when the payment is complete
      var orderComplete = function (paymentIntentId) {
        loading(false);
        //Update the PI ID we just acquired to the order data in drupal via ajax
        $.ajax({
            url: '/order/checkout/stripe/pi/update/' + paymentIntentId,
            async: false,
          });
          //#To-Do Can this be pulled from the Webform's config?
          window.location.href = "/order/confirmation/paybyorderid";
      };

      // Show the customer the error from Stripe if their card fails to charge
      var showError = function (errorMsgText) {
        loading(false);
        var errorMsg = document.querySelector("#card-error");
        errorMsg.textContent = errorMsgText;
        setTimeout(function () {
          errorMsg.textContent = "";
        }, 4000);
      };

      // Show a spinner on payment submission
      var loading = function (isLoading) {
        if (isLoading) {
          // Disable the button and show a spinner
          document.querySelector("button").disabled = true;
          document.querySelector("#spinner").classList.remove("hidden");
          document.querySelector("#button-text").classList.add("hidden");
        } else {
          document.querySelector("button").disabled = false;
          document.querySelector("#spinner").classList.add("hidden");
          document.querySelector("#button-text").classList.remove("hidden");
        }
      };


    }
  };

})(jQuery, Drupal, drupalSettings);
