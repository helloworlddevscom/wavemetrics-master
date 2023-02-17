<?php

namespace Drupal\wavemetrics_order_management\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\webformSubmissionInterface;
use Drupal\wavemetrics_order_management\Controller\wavemetrics_order_managementController;
use Drupal\Core\Url;
use Drupal\Core\Routing\TrustedRedirectResponse;


/**
 * Form submission handler.
 *
 * @WebformHandler(
 *   id = "WMDigitalRiverHandler_form_handler",
 *   label = @Translation("WM Digital River"),
 *   category = @Translation("Form Handler"),
 *   description = @Translation("Record Order Data and Send the Customer to a Digital River Product"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 * )
 */
class WMDigitalRiverHandler extends WebformHandlerBase
{

    private $dr_base_url = "https://order.shareit.com/cart/new";
    private $dr_vendorID = "200276890";

    private $products = array (
        'igor' => array(
            'standard' => array(
                'full' => array(
                    'price' => 995,
                    'sku' => "16-500",
                    'dr_pid' => "300848707",
                    'name' => "Igor Pro",
                ),
                'upgrade' => array(
                    'price' => 225,
                    'sku' => "16-525",
                    'name' => "Igor Pro Upgrade",
                ),
            ),
            'academic' => array(
                'full' => array(
                    'price' => 995 / 2,
                    'sku' => "16-500",
                    'name' => "Igor Pro",
                ),
                'upgrade' => array(
                    'price' => 175,
                    'sku' => "16-525",
                    'name' => "Igor Pro Upgrade",
                ),
            ),
            'student' => array(
                'full' => array(
                    'price' => 75,
                    'sku' => "16-500",
                    'name' => "Igor Pro",
                ),
            ),
            'coursework' => array(
                'full' => array(
                    'price' => 125,
                    'sku' => "16-625",
                    'name' => "Igor Pro",
                ),
            ),
        ),
        'nidaq' => array(
            'standard' => array(
                'full' => array(
                    'price' => 225,
                    'sku' => "11-375",
                    'dr_pid' => "300848709",
                    'name' => "NIDAQ Tools MX",
                ),
                'upgrade' => array(
                    'price' => 0,
                    'sku' => "11-425",
                    'name' => "NIDAQ Tools MX Upgrade",
                ),
            ),
            'academic' => array(
                'full' => array(
                    'price' => 225,
                    'sku' => "11-375",
                    'dr_pid' => "300848709",
                    'name' => "NIDAQ Tools MX",
                ),
                'upgrade' => array(
                    'price' => 0,
                    'sku' => "11-425",
                    'name' => "NIDAQ Tools MX Upgrade",
                ),
            ),
            'student' => array(
                'full' => array(
                    'price' => 225,
                    'sku' => "11-375",
                    'dr_pid' => "300848709",
                    'name' => "NIDAQ Tools MX",
                ),
                'upgrade' => array(
                    'price' => 0,
                    'sku' => "11-425",
                    'name' => "NIDAQ Tools MX Upgrade",
                ),
            ),
        ),
        'xop' => array(
            'standard' => array(
                'full' => array(
                    'price' => 100,
                    'sku' => "16-900",
                    'dr_pid' => "300848710",
                    'name' => "XOP Toolkit",
                ),
                'upgrade' => array(
                    'price' => 0,
                    'sku' => "16-925",
                    'name' => "XOP Toolkit Upgrade",
                ),
            ),
            'academic' => array(
                'full' => array(
                    'price' => 100,
                    'dr_pid' => "300848710",
                    'sku' => "16-900",
                    'name' => "XOP Toolkit",
                ),
                'upgrade' => array(
                    'price' => 0,
                    'sku' => "16-925",
                    'name' => "XOP Toolkit Upgrade",
                ),
            ),
            'student' => array(
                'full' => array(
                    'price' => 100,
                    'dr_pid' => "300848710",
                    'sku' => "16-900",
                    'name' => "XOP Toolkit",
                ),
                'upgrade' => array(
                    'price' => 0,
                    'sku' => "16-925",
                    'name' => "XOP Toolkit Upgrade",
                ),
            ),
        ),
        'igor_oneyear' => array(
            'standard' => array(
                'full' => array(
                    'price' => 350,
                    'sku' => "16-510",
                    'dr_pid' => "300851584",
                    'name' => "Igor Pro One Year License",
                ),
            ),
            'academic' => array(
                'full' => array(
                    'price' => 350 / 2,
                    'sku' => "16-510",
                    'name' => "Igor Pro One Year License",
                ),
            ),
        ),
        'igor_multiuser' => array(
            'standard' => array(
                'full' => array(
                    'price' => 880, #quantity discounts applied.
                    'sku' => "16-500",
                    'dr_pid' => "300848711",
                    'name' => "Igor Pro Multiuser",
                ),
            ),
            'academic' => array(
                'full' => array(
                    'price' => 880 / 2, #quantity discounts applied.
                    'sku' => "16-500",
                    'name' => "Igor Pro Multiuser",
                ),
            ),
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

    public function defaultConfiguration()
    {
        return [
            'wm_dr_order_type' => 'standard',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function buildConfigurationForm(array $form, FormStateInterface $form_state)
    {
        $form['wm_dr_order_type'] = [
            '#type' => 'select',
            '#title' => $this->t('Order Type'),
            '#description' => $this->t('The Order Type of these products'),
            '#default_value' => $this->configuration['wm_dr_order_type'],
            '#required' => TRUE,
            '#options' => array(
                "standard"=>"Standard",
                "academic"=>"Academic",
                "student"=>"Student",
                "coursework"=>"Coursework",
            ),
        ];
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
    {
        parent::submitConfigurationForm($form, $form_state);
        $results = $this->applyFormStateToConfiguration($form_state);
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
        $licenseType = $this->configuration['wm_dr_order_type'];

        //Set some fields
        $email = !empty($values['order_email']) ? $values['order_email'] : "";
        $name = !empty($values['order_name']) ? $values['order_name']['first'] . " " . $values['order_name']['last'] : "";

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
        $sn = !empty($values['order_upgrade_serial']) ? str_replace(" ","",$values['order_upgrade_serial']) : "";
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

        //keep track of all the DR products to add to the URL
        $dr_url_products = array();
        $all_digital_river = true;

        //cycle through the submitted products and add them to the orderitems and possibly the digital river cart url
        $product_field_prefix = "product_quantity_";
        $product_upgrade_field_suffix = "_upgrade";
        foreach($values as $field_name => $field_value){
            //only if the field starts with product_
            if(strpos($field_name,$product_field_prefix)!==false && is_numeric($field_value) && $field_value>0){
                //See if this is an upgrade
                //remove the prefix
                $product_name = substr($field_name,strlen($product_field_prefix));
                if(substr($field_name,-(strlen($product_upgrade_field_suffix)))==$product_upgrade_field_suffix){
                    $full_or_upgrade = 'upgrade';
                    //remove the suffix
                    $product_name = substr($product_name,0,-(strlen($product_upgrade_field_suffix)));
                    //add the serial
                    $sn_for_db = $sn;
                }else{
                    $full_or_upgrade = 'full';
                    //remove the serial (its for the upgrades)
                    $sn_for_db = "";
                }
                $quan = $field_value;
                $productName = $this->products[$product_name][$licenseType][$full_or_upgrade]['name'];
                //sku changes by Upgrade
                $partNum = $this->products[$product_name][$licenseType][$full_or_upgrade]['sku'];
                $productPrice = $this->products[$product_name][$licenseType][$full_or_upgrade]['price'];
                //if this product is the multiuser product the price is quantity discounted
                if($product_name == "igor_multiuser"){
                    $productPrice = $this->getMultiUserPrice($quan,$licenseType=='academic' ? true : false );
                }
                $tot = $productPrice * $quan;
                $totalCalc += $tot;
                $orderItems .= str_pad($quan, 8, ' ') . str_pad($productName, 25, ' ') . str_pad($partNum, 20, ' ') . str_pad(number_format($tot, 2,'.',''), 17, ' ') . $sn_for_db . "\n";
                //if this is a Digital River Purchasable product, append the DR URL.
                if (!empty($this->products[$product_name][$licenseType][$full_or_upgrade]['dr_pid'])) {
                    $dr_url_products[] = "PRODUCT[" . $this->products[$product_name][$licenseType][$full_or_upgrade]['dr_pid'] . "]=" . $quan;
                }
                else{
                    $all_digital_river = false;
                }
            }
        }
        //calculate the total
        $amountcents = $totalCalc * 100;

        //if this is going to DR, set this field to 2
        if($all_digital_river) {
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
            'source' => 'webforms_digitalriver'
        );

        //load the WaveMetrics Order Management
        $order_manager = new wavemetrics_order_managementController;
        //Save this form submission as an order.
        $order_manager->saveOrder($data);

        //generate the Digital River URL
        //only for digital river products
        $dr_url = false;
        if($all_digital_river){
            $queryString = implode("&", $dr_url_products);
            $dr_url = $this->dr_base_url . "?vendorid=" . $this->dr_vendorID . "&" . $queryString;
        }
        $form_state->set('dr_url', $dr_url);
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function confirmForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission)
    {
        //if there is a digital river URL
        $url = $form_state->get('dr_url');
        if($url){
            //should just need to set the Redirect or Response, not both. but I had a mix of results during AJAX so I set both. 
            $response = new TrustedRedirectResponse($url);
            $metadata = $response->getCacheableMetadata();
            $metadata->setCacheMaxAge(0);
            $form_state->setResponse($response);
            $url = Url::fromUri($url);
            $form_state->setRedirectUrl($url);
        }
        return;
    }
}
?>
