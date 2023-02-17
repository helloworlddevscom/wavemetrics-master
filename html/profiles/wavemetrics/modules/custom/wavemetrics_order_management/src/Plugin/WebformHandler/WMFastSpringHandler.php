<?php

namespace Drupal\wavemetrics_order_management\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\webformSubmissionInterface;
use Drupal\wavemetrics_order_management\Controller\wavemetrics_order_managementController;
use Drupal\Core\Url;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\user\PrivateTempStoreFactory;


/**
 * Form submission handler.
 *
 * @WebformHandler(
 *   id = "WMFastSpringHandler_form_handler",
 *   label = @Translation("WM Fast Spring"),
 *   category = @Translation("Form Handler"),
 *   description = @Translation("Record Order Data and Send the Customer to a Fast Spring for payment"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 * )
 */
class WMFastSpringHandler extends WebformHandlerBase
{

  private $products = array (
    'igor-su-std' => array(
      'price' => 1095,
      'fsid' => 'igor-su-std',
      'drid' => '300848707',
      'name' => 'Igor Single User Standard',
      'sku' => 'igor-su-std',
      'academic_pricing' => false,
      'multiuser_pricing' => false,
      'serials_expected' => false,
    ),
    'igor-su-std-upgrade' => array(
      'price' => 245,
      'name' => 'Igor Single User Standard Upgrade',
      'sku' => 'igor-su-std-upgrade',
      'academic_pricing' => false,
      'multiuser_pricing' => false,
      'serials_expected' => true,
    ),

    'igor-su-std-oldupgrade' => array(
      'price' => 345,
      'name' => 'Igor Single User Standard Old Upgrade',
      'sku' => 'igor-su-std-oldupgrade',
      'academic_pricing' => false,
      'multiuser_pricing' => false,
      'serials_expected' => true,
    ),
    'igor-su-std-expiring' => array(
      'price' => 350,
      'name' => 'Igor Single User Standard Expiring',
      'sku' => 'igor-su-std-expiring',
      'fsid' => 'igor-su-std-expiring',
      'drid' => '300851584',
      'academic_pricing' => false,
      'multiuser_pricing' => false,
      'serials_expected' => false,
    ),
    'igor-mu-std' => array(
      'price' => 880,
      'name' => 'Igor MultiUser Standard',
      'sku' => 'igor-mu-std',
      'fsid' => 'igor-mu-std',
      'drid' => '300848711',
      'academic_pricing' => false,
      'multiuser_pricing' => true,
      'serials_expected' => false,
    ),
    'igor-mu-std-add10' => array(
      'price' => 0,
      'name' => 'Igor MultiUser Standard Add 10',
      'sku' => 'igor-mu-std-add10',
      'academic_pricing' => false,
      'multiuser_pricing' => true,
      'serials_expected' => false,
    ),
    'igor-mu-std-add20' => array(
      'price' => 0,
      'name' => 'Igor MultiUser Standard Add 20',
      'sku' => 'igor-mu-std-add20',
      'academic_pricing' => false,
      'multiuser_pricing' => true,
      'serials_expected' => false,
    ),
    'igor-mu-std-upgrade' => array(
      'price' => 0,
      'name' => 'Igor MultiUser Standard Upgrade',
      'sku' => 'igor-mu-std-upgrade',
      'academic_pricing' => false,
      'multiuser_pricing' => true,
      'serials_expected' => true,
    ),
    'igor-su-acad' => array(
      'price' => 695,
      'name' => 'Igor Single User Academic',
      'sku' => 'igor-su-acad',
      'academic_pricing' => true,
      'multiuser_pricing' => false,
      'serials_expected' => false,
    ),
    'igor-su-acad-upgrade' => array(
      'price' => 195,
      'name' => 'Igor Single User Academic Upgrade',
      'sku' => 'igor-su-acad-upgrade',
      'academic_pricing' => true,
      'multiuser_pricing' => false,
      'serials_expected' => true,
    ),
    'igor-su-acad-oldupgrade' => array(
      'price' => 295,
      'name' => 'Igor Single User Academic Old Upgrade',
      'sku' => 'igor-su-acad-oldupgrade',
      'academic_pricing' => true,
      'multiuser_pricing' => false,
      'serials_expected' => true,
    ),
    'igor-su-acad-expiring' => array(
      'price' => 225,
      'name' => 'Igor Single User Academic Expiring',
      'sku' => 'igor-su-acad-expiring',
      'academic_pricing' => true,
      'multiuser_pricing' => false,
      'serials_expected' => false,
    ),
    'igor-mu-acad' => array(
      'price' => 440,
      'name' => 'Igor MultiUser Academic',
      'sku' => 'igor-mu-acad',
      'academic_pricing' => true,
      'multiuser_pricing' => true,
      'serials_expected' => false,
    ),
    'igor-mu-acad-add10' => array(
      'price' => 0,
      'name' => 'Igor MultiUser Academic Add 10',
      'sku' => 'igor-mu-acad-add10',
      'academic_pricing' => true,
      'multiuser_pricing' => true,
      'serials_expected' => false,
    ),
    'igor-mu-acad-add20' => array(
      'price' => 0,
      'name' => 'Igor MultiUser Academic Add 20',
      'sku' => 'igor-mu-acad-add20',
      'academic_pricing' => true,
      'multiuser_pricing' => true,
      'serials_expected' => false,
    ),
    'igor-mu-acad-upgrade' => array(
      'price' => 0,
      'name' => 'Igor MultiUser Academic Upgrade',
      'sku' => 'igor-mu-acad-upgrade',
      'academic_pricing' => true,
      'multiuser_pricing' => true,
      'serials_expected' => true,
    ),
    'igor-coursework' => array(
      'price' => 125,
      'name' => 'Igor Coursework',
      'sku' => 'igor-coursework',
      'academic_pricing' => true,
      'multiuser_pricing' => false,
      'serials_expected' => false,
    ),
    'igor-student' => array(
      'price' => 75,
      'name' => 'Igor Student',
      'sku' => 'igor-student',
      'academic_pricing' => false,
      'multiuser_pricing' => false,
      'serials_expected' => false,
    ),
    'xop-toolkit' => array(
      'price' => 100,
      'name' => 'XOP Toolkit',
      'sku' => 'xop-toolkit',
      'fsid' => 'xop-toolkit',
      'drid' => '300848710',
      'academic_pricing' => false,
      'multiuser_pricing' => false,
      'serials_expected' => false,
    ),
    'xop-toolkit-upgrade' => array(
      'price' => 0,
      'name' => 'XOP Toolkit Upgrade',
      'sku' => 'xop-toolkit-upgrade',
      'academic_pricing' => false,
      'multiuser_pricing' => false,
      'serials_expected' => true,
    ),
    'nidaq-tools-mx' => array(
      'price' => 225,
      'name' => 'Nidaq Tools MX',
      'sku' => 'nidaq-tools-mx',
      'fsid' => 'nidaq-tools-mx',
      'drid' => '300848709',
      'academic_pricing' => false,
      'multiuser_pricing' => false,
      'serials_expected' => false,
    ),
    'nidaq-tools-mx-upgrade' => array(
      'price' => 0,
      'name' => 'Nidaq Tools MX Upgrade',
      'sku' => 'nidaq-tools-mx-upgrade',
      'academic_pricing' => false,
      'multiuser_pricing' => false,
      'serials_expected' => true,
    ),
  );

    private function getMultiUserPrice($quan,$academic = false) {
        if($quan<3){
            return false;
        }
        elseif($quan<11){
            $value = 880;
        }
        elseif($quan<21){
            $value = 785;
        }
        elseif($quan<31){
            $value = 725;
        }
        elseif($quan<41){
            $value = 685;
        }
        elseif($quan<61){
            $value = 655;
        }
        elseif($quan<81){
            $value = 625;
        }
        elseif($quan>=81){
            $value = 605;
        }

        if($academic==true){
            $value = $value/2;
        }
        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission)
    {

        //submitted values
        $values = $webform_submission->getData();

        $webform_submission->setSticky(!$webform_submission->isSticky())->save();
        $sid = $webform_submission->id();

        //get some fields from Config.
        $licenseType = $this->configuration['wm_fs_order_type'];

        //Set some fields
        $email = !empty($values['order_email']) ? $values['order_email'] : "";
        $name = !empty($values['order_name']) ? $values['order_name']['first'] . " " . $values['order_name']['last'] : "";
        $fname = !empty($values['order_name']) ? $values['order_name']['first'] : "";
        $lname = !empty($values['order_name']) ? $values['order_name']['last'] : "";

        //if the complex address field was used.
        if(!empty($values['order_address']['address']) || !empty($values['order_address']['city']) || !empty($values['order_address']['state_province'])|| !empty($values['order_address']['postal_code']) || !empty($values['order_address']['country']) )
        {
            $address = !empty($values['order_address']['address']) ? $values['order_address']['address'] : "";
            $city = !empty($values['order_address']['city']) ? $values['order_address']['city'] : "";
            $state = !empty($values['order_address']['state_province']) ? $values['order_address']['state_province'] : "";
            $zip = !empty($values['order_address']['postal_code']) ? $values['order_address']['postal_code'] : "";
            $country = !empty($values['order_address']['country']) ? $values['order_address']['country'] : "";
        }
        else {
            $address = !empty($values['order_address']) ? $values['order_address'] : "";
            $city =  "";
            $state = "";
            $zip =  "";
            $country = "";
        }
        $phone = !empty($values['order_phone']) ? $values['order_phone'] : "";
        $comment = !empty($values['order_comment']) ? $values['order_comment'] : "";
        $sn = !empty($values['order_upgrade_serial']) ? "[".str_replace(" ","",$values['order_upgrade_serial'])."]" : "";
        $activation_code = !empty($values['order_activation_code']) ? $values['order_activation_code'] : "";
        $position = !empty($values['order_position']) ? $values['order_position'] : "";
        $course = !empty($values['order_course']) ? $values['order_course'] : "";
        $institution = !empty($values['order_institution']) ? $values['order_institution'] : "";
        $department = !empty($values['order_department']) ? $values['order_department'] : "";
        $degree = !empty($values['order_degree']) ? $values['order_degree'] : "";
        $urlhelper = !empty($values['order_urlhelper']) ? $values['order_urlhelper'] : "";

        //generate the order items string
        //The WaveMetrics OrderItems is a text field with a specific string inside. This code is based on their existing code.
        $orderItems = "";
        $totalCalc = 0;

        //keep track of all the FS products to add to the URL
        $fs_data_products = array();
        $all_fast_spring = true;

        //cycle through the submitted products and add them to the orderitems and possibly the fast spring cart url
        $product_field_prefix = "product_quantity_";
        foreach($values as $field_name => $field_value){
            //only if the field starts with product_
            if(strpos($field_name,$product_field_prefix)!==false && is_numeric($field_value) && $field_value>0){
                //Get the product info, remove the prefix, covert underscores to hyphens
                $product_name = str_replace("_","-",substr($field_name,strlen($product_field_prefix)));
                $quan = $field_value;
                $partNum = $this->products[$product_name]['sku'];
                $productPrice = $this->products[$product_name]['price'];
                //if this product is the multiuser product the price is quantity discounted
                if($this->products[$product_name]['multiuser_pricing']){
                    $productPrice = $this->getMultiUserPrice($quan,$this->products[$product_name]['academic_pricing']);
                }
                $tot = $productPrice * $quan;
                $totalCalc += $tot;
                //only add the submitted serial numbers to the products that require them (upgrades).
                if($this->products[$product_name]['serials_expected']){
                    //prepend with the single space delimiters since this is the last and optional field.
                    $sn_for_db = " ".$sn;
                }else{
                    $sn_for_db = "";
                }
                //add a new entry into the orderitems field.
                $orderItems .= $quan . " " . $partNum . $sn_for_db . ";";
                //if this is a Fast Spring Purchasable product, append the FS Data.
                if (!empty($this->products[$product_name]['fsid'])) {
                    $fsid = $this->products[$product_name]['fsid'];
                    $fs_data_products[$fsid]['path'] = $fsid;
                    $fs_data_products[$fsid]['quantity'] = empty($fs_data_products[$fsid]['quantity']) ? $quan : $fs_data_products[$fsid]['quantity'] + $quan;
                }
                else{
                    $all_fast_spring = false;
                }
            }
        }
        //calculate the total
        $amountcents = $totalCalc * 100;

        //if this is going to FS, set this field to 2
        if($all_fast_spring) {
            $request = 2;
        }
        else {
            $request = 1;
        }

        //Data for the WaveMetrics Order Management
        $data = array(
            'id' => '',
            "orderid" => $sid,
            "email" => $email,
            "name" => $name,
            "amountcents" => $amountcents,
            "address1" => $address,
            "city" => $city,
            "state" => $state,
            "zip" => $zip,
            "country" => $country,
            "phone" => $phone,
            "ordertime" => date("Y-m-d G:i:s", $webform_submission->getCreatedTime()),
            "handled" => false,
            "wmdborderid" => '',
            "stripecustomerid" => "",
            "orderitems" => $orderItems,
            "comment" => $comment,
            "ordertype" => ucfirst($licenseType),
            "confirmation" => $sid,
            'institution' => $institution,
            'department' => $department,
            'degree' => $degree,
            'urlhelper' => $urlhelper,
            'request' => $request,
            'course' => $course,
            'serials' => $sn,
            'position' => $position,
            'activation' => $activation_code,
            'source' => 'webforms_fastspring'
        );

        //load the WaveMetrics Order Management
        $order_manager = new wavemetrics_order_managementController;
        //Save this form submission as an order.
        $order_manager->saveOrder($data);

        //generate the Fast Spring Data
        //only for fast spring products
        if($all_fast_spring) {
            $fs_data = array(
                'reset' => true,
                'coupon' => '',
                'paymentContact' => array(
                    'email' => $email,
                    'firstName' => $fname,
                    'lastName' => $lname,
                    'company' => $institution,
                    'addressLine1' => $address,
                    'city' => $city,
                    'region' => $state,
                    'country' => $country,
                    'postalCode' => $zip
                ),
            );
            //do this in a loop. This array as keys that match the path. This was so wavemetrics.com products that share the same FS path would have their quantities combined. But FS JS wants those keys to not be associative.
            foreach($fs_data_products as $fs_data_product){
                $fs_data['products'][] = $fs_data_product;
            }
        }
        else {
            $fs_data = false;
        }
        $form_state->set('fs_data', $fs_data);
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function confirmForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission)
    {
        //if there is Fast Spring Data
       $fs_data = $form_state->get('fs_data');
       if($fs_data){
           //set the data to a session var to be later retrieved
           $session = \Drupal::service('user.private_tempstore')->get('WMFastSpringHandler');
           $session->set('orderData', $fs_data);

           //Redirect to a "Checkout Page"
           $host = \Drupal::request()->getSchemeAndHttpHost();
           $url = $host."/order/checkout/fastspring";

           $response = new TrustedRedirectResponse($url);
           $metadata = $response->getCacheableMetadata();
           $metadata->setCacheMaxAge(0);
           $form_state->setResponse($response);
           $url = Url::fromUri($url);
           $form_state->setRedirectUrl($url);
       }
       //if this did not qualify for Fast Spring, then blank out this session data (a previous order that did qualify could still be in the session)
       else{
           $session = \Drupal::service('user.private_tempstore')->get('WMFastSpringHandler');
           $session->set('orderData', false);
       }



        return;

    }
}
?>
