<?php
/**
 * @file
 * Contains \Drupal\wavemetrics_importer\Form\wmImportRunImport
 */

namespace Drupal\wavemetrics_importer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\wavemetrics_importer\Controller\wavemetrics_importerController;

class wmImportRunImport extends FormBase
{

    protected $importer;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->importer = new wavemetrics_importerController;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'wm_import_admin_config';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state)
    {
        $form['import_type'] = array(
            '#type' => 'select',
            '#title' => ('Content Type'),
            '#options' => array(
                'fix_links_basic_pages' => t('-) Fix links in Basic Pages'),
                'users' => t('1) Users'),
                'taxonomy' => t('2) Taxonomy - Forum Topics, etc.'),
                'forums' => t('3) Forum Topics'),
                'forum_comments' => t('4) Forum Topic Comments'),
                'snippets' => t('5) Snippets'),
                'code_snippet_comments' => t('6) Snippets Comments'),
                'projects' => t('7) Projects'),
                'project_releases' => t('8) Project Releases'),
//                'project_comments' => t('9) N/A - Project Comments'),
                'basic_page' => t('10) Basic Pages'),
                'gallerypost' => t('11) Gallery Posts'),
                'casestudies' => t('12) Case Studies'),
                'news' => t('13) Blog and News from SquareSpace'),
                'menu_products' => t('14) Menu'),
                'project_versions' => t('15) Project Igor Version Info'),
                'fix_01_forum' => t('16) Fix Run 1 - Forums'),
                'fix_01_codesnippets' => t('17) Fix Run 1 - Code Snippets'),
                'fix_01_comments' => t('18) Fix Run 1 - Comments'),
                'fix_01_projects' => t('19) Fix Run 1 - Projects'),
                'fix_01_projects_cvs' => t('20) Fix Run 1 - Projects CVS and Demo Link'),
                'fix_02_projects_brs' => t('21) Fix Run 2 - Projects Extra Brs in Projects'),
                'fix_03_projects_paths' => t('22) Fix Run 3 - Projects SEO URLs'),
                'fix_03_user_fields' => t('23) Fix Run 3 - User Fields'),
                'fix_03_multi_files' => t('24) Fix Run 3 - Multifiles'),
                'fix_04_comments_igor_no_closing' => t('25) Fix Run 4 - Some comments had no closing igor tags'),
                'fix_04_nodes_igor_no_closing' => t('26) Fix Run 4 - Some nodes had no closing igor tags'),
            ),
        );
        $form['actions']['#type'] = 'actions';
        $form['actions']['submit'] = array(
            '#type' => 'submit',
            '#value' => $this->t('Run'),
            '#button_type' => 'primary',
        );
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        $this->importer->import_type = $form_state->getValue('import_type');
        if (method_exists($this->importer, $this->importer->getWMImportMethod()) === false) {
            dpm("error");
            dpm($importer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->importer->import_type = $form_state->getValue('import_type');
        $this->importer->wmImport();
        //     drupal_set_message($output);
    }
}
