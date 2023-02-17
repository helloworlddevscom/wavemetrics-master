<?php

namespace Drupal\wavemetrics_order_management\Controller;

use Drupal\Core\Controller\ControllerBase;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\JsonResponse;
use \Drupal\Core\TempStore\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a storage handler class that handles the node grants system.
 *
 * This is used to return a stripe json response.
 *
 * @ingroup wavemetrics_order_management
 */
class wavemetrics_order_managementStripeCheckout extends ControllerBase{

  /**
   * Stores the tempstore.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStore
   */
  protected $tempStore;

  public function __construct(PrivateTempStoreFactory $temp_store_factory) {
    $this->tempStore = $temp_store_factory
      ->get('wavemetrics_order_management');
    $this->wavemetrics_order_db = \Drupal::service('wavemetrics_order_management.db_logic');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container
      ->get('tempstore.private'), $container);
  }

  public function wm_stripe_checkout_checkout_page() {

    $orderData = $this->tempStore->get('orderData');
    if (!empty($orderData)) {
      $content['stripe']['header'] = [
        '#type' => 'markup',
        '#markup' => '<h1>Pay By OrderID</h1></h1><p class="stripe-payment-label">Payment Information</p>',
        '#attached' => [
          'library' => [
            'wavemetrics_order_management/wm-stripe-checkout',
            'wavemetrics_order_management/wm-stripe-checkout_v3',
            'wavemetrics_order_management/wm-stripe-polyfill-fetch',
          ],
          'drupalSettings' => [
            "orderData" => $orderData
          ]
        ],
      ];

      $form_class = '\Drupal\wavemetrics_order_management\Form\wavemetrics_stripe_checkout_form';
      $content['stripe']['form'] = \Drupal::formBuilder()->getForm($form_class);
    }
    else {
      $content['stripe']['nodata'] = [
        '#type' => 'markup',
        '#markup' => '<h1>Your payment can not be completed at this time.</h1>',
      ];
    }
    return $content;
  }

  public function wm_stripe_checkout_json_page() {

    $sk = \Drupal::service('key.repository')->getKey("stripe_webform_api_secret")->getKeyValue();
    Stripe::setApiKey($sk);

    try {
      // retrieve JSON from POST body
      $json_str = file_get_contents('php://input');
      $json_obj = json_decode($json_str);
      $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $this->calculateOrderAmount(),
        'currency' => 'usd',
        'capture_method' => 'manual',
      ]);

      return new JsonResponse(['clientSecret' => $paymentIntent->client_secret]);

    } catch (Error $e) {
      http_response_code(500);
      return new JsonResponse(['error' => $e->getMessage()]);
    }

  }

  public function calculateOrderAmount(): int {
    //get the order data
    $tempstore = \Drupal::service('tempstore.private')->get('wavemetrics_order_management');
    $orderData = $tempstore->get('orderData');
    if (isset($orderData["price"]) && !empty($orderData["price"]) && $orderData["price"] > 0) {
      $orderAmount = $orderData["price"];
      return $orderAmount;
    }
    return false;
  }

  public function wm_stripe_checkout_pi_update($piid) {
    //see if this user has an order in their private storage. If not, reject.
    $tempstore = \Drupal::service('tempstore.private')->get('wavemetrics_order_management');
    //get the details
    $orderData = $tempstore->get('orderData');
    $price = $orderData['price'];
    $order_name = $orderData['order_name'];
    $order_email = $orderData['order_email'];
    $order_id = $tempstore->get('order_id');
    $quote_num = $tempstore->get('quote_num');
    if( !empty($price) && !empty($order_id) ) {

      $comment = !empty($values['order_comment']) ? $values['order_comment'].PHP_EOL : "";
      $comment .= "WaveMetrics reference number: ".$quote_num."\n\n";

      //generate the order items string
      //The WaveMetrics OrderItems is a text field with a specific string inside. This code is based on their existing code.
      $orderItems = "";

      $orderItems .= str_pad("1",8,' ').str_pad("Pay by Order ID",25,' ').str_pad("PayByOrderID",20,' ').str_pad(number_format($price/100,2,".",""),17,' ')."\n";

      //Data for the WaveMetrics Order Management
      $data = array(
        'id' => '',
        "orderid" => $order_id,
        "email" => $order_email,
        "name" => $order_name,
        "amountcents" => $price,
        "address1" => "",
        "city" => "",
        "state" => "",
        "zip" => "",
        "country" => "",
        "phone" => "",
        "ordertime" => date("Y-m-d G:i:s"),
        "handled" => false,
        "wmdborderid" => '',
        "stripeid" => $piid,
        "orderitems" => $orderItems,
        "comment" => $comment,
        "ordertype" => "Reseller",
        "confirmation" => $order_id,
        'institution' => "",
        'department' => "",
        'degree' => "",
        'urlhelper' => "",
        'request' => 0,
        'course' => "",
        'serials' => "",
        'position' => "",
        'activation' => "",
        'source' => 'webforms_stripecheckout'
      );

      //load the WaveMetrics Order Management
      $order_manager = new wavemetrics_order_managementController;
      //Save this form submission as an order.
      $order_manager->saveOrder($data);

      //remove some order data from the private store so the user doesn't rerun the transaction.
      $tempstore->set('orderData',"");
      $tempstore->set('quote_num',"");

    }
    return new \Drupal\Core\Ajax\AjaxResponse();
  }

}
