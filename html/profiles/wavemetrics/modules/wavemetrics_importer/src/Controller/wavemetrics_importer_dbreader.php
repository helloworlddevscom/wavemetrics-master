<?php
/**
 * @file
 * Contains Drupal\wavemetrics_importer\Controller\wavemetrics_importer_scraper.
 */

namespace Drupal\wavemetrics_importer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\comment\Entity\Comment;

class wavemetrics_importer_dbreader extends ControllerBase
{

    public $scraper_file_path;
    public $scraper_items;
    public $scraped_item;
    public $scraper_inprocess_item;
    public $throttle_forum_comments;

    public function __construct($import_type)
    {
        $this->throttle_forum_comments = array(); //The range for forums to evaluate for comments in a single batch.
        $this->throttle_snippets = array();
        $this->import_type = $import_type;
        $this->scraper_file_path = "/home/wavemetrics/igorfiles/";
        $this->scraper_items = $this->getRows($import_type);
        $this->scraped_item = array();
        $this->user_field_map = array(
            "username" => "",
            "mail" => "",
            "profile_old_path" => array(),
            "profile_old_id" => array(),
            "profile_" => "",
            "profile_activation_key" => "",
            "profile_birthday" => "",
            "profile_companies" => "",
            "profile_contlist_betatester" => "",
            "profile_contlist_contrib" => "",
            "profile_contlist_forums_list" => "",
            "profile_contlist_igor_pro" => "",
            "profile_contlist_igor_user" => "",
            "profile_contlist_maintainer" => "",
            "profile_contlist_wavemetrics" => "",
            "profile_contributions" => "",
            "profile_country" => "",
            "profile_fullName" => "",
            "profile_gender" => "",
            "profile_homepageURL" => "",
            "profile_IgorUse" => "",
            "profile_igor_uses" => "",
            "profile_industries" => "",
            "profile_interests" => "",
            "profile_jobtitle" => "",
            "profile_scientific_discipline" => "",
            "profile_serial_number" => "",
            "profile_uses_creating_graphics" => "",
            "profile_uses_curve_fitting" => "",
            "profile_uses_data_acquisition" => "",
            "profile_uses_image_analysis" => "",
            "profile_uses_programming" => "",
            "created" => "",
            "signature" => "",
            "timezone" => "",
            "old_uid" => "old_uid",
            "picture" => "picture",
        );
        $this->scraper_inprocess_item = array();
    }

    public function getRows($filter_type)
    {
        $rows = array();
        switch ($filter_type) {
            case"users":
                $rows=$this->getUserRows();
                break;
            case"taxonomy":
                $rows=$this->getTaxonomyRows();
                break;
            case"forums":

                $rows=$this->getForumsRows();
                break;
            case"forum_comments":
            case"fix_03_multi_files":
                $rows=$this->getForumCommentsRows($this->throttle_forum_comments);
                break;
            case"snippets":
                $rows=$this->getSnippetRows($this->throttle_snippets);
                break;
            case"code_snippet_comments":
                $rows=$this->getSnippetCommentRows($this->throttle_forum_comments);
                break;
            case"projects":
                $rows=$this->getProjectsRows();
                break;
            case"project_comments":
                $rows=$this->getProjectCommentsRows($this->throttle_forum_comments);
                break;
            case"project_releases":
                $rows=$this->getProjectReleasesRows($this->throttle_forum_comments);
                break;
            case"fix_links_basic_pages":
                $rows=$this->getD8Nodes('basic_page');
                break;
            case"fix_01_forum":
                $rows=$this->getD8Nodes('forum');
                break;
            case"fix_01_codesnippets":
                $rows=$this->getD8Nodes('code_snippet');
                break;
            case"fix_01_comments":
                $rows=$this->getD8Comments('comment_forum');
                break;
            case"fix_04_comments_igor_no_closing":
                $rows=$this->getD8CommentsNoClosingIgor();
                break;
            case"fix_04_nodes_igor_no_closing":
                $rows=$this->getD8NodesNoClosingIgor();
                break;

            case"fix_01_projects":
            case"fix_01_projects_cvs":
            case"fix_02_projects_brs":
            case"fix_03_projects_paths":
            case"project_versions":
                $rows=$this->getD8Nodes('project_project');
                break;
            case"fix_03_user_fields":
                $rows=$this->getD8Users();
                break;
        }
        return $rows;
    }

    public function getUserRows()
    {
        $query = \Drupal::database()->select('main_users', 'm');
$query->condition("m.uid","14973",">");

        //exclude the God (0) user and the 'admin' (1) user
        $query->condition('m.uid',array(0,1,3,21,13475),"NOT IN");
//        $query->range(0,10);
        $query->fields('m', ['uid']);
        $result = $query->execute();
        $rows = array();
        while ($row = $result->fetchAssoc()) {
            $rows[$row['uid']] = $row['uid'];
        }
        return $rows;
    }

    public function getTaxonomyRows()
    {
        $query = \Drupal::database()->select('main_term_data', 't');
        $query->fields('t', ['tid']);
        $query->join('main_term_hierarchy','h','h.tid = t.tid');
        $query->orderBy('h.parent', 'desc');
        $result = $query->execute();
        $rows = array();
        while ($row = $result->fetchAssoc()) {
            $rows[$row['tid']] = $row['tid'];
        }
        return $rows;
    }

    public function getForumsRows($range = array())
    {
        $query = \Drupal::database()->select('main_forum', 'f');
        $query->join('main_node','n','n.nid = f.nid and n.vid = f.vid and n.status =1');
        $query->fields('f', ['nid']);
        $query->orderBy('f.nid');
$query->condition("n.nid","8395",">");
//$query->range(0,200);
//$query->orderBy('f.nid','desc');
        $result = $query->execute();
        $rows = array();
        while ($row = $result->fetchAssoc()) {
            $rows[$row['nid']] = $row['nid'];
        }
        return $rows;
    }
    public function getForumCommentsRows($range = array())
    {
        //Originally I was going to go per forum topic. Because I felt parental hierarchy would be imported in this step.
            //But there are forum topics with up to 73 comments. Rather than queue (and worrry about async).
            //Instead, I will import all comments. Then fix the parents.
            //This is the original, per forum code

//            $query = \Drupal::entityQuery('node');
//            $query->condition('status', 1);
//            $query->condition('type', "forum");
//            $query->sort('nid');
//            if(count($range)==2)
//            {
//                $query->range($range[0],$range[1]);
//            }
//            $forumTopics = $query->execute();
//            $rows = array();
//            foreach ($forumTopics as $revisionID => $forumID) {
//                $rows[$forumID] = $forumID;
//            }
//            return $rows;

        $query = \Drupal::database()->select('main_comments', 'c');
        $query->join('main_node','n','n.nid = c.nid and n.type = \'forum\' and n.status =1');
        $query->fields('c', ['cid']);
$query->condition("c.cid","16889",">");

        //join the existing drupal node table, in theory all forum topics should be imported, but this enable partial import and testing.
        $query->join('node','dn','dn.nid = n.nid and dn.type = \'forum\'');
        $query->orderBy('c.cid');
        if(count($range)==2)
        {
            $query->range($range[0],$range[1]);
        }
        $result = $query->execute();
        $rows = array();
        while ($row = $result->fetchAssoc()) {
            $rows[$row['cid']] = $row['cid'];
        }
        return $rows;
    }

    public function getSnippetRows($range = array())
    {
        $query = \Drupal::database()->select('main_node', 'n');
        $query->fields('n', ['nid']);
        $query->condition('n.type',"code_snippet");
        $query->condition('n.status',1);
$query->condition("n.nid","8395",">");
        $query->orderBy('n.nid');
        if(count($range)==2)
        {
            $query->range($range[0],$range[1]);
        }
        $result = $query->execute();
        $rows = array();
        while ($row = $result->fetchAssoc()) {
            $rows[$row['nid']] = $row['nid'];
        }
        return $rows;
    }

    public function getSnippetCommentRows($range = array())
    {
        $query = \Drupal::database()->select('main_comments', 'c');
$query->condition("c.cid","16889",">");

        $query->join('main_node','n','n.nid = c.nid and n.type = \'code_snippet\' and n.status =1');
        $query->fields('c', ['cid']);
        //join the existing drupal node table, in theory all forum topics should be imported, but this enable partial import and testing.
        $query->join('node','dn','dn.nid = n.nid and dn.type = \'code_snippet\'');
        $query->orderBy('c.cid');
        if(count($range)==2)
        {
            $query->range($range[0],$range[1]);
        }
        $result = $query->execute();
        $rows = array();
        while ($row = $result->fetchAssoc()) {
            $rows[$row['cid']] = $row['cid'];
        }
        return $rows;
    }

    public function getProjectsRows($range = array())
    {
        $query = \Drupal::database()->select('main_project_projects', 'p');
$query->condition("p.nid","8395",">");
//$query->condition("p.nid","8197","=");

        $query->join('main_node','n','n.nid = p.nid and n.status =1');
        $query->fields('p', ['nid']);
        $query->orderBy('p.nid');
//$query->condition("n.nid","1385");
//$query->range(0,200);
//$query->orderBy('f.nid','desc');
        $result = $query->execute();
        $rows = array();
        while ($row = $result->fetchAssoc()) {
            $rows[$row['nid']] = $row['nid'];
        }
        return $rows;

    }
    public function getProjectCommentsRows($range = array())
    {
        $query = \Drupal::database()->select('main_comments', 'c');
$query->condition("c.cid","16889",">");

        $query->join('main_node','n','n.nid = c.nid and n.type = \'project_project\' and n.status =1');
        $query->fields('c', ['cid']);
        //join the existing drupal node table, in theory all forum topics should be imported, but this enable partial import and testing.
        $query->join('node','dn','dn.nid = n.nid and dn.type = \'project_project\'');
        $query->orderBy('c.cid');
        if(count($range)==2)
        {
            $query->range($range[0],$range[1]);
        }
        $result = $query->execute();
        $rows = array();
        while ($row = $result->fetchAssoc()) {
            $rows[$row['cid']] = $row['cid'];
        }
        return $rows;
    }

    public function getProjectReleasesRows($range = array())
    {
        $query = \Drupal::database()->select('main_project_release_nodes', 'r');
$query->condition("r.nid","8395",">");

        $query->join('node','dn','dn.nid = r.pid and dn.type = \'project_project\'');
        $query->join('main_node','n','n.nid = r.nid and n.type = \'project_release\' and n.status=1');
        $query->fields('r', ['nid']);
        //join the existing drupal node table, in theory all forum topics should be imported, but this enable partial import and testing.
        $query->orderBy('r.nid');
        if(count($range)==2)
        {
            $query->range($range[0],$range[1]);
        }
        $result = $query->execute();
        $rows = array();
        while ($row = $result->fetchAssoc()) {
            $rows[$row['nid']] = $row['nid'];
        }
        return $rows;
    }

    private function getD8Nodes($node_type){
        $nids = \Drupal::entityQuery('node')->condition('type',$node_type)->execute();
        return $nids;
    }

    private function getD8Users(){
        $uids = \Drupal::entityQuery('user')->execute();
        return $uids;
    }
    private function getD8Comments($comment_type){
        $cids = \Drupal::entityQuery('comment')->condition('comment_type',$comment_type)->execute();
        return $cids;
    }
    private function getD8CommentsNoClosingIgor(){
            $cids = \Drupal::entityQuery('comment')->condition('comment_body.value','%<igor>%', 'like')->condition('comment_body.value','%</igor>%', 'not like')->execute();
            return $cids;
    }

    private function getD8NodesNoClosingIgor(){
        $nids = \Drupal::entityQuery('node')->condition('type',array('code_snippet','forum'),"IN")->condition('body.value','%<igor>%', 'like')->condition('body.value','%</igor>%', 'not like')->execute();
        return $nids;
    }

    public function getUserDetails($uid)
    {
        $query = \Drupal::database()->select('main_users', 'm');
        $query->leftjoin('main_profile_values','p','p.uid = m.uid');
        $query->leftjoin('main_profile_fields','f','f.fid = p.fid');
        $query->fields('m', ['uid','name','mail','created','timezone','picture','signature']);
        $query->addField('p','value');
        $query->addField('f','name','field_name');
        $query->condition('m.uid',$uid);
        $result = $query->execute();
        $this->scraper_inprocess_item = array();
        while ($row = $result->fetchAssoc()) {
            //These are always the same in each pass, but whatever
            $this->scraper_inprocess_item['old_uid'] = $row['uid'];
            $this->scraper_inprocess_item['name'] = $row['name'];
            $this->scraper_inprocess_item['mail'] = $row['mail'];
            $this->scraper_inprocess_item['created'] = $row['created'];
            $this->scraper_inprocess_item['signature'] = $row['signature'];
            $this->scraper_inprocess_item['timezone'] = $row['timezone'];
            //add the image
            $this->scraper_inprocess_item['images'][$row['picture']]['src'] = $row['picture'];
            //The dynamic value
            $this->scraper_inprocess_item[$row['field_name']] = $row['value'];

        }
        //add as completed.
        $this->scraped_item = $this->scraper_inprocess_item;
    }

    public function getTaxonomyDetails($tid)
    {
        $query = \Drupal::database()->select('main_term_data', 't');
        $query->fields('t', ['tid','name','description','weight','vid']);
        $query->join('main_term_hierarchy','h','h.tid = t.tid');
        $query->fields('h', ['parent']);
        $query->condition('t.tid', $tid);
        $result = $query->execute();
        $this->scraper_inprocess_item = array();
        while ($row = $result->fetchAssoc()) {
            //These are always the same in each pass, but whatever
            $this->scraper_inprocess_item['old_tid'] = $row['tid'];
            $this->scraper_inprocess_item['name'] = $row['name'];
            $this->scraper_inprocess_item['description'] = $row['description'];
            $this->scraper_inprocess_item['weight'] = $row['weight'];
            $this->scraper_inprocess_item['parent'] = $row['parent'];
            $this->scraper_inprocess_item['old_vid'] = $row['vid'];
        }
        //add as completed.
        $this->scraped_item = $this->scraper_inprocess_item;
    }

    public function getForums($fid)
    {
        $query = \Drupal::database()->select('main_forum', 'f');
        $query->join('main_node','n','n.nid = f.nid AND n.vid = f.vid');
        $query->join('main_node_revisions','r','r.nid = f.nid AND r.vid = f.vid');
        $query->leftjoin('main_node_counter','c','c.nid = f.nid');
        $query->fields('f', ['nid','vid','tid']);
        $query->fields('n', ['uid','created','changed']);
        $query->fields('r', ['title','body','teaser']);
        $query->fields('c', ['totalcount']);
        $query->condition('f.nid', $fid);
        $result = $query->execute();
        $this->scraper_inprocess_item = array();
        while ($row = $result->fetchAssoc()) {
            $this->scraper_inprocess_item['old_nid'] = $row['nid'];
            $this->scraper_inprocess_item['old_vid'] = $row['vid'];
            $this->scraper_inprocess_item['old_tid'] = $row['tid'];
            $this->scraper_inprocess_item['count'] = $row['totalcount'];
            $this->scraper_inprocess_item['title'] = $row['title'];
            $this->scraper_inprocess_item['body'] = $row['body'];
            $this->scraper_inprocess_item['teaser'] = $row['teaser'];
            $this->scraper_inprocess_item['uid'] = $row['uid'];
            $this->scraper_inprocess_item['created'] = $row['created'];
            $this->scraper_inprocess_item['changed'] = $row['changed'];
        }
        //See if there are images.
        $query = \Drupal::database()->select('main_image_attach', 'i');
        $query->condition('i.nid', $this->scraper_inprocess_item['old_nid']);
        //join main_node to ensure the image is still enabled.
        $query->join('main_node','n','n.nid = i.iid');
        $query->condition('n.status', 1);
        //join revisions to get the image desciption (body, etc)
        $query->join('main_node_revisions','r','r.nid = n.nid AND r.vid = n.vid');
        $query->fields('r', ['title','body','teaser']);
        //join files to get the file path, etc.
        $query->join('main_files','f','f.nid = i.iid');
        $query->fields('f', ['filename','filepath']);
        //Run the query
        $result = $query->execute();
        while ($row = $result->fetchAssoc()) {
            $this->scraper_inprocess_item['image']['title'] = $row['title'];
            $this->scraper_inprocess_item['image']['body'] = $row['body'];
            $this->scraper_inprocess_item['image']['teaser'] = $row['teaser'];
            $this->scraper_inprocess_item['image']['filename'] = $row['filename'];
            $this->scraper_inprocess_item['image']['src'] =  $this->scraper_file_path.$row['filepath'];
        }

        //See if there are attachments.
        $query = \Drupal::database()->select('main_files', 'f');
        $query->condition('f.nid', $this->scraper_inprocess_item['old_nid']);
        $query->fields('f', ['filename','filepath','fid']);
        //join main_node to get the version id
        $query->join('main_node','n','n.nid = f.nid');
        //join revisions to get the image desciption (body, etc)
        $query->join('main_file_revisions','r','r.fid = f.fid and r.vid = n.vid');
        $query->fields('r', ['description']);
        //run the query
        $result = $query->execute();
        while ($row = $result->fetchAssoc()) {
            $this->scraper_inprocess_item['files'][$row['fid']]['description'] = $row['description'];
            $this->scraper_inprocess_item['files'][$row['fid']]['filename'] = $row['filename'];
            $this->scraper_inprocess_item['files'][$row['fid']]['src'] =  $this->scraper_file_path.$row['filepath'];
        }
        //add as completed.
        $this->scraped_item = $this->scraper_inprocess_item;
    }

    public function getForumComments($cid)
    {
        $query = \Drupal::database()->select('main_comments', 'c');
        $query->fields('c', ['cid','nid','uid','subject','comment','timestamp']);
        $query->condition('c.cid', $cid);
        $query->join('main_igorexchange_comments','i','i.cid = c.cid');
        $query->fields('i', ['updated']);
        $result = $query->execute();
        $this->scraper_inprocess_item = array();
        while ($row = $result->fetchAssoc()) {
            $this->scraper_inprocess_item['parent_nid'] = $row['nid'];
            $this->scraper_inprocess_item['old_cid'] = $row['cid'];
            $this->scraper_inprocess_item['body'] = $row['comment'];
            $this->scraper_inprocess_item['subject'] = $row['subject'];
            $this->scraper_inprocess_item['uid'] = $row['uid'];
            $this->scraper_inprocess_item['created'] = $row['timestamp'];
            $this->scraper_inprocess_item['changed'] = $row['updated'];
        }

        //See if there are attachments.
        $query = \Drupal::database()->select('main_comment_upload_files', 'f');
        $query->condition('f.nid', $this->scraper_inprocess_item['parent_nid']);
        $query->condition('f.cid', $this->scraper_inprocess_item['old_cid']);
        $query->fields('f', ['filename','filepath','description','fid']);
        //run the query
        $result = $query->execute();
        while ($row = $result->fetchAssoc()) {
            $this->scraper_inprocess_item['files'][$row['fid']]['description'] = $row['description'];
            $this->scraper_inprocess_item['files'][$row['fid']]['filename'] = $row['filename'];
            $this->scraper_inprocess_item['files'][$row['fid']]['src'] =  $this->scraper_file_path.$row['filepath'];
        }
        //add as completed.
        $this->scraped_item = $this->scraper_inprocess_item;
    }

    public function getProjects($pid)
    {
        #TO-DO Project Type and OS Category
        //get this from main_term_data
        $query = \Drupal::database()->select('main_project_projects', 'p');
        $query->join('main_node_revisions','r','r.nid = p.nid');
        $query->join('main_node','n','n.nid = p.nid');
        $query->fields('p', ['nid','homepage','uri','changelog','cvs','demo','documentation','screenshots','license']);
        $query->fields('n', ['uid','created','changed']);
        $query->fields('r', ['title','body','teaser']);
        $query->condition('p.nid', $pid);
        $result = $query->execute();
        $this->scraper_inprocess_item = array();
        while ($row = $result->fetchAssoc()) {
            $this->scraper_inprocess_item['old_nid'] = $row['nid'];
            $this->scraper_inprocess_item['uid'] = $row['uid'];
            $this->scraper_inprocess_item['title'] = $row['title'];
            $this->scraper_inprocess_item['body'] = $row['body'];
            $this->scraper_inprocess_item['teaser'] = $row['teaser'];
            $this->scraper_inprocess_item['created'] = $row['created'];
            $this->scraper_inprocess_item['changed'] = $row['changed'];
            $this->scraper_inprocess_item['homepage'] = $row['homepage'];
            $this->scraper_inprocess_item['uri'] = $row['uri'];
            $this->scraper_inprocess_item['changelog'] = $row['changelog'];
            $this->scraper_inprocess_item['cvs'] = $row['cvs'];
            $this->scraper_inprocess_item['demo'] = $row['demo'];
            $this->scraper_inprocess_item['documentation'] = $row['documentation'];
            $this->scraper_inprocess_item['screenshots'] = $row['screenshots'];
            $this->scraper_inprocess_item['license'] = $row['license'];
        }
        //See if there are images.
        $query = \Drupal::database()->select('main_image_attach', 'i');
        $query->condition('i.nid', $this->scraper_inprocess_item['old_nid']);
        //join main_node to ensure the image is still enabled.
        $query->join('main_node','n','n.nid = i.iid');
        $query->condition('n.status', 1);
        //join revisions to get the image desciption (body, etc)
        $query->join('main_node_revisions','r','r.nid = n.nid AND r.vid = n.vid');
        $query->fields('r', ['title','body','teaser']);
        //join files to get the file path, etc.
        $query->join('main_files','f','f.nid = i.iid');
        $query->fields('f', ['filename','filepath']);
        //Run the query
        $result = $query->execute();
        while ($row = $result->fetchAssoc()) {
            $this->scraper_inprocess_item['image']['title'] = $row['title'];
            $this->scraper_inprocess_item['image']['body'] = $row['body'];
            $this->scraper_inprocess_item['image']['teaser'] = $row['teaser'];
            $this->scraper_inprocess_item['image']['filename'] = $row['filename'];
            $this->scraper_inprocess_item['image']['src'] =  $this->scraper_file_path.$row['filepath'];
        }

        //get the TIDs for project type (1) and os supported versions (4)
        $query = \Drupal::database()->select('main_term_node', 't');
        $query->condition('t.nid', $this->scraper_inprocess_item['old_nid']);
        $query->fields('t', ['tid']);
        //join the main term data to know the vocab
        $query->join('main_term_data','d','d.tid = t.tid');
        $query->fields('d', ['vid']);
        //run the query
        $result = $query->execute();
        while ($row = $result->fetchAssoc()) {
            switch($row['vid'])
            {
                case"1":
                    //Don't include the parent
                    if($row['tid']!=23){
                        $this->scraper_inprocess_item['project_type_tid'][] = $row['tid'];
                    }
                    break;
                case"4":
                    $this->scraper_inprocess_item['os_tid'][] = $row['tid'];
                    break;
            }

        }

        //add as completed.
        $this->scraped_item = $this->scraper_inprocess_item;
    }

    public function getProjectComments($cid)
    {
        $query = \Drupal::database()->select('main_comments', 'c');
        $query->fields('c', ['cid','nid','uid','subject','comment','timestamp']);
        $query->condition('c.cid', $cid);
        $result = $query->execute();
        $this->scraper_inprocess_item = array();
        while ($row = $result->fetchAssoc()) {
            $this->scraper_inprocess_item['parent_nid'] = $row['nid'];
            $this->scraper_inprocess_item['old_cid'] = $row['cid'];
            $this->scraper_inprocess_item['body'] = $row['comment'];
            $this->scraper_inprocess_item['subject'] = $row['subject'];
            $this->scraper_inprocess_item['uid'] = $row['uid'];
            $this->scraper_inprocess_item['created'] = $row['timestamp'];
            $this->scraper_inprocess_item['changed'] = $row['timestamp'];
        }

        //See if there are attachments.
        $query = \Drupal::database()->select('main_comment_upload_files', 'f');
        $query->condition('f.nid', $this->scraper_inprocess_item['parent_nid']);
        $query->condition('f.cid', $this->scraper_inprocess_item['old_cid']);
        $query->fields('f', ['filename','filepath','description','fid']);
        //run the query
        $result = $query->execute();
        while ($row = $result->fetchAssoc()) {
            $this->scraper_inprocess_item['files'][$row['fid']]['description'] = $row['description'];
            $this->scraper_inprocess_item['files'][$row['fid']]['filename'] = $row['filename'];
            $this->scraper_inprocess_item['files'][$row['fid']]['src'] =  $this->scraper_file_path.$row['filepath'];
        }
        //add as completed.
        $this->scraped_item = $this->scraper_inprocess_item;
    }

    public function getProjectReleases($rid)
    {
        $query = \Drupal::database()->select('main_project_release_nodes', 'r');
        $query->fields('r', ['pid','nid','version','version_extra','file_path','version_major','version_patch','file_hash','file_date']);
        $query->join('main_node','n','n.nid = r.nid');
        $query->fields('n', ['title']);
        $query->join('main_node_revisions','b','b.nid = r.nid AND b.vid = n.vid');
        $query->fields('b', ['title','body','teaser']);
        $query->condition('r.nid', $rid);
        $result = $query->execute();
        $this->scraper_inprocess_item = array();
        while ($row = $result->fetchAssoc()) {
            $this->scraper_inprocess_item['parent_nid'] = $row['pid'];
            $this->scraper_inprocess_item['old_nid'] = $row['nid'];
            $this->scraper_inprocess_item['title'] = $row['title'];
            $this->scraper_inprocess_item['body'] = $row['body'];
            $this->scraper_inprocess_item['teaser'] = $row['teaser'];
            $this->scraper_inprocess_item['version'] = $row['version'];
            $this->scraper_inprocess_item['version_extra'] = $row['version_extra'];
            $this->scraper_inprocess_item['filepath'] = $row['file_path'];
            $this->scraper_inprocess_item['version_major'] = $row['version_major'];
            $this->scraper_inprocess_item['version_patch'] = $row['version_patch'];
            $this->scraper_inprocess_item['version_md5'] = $row['file_hash'];
            $this->scraper_inprocess_item['version_date'] = $row['file_date'];
        }
        //the release file
        if ($this->scraper_inprocess_item['filepath']!="") {
            $this->scraper_inprocess_item['files'][0]['src'] =  $this->scraper_file_path.$this->scraper_inprocess_item['filepath'];
        }
        //get the TIDs for os supported versions (4) and igor version 2
        $query = \Drupal::database()->select('main_term_node', 't');
        $query->condition('t.nid', $this->scraper_inprocess_item['old_nid']);
        $query->fields('t', ['tid']);
        //join the main term data to know the vocab
        $query->join('main_term_data','d','d.tid = t.tid');
        $query->fields('d', ['vid']);
        //run the query
        $result = $query->execute();
        while ($row = $result->fetchAssoc()) {
            switch($row['vid'])
            {
                case"2":
                    //I now believe this is project data not release data.
//                    $this->scraper_inprocess_item['supported_igor_tid'][] = $row['tid'];
                    break;
                case"4":
                    $this->scraper_inprocess_item['os_tid'][] = $row['tid'];
                    break;
            }

        }
        //add as completed.
        $this->scraped_item = $this->scraper_inprocess_item;
    }

    public function getProjectVersions($pid)
    {
        $query = \Drupal::database()->select('main_project_release_supported_versions', 'v');
        $query->fields('v', ['tid']);
        $query->condition('v.nid', $pid);
        $result = $query->execute();
        $this->scraper_inprocess_item = array();
        while ($row = $result->fetchAssoc()) {
            $this->scraper_inprocess_item['pid'] = $pid;
            $this->scraper_inprocess_item['old_nid'] = $pid;
            $this->scraper_inprocess_item['tids'][] = $row['tid'];
        }
        //add as completed.
        $this->scraped_item = $this->scraper_inprocess_item;
    }

    public function getSnippetDetails($nid)
    {
        $query = \Drupal::database()->select('main_node', 'n');
        $query->join('main_node_revisions','r','r.nid = n.nid AND r.vid = n.vid');
        $query->leftjoin('main_node_counter','c','c.nid = n.nid');
        $query->fields('n', ['nid','vid','uid','created','changed']);
        $query->fields('r', ['title','body','teaser']);
        $query->fields('c', ['totalcount']);
        $query->condition('n.nid', $nid);
        $result = $query->execute();
        $this->scraper_inprocess_item = array();
        while ($row = $result->fetchAssoc()) {
            $this->scraper_inprocess_item['old_nid'] = $row['nid'];
            $this->scraper_inprocess_item['old_vid'] = $row['vid'];
            $this->scraper_inprocess_item['count'] = $row['totalcount'];
            $this->scraper_inprocess_item['title'] = $row['title'];
            $this->scraper_inprocess_item['body'] = $row['body'];
            $this->scraper_inprocess_item['teaser'] = $row['teaser'];
            $this->scraper_inprocess_item['uid'] = $row['uid'];
            $this->scraper_inprocess_item['created'] = $row['created'];
            $this->scraper_inprocess_item['changed'] = $row['changed'];
        }
        //See if there are images.
        $query = \Drupal::database()->select('main_image_attach', 'i');
        $query->condition('i.nid', $this->scraper_inprocess_item['old_nid']);
        //join main_node to ensure the image is still enabled.
        $query->join('main_node','n','n.nid = i.iid');
        $query->condition('n.status', 1);
        //join revisions to get the image desciption (body, etc)
        $query->join('main_node_revisions','r','r.nid = n.nid AND r.vid = n.vid');
        $query->fields('r', ['title','body','teaser']);
        //join files to get the file path, etc.
        $query->join('main_files','f','f.nid = i.iid');
        $query->fields('f', ['filename','filepath']);
        //Run the query
        $result = $query->execute();
        while ($row = $result->fetchAssoc()) {
            $this->scraper_inprocess_item['image']['title'] = $row['title'];
            $this->scraper_inprocess_item['image']['body'] = $row['body'];
            $this->scraper_inprocess_item['image']['teaser'] = $row['teaser'];
            $this->scraper_inprocess_item['image']['filename'] = $row['filename'];
            $this->scraper_inprocess_item['image']['src'] =  $this->scraper_file_path.$row['filepath'];
        }

        //See if there are attachments.
        $query = \Drupal::database()->select('main_files', 'f');
        $query->condition('f.nid', $this->scraper_inprocess_item['old_nid']);
        $query->fields('f', ['filename','filepath','fid']);
        //join main_node to get the version id
        $query->join('main_node','n','n.nid = f.nid');
        //join revisions to get the image desciption (body, etc)
        $query->join('main_file_revisions','r','r.fid = f.fid and r.vid = n.vid');
        $query->fields('r', ['description']);
        //run the query
        $result = $query->execute();
        while ($row = $result->fetchAssoc()) {
            $this->scraper_inprocess_item['files'][$row['fid']]['description'] = $row['description'];
            $this->scraper_inprocess_item['files'][$row['fid']]['filename'] = $row['filename'];
            $this->scraper_inprocess_item['files'][$row['fid']]['src'] =  $this->scraper_file_path.$row['filepath'];
        }
        //add as completed.
        $this->scraped_item = $this->scraper_inprocess_item;

        //get the TIDs for code type and igor supported versions
        //See if there are attachments.
        $query = \Drupal::database()->select('main_term_node', 't');
        $query->condition('t.nid', $this->scraper_inprocess_item['old_nid']);
        $query->fields('t', ['tid']);
        //join the main term data to know the vocab
        $query->join('main_term_data','d','d.tid = t.tid');
        $query->fields('d', ['vid']);
        //run the query
        $result = $query->execute();
        while ($row = $result->fetchAssoc()) {
            switch($row['vid'])
            {
                case"6":
                    $this->scraper_inprocess_item['tid_type'][] = $row['tid'];
                    break;
                case"2":
                    $this->scraper_inprocess_item['tid_igor'] = $row['tid'];
                    break;
            }

        }
        //add as completed.
        $this->scraped_item = $this->scraper_inprocess_item;
    }

    public function getSnippetComments($cid)
    {
        $query = \Drupal::database()->select('main_comments', 'c');
        $query->fields('c', ['cid','nid','uid','subject','comment','timestamp']);
        $query->condition('c.cid', $cid);
        $result = $query->execute();
        $this->scraper_inprocess_item = array();
        while ($row = $result->fetchAssoc()) {
            $this->scraper_inprocess_item['parent_nid'] = $row['nid'];
            $this->scraper_inprocess_item['old_cid'] = $row['cid'];
            $this->scraper_inprocess_item['body'] = $row['comment'];
            $this->scraper_inprocess_item['subject'] = $row['subject'];
            $this->scraper_inprocess_item['uid'] = $row['uid'];
            $this->scraper_inprocess_item['created'] = $row['timestamp'];
            $this->scraper_inprocess_item['changed'] = $row['timestamp'];
        }

        //See if there are attachments.
        $query = \Drupal::database()->select('main_comment_upload_files', 'f');
        $query->condition('f.nid', $this->scraper_inprocess_item['parent_nid']);
        $query->condition('f.cid', $this->scraper_inprocess_item['old_cid']);
        $query->fields('f', ['filename','filepath','description','fid']);
        //run the query
        $result = $query->execute();
        while ($row = $result->fetchAssoc()) {
            $this->scraper_inprocess_item['files'][$row['fid']]['description'] = $row['description'];
            $this->scraper_inprocess_item['files'][$row['fid']]['filename'] = $row['filename'];
            $this->scraper_inprocess_item['files'][$row['fid']]['src'] =  $this->scraper_file_path.$row['filepath'];
        }
        //add as completed.
        $this->scraped_item = $this->scraper_inprocess_item;
    }


    public function getNodeDetails($nid){
        $this->scraped_item =  \Drupal\node\Entity\Node::load($nid);
    }

    public function getD8UserDetails($uid){
        $this->scraped_item =  \Drupal\user\Entity\User::load($uid);
    }

    public function getCommentDetails($cid){
        $this->scraped_item =  \Drupal\comment\Entity\Comment::load($cid);
    }

    private function processLinks()
    {

        //some more details of the path
        $pathinfo = pathinfo($this->scraper_server_path .$this->scraper_inprocess_item['path']);

        //Load as DOM to easily manipulate. We can't use LIBXML_HTML_NOIMPLIED as WM HTML is not valid. Hence we will have to manually remove the tags.
        $dom = new \DomDocument;
        $dom->loadHTML($this->scraper_inprocess_item['content'], LIBXML_HTML_NODEFDTD);

        //Find each link,
        $links = $dom->getElementsByTagName('a');
        //Iterate over the extracted links and display their URLs
        foreach ($links as $link) {
            //Extract and show the "href" attribute.
            $anchorTxt = $link->nodeValue;
            $href = $link->getAttribute('href');
            //if its an internal page or resource repace with a standardized full relative path (From webroot)
            if (substr($href, 0, 4) != "http" && substr($href, 0, 7) != "mailto:" && substr($href, 0, 1) != "#") {
                //if the link is to an internal doc not in this dir we will assume it is a relative path from the docroot (it could just be a parent. We hope not.)
                if (substr($href, 0, 2) == "..") {
                    //strip the leading relative dots
                    $href = ltrim($href, "/.");
                    //add the current script path.
                    $href = $href;
                } else {
                    //add the current script path.
                    $href = $pathinfo["dirname"] . '/' . $href;
                }
                //replace the entire link with a simple and standardized link.
                $new_link = $dom->createElement('a');
                $new_link->setAttribute('href', $href);
                $new_link->nodeValue = $anchorTxt;
                if($link->hasChildNodes()){
                    $children = [];
                    foreach ($link->childNodes as $child) {
                        $children[] = $child;
                    }
                    foreach ($children as $child) {
                        $new_link->appendChild($child);
                    }
                }
                $link->parentNode->replaceChild($new_link, $link);
                //if its not an internal page we will assume its a resource and add it to the media array.
                //originally we just checked if the file ended in htm. But some files end in .htm#someAnchorTextThatAlsoHas.periods
                //so now isolate the last part of the path and look for the first three laters after the first period. We assume this is an entension.
                //Its possible a File is named some.name.pdf which would fail to be registered as a file and create a broken link.
                $isAResource = false;
                $dirs = explode("/", $href);
                if (count($dirs) > 1) {
                    $last_path_part = array_pop($dirs);
                } //if there were no slashes, then this is the last path part.
                else {
                    $last_path_part = $href;
                }

                $file_parts = explode(".", $last_path_part);
                if (count($file_parts) > 1) {
                    if (substr($file_parts[1], 0, 3) != "htm") {
                        $this->scraper_inprocess_item['media'][$href] = $href;
                    }
                }
            }
        }
        //convert back to HTML string from DOM.
        //because the HTML was not valid we could not use LIBXML_HTML_NOIMPLIED
        //remove the opening <html><body>  and closing </body></html>
        $this->scraper_inprocess_item['content'] = substr(substr($dom->saveHTML(), 12), 0, -15);

    }

    private function processImages()
    {

        //some more details of the path
        $pathinfo = pathinfo($this->scraper_server_path . $this->scraper_inprocess_item['path']);

        //Load as DOM to easily manipulate.
        $dom = new \DomDocument;
        $dom->loadHTML($this->scraper_inprocess_item['content'], LIBXML_HTML_NODEFDTD);
        //Find each img, return in the meda array. Strip all classes
        $images = $dom->getElementsByTagName('img');
        //Iterate over the extracted links and display their URLs
        foreach ($images as $image) {
            //Extract and show the "href" attribute.
            $src = $image->getAttribute('src');
            $width = $image->getAttribute('width');
            $height = $image->getAttribute('height');
            $imgTitle = $image->getAttribute('title');
            $imgAlt = $image->getAttribute('alt');
            //pull a little more info
            $imgpathinfo = pathinfo($src);
            //if its an internal page or resource repace with a standardized full relative path (From webroot)
            if (substr($src, 0, 4) != "http" && substr($src, 0, 5) != "data:") {
                //if the link is to an internal doc not in this dir we will assume it is a relative path from the docroot (it could just be a parent. We hope not.)
                if (substr($src, 0, 2) == "..") {
                    //strip the leading relative dots
                    $src = ltrim($src, "/.");
                    //add the current script path.
                    $src = $src;
                } else {
                    //add the current script path.
                    $src = $pathinfo["dirname"] . '/' . $src;
                }
                //replace the entire link with a simple and standardized link.
                $new_image = $dom->createElement('img');
                $new_image->setAttribute('src', "/" . $src);
                $new_image->setAttribute('width', $width);
                $new_image->setAttribute('height', $height);
                $new_image->setAttribute('title', $imgTitle);
                $new_image->setAttribute('alt', $imgAlt);
                $image->parentNode->replaceChild($new_image, $image);
                //if its not an internal page we will assume its a resource and add it to the media array.
                $this->scraper_inprocess_item['images'][$src]['src'] = $src;
                $this->scraper_inprocess_item['images'][$src]['title'] = $imgTitle;
                $this->scraper_inprocess_item['images'][$src]['alt'] = $imgAlt;
            }
        }
        //convert back to HTML string from DOM.
        //because the HTML was not valid we could not use LIBXML_HTML_NOIMPLIED
        //remove the opening <html><body>  and closing </body></html>
        $this->scraper_inprocess_item['content'] = substr(substr($dom->saveHTML(), 12), 0, -15);
    }


}
