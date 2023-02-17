<?php
/**
 * @file
 * Contains \Drupal\quote\Form\QuoteSettingsForm.
 */

namespace Drupal\quote\Form;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;

/**
 * Configure custom settings for the quote module.
 */
class QuoteSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'quote_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'quote.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('quote.settings');

    $form['quote'] = array(
      '#type' => 'fieldset',
      '#title' => t('Quote module settings'),
      '#tree' => TRUE
    );
    $form['quote']['node_types'] = array(
      '#type' => 'checkboxes',
      '#title' => t('Node associations'),
      '#description' => t('Select the node types to associate with the quote filter.'),
      '#options' => node_type_get_names(),
      '#default_value' => $config->get('node_types'),
    );
    $form['quote']['node_link_display'] = array(
      '#type' => 'checkbox',
      '#title' => t('Display the quote link for nodes'),
      '#description' => t('While the quote link is always displayed for comments, it can also be displayed for nodes.'),
      '#default_value' => $config->get('node_link_display'),
    );

    // List all formats regardless of permission issues.
    $formats = filter_formats();
    $options = array('0' => t('None'));
    foreach ($formats as $format) {
      $options[$format->id()] = $format->label();
    }

    $form['quote']['format'] = array(
      '#type' => 'select',
      '#title' => t('Text format'),
      '#description' => t('Select the text format that the quote should be filtered through prior to display in a text field.
      This is useful in situations where the raw quote might potentially contain sensitive content/code. It is recommended
      that a dedicated format be used for this purpose containing the appropriate filters.'),
      '#options' => $options,
      '#default_value' => $config->get('format'),
    );

    $form['quote']['nest'] = array(
      '#type' => 'select',
      '#title' => t('Level of nesting'),
      '#description' => t('Recursively nested quotes can lead to unsightly pages. This can be minimised by limiting the
      order of nesting. 0 will display all levels. The default and recommended value is 2.'),
      '#options' => range(0, 10),
      '#default_value' => $config->get('nest'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Run the node type checkboxes through array_filter to strip unselected
    // items.
    $form_state->setValueForElement($form['quote']['node_types'], array_filter($form_state->getValue(array('quote', 'node_types'))));
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('quote.settings')
      ->set('node_types', $form_state->getValue(array('quote', 'node_types')))
      ->set('node_link_display', $form_state->getValue(array('quote', 'node_link_display')))
      ->set('format', $form_state->getValue(array('quote', 'format')))
      ->set('nest', $form_state->getValue(array('quote', 'nest')))
      ->save();
     parent::submitForm($form, $form_state);
  }
}
