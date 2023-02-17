<?php
/**
 * @file
 * Contains \Drupal\wavemetrics_order_management\Controller\wavemetrics_order_managementController.
 */

namespace Drupal\wavemetrics_order_management\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_payment\PaymentStorage;
use Stripe\Exception\ApiErrorException as StripeError;

class wavemetrics_order_managementController extends ControllerBase
{
    public $wavemetrics_order;
    private $wavemetrics_order_db;

    public function __construct()
    {
        $this->wavemetrics_order = array(
            "id" => 0,
            "entityid" => 0,
            "orderid" => 0,
            "email" => "",
            "name" => "",
            "amountcents" => 0,
            "address1" => "",
            "city" => "",
            "state" => "",
            "zip" => "",
            "country" => "",
            "phone" => "",
            "ordertime" => "1969-01-01 00:00:00",
            "handled" => false,
            "wmdborderid" => 0,
            "stripecustomerid" => "",
            "orderitems" => "",
            "comment" => "",
            "ordertype" => "",
            "confirmation" => "",
            "source" => "",
            "position" => "",
            "activation" => "",
            "course" => "",
            "institution" => "",
            "department" => "",
            "degree" => "",
            "urlhelper" => "",
            "request" => "",
            "serials" => "",
        );
        $this->wavemetrics_order_db = \Drupal::service('wavemetrics_order_management.db_logic');
    }

    public function testbed()
    {
        $content = array();
        $content['markup'] = array(
            '#type' => 'markup',
            '#markup' => $this->t('Wave Metrics Testbed'),
        );
        $form_class = '\Drupal\wavemetrics_order_management\Form\testbedForm';
        $content['testbedForm'] = \Drupal::formBuilder()->getForm($form_class);
        return $content;
    }

    public function showOrders() {
        if ($record = $this->wavemetrics_order_db->getAll()) {
            return $record;
        }
        else{
            return false;
        }
    }

    /**
     * Process a Drupal Commerce Order into the WaveMetrics Order fields
     *
     * @return array
     *   An array of the WaveMetrics Order info
     */
    public function processOrder(OrderInterface $order) {
        //These are fields added for Digital River. As they don''t impact any CURRENT products these are just set to blank for now:
        $this->wavemetrics_order['institution'] = "";
        $this->wavemetrics_order['department'] = "";
        $this->wavemetrics_order['degree'] = "";
        $this->wavemetrics_order['urlhelper'] = "";
        $this->wavemetrics_order['request'] = "0";
        $this->wavemetrics_order['course'] = "";
        $this->wavemetrics_order['serials'] = "";
        $this->wavemetrics_order['source'] = "drupal_commerce";
        $this->wavemetrics_order['activation'] = "";
        $this->wavemetrics_order['position'] = "";


        //we may override ot append the submitted comments so track that in a var
        $comment = "";
        //The Drupal OrderID
        $this->wavemetrics_order['orderid'] = $order->getOrderNumber();
        //This column is the order ID out of the old system, I will add our order ID here. I assume this is desired.
        //This makes this column a duplicate of `orderid`, however I don't have confidence that we can use this field
        //`confirmation` for this purpose as it comes from WaveMetrics system. Hence our additional `orderid` col.
        $this->wavemetrics_order['confirmation'] =  $order->getOrderNumber();
        //Load the Billing Profile and get the address info
        $profile = $order->getBillingProfile();
        $address = $profile->get('address');
        foreach($address as $addy) {
            $this->wavemetrics_order['name'] = $addy->get('given_name')->getValue()." ".$addy->get('family_name')->getValue();
            $this->wavemetrics_order['address1'] = $addy->get('address_line1')->getValue();
            $this->wavemetrics_order['city'] = $addy->get('locality')->getValue();
            $this->wavemetrics_order['state'] = $addy->get('administrative_area')->getValue();
            $this->wavemetrics_order['zip'] = $addy->get('postal_code')->getValue();
            $this->wavemetrics_order['country'] = $addy->get('country_code')->getValue();
        }
        $this->wavemetrics_order['email'] = $profile->get('field_email')->getString();
        //phone
        if($profile->hasField('field_customer_phone')) {
            $this->wavemetrics_order['phone'] = $profile->get('field_customer_phone')->getString();
        }
        //price
        $totalPrice =  $order->getTotalPrice();
        $this->wavemetrics_order['amountcents'] = $totalPrice->getNumber()*100;
        //timestamp
        $this->wavemetrics_order['ordertime'] = date("Y-m-d G:i:s",$order->getPlacedTime());
        //payment id
        $payment_storage = \Drupal::entityTypeManager()->getStorage('commerce_payment');
        $payments = $payment_storage->loadMultipleByOrder($order);
        $payment = end($payments);
        $intent_id = $order->getData('stripe_intent');
        if($intent_id) {
          $this->wavemetrics_order['stripeid'] = $intent_id;
        }
        else {
          $this->wavemetrics_order['stripeid'] = $payment->getRemoteId();
        }
        //order items
        $order_items = $order->getItems();
        //The WaveMetrics OrderItems is a text field with a specific string inside. This code is based on their existing code.
        $orderItems = "";
        //track how many items are in each of these product types
        $standardOrderItems = 0;
        $academicOrderItems = 0;
        $studentOrderItems = 0;
        $resellerOrderItems = 0;
        $serials = array();
        $activation_keys = array();
        foreach($order_items as $order_item){
            //Load some needed entities
            $productVariation = $order_item->getPurchasedEntity();
            $product = $productVariation->getProduct();
            //Name
            $name = $product->getTitle();
            //Serial
            if($order_item->hasField('field_serial_number')){
                $sn = $order_item->get('field_serial_number')->getString();
                $serials[] = $order_item->get('field_serial_number')->getString();
            }
            else {
                $sn = "";
            }
            //Activation Keys
            if($order_item->hasField('field_activation_key')){
                $activation_keys[] = $order_item->get('field_activation_key')->getString();
            }

            //if the variation has the attribute 'upgrade' add it to the name.
            if($productVariation->hasField('attribute_license_type')) {
                $licenseType = $productVariation->getAttributeValue("attribute_license_type");
                $userTypeID = $licenseType->id();
                if ($userTypeID == 10) {
                    $name .= " Upgrade";
                }
            }
            //SKU
            $partNum= $productVariation->getSku();
            //Student Editions don't have a unique sku at WM, We append a -s to the sku so that we can have unique skus in drupal. Remove that appendage.
            if($productVariation->hasField('attribute_license_user_type')) {
                $userType = $productVariation->getAttributeValue("attribute_license_user_type");
                $userTypeID = $userType->id();
                if ($userTypeID == 8 && strtolower(substr($partNum, -2)) == "-s") {
                    $partNum = substr($partNum, 0, strlen($partNum) - 2);
                }
                if ($userTypeID == 7 && strtolower(substr($partNum, -2)) == "-a") {
                    $partNum = substr($partNum, 0, strlen($partNum) - 2);
                }
                switch ($userTypeID) {
                    case"8":
                        $studentOrderItems++;
                        break;
                    case"6":
                        $standardOrderItems++;
                        break;
                    case"7":
                        $academicOrderItems++;
                        break;
                }
            }
            //is this a reseller
            //if this is of the order type 'paybyinvoice' then it must be a reseller.
            $orderItemType = $order_item->get('type')->getString();
            if ($orderItemType == "paybyinvoice_order_type") {
                $resellerOrderItems++;
                //also create the comment
                if($order_item->hasField('field_invoice_id')) {
                    $comment .= "WaveMetrics reference number: ".$order_item->get('field_invoice_id')->getString()."\n\n";
                }
            }
            //Quantity
            $quan = round($order_item->getQuantity());
            //Total
            $tot = $order_item->getTotalPrice()->getNumber();
            //create the line within the entry.
            $orderItems .= str_pad($quan,8,' ').str_pad($name,25,' ').str_pad($partNum,20,' ').str_pad(number_format($tot,2,".",""),17,' ').$sn."\n";
            //if this is a PayByInvoice product, make the ordertype 'reseller.
        }
        $this->wavemetrics_order['orderitems'] = $orderItems;

        //Standard, Acedemic, Reseller, Student.
        //Otherwise this data is hard to derive in this new sites. There could be a mix of standard, student and acedemic products
        //Until I have WaveMetric's feedback I will make this the cat that had the MOST and do so in a way that defaults ties to this order
        //standard, acedemic, student.
            //see which category has the most.
            $catCountMax = max($standardOrderItems,$studentOrderItems,$academicOrderItems,$resellerOrderItems);
            //Since there could be ties and there are only three cats to anazlye we will find the winner this way.
            //use std if it is the max or tied for the max
            if($standardOrderItems==$catCountMax) {
                $this->wavemetrics_order['ordertype'] = "Standard";
            }
            //use academic if it is the max or tied for the max (but not with standard)
            elseif($academicOrderItems==$catCountMax) {
                $this->wavemetrics_order['ordertype'] = "Academic";
            }
            //use student if it is the max or tied (but not with standard or academic)
            elseif($studentOrderItems==$catCountMax) {
                $this->wavemetrics_order['ordertype'] = "Student";
            }
            //use reseller if it is the max
            elseif($resellerOrderItems==$catCountMax) {
                $this->wavemetrics_order['ordertype'] = "Reseller";
            }
            //default to standard
            else {
                $this->wavemetrics_order['ordertype'] = "Standard";
            }


        //comment
        //if the comment variable is set use it (This is means it was an override at someepoint in this function).
        //If it is not and the field on the order is set, use it.
        if(isset( $comment) &&  $comment!="") {
            $this->wavemetrics_order['comment'] = $comment;
            if($order->hasField('field_order_comment')) {
                $this->wavemetrics_order['comment'] .= $order->get('field_order_comment')->getString();
            }
        }
        elseif($order->hasField('field_order_comment')) {
            $this->wavemetrics_order['comment'] = $order->get('field_order_comment')->getString();
        }
        else {
            $this->wavemetrics_order['comment'] = "";
        }

        //implode all serials into a csv for the new serials table.
        if(count($serials)>0) {
            $this->wavemetrics_order['serials'] = implode(",",$serials);
        }

        //implode all activation keys into a csv for the new activation key table.
        if(count($activation_keys)>0) {
            $this->wavemetrics_order['activation'] = implode(",",$activation_keys);
        }

        return $this->wavemetrics_order;
    }

    /**
     * Save the WaveMetrics Order Info to the WaveMetrics Order Management Database Table.
     *
     * @return The last insert ID of the query, if one exists. If no
     *   fields are specified, this method will do nothing and return NULL.
     */

    public function saveOrder(array $wavemetrics_order) {
        $results = $this->wavemetrics_order_db->add($wavemetrics_order["orderid"], $wavemetrics_order["email"], $wavemetrics_order["name"], $wavemetrics_order["amountcents"], $wavemetrics_order["address1"], $wavemetrics_order["city"], $wavemetrics_order["state"], $wavemetrics_order["zip"], $wavemetrics_order["country"], $wavemetrics_order["phone"], $wavemetrics_order["ordertime"], $wavemetrics_order["stripeid"], $wavemetrics_order["orderitems"], $wavemetrics_order["comment"], $wavemetrics_order["ordertype"], $wavemetrics_order["confirmation"], $wavemetrics_order["institution"], $wavemetrics_order["department"], $wavemetrics_order["degree"], $wavemetrics_order["urlhelper"], $wavemetrics_order["request"], $wavemetrics_order["course"], $wavemetrics_order["serials"], $wavemetrics_order["source"],$wavemetrics_order["position"],$wavemetrics_order["activation"]);
        return $results;
    }


}
