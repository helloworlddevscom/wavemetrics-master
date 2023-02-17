<?php

namespace Drupal\price_customizations;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_order\OrderProcessorInterface;
use Drupal\commerce_price\Price;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_order\Adjustment;

/**
 * Provides an order processor that modifies the cart according to the business logic.
 */
class CustomOrderProcessor implements OrderProcessorInterface {
    /**
    * {@inheritdoc}
    */
    public function process(OrderInterface $order) {
        
        foreach ($order->getItems() as $order_item) {
            
            // SetAdjustment to empty initially.
            $order_item->setAdjustments([]);
            $product_variation = $order_item->getPurchasedEntity();
            
            //Check there are items
            if (!empty($product_variation)) {
                
                $product_id = $product_variation->get('product_id')->getValue()[0]['target_id'];
                $product = Product::load($product_id);
                $quantity = $order_item->getQuantity();
                $product_title = $product->getTitle();
                $product_price = $order_item->getUnitPrice();
                $product_unit_price = round(intval($product_price->getNumber()), 2);
                
                //Choose when to run
                if ($quantity > 2 && $product_unit_price > 1) {
                    //Loop through number of items and reduce price each time
                    for ($i = 0; $i < $quantity; $i++) {
                        $new_adjustment = 10;
                    }
                                        
                    $adjustments = $order_item->getAdjustments();
                    
                    // Apply custom adjustment.
                    $adjustments[] = new Adjustment([
                        'type' => 'custom_price_adjustment',
                        'label' => $product_title . ' - Multiple License Discount',
                        'amount' => new Price('-' . $new_adjustment, 'USD'),
                    ]);
                    $order_item->setAdjustments($adjustments);
                    //  $order_item->save();
                    $adjustments;
                }                
            }
        }
    }
}
