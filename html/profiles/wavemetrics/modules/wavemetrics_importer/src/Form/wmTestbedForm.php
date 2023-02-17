<?php
/**
 * @file
 * Contains \Drupal\wavemetrics_importer\Form\wmImportRunImport
 */

namespace Drupal\wavemetrics_importer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\wavemetrics_importer\Controller\wavemetrics_importerController;

class wmTestbedForm extends FormBase
{

    protected $importer;

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'wm_testbed';
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
        drupal_set_message("Testbed Completed");
    }
}
