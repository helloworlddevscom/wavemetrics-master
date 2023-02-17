<?php
/**
 * @file
 * Contains \Drupal\wavemetrics_importer\Controller\wavemetrics_importerController.
 */

namespace Drupal\wavemetrics_importer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\wavemetrics_importer\Controller\wavemetrics_importer_scraper;
use Drupal\wavemetrics_importer\Controller\wavemetrics_importer_dbreader;
use Drupal\user\Entity\User;
use Drupal\file\Entity\File;
use Drupal\taxonomy\Entity\Term;
use Drupal\comment\Entity\Comment;


class wavemetrics_importerController extends ControllerBase
{

    public $import_type;
    protected $media_directory;
    protected $image_directory;

    public function __construct()
    {
        $this->media_directory = "documents-imported/";
        $this->user_pics_directory = "user-profiles/";
        $this->image_directory = "images-imported/";
    }

    public function page()
    {
        $content = array();
        $content['markup'] = array(
            '#type' => 'markup',
            '#markup' => $this->t('Wave Metrics Importer!!'),
        );
        $form_class = '\Drupal\wavemetrics_importer\Form\wmImportRunImport';
        $content['form'] = \Drupal::formBuilder()->getForm($form_class);
        return $content;
    }

    public function wmImport()
    {
        //get the importer type.
        $importer_type = $this->getWMImportMethod();
        switch($importer_type){
            case"import_users":
            case"import_taxonomy":
            case"import_forum_comments":
            case"import_forums":
            case"import_forum_meta":
            case"import_snippets":
            case"import_code_snippet_comments":
            case"import_projects":
            case"import_project_comments":
            case"import_project_releases":
            case"import_fix_01_forum":
            case"import_fix_01_codesnippets":
            case"import_fix_01_comments":
            case"import_fix_01_projects":
            case"import_fix_01_projects_cvs":
            case"import_fix_02_projects_brs":
            case"import_fix_03_projects_paths":
            case"import_fix_03_user_fields":
            case"import_fix_03_multi_files":
            case"import_project_versions":
            case"import_fix_04_comments_igor_no_closing":
            case"import_fix_04_nodes_igor_no_closing":
                $scraper = new wavemetrics_importer_dbreader($this->import_type);
                break;
            case"import_fix_links_basic_pages":
                $scraper = new wavemetrics_importer_dbreader($this->import_type);
                break;
            default:
                $scraper = new wavemetrics_importer_scraper($this->import_type);
                break;
        }
        //Queue the scraper and importer for batch
        $batch_operations = array();
        asort($scraper->scraper_items);
        foreach ($scraper->scraper_items as $scrape_path => $scrape_item) {
            $batch_operations[] = array([$this, "scrapeAndImportForBatch"], array($this, $scraper, $scrape_item));
//                         $tmp = "";
//                         $this->scrapeAndImportForBatch($this, $scraper, $scrape_item,$tmp);
        }

        //Run the batch.
        $batch = array(
            'title' => t('Importing...'),
            'operations' =>
                $batch_operations
        ,
            'finished' => 'Drupal\wavemetrics_importer\Controller\wavemetrics_importerController::scrapeAndImportForBatchFinish',
        );
        batch_set($batch);
    }

    public function scrapeAndImportForBatch($importer, $scraper, $scrape_item, &$context)
    {
        //get the importer type.
        $importer_type = $importer->getWMImportMethod();
        //run the importer, its different between content and menus.
        if(substr($importer_type,0,12)=="import_menu_"){
            $scraper->scrapeMenuItem($scrape_item);
        }
        elseif($importer_type=="import_users"){
            $scraper->getUserDetails($scrape_item);
        }
        elseif($importer_type=="import_taxonomy"){
            $scraper->getTaxonomyDetails($scrape_item);
        }
        elseif($importer_type=="import_forums"){
            $scraper->getForums($scrape_item);
        }
        elseif($importer_type=="import_forum_comments" || $importer_type=="import_fix_03_multi_files") {
            $scraper->getForumComments($scrape_item);
        }
        elseif($importer_type=="import_snippets") {
            $scraper->getSnippetDetails($scrape_item);
        }
        elseif($importer_type=="import_code_snippet_comments") {
            $scraper->getSnippetComments($scrape_item);
        }
        elseif($importer_type=="import_projects") {
            $scraper->getProjects($scrape_item);
        }
        elseif($importer_type=="import_project_comments") {
            $scraper->getProjectComments($scrape_item);
        }
        elseif($importer_type=="import_project_releases"){
            $scraper->getProjectReleases($scrape_item);
        }
        elseif($importer_type=="import_fix_links_basic_pages" || $importer_type=="import_fix_01_forum" || $importer_type=="import_fix_01_codesnippets" || $importer_type=="import_fix_01_projects" || $importer_type=="import_fix_01_projects_cvs"  || $importer_type=="import_fix_02_projects_brs" || $importer_type=="import_fix_03_projects_paths" || $importer_type=="import_fix_04_nodes_igor_no_closing") {
            $scraper->getNodeDetails($scrape_item);
        }
        elseif($importer_type=="import_fix_01_comments") {
            $scraper->getCommentDetails($scrape_item);
        }
        elseif($importer_type=="import_fix_04_comments_igor_no_closing") {
            $scraper->getCommentDetails($scrape_item);
        }
        elseif($importer_type=="import_fix_03_user_fields"){
            $scraper->getD8UserDetails($scrape_item);
        }
        elseif($importer_type=="import_project_versions"){
            $scraper->getProjectVersions($scrape_item);
        }
        else{
            $scraper->scrapeItem($scrape_item);
        }
        $importer->$importer_type($scraper->scraped_item);
    }

    private function scrapeAndImportForBatchFinish($success, $results, $operations)
    {
        drupal_set_message("Fin");
    }

    public function getWMImportMethod()
    {
        $importer = "import_" . $this->import_type;
        return $importer;
    }

    /**
     * Import content into the content type 'Basic Page'
     *
     * @return node
     *   A newly created node object.
     */
    private function import_basic_page($data)
    {

        //Create a new node.
        $node = $this->createBasicNode('basic_page');

        //Set the title
        $node->setTitle($data['title']);

        //Add the SEO Title
        //if there was a specific SEO Title use it.
        if (isset($data['seo_title']) && $data['seo_title'] != "") {
            $this->addSEOTitle($node, $data['seo_title']);
        } //if not, use the main title.
        else {
            $this->addSEOTitle($node, $data['title']);
        }

        //Add the current path info to the node
        $this->addCurrentPath($node, $data['path']);

        //Add any images to the drupal file system
        $image_files = array();
        if (isset($data['images']) && count($data['images']) > 0) {
            foreach ($data['images'] as $image) {
                $image_files[$image['src']] = $this->addImageToDrupal($image['src']);
            }
        }

        //Add any media files to the drupal file system
        $media_files = array();
        if (isset($data['media']) && count($data['media']) > 0) {
            foreach ($data['media'] as $media) {
                $media_files[$media] = $this->addFileToDrupal($media);
            }
        }

        //Process the content (updates paths, massage, etc.)
        $this->processContent($data['content'], $image_files, $media_files);

        //Add the content to the node
        $this->addMainContent($node, $data['content']);

        //Save
        $this->saveNode($node);

        //Add a path alias for this node.
        \Drupal::service('path.alias_storage')->save("/node/" . $node->id(), "/" . substr($data['path'], 0, -4).".htm", "en");
        \Drupal::service('path.alias_storage')->save("/node/" . $node->id(), "/" . substr($data['path'], 0, -4), "en");
    }

    private function import_news($data)
    {

        //Create a new node.
        $node = $this->createBasicNode('basic_page');

        //Set the title
        $node->setTitle($data['title']);

        //Add the SEO Title
        //if there was a specific SEO Title use it.
        if (isset($data['seo_title']) && $data['seo_title'] != "") {
            $this->addSEOTitle($node, $data['seo_title']);
        } //if not, use the main title.
        else {
            $this->addSEOTitle($node, $data['title']);
        }

        //Add the current path info to the node
        $this->addCurrentPath($node, $data['path']);

        //Add any images to the drupal file system
        $image_files = array();
        if (isset($data['images']) && count($data['images']) > 0) {
            foreach ($data['images'] as $image) {
                $image_files[$image] = $this->addImageToDrupal($image);
            }
        }

        //Add any media files to the drupal file system
        $media_files = array();
        if (isset($data['media']) && count($data['media']) > 0) {
            foreach ($data['media'] as $media) {
                $media_files[$media] = $this->addFileToDrupal($media);
            }
        }

        //Process the content (updates paths, massage, etc.)
        $this->processContent($data['content'], $image_files, $media_files);

        //Add the content to the node
        $this->addMainContent($node, $data['content']);

        //Save
        $this->saveNode($node);

        //Add a path alias for this node.
        \Drupal::service('path.alias_storage')->save("/node/" . $node->id(), "/" . substr($data['path'], 0, -4).".htm", "en");
        \Drupal::service('path.alias_storage')->save("/node/" . $node->id(), "/" . substr($data['path'], 0, -4), "en");
    }

    private function import_menu_products($data)
    {
        $curr_parent = "";

        //there is an assumption that parents will be created before children. So sort the batch creation accordingly. We are not going to allow a level 4 item to be created before we know levels 2 and 3.
        $level = 0;
        $menu_name = 'main';
        $menu_tree = \Drupal::menuTree();
        $parameters = new MenuTreeParameters();
        $parameters->setMaxDepth(100);
        $parameters->setMinDepth(0);
        $parameters->setRoot("");
        $tree = $menu_tree->load($menu_name, $parameters);
        $this->getExistingMenuSubfoldersByOldPath($menu, $tree);
        foreach($menu as $pluginID => $link) {
            if($link && $link==$data['path']){
                //since it exists, use this as the parent.
                $curr_parent = $pluginID;
                break;
            }
        }
        if($curr_parent != "") {
            //foreach line in the nav file, create a menu link with parent.
            //load a TSV
            $tsv_menu = str_getcsv($data['content'], "\n"); //parse the rows
            $rowCount = 1;
            foreach ($tsv_menu as $tsv_menu_item) {
                //get the data for each row
                $row = str_getcsv($tsv_menu_item, "\t");
                //Break the row into variables of interest.
                $linkName = $row[0];
                $linkDestination = 'internal:/' . substr($data['path'], 0, -14) . $row[1];
                $linkType = $row[2];
                if ($linkType == "openable" || $linkType == "notopenable" || $linkType == "subfolder" || $linkType == "subfolders" ) {
                    //Create the new link with EntityTypeManager
                    $newLink = \Drupal::entityTypeManager()->getStorage('menu_link_content')->create([
                        'link' => ['uri' => $linkDestination]
                    ]);
                    $newLink->set('enabled', 1);
                    $newLink->set('title', trim($linkName));
                    $newLink->set('menu_name', $menu_name);
                    $newLink->set('bundle', $menu_name);
                    $newLink->set('parent', $curr_parent);
                    $newLink->set('weight', $rowCount * 10);
                    $newLink->save();
                    //Not sure why but in order to save into the "Menu Extra Items" menu fields I have to 're'-load the entity.
                    //See https://www.drupal.org/project/menu_item_extras/issues/2931915#comment-12391952
                    $newLinkUuid = $newLink->get('uuid')->value;;
                    $newLinkEntity = \Drupal::service('entity.repository')->loadEntityByUuid('menu_link_content', $newLinkUuid);
                    //Set where this entry came from.
                    $newLinkEntity->set('field_menu_old_source', $data['path']);
                    //if this of the type subfolder we need to add that subfolder path to aa_nav to the old path field.
                    if ($linkType == "subfolder" || $linkType == "subfolders" || $linkType == "openable") {
                        $oldParent = substr($linkDestination,10)."/aa_navlist.txt";
                        $newLinkEntity->set('field_menu_old_parent',$oldParent);
                    }
                    $newLinkEntity->save();
                    $rowCount++;
                }
            } //parse the items in rows
        }
    }

     private function import_gallerypost($data)
    {
        //Create a new node.
        $node = $this->createBasicNode('photo_gallery_item');

        //Set the title
        $node->setTitle($data['title']);

        //Add the SEO Title
        $this->addSEOTitle($node, $data['seo_title']);

        //Add the current path info to the node
        $this->addCurrentPath($node, $data['path']);

        //Add the gallery image to the drupal file system
        if (isset($data['gallery_image']) && count($data['gallery_image']) > 0) {
            foreach ($data['gallery_image'] as $image) {
                $galleryImage = $this->addImageToDrupal($image['src']);
                //prepare to add it to the node.
                $image_params = array(
                    "alt" => $image['alt'],
                    "title" => $image['title'],
                );
                $this->addImageReference($node,$galleryImage->id(),"field_image_single",$image_params);

                break; //only one image expected, only one image allowed
            }
        }

        //Add any media files to the drupal file system
        $media_files = array();
        if (isset($data['media']) && count($data['media']) > 0) {
            foreach ($data['media'] as $media) {
                $media_files[$media] = $this->addFileToDrupal($media);
            }
        }

        //Add the gallery type reference to the taxonomy item.
        $this->addTaxonomyReference($node,$data['gallery_type'],"field_gallery_type_reference");

        //there will not be images files as there is only one image and it is the main images added to a field.
        $image_files = array();

        //Process the content (updates paths, massage, etc.)
        $this->processContent($data['content'], $image_files, $media_files);

        //Add the content to the node
        $this->addMainContent($node, $data['content']);

        //Save
        $this->saveNode($node);

        //Add a path alias for this node.
        \Drupal::service('path.alias_storage')->save("/node/" . $node->id(), "/" . substr($data['path'], 0, -4).".htm", "en");
        \Drupal::service('path.alias_storage')->save("/node/" . $node->id(), "/" . substr($data['path'], 0, -4), "en");

    }

    /**
     * Import content into the content type 'Case Studies'
     *
     * @return node
     *   A newly created node object.
     */
    private function import_casestudies($data)
    {

        //Create a new node.
        $node = $this->createBasicNode('case_studies');

        //Set the title
        $node->setTitle($data['title']);

        //Add the SEO Title
        //if there was a specific SEO Title use it.
        if (isset($data['seo_title']) && $data['seo_title'] != "") {
            $this->addSEOTitle($node, $data['seo_title']);
        } //if not, use the main title.
        else {
            $this->addSEOTitle($node, $data['title']);
        }

        //Add the current path info to the node
        $this->addCurrentPath($node, $data['path']);

        //Add any images to the drupal file system
        $image_files = array();
        if (isset($data['images']) && count($data['images']) > 0) {
            foreach ($data['images'] as $image) {
                $image_files[$image['src']] = $this->addImageToDrupal($image['src']);
            }
        }

        //Add any media files to the drupal file system
        $media_files = array();
        if (isset($data['media']) && count($data['media']) > 0) {
            foreach ($data['media'] as $media) {
                $media_files[$media] = $this->addFileToDrupal($media);
            }
        }

        //Process the content (updates paths, massage, etc.)
        $this->processContent($data['content'], $image_files, $media_files);

        //Add the content to the node
        $this->addMainContent($node, $data['content']);

        //Save
        $this->saveNode($node);

        //Add a path alias for this node.
        \Drupal::service('path.alias_storage')->save("/node/" . $node->id(), "/" . substr($data['path'], 0, -4).".htm", "en");
        \Drupal::service('path.alias_storage')->save("/node/" . $node->id(), "/" . substr($data['path'], 0, -4), "en");
    }

    /**
     * Import users into the entity type 'Users'
     *
     * @return node
     *   A newly created node object.
     */
    private function import_users($data)
    {
        //Create a new user.
        $user = $this->createBasicUser($data);

        //Add the original ID should be the same as the new UID.
        $this->addUserField($user, 'field_profile_old_id', $data['old_uid']);

        //for each field, see if its a profile field
        foreach($data as $f => $v)
        {
            if (substr($f,0,8) == "profile_")
            {
                $this->addProfileField($user, $f, $v);
            }
            else{
                //for jed trace.
                $trace[] = $f;
            }
        }
        //Add the profile image to the drupal file system
        if (isset($data['images']) && count($data['images']) > 0) {
            foreach ($data['images'] as $image) {
                if($image['src']!="") {
                    $profileImage = $this->addImageToDrupal("http://www.igorexchange.com/".$image['src']);
                    $this->addImageReference($user, $profileImage->id(), "field_profile_picture");
                    break; //only one image expected, only one image allowed
                }
            }
        }

        $this->fix_user_fields($user,$data);

        //Save
        $user->save();
    }

    /**
     * Import content into the Vocabulary/Taxonomy System
     *
     * @return node
     *   A newly created node object.
     */
    private function import_taxonomy($data)
    {
        //look up the Vocabulary this belongs to.
        $data['new_vid'] = $this->taxonomyFindVocabulary($data);

        //Create a new term.
        $term = $this->createTaxonomyTerm($data);

        //Add the original ID should be the same as the new UID.
        $term->set('field_taxonomy_old_tid', $data['old_tid']);

        //if the parent was not 0, we need to find the new parent's TID.
        if($data['parent']>0) {
            $data['new_pid'] = $this->taxonomyFindTermByOldTID($data['parent']);
            $term->set('parent', $data['new_pid']);
        }

        //Save
        $term->save();

    }

    /**
     * Import content into the content type 'Forum Topic'
     *
     * @return node
     *   A newly created node object.
     */
    private function import_forums($data)
    {
        //Create a new node.
        $initParams = array(
            "nid" => $data['old_nid'],
            "created" => $data['created'],
            "changed" => $data['changed'],
        );
        $node = $this->createBasicNode('forum',$initParams,$data['uid']);

        //Set the title
        $node->setTitle($data['title']);

        //Add the SEO Title
        $this->addSEOTitle($node, $data['title']);

        //Add the forum type reference to the taxonomy item.
        $this->addTaxonomyReference($node,$this->taxonomyFindTermByOldTID($data['old_tid']),"taxonomy_forums");


        //Add the current path info to the node
        $this->addCurrentPath($node, "node/".$data['old_nid']);

        //Add the gallery image to the drupal file system
        if (isset($data['image'])) {
            $image = $this->addImageToDrupal($data['image']['src']);
            //prepare to add it to the node.
            $image_params = array(
                "title" => $data['image']['title'],
            );
            $this->addImageReference($node,$image->id(),"field_image_multiple",$image_params);
         }

        //Add any media files to the drupal file system
        if (isset($data['files']) && count($data['files']) > 0) {
            foreach ($data['files'] as $file) {
                $drupalFile = $this->addFileToDrupal($file['src']);
                //prepare to add it to the node.
                #TODO add the file description
                $file_params = null;
//                    $file_params = array(
//                        "title" => $image['description'],
//                    );
                $this->addFileReference($node,$drupalFile->id(),"field_file_attachment",$file_params);
            }
        }

        //Process the content (updates paths, massage, etc.)
        $this->processContent($data['body'],array(),array(),array('nl2br'=>true));
        $this->processContent($data['teaser'],array(),array(),array('nl2br'=>true));

        $this->fix_04_igor_no_closing($data['body']);

        //Add the content to the node
        $this->addBody($node, $data['body'],$data['teaser']);

        $this->fix_code_specialchar_body($node);
        $this->fix_code_other_languages_body($node);

        //Save
        $this->saveNode($node);

        //update the node view count
        \Drupal::database()->insert('node_counter')
            ->fields([
                'nid' => $node->id(),
                'totalcount' => $data['count'],
                'daycount' => 0,
                'timestamp' => time(),
            ])
            ->execute();
    }

    /**
     * Import comments for the content type 'Forum Topic'
     *
     * @return node
     *   A newly created node object.
     */
    private function import_forum_comments($data)
    {
        //Create a new comment.
        $initParams = array(
            "created" => $data['created'],
            "changed" => $data['changed'],
            "cid"=>$data['old_cid']
        );
        $comment = $this->createBasicComment($data['parent_nid'],$data['uid'],$initParams);

        //Set the Subject
        $this->addCommentSubject($comment,$data['subject']);

        //Add the current cid info to the node
        $comment->set('field_old_cid', $data['old_cid']);

        //Add any media files to the drupal file system
        if (isset($data['files']) && count($data['files']) > 0) {
            foreach ($data['files'] as $file) {
                $drupalFile = $this->addFileToDrupal($file['src']);
                //prepare to add it to the node.
                #TODO add the file description
                $file_params = null;
//                    $file_params = array(
//                        "title" => $image['description'],
//                    );
                $this->addFileReference($comment,$drupalFile->id(),"field_comment_file",$file_params);
            }
        }

        //Process the content (updates paths, massage, etc.)
        $this->processContent($data['body'],array(),array(),array('nl2br'=>true));
        $this->fix_code_specialchar_dataOnly($data['body']);
        $this->fix_code_other_languages_dataOnly($data['body']);
        $this->fix_04_igor_no_closing($data['body']);

        //Add the content to the node
        $this->addCommentBody($comment, $data['body']);

        //Save
        $this->saveComment($comment);

        drupal_set_message("Forum Comments Imported");
    }

    /**
     * Import content into the content type 'code_snippet'
     *
     * @return node
     *   A newly created node object.
     */
    private function import_snippets($data)
    {
        //Create a new node.
        $initParams = array(
            "nid" => $data['old_nid'],
            "created" => $data['created'],
            "changed" => $data['changed'],
        );
        $node = $this->createBasicNode('code_snippet',$initParams,$data['uid']);

        //Set the title
        $node->setTitle($data['title']);

        //Add the SEO Title
        $this->addSEOTitle($node, $data['title']);

        //Add the current path info to the node
        $this->addCurrentPath($node, "node/".$data['old_nid']);

        //Add the snippet type reference to the taxonomy item.
        //multiple selections allowed
        $this->addTaxonomyReference($node,$this->taxonomyFindTermByOldTID($data['tid_igor']),"field_supported_version");

        //Add the igor supported version type reference to the taxonomy item.
        foreach($data['tid_type'] as $old_tid)
        {
            $values = array(
                'target_id' => $this->taxonomyFindTermByOldTID($old_tid),
            );
            $this->addTaxonomyReference($node,$values,"field_code_type");
        }

        //Add the image to the drupal file system
        if (isset($data['image'])) {
            $image = $this->addImageToDrupal($data['image']['src']);
            //prepare to add it to the node.
            $image_params = array(
                "title" => $data['image']['title'],
            );
            $this->addImageReference($node,$image->id(),"field_image_multiple",$image_params);
        }

        //Add any media files to the drupal file system
        if (isset($data['files']) && count($data['files']) > 0) {
            foreach ($data['files'] as $file) {
                $drupalFile = $this->addFileToDrupal($file['src']);
                //prepare to add it to the node.
                #TODO add the file description
                $file_params = null;
//                    $file_params = array(
//                        "title" => $image['description'],
//                    );
                $this->addFileReference($node,$drupalFile->id(),"field_file_attachment",$file_params);
            }
        }

        //Process the content (updates paths, massage, etc.)
        $this->processContent($data['body'],array(),array(),array('nl2br'=>true));
        $this->processContent($data['teaser'],array(),array(),array('nl2br'=>true));


        $this->fix_04_igor_no_closing($data['body']);

        //Add the content to the node
        $this->addBody($node, $data['body'],$data['teaser']);
        $this->fix_code_specialchar_body($node);
        $this->fix_code_other_languages_body($node);

        //Save
        $this->saveNode($node);

        //update the node view count
        \Drupal::database()->insert('node_counter')
            ->fields([
                'nid' => $node->id(),
                'totalcount' => $data['count'],
                'daycount' => 0,
                'timestamp' => time(),
            ])
            ->execute();
    }

    /**
     * Import comments for the content type 'Code Snippet'
     *
     * @return node
     *   A newly created node object.
     */
    private function import_code_snippet_comments($data)
    {
        //Create a new comment.
        $initParams = array(
            "created" => $data['created'],
            "changed" => $data['changed'],
            "cid"=>$data['old_cid']
        );
        $comment = $this->createBasicComment($data['parent_nid'],$data['uid'],$initParams,"field_comments_basic","comment_basic");

        //Set the Subject
        $this->addCommentSubject($comment,$data['subject']);

        //Add the current cid info to the node
        $comment->set('field_old_cid', $data['old_cid']);

        //Add any media files to the drupal file system
        if (isset($data['files']) && count($data['files']) > 0) {
            foreach ($data['files'] as $file) {
                $drupalFile = $this->addFileToDrupal($file['src']);
                //prepare to add it to the node.
                #TODO add the file description
                $file_params = null;
//                    $file_params = array(
//                        "title" => $image['description'],
//                    );
                $this->addFileReference($comment,$drupalFile->id(),"field_comment_file",$file_params);
            }
        }

        //Process the content (updates paths, massage, etc.)
        $this->processContent($data['body'],array(),array(),array('nl2br'=>true));
        $this->fix_code_specialchar_dataOnly($data['body']);
        $this->fix_code_other_languages_dataOnly($data['body']);
        $this->fix_04_igor_no_closing($data['body']);

        //Add the content to the node
        $this->addCommentBody($comment, $data['body']);

        //Save
        $this->saveComment($comment);

        drupal_set_message("Code Snippet Comments Imported");
    }
    /**
     * Import content into the content type 'project'
     *
     * @return node
     *   A newly created node object.
     */
    private function import_projects($data)
    {

        //Create a new node.
        $initParams = array(
            "nid" => $data['old_nid'],
            "created" => $data['created'],
            "changed" => $data['changed'],
        );
        $node = $this->createBasicNode('project_project',$initParams,$data['uid']);

        //Set the title
        $node->setTitle($data['title']);

        //Add the SEO Title
        $this->addSEOTitle($node, $data['title']);

        //Add the project type reference to the taxonomy item.
        foreach($data['project_type_tid'] as $old_tid)
        {
            $values = array(
                'target_id' => $this->taxonomyFindTermByOldTID($old_tid),
            );
            $this->addTaxonomyReference($node,$values,"field_project_type");
        }

        //Add the os supported type reference to the taxonomy item.
        foreach($data['os_tid'] as $old_tid)
        {
            $values = array(
                'target_id' => $this->taxonomyFindTermByOldTID($old_tid),
            );
            $this->addTaxonomyReference($node,$values,"field_os_compatibility");
        }

        //Add the current path info to the node
        $this->addCurrentPath($node, "node/".$data['old_nid']);

        //add the fields
        $this->addProjectHomepage($node,$data['homepage']);
        $this->addProjectDocumentation($node,$data['documentation']);
        $this->addProjectLicense($node,$data['license']);
        $this->addProjectScreenshots($node,$data['screenshots']);
        $this->addProjectChangelog($node,$data['changelog']);
        $this->addProjectDemo($node,$data['demo']);
        $this->addProjectCvs($node,$data['cvs']);

        //add the short title (field_short_title)
        $this->addShortTitle($node,$data['uri']);

        //Add the  image to the drupal file system
        if (isset($data['image'])) {
            $image = $this->addImageToDrupal($data['image']['src']);
            //prepare to add it to the node.
            $image_params = array(
                "title" => $data['image']['title'],
            );
            $this->addImageReference($node,$image->id(),"field_image_multiple",$image_params);
        }

        //Process the content (updates paths, massage, etc.)
        $this->processContent($data['body'],array(),array(),array('nl2br'=>true));
        $this->processContent($data['teaser'],array(),array(),array('nl2br'=>true));

        //Add the content to the node
        $this->addBody($node, $data['body'],$data['teaser']);
        $this->fix_project_linebreaks_body($node);

        //Save
        $this->saveNode($node);

        //add in the supported version info.
        //requery the old db and get the cvs and demo links
        $scraper_for_versions = new wavemetrics_importer_dbreader($this->import_type);
        $scraper_for_versions->getProjectVersions($node->id());
        //add this taxonomy info
        $this->import_project_versions($scraper_for_versions->scraped_item);

        //Add a path alias for this node.
        \Drupal::service('path.alias_storage')->save("/node/" . $node->id(), "/project/" .$data['uri'] , "en");

    }

    /**
     * Import comments for the content type 'Projects'
     *
     * @return node
     *   A newly created node object.
     */
    private function import_project_comments($data)
    {
        //there are no project comments.
    }

    /**
     * Import comments for the content type 'Projects'
     *
     * @return node
     *   A newly created node object.
     */
    private function import_project_releases($data)
    {
        //load the project node
        $node = Node::load($data['parent_nid']);

        //add this data as a new paragraph item

        //create a new paragraph for the release
        $release = Paragraph::create([
            'type' => 'paragraph_project_release'
        ]);

        //add the fields example: $fieldCollectionItem->set('field_name',$value);
        //OS Compatability
        //Add the os supported type reference to the taxonomy item.
        foreach($data['os_tid'] as $old_tid)
        {
            $values = array(
                'target_id' => $this->taxonomyFindTermByOldTID($old_tid),
            );
            $this->addTaxonomyReference($release,$values,"field_paragraph_ref_os_compat");
        }
        //Release File
        //Add any media files to the drupal file system
        if (isset($data['files']) && count($data['files']) > 0) {
            foreach ($data['files'] as $file) {
                $drupalFile = $this->addFileToDrupal($file['src']);
                //prepare to add it to the node.
                #TODO add the file description
                $file_params = null;
                $this->addFileReference($release,$drupalFile->id(),"field_paragraph_file",$file_params);
            }
        }
        //Release Identifier (Title)
        $release->field_paragraph_h2 = $data['title'];
        //Release Notes (Body)
        $this->processContent($data['body'],array(),array(),array('nl2br'=>true));
        $release->field_paragraph_full_html= array("value"=>$data['body'],"format"=>"full_html");
        //Version Info
        $release->field_paragraph_version_extra = $data['version_extra'];
        $release->field_paragraph_version_major = $data['version_major'];
        $release->field_paragraph_version_patch = $data['version_patch'];
        $release->field_paragraph_version_date = $data['version_date'];
        $release->field_paragraph_version_md5 = $data['version_md5'];
        //Supported Igor Version
        //I now believe this is project data, not release data.
//        foreach($data['supported_igor_tid'] as $supported_igor_tid)
//        {
//            $values = array(
//                'target_id' => $this->taxonomyFindTermByOldTID($supported_igor_tid),
//            );
//            $this->addTaxonomyReference($release,$values,"field_paragraph_ref_igor_version");
//        }
        //Version
        $release->field_paragraph_version= $data['version'];

        //save the paragraph
        $release->isNew();
        $release->save();

        //add the paragraph to the project node
        //$this->addParagraphToNode($node, $release);
        $node->field_paragraphs[] = array(
            'target_id' => $release->id(),
            'target_revision_id' => $release->getRevisionId(),
        );

        //Save
        $this->saveNode($node);

    }

    private function import_fix_links_basic_pages(node $node)
    {
        //Re-Process the content fixing known issues.
        $this->fix_links_basic_page($node);
        //Save
        $this->saveNode($node);
    }

    private function import_fix_01_forum(node $node)
    {
        //Re-Process the content fixing known issues.
        $this->fix_code_specialchar_body($node);
        $this->fix_code_other_languages_body($node);
        //Save
        $this->saveNode($node);
    }

    private function import_fix_01_codesnippets(node $node)
    {
        //Re-Process the content fixing known issues.
        $this->fix_code_specialchar_body($node);
        $this->fix_code_other_languages_body($node);
        //Save
        $this->saveNode($node);
    }

    private function import_fix_01_comments(Comment $comment)
    {
        $body =  $comment->get('comment_body')->value;
        $this->fix_code_specialchar_dataOnly($body);
        $this->fix_code_other_languages_dataOnly($body);
        //Body can now be an array with a value and a format.
        $bodyArray = [
            'value' => "$body",
            'format' => 'full_html'
        ];
        $comment->set('comment_body', $bodyArray);

        //originally code snippet comments used the comment type 'comment_forum' but should have used 'comment_basic'. This attempts to fix that.
        if($comment->getCommentedEntity()->bundle() == "code_snippet") {
            $comment->set('comment_type','comment_basic');
        }

        //Save
        $this->saveComment($comment);
    }

    private function import_fix_01_projects(node $node){
        //Re-Process the content fixing known issues.
        $this->fix_code_specialchar_body($node);
        $this->fix_code_other_languages_body($node);
        $this->fix_project_linebreaks_body($node);

        //Save
        $this->saveNode($node);

    }

    private function import_fix_01_projects_cvs(node $node){
        //requery the old db and get the cvs and demo links
        $scraperForFix = new wavemetrics_importer_dbreader($this->import_type);
        $scraperForFix->getProjects($node->id());
        //reset these fields
        $this->addProjectDemo($node,$scraperForFix->scraped_item['demo']);
        $this->addProjectCvs($node,$scraperForFix->scraped_item['cvs']);
        //Re-Process the content fixing known issues.
        //Save
        $this->saveNode($node);

    }

    private function import_fix_02_projects_brs(node $node){
        //Re-Process the content fixing known issues.
        $this->fix_project_linebreaks_body($node);
        //Save
        $this->saveNode($node);
    }

    private function import_fix_03_projects_paths(node $node){
        $uri = $node->get(field_short_title)->getValue();
        //Add a path alias for this node.
        \Drupal::service('path.alias_storage')->save("/node/" . $node->id(), "/project/" .$uri[0]['value'] , "en");
    }

    private function import_fix_03_user_fields(user $user)
    {
        if ($user->id() > 0) {
            //Also get the old data
            $scraperForUser = new wavemetrics_importer_dbreader($this->import_type);
            $scraperForUser->getUserDetails($user->id());
            $oldUser = $scraperForUser->scraped_item;
            //only if on of the fields we are adjusting exists in the old data.
            if(is_array($oldUser) && ( ( isset($oldUser['signature']) && $oldUser['signature']!="" ) || ( isset($oldUser['timezone']) && $oldUser['timezone']!="" ) ) ){
                $this->fix_user_fields($user, $oldUser);
                //Save
                $user->save();
            }
        }
    }

    private function import_fix_03_multi_files($data){

        //Also get the new data
        $existingComment = Comment::load($data['old_cid']);
        $existingFiles = array();
        //gather the filenames for all existing files so that we don't add them again.
        foreach($existingComment->get('field_comment_file') as $a){
            $fid = $a->get('target_id')->getCastedValue();
            $file = File::load($fid);
            $filename = $file->get('filename')->value;
            $existingFiles[$filename] = $filename;
        }

        //Add any media files to the drupal file system
        if (isset($data['files']) && count($data['files']) > 0) {
            foreach ($data['files'] as $file) {
                //skip if already exists
                if(!in_array($file['filename'],$existingFiles)){
                    $drupalFile = $this->addFileToDrupal($file['src']);
                    $file_params = null;
                    $this->addFileReference($existingComment,$drupalFile->id(),"field_comment_file",$file_params);
                }
            }
        }

        //Save
        $this->saveComment($existingComment);

    }

    private function import_fix_04_comments_igor_no_closing(Comment $comment)
    {
        $body =  $comment->get('comment_body')->value;

        $this->fix_04_igor_no_closing($body);

        //Body can now be an array with a value and a format.
        $bodyArray = [
            'value' => "$body",
            'format' => 'full_html'
        ];
        $comment->set('comment_body', $bodyArray);

        //Save
        $this->saveComment($comment);
    }

    private function import_fix_04_nodes_igor_no_closing(Node $node)
    {
        $body =  $node->get('body')->value;

        $this->fix_04_igor_no_closing($body);

        //Body can now be an array with a value and a format.
        $bodyArray = [
            'value' => "$body",
            'format' => 'full_html'
        ];
        $node->set('body', $bodyArray);

        //Save
        $this->saveNode($node);
    }

    private function fix_04_igor_no_closing(&$body)
    {

        $this->remove_brs_after_orphan_igor_tag($body);

    }

    private function import_project_versions($data){
        //some nodes don't have any associated versions
        if(isset($data['tids'])){
            //load the node
            $project = \Drupal\node\Entity\Node::load($data['pid']);
            //add the taxonomy info
            foreach($data['tids'] as $tid) {
                if($tid>0){
                    $values = array(
                        'target_id' => $this->taxonomyFindTermByOldTID($tid),
                    );
                    $this->addTaxonomyReference($project,$values,"field_supported_version");
                }
            }
            $this->saveNode($project);
        }
    }

    private function createBasicNode($node_type = "basic_page",$initParams = array(),$uid = 0 )
    {
        $created_date = isset($initParams['created']) ? $initParams['created'] : time();
        $changed_date = isset($initParams['changed']) ? $initParams['changed'] : time();
        if(isset($initParams['nid']))
        {
            $node = Node::create([
                'type' => $node_type,
                'langcode' => 'en',
                'created' => $created_date,
                'changed' => $changed_date,
                'uid' => 1,
                'moderation_state' => 'published',
                'nid' => $initParams['nid'],
                'uid' => $uid,
            ]);
        }
        else
        {
            $node = Node::create([
                'type' => $node_type,
                'langcode' => 'en',
                'created' => $created_date,
                'changed' => $changed_date,
                'uid' => 1,
                'moderation_state' => 'published',
                'uid' => $uid,
            ]);
        }
        return $node;
    }



    private function createBasicUser($userData)
    {
        $user = User::create();

        // The old site did not use pretty urls, the urls were "user/%uid". This is  the system path to the user in D8.
        // Hence a rewrite/redirect is really impossible. I opted to keep the UIDs the same.
        $user->set("uid",$userData['old_uid']);

        //Mandatory settings
        $user->setPassword(user_password());
        $user->enforceIsNew();
        $user->setEmail($userData['mail']);
        $user->setUsername($userData['name']);

        //Optional settings
        $language = 'en';
        $user->set("init", 'email');
        $user->set("langcode", $language);
        $user->set("preferred_langcode", $language);
        $user->set("preferred_admin_langcode", $language);
        $user->activate();
        $user->set("created", $userData['created']);
        #TODO timezone

        //Save user
        return $user;

    }

    private function createBasicComment($nid,$uid,$initParams = array(),$field="comment_forum",$comment_type="comment_forum",$entity_type="node" )
    {
        $created_date = isset($initParams['created']) ? $initParams['created'] : time();
        $changed_date = isset($initParams['changed']) ? $initParams['changed'] : time();

        if(isset($initParams['cid']))
        {
            $comment = Comment::create([
                'entity_type' => $entity_type,
                'entity_id' => $nid,
                'langcode' => 'en',
                'created' => $created_date,
                'changed' => $changed_date,
                'field_name'  => $field,
                'comment_type' => $comment_type,
                'status' => 1,
                'cid' => $initParams['cid'],
                'uid' => $uid,
            ]);
        }
        else
        {
            $comment = Comment::create([
                'entity_type' => $entity_type,
                'entity_id' => $nid,
                'langcode' => 'en',
                'created' => $created_date,
                'changed' => $changed_date,
                'field_name'  => $field,
                'comment_type' => $comment_type,
                'status' => 1,
                'uid' => $uid,
            ]);
        }

        return $comment;
    }

    private function taxonomyFindVocabulary($termData)
    {
        //Ideally I would query for the vocabulary. But there are only 5. I didn't import them so there is no 'old id' field.
        //I am going to hard code the VID mapping.
        switch ($termData['old_vid']) {
            case"1":
                return 'project_type';
                break;
            case"2":
                return 'supported_igor_version';
                break;
            case"3":
                return 'forums';
                break;
            case"4":
                return 'operating_system_compatability';
                break;
            case"6":
                return 'type';
                break;
        }
    }

    private function taxonomyFindTermByOldTID($old_tid)
    {
        $query = \Drupal::entityQuery('taxonomy_term')
            ->condition('field_taxonomy_old_tid', $old_tid);
        $tids = $query->execute();
        return reset($tids);
    }

    private function createTaxonomyTerm($termData)
    {
        // Create node object
        $term = Term::create([
            'name' => $termData['name'],
            'vid' => $termData['new_vid'],
            'description' => $termData['description'],
            'weight' =>  $termData['weight'],
        ]);
        return $term;

    }

    private function addSEOTitle(Node &$node, $title)
    {
        $node->set('field_meta_tags', serialize([
            'title' => $title,
        ]));
    }

    private function addTaxonomyReference(&$entity, $values, $field)
    {

        if(!is_array($values)){
            $values = array(
                'target_id' => $values,
            );
        }
        $entity->$field[] = $values;
    }

    private function addImageReference(&$entity, $fid, $field, $params = array())
    {
        $this->addFileReference($entity, $fid, $field, $params);
    }

    private function addFileReference(&$entity, $fid, $field, $params = array())
    {
        $all_params = array(
            "target_id" => $fid,
        );
        foreach($params as $key => $value) {
            $all_params[$key] = $value;
        }
        $entity->$field[] = $all_params;
    }

    private function addMainContent(Node &$node, $content)
    {
        $paragraph = $this->createParagraph('paragraph_full_html', $content);
        $this->addParagraphToNode($node, $paragraph);
    }

    private function createParagraph($type, $content)
    {
        $paragraph = Paragraph::create([
            'type' => $type,
            'field_paragraph_full_html' => array(
                "value" => $content,
                "format" => "full_html"
            ),
        ]);
        $paragraph->isNew();
        $paragraph->save();
        return $paragraph;
    }

    private function addBody(&$node, $bodyContent, $teaser = "")
    {
        //Body can now be an array with a value and a format.
        $body = [
            'value' => "$bodyContent",
            'format' => 'full_html',
            'summary' => "$teaser",
        ];
        $node->set('body', $body);
    }

    private function addCommentBody(&$comment, $body,$body_field = "comment_body")
    {
        //Body can now be an array with a value and a format.
        $body = [
            'value' => "$body",
            'format' => 'full_html'
        ];
        $comment->set($body_field, $body);
    }

    private function addCommentSubject(&$comment, $subject,$subject_field = "field_comment_subject")
    {
        $comment->set($subject_field, $subject);
    }

    private function addParagraphToNode(Node &$node, $paragraph)
    {
        $node->field_paragraphs = array(
            array(
                'target_id' => $paragraph->id(),
                'target_revision_id' => $paragraph->getRevisionId(),
            )
        );
    }

    private function addCurrentPath(Node &$node, $path)
    {
        //we are using their build files which get converted to .htm files. So lets swap  this.
        if (substr($path, -4) == ".txt") {
            $path = substr($path, 0, -4) . ".htm";
        }
        $node->set('field_old_path', $path);
    }

    private function addCurrentMenu(Node &$node, $menu_info)
    {
        $node->set('field_old_menu', $menu_info);
    }

    private function addShortTitle(Node &$node, $title)
    {
        $node->set('field_short_title', $title);
    }

    private function addProjectHomepage(Node &$node, $value)
    {
        $node->set('field_project_homepage', $value);
    }

    private function addProjectDocumentation(Node &$node, $value)
    {
        $node->set('field_project_documentation', $value);
    }

    private function addProjectDemo(Node &$node, $value)
    {
        $node->set('field_project_demo', $value);
    }

    private function addProjectLicense(Node &$node, $value)
    {
        $node->set('field_project_license', $value);
    }

    private function addProjectChangelog(Node &$node, $value)
    {
        $node->set('field_project_changelog', $value);
    }

    private function addProjectScreenshots(Node &$node, $value)
    {
        $node->set('field_project_screenshots', $value);
    }

    private function addProjectCvs(Node &$node, $value)
    {
        $node->set('field_project_cvs', $value);
    }

    private function addUserField(User &$user, $f,$v)
    {
        $user->set($f, $v);
    }

    private function addProfileField(User &$user, $f,$v)
    {
        //The old field will be in the format profile_%
        //The new field will be in the format field_profile_%
            //with a max length of 32 (including field_profile_).
            //all lower case.
        //Create the field name
        $field_name = "field_profile_".substr($f,8);
        //with a max length of 32 (including field_profile_).
        $field_name = strlen($field_name)>32 ? substr($field_name,0,32) : $field_name;
        //all lower case.
        $field_name = strtolower($field_name);
        //A few exceptions
        $skip = false;
        switch($field_name)
        {
            case"field_profile_country":
                //get the country list from drupal
                $countries = \Drupal::service('country_manager')->getList();
                //We need the key, the old format should match the string value.
                //These countries are not matched in the service
                //Setting a Country code that is not
                $exceptions = array(
                    "United States Virgin Islands" => "VI",
                    "Federated States of Micronesia" => "FM",
                    "Serbia and Montenegro (Yugoslavia)" => "RS", //Serbia
                    "Macau" => "MO",
                    "Hong Kong" => "HK",
                );
                if(in_array($v,$countries)) {
                   $v = array_search($v,$countries);
                }
                elseif(isset($exceptions[$v]))
                {
                    $v = $exceptions[$v];
                }
                else{
                    \Drupal::logger('wavemetrics_importer')->error("The country %country could not be matched.",array("%country"=>$v));
                    $skip = true;
                }
            break;
            case"field_profile_birthday":
                //value will be in the format: a:3:{s:5:"month";s:1:"1";s:3:"day";s:2:"24";s:4:"year";s:4:"2012";}
                $datevalues = unserialize($v);
                $datevalues['month'] = strlen($datevalues['month']) < 2 ? sprintf("%02d",$datevalues['month']) : $datevalues['month'];
                $datevalues['year'] = strlen($datevalues['year']) < 2 ? sprintf("%02d",$datevalues['year']) : $datevalues['year'];
                $v = $datevalues['year']."-".$datevalues['month']."-".$datevalues['day'];
            break;
            case"field_profile_gender":
                //I am not sure the benefits of this field outweight the potential blowback.
                $skip = true;
            break;
            case"field_profile_scientific_discipl":
                //find the tid
                $properties['name'] = $v;
                $properties['vid'] = "scientific_discipline";
                $terms = \Drupal::entityManager()->getStorage('taxonomy_term')->loadByProperties($properties);
                if(count($terms)>0) {
                    $term = reset($terms);
                    if($term->id()>0) {
                        $this->addTaxonomyReference($user, $term->id(), $field_name);
                    }
                    else
                    {
                        \Drupal::logger('wavemetrics_importer')->error("The scientific discipline %sd could not be matched.",array("%sd"=>$v));
                    }
                }
                else
                {
                    \Drupal::logger('wavemetrics_importer')->error("The scientific discipline %sd could not be matched.",array("%sd"=>$v));
                }
                $skip = true;
            break;
            case"field_profile_homepageurl";
                //run this as a link instead
                $this->addLinkField($user,$field_name,$v);
                $skip = true;
            break;
        }
        if($skip != true)
            $this->addUserField($user,$field_name,$v);
    }

    private function addLinkField(&$entity, $field,$link,$text = null)
    {
        #TODO how to add the $text anchor text?
        $entity->set($field,$link);

    }

    private function addImageToDrupal($image_url)
    {
        // Create file object from remote URL.
        $image_data = file_get_contents($image_url);
        //Where should this file be saved.
        $drupal_path = "public://" . $this->image_directory;
        //get the filename
        $image_name = basename($image_url);
        //format The file name into a drupal file path (public).
        $new_drupal_path = $drupal_path . $image_name;
        $file = file_save_data($image_data, $new_drupal_path, FILE_EXISTS_RENAME);
        return $file;
    }

    private function addFileToDrupal($file_url)
    {
        // Create file object from remote URL.
        $file_data = file_get_contents($file_url);
        //Where should this file be saved.
        $drupal_path = "public://" . $this->media_directory;
        //get the filename
        $file_name = basename($file_url);
        //format The file name into a drupal file path (public).
        $new_drupal_path = $drupal_path . $file_name;
        $file = file_save_data($file_data, $new_drupal_path, FILE_EXISTS_RENAME);
        return $file;
    }


    private function processContent(&$content, $image_files = array(), $media_files = array(),$massageParams = array())
    {
        //for each image, replace the path.
        foreach ($image_files as $old_path => $new_file) {
            //get the relative path to the file (this combo of funcs were found on the internet)
            $new_path = $new_file->getFileUri();
            $new_path = file_url_transform_relative(file_create_url($new_path));
            $content = str_replace("/" . $old_path, $new_path, $content);
        }
        //for each media file, replace the path.
        foreach ($media_files as $old_path => $new_file) {

            if($new_file===false || $new_file===true){

            }

            //get the relative path to the file (this combo of funcs were found on the internet)
            $new_path = $new_file->getFileUri();
            $new_path = file_url_transform_relative(file_create_url($new_path));
            $content = str_replace($old_path, $new_path, $content);
        }

        if(isset($massageParams['nl2br']) && $massageParams['nl2br']==true){
            $content = nl2br($content );
            //for geshifilter (and maybe other things) we do not want to add <br> tags, revert those.
            //https://laracasts.com/discuss/channels/general-discussion/replace-n-with-br-except-within-pre-tags?page=1
            //https://stackoverflow.com/questions/17646041/php-how-to-keep-line-breaks-using-nl2br-with-html-purifier
            if (preg_match_all('(<code>(.*?)<\/code>)s', $content, $match)){
                foreach($match as $a){
                    foreach($a as $b){
                        $content = str_replace('<code>'.$b.'</code>', "<code>".str_replace("<br />", "", $b)."</code>", $content);
                    }
                }
            }
//            if (preg_match_all('(\[quote](.*?)\[\/quote])s', $content, $match)){
//                foreach($match as $a){
//                    foreach($a as $b){
////                        $content = str_replace('[quote]'.$b.'[/quote]', "[quote]".str_replace("<br />", PHP_EOL, $b)."[/quote]", $content);
//                    }
//                }
//            }
            if (preg_match_all('(<igor>(.*?)<\/igor>)s', $content, $match)){
                foreach($match as $a){
                    foreach($a as $b){
                        $content = str_replace('<igor>'.$b.'</igor>', "<pre><code class=\"language-igor\">".str_replace("<br />", "", $b)."</code></pre>", $content);
                    }
                }
            }
            elseif (preg_match_all('(<igor>(.*?)END)s', $content, $match)){
                foreach($match as $a){
                    foreach($a as $b){
                        $content = str_replace('<igor>'.$b.'END', "<pre>".PHP_EOL."<code class=\"language-igor\">".str_replace("<br />", "", $b)."</code></pre>", $content);
                    }
                }
            }
        }//end if nl2br was set

    }

    private function fix_links_basic_page(Node &$node)
    {
        //some bodys (not imported content, or content that doesn't use paragraphs will not be processed.
        $body = false;
        // We want to analyze imported body content. That content was imported as a paragraph. Typically there could be multiple paragraphs but we no the imported stuff only has one paragraph or the body is the first paragraph.
        if(isset($node->field_paragraphs) && count($node->field_paragraphs)>0) {
            foreach ($node->field_paragraphs as $paragraphIDs) {
                $paragraph = $paragraphIDs->entity;
                if(!empty($paragraph->field_paragraph_full_html)) {
                    $body = $paragraph->get('field_paragraph_full_html')->value;
                }
                break;//just the first paragraph, thank you.
            }

            if($body !== false) {


                //Load as DOM to easily manipulate. We can't use LIBXML_HTML_NOIMPLIED as WM HTML is not valid. Hence we will have to manually remove the tags.
                $dom = new \DomDocument;
                $dom->loadHTML($body, LIBXML_HTML_NODEFDTD);

                //Find each link,
                $links = $dom->getElementsByTagName('a');

                //Iterate over the extracted links and display their URLs
                foreach ($links as $link) {
                    $modified=false;
                    //Extract and show the "href" attribute.
                    $anchorTxt = $link->nodeValue;
                    $anchorTxtOriginal = $anchorTxt;
                    $href = $link->getAttribute('href');
                    $hrefOriginal = $href;
                    //Some links have the anchor text twice. Example <a href="http://example.com">This is my Anchor Text.This is my Anchor Text.</a>. If this link suffers from this, fix it.
                    if (substr($anchorTxt, 0, (strlen($anchorTxt) / 2)) == substr($anchorTxt, (strlen($anchorTxt) / 2))) {

                        $anchorTxt = substr($anchorTxt, 0, (strlen($anchorTxt) / 2));
                        $modified=true;
                    }
                    //Some links have the hrefs with absolute paths to htm files. These should be made webroot relative
                    if (strpos($href,"/home/wavemetrics/wm-build")!==false && substr($href,strlen($href)-strlen(".htm"))==".htm") {

                        $href = substr($href,strlen("/home/wavemetrics/wm-build"),(strlen("$href")-(strlen("/home/wavemetrics/wm-build") + strlen(".htm"))));
                        $modified=true;
                    }
                    if($modified==true){
                        //replace the entire link with a simple and standardized link.
                        $new_link = $dom->createElement('a');
                        $new_link->setAttribute('href', $href);
                        $new_link->nodeValue = $anchorTxt;
                        if ($link->hasChildNodes()) {
                            $children = [];
                            foreach ($link->childNodes as $child) {
                                if($child->wholeText!=$anchorTxtOriginal){
                                    $children[] = $child;
                                }
                            }
                            foreach ($children as $child) {
                                $new_link->appendChild($child);
                            }
                        }
                        $link->parentNode->replaceChild($new_link, $link);
                    }

                }//end foreach.
                //convert back to HTML string from DOM.
                //because the HTML was not valid we could not use LIBXML_HTML_NOIMPLIED
                //remove the opening <html><body>  and closing </body></html>
                $paragraph->set('field_paragraph_full_html', array("format"=>"full_html","value" => substr(substr($dom->saveHTML(), 12), 0, -15)));
                $paragraph->save();
            }
        }
    }

    private function get_everything_in_tags($string, $tagname)
    {
        $pattern = "#<\s*?$tagname\b[^>]*>(.*?)</$tagname\b[^>]*>#s";
        preg_match_all($pattern, $string, $matches);
        return $matches[1];
    }

    private function fix_code_specialchar_body(Node &$node)
    {
        $body = $node->get('body')->value;
        $body = "<span>".$body."</span>";
        $codeChunks = $this->get_everything_in_tags($body, "code");

        //Iterate over the extracted links and display their URLs
        foreach ($codeChunks as $codeChunk) {
            //save the original
            $codeChunkOriginal = $codeChunk;
            //replace the special chars
            $codeChunk = htmlspecialchars($codeChunk);
            //replace original with new
            $body = str_replace($codeChunkOriginal,$codeChunk,$body);
        }//end foreach.
        $node->set('body', array("format"=>"full_html",'summary' => $node->get('body')->summary,'value' => $body));
    }

    private function fix_code_specialchar_paragraph(Node &$node,$field_name = 'field_paragraphs')
    {

        foreach ($node->$field_name as $paragraphIDs) {
            $paragraph = $paragraphIDs->entity;
            if(!empty($paragraph->field_paragraph_full_html)) {
                $body = $paragraph->get('field_paragraph_full_html')->value;
            }
            break;//just the first paragraph, thank you.
        }
        $body = "<span>".$body."</span>";
        $codeChunks = $this->get_everything_in_tags($body, "code");

        //Iterate over the extracted links and display their URLs
        foreach ($codeChunks as $codeChunk) {
            //save the original
            $codeChunkOriginal = $codeChunk;
            //replace the special chars
            $codeChunk = htmlspecialchars($codeChunk);
            //replace original with new
            $body = str_replace($codeChunkOriginal,$codeChunk,$body);
        }//end foreach.
        $paragraph->set('field_paragraph_full_html', array("format"=>"full_html","value" =>  $body));

    }


    private function fix_code_specialchar_dataOnly(&$body)
    {

        $body = "<span>".$body."</span>";
        $codeChunks = $this->get_everything_in_tags($body, "code");

        //Iterate over the extracted links and display their URLs
        foreach ($codeChunks as $codeChunk) {
            //save the original
            $codeChunkOriginal = $codeChunk;
            //replace the special chars
            $codeChunk = htmlspecialchars($codeChunk);
            //replace original with new
            $body = str_replace($codeChunkOriginal,$codeChunk,$body);
        }//end foreach.
    }

    private function fix_code_other_languages_body(Node &$node)
    {
        $body = $node->get('body')->value;

        $this->fix_code_other_languages_dataOnly($body);

        $node->set('body', array("format"=>"full_html",'summary' => $node->get('body')->summary,'value' => $body));
    }

    private function fix_project_linebreaks_body(Node &$node){

        $body = $node->get('body')->value;

        //This content seems to have a some HTML so nl2br is causing lots of breaks. But it is not complete/valid html so I am going to wrap it all in a span.
        //This is weird but lets try to find three brs in a row and reduce it to to?

        //there are BRs between li(s) in ol,and ul(s)
        //The BR code to search for is XHTML
        $br = "<br />";
        $betweenUls = $codeChunks = $this->get_everything_in_tags($body, "ul");
        //Iterate over the extracted chunks and remove br tags
        foreach ($betweenUls as $codeChunk) {
            //save the original
            $codeChunkOriginal = $codeChunk;
            //replace the BRs
            $codeChunk = str_replace($br,"",$codeChunkOriginal);
            //replace original with new
            $body = str_replace($codeChunkOriginal,$codeChunk,$body);
        }//end foreach.
        $betweenOls = $codeChunks = $this->get_everything_in_tags($body, "ol");
        //Iterate over the extracted chunks and remove br tags
        foreach ($betweenUls as $codeChunk) {
            //save the original
            $codeChunkOriginal = $codeChunk;
            //replace the BRs
            $codeChunk = str_replace($br,"",$codeChunkOriginal);
            //replace original with new
            $body = str_replace($codeChunkOriginal,$codeChunk,$body);
        }//end foreach.

        //remove any <br>(s) directly after the closing of a block element: H#, OL, or UL.
        $body=str_replace("</h1><br />","</h1>",$body);
        $body=str_replace("</h1>\r\n<br />","</h1>",$body);
        $body=str_replace("</h2><br />","</h2>",$body);
        $body=str_replace("</h2>\r\n<br />","</h2>",$body);
        $body=str_replace("</h3><br />","</h3>",$body);
        $body=str_replace("</h3>\r\n<br />","</h3>",$body);
        $body=str_replace("</h4><br />","</h4>",$body);
        $body=str_replace("</h4>\r\n<br />","</h4>",$body);
        $body=str_replace("</ol><br />","</ol>",$body);
        $body=str_replace("</ol>\r\n<br />","</ol>",$body);
        $body=str_replace("</ul><br />","</ul>",$body);
        $body=str_replace("</ul>\r\n<br />","</ul>",$body);

        //reducing two Brs in a row to one.
//            $tooManyBrs = "<br />\r\n<br />";
//            $lessBrs = "<br />";
//            $body=str_replace($tooManyBrs,$lessBrs,$body);

        $node->set('body', array("format"=>"full_html",'summary' => $node->get('body')->summary,'value' => $body));

    }

    private function fix_code_other_languages_dataOnly(&$body)
    {
        //Any CCode, remove the special chars
        $codeChunks = $this->get_everything_in_tags($body, "ccode");
        foreach ($codeChunks as $codeChunk) {
            //save the original
            $codeChunkOriginal = $codeChunk;
            //replace the special chars
            $codeChunk = htmlspecialchars($codeChunk);
            //replace original with new
            $body = str_replace($codeChunkOriginal,$codeChunk,$body);
        }//end foreach.
        //now replace the tag:
        $body = str_replace("<ccode>","<pre><code class=\"language-ccode\">",$body);
        $body = str_replace("</ccode>","</code></pre>",$body);

        //Any CPP, remove the special chars
        $codeChunks = $this->get_everything_in_tags($body, "cpp");
        foreach ($codeChunks as $codeChunk) {
            //save the original
            $codeChunkOriginal = $codeChunk;
            //replace the special chars
            $codeChunk = htmlspecialchars($codeChunk);
            //replace original with new
            $body = str_replace($codeChunkOriginal,$codeChunk,$body);
        }//end foreach.
        //now replace the tag:
        $body = str_replace("<cpp>","<pre><code class=\"language-cpp\">",$body);
        $body = str_replace("</cpp>","</code></pre>",$body);

        //Any XML, remove the special chars
        $codeChunks = $this->get_everything_in_tags($body, "xml");
        foreach ($codeChunks as $codeChunk) {
            //save the original
            $codeChunkOriginal = $codeChunk;
            //replace the special chars
            $codeChunk = htmlspecialchars($codeChunk);
            //replace original with new
            $body = str_replace($codeChunkOriginal,$codeChunk,$body);
        }//end foreach.
        //now replace the tag:
        $body = str_replace("<xml>","<pre><code class=\"language-xml\">",$body);
        $body = str_replace("</xml>","</code></pre>",$body);

        //Any C, remove the special chars
        $codeChunks = $this->get_everything_in_tags($body, "c");
        foreach ($codeChunks as $codeChunk) {
            //save the original
            $codeChunkOriginal = $codeChunk;
            //replace the special chars
            $codeChunk = htmlspecialchars($codeChunk);
            //replace original with new
            $body = str_replace($codeChunkOriginal,$codeChunk,$body);
        }//end foreach.
        //now replace the tag:
        $body = str_replace("<c>","<pre><code class=\"language-c\">",$body);
        $body = str_replace("</c>","</code></pre>",$body);

        //Any C, remove the special chars
        $codeChunks = $this->get_everything_in_tags($body, "sql");
        foreach ($codeChunks as $codeChunk) {
            //save the original
            $codeChunkOriginal = $codeChunk;
            //replace the special chars
            $codeChunk = htmlspecialchars($codeChunk);
            //replace original with new
            $body = str_replace($codeChunkOriginal,$codeChunk,$body);
        }//end foreach.
        //now replace the tag:
        $body = str_replace("<sql>","<pre><code class=\"language-sql\">",$body);
        $body = str_replace("</sql>","</code></pre>",$body);
    }

    private function remove_html_from_tags(&$body,$enclosing_tag)
    {

        $codeChunks = $this->get_everything_in_tags($body, $enclosing_tag);

        //Iterate over the extracted links and display their URLs
        foreach ($codeChunks as $codeChunk) {
            //save the original
            $codeChunkOriginal = $codeChunk;
            //strip the tags
            $codeChuck = strip_tags($codeChuck);

            //replace original with new
            $body = str_replace($codeChunkOriginal,$codeChunk,$body);
        }//end foreach.

    }

    private function remove_brs_after_orphan_igor_tag(&$body)
    {

        $codeChunk = substr($body, strpos($body, "<igor"));
        $codeChunkOriginal = $codeChunk;

        //replace the br(s)
        $codeChunk = str_ireplace("<br>","",$codeChunk);
        $codeChunk = str_ireplace("<br/>","",$codeChunk);
        $codeChunk = str_ireplace("<br />","",$codeChunk);

        //if there is only one closing span tag in this codde, make an assumption. Close the igor tag before the span.
        if(substr_count($codeChunk,"</span>")===1){
            $codeChunk = str_ireplace("</span>","</igor></span>",$codeChunk);
        }

        //standardize to <pre><code class="language-igor"></code></pre>
        $codeChunk = str_ireplace("<igor>",'<pre><code class="language-igor">',$codeChunk);
        $codeChunk = str_ireplace("<igor >",'<pre><code class="language-igor">',$codeChunk);
        $codeChunk = str_ireplace("</igor>","</code></pre>",$codeChunk);
        $codeChunk = str_ireplace("</igor >","</code></pre>",$codeChunk);

        //replace original with new
        $body = str_replace($codeChunkOriginal,$codeChunk,$body);

    }


    private function fix_user_fields(&$user,$data){
        //main_users.signature
        $this->addUserField($user, 'signature',$data['signature']);
        //main_users.timezone
        $timezone = $this->timezoneMap($data['timezone']);
        $user->set('timezone',$timezone);
    }

    private function timezoneMap ($offset){
        $map = array(
            '-28800' => 'America/Anchorage',
            '36000' => 'Australia/Sydney',
            '-21600' => 'America/Denver',
            '-25200' => 'America/Los Angeles',
            '-18000' => 'America/Chicago',
            '-14400' => 'America/Detroit',
            '3600' => 'Europe/London',
            '7200' => 'Europe/Amsterdam',
            '39600' => 'Pacific/Norfolk',
            '46800' => 'Pacific/Apia',
            '32400' => 'Asia/Tokyo',
            '-10800' => 'America/Argentina/Buenos Aires',
            '28800' => 'Asia/Hong Kong',
            '19800' => 'Asia/Colombo',
            '-39600' => 'Pacific/Midway',
            '10800' => 'Asia/Jerusalem',
            '-7200' => 'Atlantic/South Georgia',
            '25200' => 'Asia/Bangkok',
            '23400' => 'Indian/Cocos',
            '-32400' => 'Pacific/Gambier',
            '43200' => 'Pacific/Fiji',
            '-36000' => 'Pacific/Honolulu',
            '20700' => 'Asia/Kathmandu',
            '-3600' => 'Atlantic/Cape Verde',
            '34200' => 'Australia/Adelaide',
        );
        return $map[$offset];
    }

    private function saveNode(Node $node)
    {
        $node->save();
    }

    private function saveComment(Comment $comment)
    {
        $comment->save();
    }

    private function getExistingMenuSubfoldersByOldPath(&$output, &$input, $parent = FALSE)
    {
        foreach ($input as $key => $item) {
            //If menu element disabled skip this branch
            if ($item->link->isEnabled()) {
                list($entity_type, $uuid) = explode(':', $key, 2);
                $entity = \Drupal::service('entity.repository')->loadEntityByUuid('menu_link_content', $uuid);
                if($entity)
                {
                    $linkOldPath = $entity->get('field_menu_old_parent')->value;
                    $output[$key] = $linkOldPath;
                    if ($item->hasChildren) {
                            $this->getExistingMenuSubfoldersByOldPath($output, $item->subtree, $key);
                    }
                }
            }
        }
    }

    public function testbed()
    {
        $content = array();
        $content['markup'] = array(
            '#type' => 'markup',
            '#markup' => $this->t('Wave Metrics Testbed'),
        );
        $form_class = '\Drupal\wavemetrics_importer\Form\wmTestbedForm';
        $content['wmTestbedForm'] = \Drupal::formBuilder()->getForm($form_class);
        return $content;
    }

}
