<?php

namespace Drupal\commerce_choose_price\Plugin\Field\FieldWidget;

use Drupal\commerce\Context;
use Drupal\commerce_price\Price;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_product\Entity\ProductVariationInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'interflora_unit_price' widget.
 *
 * @FieldWidget(
 *   id = "commerce_choose_price",
 *   label = @Translation("Choose price"),
 *   field_types = {
 *     "commerce_price",
 *   }
 * )
 */
class UnitPriceWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'description' => '',
        'display' => 0,
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    $element['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#description' => $this->t('Shown to the customer below the price element'),
      '#default_value' => $this->getSetting('description'),
    ];

    $element['display'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display default'),
      '#description' => $this->t('Display choose your own price by default'),
      '#default_value' => $this->getSetting('display'),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = [];
    $orderItem = $items->getEntity();
    $purchasable_entity = $orderItem->getPurchasedEntity();
    $product = $purchasable_entity->getProduct();
    if (!($product instanceof Product) || ($product->hasField('allow_override_price') && $product->get('allow_override_price')->value == 0)) {
      return $element;
    }
    $store = \Drupal::service('commerce_store.current_store')->getStore();
    $context = new Context(\Drupal::currentUser(), $store);
    $price = \Drupal::service('commerce_price.chain_price_resolver')
      ->resolve($purchasable_entity, 1, $context);

    // The JS that shows the form when the link is clicked should also set
    // the hidden override value to 1.
    $element['override'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Provide your own price'),
      '#default_value' => $this->getSetting('display'),
    ];
    $element['amount'] = [
      '#type' => 'commerce_price',
      '#title' => $this->t('Choose your own price'),
      '#default_value' => $price->toArray(),
      '#element_validate' => [
        [get_class($this), 'validatePrice'],
      ],
      '#states' => [
        'invisible' => [
          ':input[name="unit_price[0][override]"]' => [
            'checked' => FALSE,
          ],
        ],
      ],
    ];
    $element['description'] = [
      '#type' => 'item',
      '#markup' => $this->getSetting('description'),
      '#states' => [
        'invisible' => [
          ':input[name="unit_price[0][override]"]' => [
            'checked' => FALSE,
          ],
        ],
      ],
    ];

    return $element;
  }

  /**
   * Validates the selected price.
   *
   * @param array $element
   *   The element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public static function validatePrice(array $element, FormStateInterface $form_state) {
    /** @var \Drupal\commerce_product\Entity\ProductInterface $product */
    $product = $form_state->get('product');
    $variations = $product->getVariations();
    if (count($variations) === 1) {
      $variation = reset($variations);
    }
    else {
      // Sort the variations by price.
      uasort($variations, function (ProductVariationInterface $prodA, ProductVariationInterface $prodB) {
        $a_price = $prodA->getPrice();
        $b_price = $prodB->getPrice();

        if ($a_price->equals($b_price)) {
          return 0;
        }
        return $a_price->lessThan($b_price) ? -1 : 1;
      });
      // Remove the cheapest variation. The first one is now the medium one.
      array_shift($variations);
      $variation = reset($variations);
    }

    $value = $form_state->getValue($element['#parents']);
    if ($value['number'] < $variation->getPrice()->getNumber()) {
      $form_state->setError($element, t('The chosen price is too low'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function extractFormValues(FieldItemListInterface $items, array $form, FormStateInterface $form_state) {
    $field_name = $this->fieldDefinition->getName();
    $path = array_merge($form['#parents'], [$field_name, 0]);
    $values = NestedArray::getValue($form_state->getValues(), $path);
    if ($values && $values['override'] && is_numeric($values['amount']['number'])) {
      /** @var \Drupal\commerce_order\Entity\OrderItemInterface $order_item */
      $order_item = $items[0]->getEntity();
      $unit_price = new Price($values['amount']['number'], $values['amount']['currency_code']);
      $order_item->setUnitPrice($unit_price, TRUE);
      // Put delta mapping in $form_state, so that flagErrors() can use it.
      $field_state = static::getWidgetState($form['#parents'], $field_name, $form_state);
      foreach ($items as $delta => $item) {
        $field_state['original_deltas'][$delta] = $delta;
      }
      static::setWidgetState($form['#parents'], $field_name, $form_state, $field_state);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    $entity_type = $field_definition->getTargetEntityTypeId();
    $field_name = $field_definition->getName();
    return $entity_type == 'commerce_order_item' && $field_name == 'unit_price';
  }

}
