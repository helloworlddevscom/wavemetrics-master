<?php

namespace Drupal\wavemetrics_order_management\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\webformSubmissionInterface;
use Drupal\Core\Url;
use Drupal\Core\Routing\TrustedRedirectResponse;


/**
 * Form submission handler.
 *
 * @WebformHandler(
 *   id = "WMStripeCheckoutHandler_form_handler",
 *   label = @Translation("WM Stripe Checkkout"),
 *   category = @Translation("Form Handler"),
 *   description = @Translation("Record Order Data and Send the Customer to a Stripe Checkout for payment"),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_SINGLE,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 * )
 */
class WMStripeCheckoutHandler extends WebformHandlerBase
{

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission)
    {

        //submitted values
        $values = $webform_submission->getData();

        $webform_submission->setSticky(!$webform_submission->isSticky())->save();
        $sid = $webform_submission->id();

        $totalCalc = 0;

        //cycle through the submitted fields and find the order id and payment amount.
        foreach($values as $field_name => $field_value){
          if($field_name=="order_order_id"){
            //Get the product info, remove the prefix, covert underscores to hyphens
            $order_order_id = $field_value;
          }
          if($field_name=="product_price"){
            //Get the product info, remove the prefix, covert underscores to hyphens
            $totalCalc = $field_value;

          }
          if($field_name=="order_email") {
            if (!empty($field_value)) {
              $order_email = $field_value;
            }
            else {
              $order_email = "";
            }
          }
          if($field_name=="order_name") {
            if (!empty($field_value)) {
              $order_name = $field_value['first'] . " " . $field_value['last'];
            }
            else {
              $order_name = "";
            }
          }
        }

        //calculate the total
        $amountcents = $totalCalc * 100;

        //generate the Stripe Data
        $pk = \Drupal::service('key.repository')->getKey("stripe_webform_api_public")->getKeyValue();
        $stripe_data = array(
          'price' => $amountcents,
          'pk' => $pk,
          'order_name' => $order_name,
          'order_email' => $order_email
        );

        //also add the orderData to the the user's session. This can be used later to verify stripe callbacks.
        $tempstore = \Drupal::service('tempstore.private')->get('wavemetrics_order_management');
        $tempstore->set('order_id', $sid);
        $tempstore->set('quote_num', $order_order_id);

        $form_state->set('stripe_data', $stripe_data);

    }

    /**
     * {@inheritdoc}
     */
    public function confirmForm(array &$form, FormStateInterface $form_state, WebformSubmissionInterface $webform_submission)
    {
        //if there is Stripe Data
      $stripe_data = $form_state->get('stripe_data');
       if($stripe_data){

         //also add the orderData to the the user's session. This can be used later to verify stripe callbacks.
         $tempstore = \Drupal::service('tempstore.private')->get('wavemetrics_order_management');
         $tempstore->set('orderData', $stripe_data);

         //Redirect to a "Checkout Page"
           $host = \Drupal::request()->getSchemeAndHttpHost();
           $url = $host."/order/checkout/stripe";

           $response = new TrustedRedirectResponse($url);
           $metadata = $response->getCacheableMetadata();
           $metadata->setCacheMaxAge(0);
           $form_state->setResponse($response);
           $url = Url::fromUri($url);
           $form_state->setRedirectUrl($url);
       }
       //if this did not qualify for Fast Spring, then blank out this session data (a previous order that did qualify could still be in the session)
       else{
         $tempstore = \Drupal::service('tempstore.private')->get('wavemetrics_order_management');
         $tempstore->set('orderData', false);
       }
    }
}
