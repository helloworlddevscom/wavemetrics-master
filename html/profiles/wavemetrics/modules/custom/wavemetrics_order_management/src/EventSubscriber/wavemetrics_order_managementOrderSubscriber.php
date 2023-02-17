<?php

/**
 * @file
 * Contains \Drupal\wavemetrics_order_management\EventSubscriber\wavemetrics_order_managementOrderSubscriber.
 */

namespace Drupal\wavemetrics_order_management\EventSubscriber;

use Drupal\wavemetrics_order_management\Controller\wavemetrics_order_managementController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\state_machine\Event\WorkflowTransitionEvent;

/**
 * Event Subscriber MyEventSubscriber.
 */
class wavemetrics_order_managementOrderSubscriber implements EventSubscriberInterface {

    /**
     * Code that should be triggered when an order is placed.
     * This is the order state 'validation' according to our chosen workflow.
     */
    public function onCompletedOrders(WorkflowTransitionEvent $event) {
        if ($event->getToState()->getId() == 'validation') {
            $order = $event->getEntity();
            $order_manager = new wavemetrics_order_managementController;
            $wavemetrics_order = $order_manager->processOrder($order);
            $results = $order_manager->saveOrder($wavemetrics_order);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() {
        // Trigger after an order is made
        $events['commerce_order.place.post_transition'][] = ['onCompletedOrders'];
        return $events;
    }

}