<?php
/**
 * @file
 * Contains \Drupal\wavemetrics_importer\Form\wmImportRunImport
 */

namespace Drupal\wavemetrics_order_management\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\wavemetrics_order_management\Controller\wavemetrics_order_managementController;

class testbedForm extends FormBase
{

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->data = new wavemetrics_order_managementController;
    }


    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'order_management_testbed';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Run The Testbed Func'),
            '#button_type' => 'primary',
        );
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $rows = $this->data->showOrders();
        drupal_set_message("Testbed Completed");
    }
}
