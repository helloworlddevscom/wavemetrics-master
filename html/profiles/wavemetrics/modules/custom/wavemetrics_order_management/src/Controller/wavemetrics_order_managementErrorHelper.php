<?php
/**
 * @file
 * Contains \Drupal\wavemetrics_order_management\Controller\wavemetrics_order_managementErrorHelper.
 */

namespace Drupal\wavemetrics_order_management\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\commerce_order\Entity\Order;

class wavemetrics_order_managementErrorHelper extends ControllerBase
{

    //Any admins that should receive notifications about commerce errors.
    // @todo Add this to the admin panel.
    public $admin_emails;

    public function __construct()
    {
        $this->admin_emails = "tom@wavemetrics.com,sales@wavemetrics.com";

        //get the order id from the URL
        $current_path = \Drupal::service('path.current')->getPath();
        $path_args = explode('/', $current_path);
        //a little validation of a URL parameter. Should be an ID
        if( is_numeric($path_args[2]) ) {
            $this->order_id = $path_args[2];
        }
        else {
            $this->order_id = null;
        }

    }

    /**
     * Given any error, Display it as a Drupal Message.
     *
     * @return boolean
     *   True on success, False on errors.
     */
    public function displayError($error_message) {
        drupal_set_message($error_message, 'warning');
        return true;
    }

    /**
     * Given any error, Email it to defined admins.
     *
     * @return boolean
     *   True on success, False on errors.
     */
    public function emailError($error_message, Order $order = null) {

        $mailManager = \Drupal::service('plugin.manager.mail');

        $module = 'wavemetrics_order_management';
        $key = 'card_error';
        $to = $this->admin_emails;
        $langcode = \Drupal::currentUser()->getPreferredLangcode();
        $send = true;

        //Get order info and do some formatting.
        if(!empty($this->order_id)){
            $order_id = $this->order_id;
        }
        else{
            $order_id = "Unknown";
        }

        //Craft the message
        $params['message'] = "An order was attempted on WaveMetrics.com but received an error. These are the order details.".PHP_EOL.PHP_EOL;
        $params['message'] .= "Stripe Error Message: ".$error_message.PHP_EOL;
        $params['message'] .= "Order ID: ".$order_id.PHP_EOL;
        $params['message'] .= PHP_EOL;
        if(!empty($order)){
            $params['message'] .= "Order Time: ".date("Y-m-d G:i:s",$order->getCreatedTime()).PHP_EOL;
            $params['message'] .= "Email: ".$order->getEmail().PHP_EOL;
            $profile = $order->getBillingProfile();
            $address = $profile->get('address');
            foreach($address as $addy) {
                $params['message'] .= "Name: ".$addy->get('given_name')->getValue() . " " . $addy->get('family_name')->getValue().PHP_EOL;
                $params['message'] .= "Adress: ".$addy->get('address_line1')->getValue().PHP_EOL;
                $params['message'] .= "City: ".$addy->get('locality')->getValue().PHP_EOL;
                $params['message'] .= "State: ".$addy->get('administrative_area')->getValue().PHP_EOL;
                $params['message'] .= "Zip: ".$addy->get('postal_code')->getValue().PHP_EOL;
                $params['message'] .= "Country: ".$addy->get('country_code')->getValue().PHP_EOL;
            }
            $params['message'] .= "Price: ".$order->getTotalPrice().PHP_EOL;
        }
        $params['message'] .= PHP_EOL.PHP_EOL;

        //send the message
        $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);

        //Handle the event's success or failure
        if ($result['result'] !== true) {
            $message = t('There was a problem sending wavemetrics admins an email notification to @email concerning a declined transaction on order @id.', array('@email' => $to, '@id' => $order_id));
            drupal_set_message($message, 'error');
            \Drupal::logger('d8mail')->error($message);
            return false;
        }
        else {
            return true;
        }

    }

    /**
     * Given any error, Log it as a Drupal Watchdog.
     *
     * @return boolean
     *   True on success, False on errors.
     */
    public function logError($error_message) {
        \Drupal::logger('wavemetrics_order_management')->warning($error_message);
        return true;
    }

    /**
     * Given any error, Log it as a Drupal Watchdog.
     *
     * @return boolean
     *   True on success, False on errors.
     */
    public function ProcessErrorsFully($error_message) {
        //We will need the full order details
        //if the order id was not null (could be null if validation failed)
        if(!empty($this->order_id)) {
            $order = \Drupal\commerce_order\Entity\Order::load($this->order_id);
        }
        else {
            $order = null;
        }

        //log it
        $log_results = $this->logError($error_message);

        //email it
        $email_results = $this->emailError($error_message,$order);

        //display it
        $display_results = $this->displayError($error_message);
        if($log_results===true && $email_results===true && $display_results===true) {
            return true;
        }
        else {
            return false;
        }

    }


}