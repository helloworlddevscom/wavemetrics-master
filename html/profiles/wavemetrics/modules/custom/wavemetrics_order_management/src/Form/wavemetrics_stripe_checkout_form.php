<?php

namespace Drupal\wavemetrics_order_management\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class wavemetrics_stripe_checkout_form extends FormBase {

  public function getFormId() {
    return 'payment-form';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['ordersummary'] = [
      '#type' => 'markup',
      '#markup' => "<div id=\"stripe-wm-order-summary\"><!--JS injects the Order Summary--></div>",
    ];

    $form['paymentform'] = [
      '#type' => 'markup',
      '#markup' => "<div id=\"card-element\"><!--Stripe.js injects the Card Element--></div>",
    ];

    $form_button_html = "";
    $form['form_button_html'] = [
      '#type' => 'button',
      '#value' => $form_button_html,
      '#attributes' => [
        'id' => 'submit'
      ]
    ];

    $form_footer_html = "
      <p id=\"card-error\" role=\"alert\"></p>
      <p class=\"result-message hidden\">
        Payment succeeded.
      </p>
    ";
    $form['form_footer_html'] = [
      '#type' => 'markup',
      '#markup' => $form_footer_html,
    ];

    return $form;

  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // This is a required method but this form will be 'hijacked' by stripe
    // A submit handler is not needed.

  }
}
