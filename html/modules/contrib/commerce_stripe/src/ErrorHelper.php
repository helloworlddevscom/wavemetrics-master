<?php

namespace Drupal\commerce_stripe;

use Drupal\commerce_payment\Exception\AuthenticationException;
use Drupal\commerce_payment\Exception\DeclineException;
use Drupal\commerce_payment\Exception\HardDeclineException;
use Drupal\commerce_payment\Exception\InvalidRequestException;
use Drupal\commerce_payment\Exception\InvalidResponseException;
//Additional Stripe Error Helping - Added by Outlier For WaveMetrics
use Drupal\wavemetrics_order_management\Controller\wavemetrics_order_managementErrorHelper;

/**
 * Translates Stripe exceptions and errors into Commerce exceptions.
 */
class ErrorHelper {

  /**
   * Translates Stripe exceptions into Commerce exceptions.
   *
   * @param \Stripe\Exception\ApiErrorException $exception
   *   The Stripe exception.
   *
   * @throws \Drupal\commerce_payment\Exception\PaymentGatewayException
   *   The Commerce exception.
   */
  public static function handleException(\Stripe\Exception\ApiErrorException $exception) {
    if ($exception instanceof \Stripe\Exception\CardException) {
      \Drupal::logger('commerce_stripe')->warning($exception->getMessage());
      if ($exception->getStripeCode() == 'card_declined' && $exception->getDeclineCode() == 'card_not_supported') {
        // Stripe only supports Visa/MasterCard/Amex for non-USD transactions.
        // @todo Find a better way to communicate this to the customer.
        $message = t('Your card is not supported. Please use a Visa, MasterCard, or American Express card.');
        \Drupal::messenger()->addWarning($message);
        throw new HardDeclineException($message);
      }
      else {

        //Additional Stripe Error Helping - Added by Outlier For WaveMetrics
        $message = ( !empty($exception->getMessage()) && $exception->getMessage()!==false ) ? $exception->getMessage() : "We encountered an error processing your card details. Please verify your details and try again.";
        $wm_errorHelper = new wavemetrics_order_managementErrorHelper;
        $wm_errorProcessing = $wm_errorHelper->ProcessErrorsFully($message);
        drupal_set_message($message, 'warning');

        throw new DeclineException('We encountered an error processing your card details. Please verify your details and try again.');
      }
    }
    elseif ($exception instanceof \Stripe\Exception\RateLimitException) {
      \Drupal::logger('commerce_stripe')->warning($exception->getMessage());
      throw new InvalidRequestException('Too many requests.');
    }
    elseif ($exception instanceof \Stripe\Exception\InvalidRequestException) {
      \Drupal::logger('commerce_stripe')->warning($exception->getMessage());
      throw new InvalidRequestException('Invalid parameters were supplied to Stripe\'s API.');
    }
    elseif ($exception instanceof \Stripe\Exception\AuthenticationException) {
      \Drupal::logger('commerce_stripe')->warning($exception->getMessage());
      throw new AuthenticationException('Stripe authentication failed.');
    }
    elseif ($exception instanceof \Stripe\Exception\ApiConnectionException) {
      \Drupal::logger('commerce_stripe')->warning($exception->getMessage());
      throw new InvalidResponseException('Network communication with Stripe failed.');
    }
    elseif ($exception instanceof \Stripe\Exception\ApiErrorException) {
      \Drupal::logger('commerce_stripe')->warning($exception->getMessage());
      throw new InvalidResponseException('There was an error with Stripe request.');
    }
    else {
      throw new InvalidResponseException($exception->getMessage());
    }
  }

  /**
   * Translates Stripe errors into Commerce exceptions.
   *
   * @todo
   *   Make sure if this is really needed or handleException cover all
   *   possible errors.
   *
   * @param object $result
   *   The Stripe result object.
   *
   * @throws \Drupal\commerce_payment\Exception\PaymentGatewayException
   *   The Commerce exception.
   */
  public static function handleErrors($result) {
    $result_data = $result->toArray();
    if ($result_data['status'] == 'succeeded') {
      return;
    }

    // @todo: Better handling for possible Stripe errors.
    if (!empty($result_data['failure_code'])) {
      $failure_code = $result_data['failure_code'];
      // https://stripe.com/docs/api?lang=php#errors
      // Validation errors can be due to a module error (mapped to
      // InvalidRequestException) or due to a user input error (mapped to
      // a HardDeclineException).
      $hard_decline_codes = ['processing_error', 'missing', 'card_declined'];
      if (in_array($failure_code, $hard_decline_codes)) {
        throw new HardDeclineException($result_data['failure_message'], $failure_code);
      }
      else {
        throw new InvalidRequestException($result_data['failure_message'], $failure_code);
      }
    }
  }

}
