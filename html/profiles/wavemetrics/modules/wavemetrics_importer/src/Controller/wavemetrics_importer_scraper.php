<?php
/**
 * @file
 * Contains Drupal\wavemetrics_importer\Controller\wavemetrics_importer_scraper.
 */

namespace Drupal\wavemetrics_importer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

class wavemetrics_importer_scraper extends ControllerBase
{

    public $scraper_server_path;
    public $scraper_import_path;
    public $scraper_items;
    public $scraped_item;
    public $scraper_inprocess_item;

    public function __construct($import_type)
    {
        $this->import_type = $import_type;
        $this->scraper_server_path = "/home/wavemetrics/wm-build/";
        $this->scraper_import_type_path = $this->getImportPath($import_type);
        $this->scraper_import_path = $this->scraper_server_path . $this->scraper_import_type_path;
        $this->scraper_items = $this->getPathsToScrape();
        $this->scraped_item = array();
        $this->scraper_inprocess_item = array(
            "title" => "",
            "content" => "",
            "media" => array(),
            "images" => array(),
            "path" => "",
            "menu_link" => "",
            "mid" => "",
            "parent_menu" => "",
            "seo_title" => "",
            "gallery_type" => "",
        );
    }

    public function getImportPath($filter_type)
    {
        switch ($filter_type) {
            default:
                $importPath = "";
                break;
            case"basic_page":
                $importPath = "";
                break;
            case"news":
                $importPath = "news/";
                break;
            case"basic_page":
                $importPath = "";
                break;
            case"menu_products":
                $importPath = "";
                break;
            case"gallerypost":
                $importPath = "products/igorpro/gallery/";
                break;
            case"casestudies":
                $importPath = "products/igorpro/gallery/casestudies/";
                break;
        }
        return $importPath;
    }

    public function getPathsToScrape()
    {
        $start_dir = $this->scraper_import_path;
        //Excluded files regardless of path/location
        $exclude_files = array(
            'aa_header.txt',
            'aa_footer.txt',
            'aa_navlist.txt',
        );
        $limit_to_files = array();
        $exclude_paths[] = "/css";
        $exclude_paths[] = "/images";
        $exclude_paths[] = "/order";
        $exclude_paths[] = "/outlier";
        $exclude_paths[] = "/Pics";
        $exclude_paths[] = "/images";
        $exclude_paths[] = "/register";
        $exclude_paths[] = "/search";
        //importer type depenedent settings.

        switch ($this->import_type) {
            case"basic_page":
                //$exclude_files[] = ""; //not extra excludes
                $exclude_paths[] = "/products/igorpro/gallery.txt";
                $exclude_paths[] = "/news/";
                $exclude_paths[] = "/products/igorpro/gallery/";
                break;
            case"news":
                break;
            case"menu_products":
                unset($exclude_files[array_search("aa_navlist.txt",$exclude_files)]);
                $exclude_paths[] = "/_tools";
                $exclude_paths[] = "aa_navlist.txt";
                $exclude_paths[] = "/news";
                $exclude_paths[] = "/support";
                $exclude_paths[] = "/Updates";
                $exclude_paths[] = "/users";
                $limit_to_files[] = "aa_navlist.txt";
                break;
            case"gallerypost":
                $exclude_files[] = "user_tyminski.txt";
                $exclude_files[] = "user_fitzpatrick.txt";
                $exclude_files[] = "user_perney.txt";
                $exclude_files[] = "user_lizard.txt";
                $exclude_files[] = "user_pwright.txt";
                $exclude_files[] = "3dsamps_kuhn.txt";
                $exclude_files[] = "3dsamps_Minoofar.txt";
                $exclude_files[] = "interface_lelievre.txt";
                $exclude_paths[] = "/casestudies/";
                break;
            case"casestudies":
                break;
        }
        return $this->findFiles($start_dir, $exclude_files, $exclude_paths,$limit_to_files);
    }

    private function findFiles($dir, $exclude_files, $exclude_paths,$limit_to_files)
    {
        $all_files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        $files = new \RegexIterator($all_files, '/\.txt$/');
        $list = array();
        foreach ($files as $file) {
            //cycle through all the excluded paths to see if a substr is matched.
            $exclued = false;
            //ensure this directory was not excluded
            foreach ($exclude_paths as $exclude_path) {
                if (substr($files->getSubPathname(), 0, strlen(substr($exclude_path, 1))) == substr($exclude_path, 1)) {
                    $exclued = true;
                }
            }
            //ensure this file was not excluded
            if (in_array(basename($file), $exclude_files)) {
                $exclued = true;
            }
            //ensure this file was explicetly included if files are limited
            if (count($limit_to_files)>0 && !in_array(basename($file), $limit_to_files)) {
                $exclued = true;
            }
            //Only import if not excluded
            if ($exclued == false) {
                $list[$files->getSubPathname()] = $files->getSubPathname();
            }
        }
        return $list;
    }

//we may want a scrapeItems method even though we don't plan to use it.

    public function scrapeItem($item_path)
    {

        //load this current path as in process
        $this->scraper_inprocess_item['path'] = $this->scraper_import_type_path . $item_path;

        //get any applicaple info from the nav file.
        $this->extractNavInfo();

        //scrape the main file.
        $this->scraper_inprocess_item['content'] = file_get_contents($this->scraper_import_path . $item_path);

        //process the main content.
        //Remove HTML Comments.
        $this->processRemoveHTMLCommments();

        //process path to links including internal resources
        $this->processLinks();

        //process images
        if($this->import_type=="gallerypost"){
            $this->processGalleryImage();
        }
        else {
            $this->processImages();
        }

        //process gallery type
        if($this->import_type=="gallerypost"){
            $this->processGalleryType();
        }

        //pull the title
        $this->extractTitle();

        //remove the link from all headers
        $this->removeLinksFromHeaders();

        //add as completed.
        $this->scraped_item = $this->scraper_inprocess_item;

    }

    public function scrapeMenuItem($item_path)
    {

        //load this current path as in process
        $this->scraper_inprocess_item['path'] = $this->scraper_import_type_path . $item_path;

        //scrape the main file.
        $this->scraper_inprocess_item['content'] = file_get_contents($this->scraper_import_path . $item_path);

        //add as completed.
        $this->scraped_item = $this->scraper_inprocess_item;

    }

    private function extractNavInfo()
    {
        $rowCount = 1;
        $infoFound = false;
        //find and load the nav file
        $pathinfo = pathinfo($this->scraper_inprocess_item['path']);
        //links are the file name with no extension
        $navFile = $this->scraper_import_path . '/aa_navlist.txt';
        //this file is a tab delimited file, so lets load it as such.
        if ( file_exists(($navFile) && $file = fopen("$navFile", "r")) !== FALSE) {
            while (($row = fgetcsv($file, 0, "\t")) !== FALSE) {
                //see if this is the row that matches this item.
                if ($row[1] == $pathinfo['filename']) {
                    $this->scraper_inprocess_item['menu_link'] = $row[0];
                    if (isset($row[3])) {
                        $this->scraper_inprocess_item['seo_title'] = $row[3];
                    }
                    $infoFound = true;
                }
            }
            fclose($file);
        }
        //if the info was not found we may find it in the parent nav.
        //only if there are parent directories.
        $dirs = explode("/", $pathinfo['dirname']);
        if ($infoFound == false && count($dirs) > 1) {
            array_pop($dirs);
            $dir = implode("/", $dirs);
            $rowCount = 1;
            //find and load the nav file
            $pathinfo = pathinfo($this->scraper_inprocess_item['path']);
            //links are the file name with no extension
            $navFile = $this->scraper_server_path  . $dir . '/aa_navlist.txt';
            //this file is a tab delimited file, so lets load it as such.
            if (($file = fopen("$navFile", "r")) !== FALSE) {
                while (($row = fgetcsv($file, 0, "\t")) !== FALSE) {
                    //see if this is the row that matches this item.
                    if ($row[1] == $pathinfo['filename']) {
                        $this->scraper_inprocess_item['menu_link'] = $row[0];
                        if (isset($row[3])) {
                            $this->scraper_inprocess_item['seo_title'] = $row[3];
                        }else{
                            $this->scraper_inprocess_item['seo_title'] = $row[0];
                        }
                        $infoFound = true;
                    }
                }
                fclose($file);
            }
        }
    }

    // Remove unwanted HTML comments
    private function processRemoveHTMLCommments()
    {
        $this->scraper_inprocess_item['content'] = preg_replace('/<!--(.|\s)*?-->/', '', $this->scraper_inprocess_item['content']);
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

    private function processGalleryImage()
    {
        $this->scraper_inprocess_item['gallery_image'] = array();
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
            $imgTitle = "";
            $imgAlt = "";
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
                $image->parentNode->removeChild($image);
                //if its not an internal page we will assume its a resource and add it to the media array.
                $this->scraper_inprocess_item['gallery_image'][$src]['src'] = $src;
                $this->scraper_inprocess_item['gallery_image'][$src]['title'] = $imgTitle;
                $this->scraper_inprocess_item['gallery_image'][$src]['alt'] = $imgAlt;
            }
        }
        //convert back to HTML string from DOM.
        //because the HTML was not valid we could not use LIBXML_HTML_NOIMPLIED
        //remove the opening <html><body>  and closing </body></html>
        $this->scraper_inprocess_item['content'] = substr(substr($dom->saveHTML(), 12), 0, -15);
    }


    private function processGalleryType()
    {
        //get the filename
        $pathinfo = pathinfo($this->scraper_inprocess_item['path']);
        $filename = $pathinfo['filename'];
        //if the filename starts with '3dsamps_' its a 3D Graph Sample (tid = 14)
        if(substr($filename,0,8)=="3dsamps_")
        {
            $this->scraper_inprocess_item['gallery_type'] = 14;
        }
        //if the filename starts with 'interface_' its a 3D Graph Sample (tid = 15)
        elseif(substr($filename,0,10)=="interface_")
        {
            $this->scraper_inprocess_item['gallery_type'] = 15;
        }
        //DEFAULT
        //These should start with "user_" but will also be the fallback. (tid = 13)
        else
        {
            $this->scraper_inprocess_item['gallery_type'] = 13;
        }
    }

    /**
     * Find the first occurance of an H# tag. Extract it to become the node title.
     */
    private function extractTitle()
    {
        //Load as DOM to easily manipulate.
        $dom = new \DomDocument;
        $dom->loadHTML($this->scraper_inprocess_item['content'], LIBXML_HTML_NODEFDTD);
        $i = 1;
        for ($this->scraper_inprocess_item['title'] = ""; $this->scraper_inprocess_item['title'] == ""; $i++) {
            $testHeader = "h" . $i;
            $headers = $dom->getElementsByTagName($testHeader);
            //Iterate over the extracted links and display their URLs
            foreach ($headers as $header) {
                $this->scraper_inprocess_item['title'] = $header->nodeValue;
                $header->parentNode->removeChild($header);
                break;
            }
            //There may not be a title. If we get to H10, lets stop and set the title to the filename.
            if ($i == 10) {
                $pathinfo = pathinfo($this->scraper_inprocess_item['path']);
                $this->scraper_inprocess_item['title'] = $pathinfo['filename'];
            }
        }
        //convert back to HTML string from DOM.
        //because the HTML was not valid we could not use LIBXML_HTML_NOIMPLIED
        //remove the opening <html><body>  and closing </body></html>
        //$this->scraper_inprocess_item['content'] = $dom->saveHTML();
        $this->scraper_inprocess_item['content'] = substr(substr($dom->saveHTML(), 12), 0, -15);

    }

    /**
     * Find the first occurance of an H# tag. Extract it to become the node title.
     */
    private function removeLinksFromHeaders()
    {
        //Load as DOM to easily manipulate.
        $dom = new \DomDocument;
        $dom->loadHTML($this->scraper_inprocess_item['content'], LIBXML_HTML_NODEFDTD);
        $i = 1;
        for ($i = 1; $i < 11; $i++) {
            $testHeader = "h" . $i;
            $headers = $dom->getElementsByTagName($testHeader);
            //Iterate over the extracted links and display their URLs
            foreach ($headers as $header) {
                $new_header = $dom->createElement($testHeader);
                $new_header->nodeValue = $header->nodeValue;
                $header->parentNode->replaceChild($new_header, $header);
            }
        }
        //convert back to HTML string from DOM.
        //because the HTML was not valid we could not use LIBXML_HTML_NOIMPLIED
        //remove the opening <html><body>  and closing </body></html>
        //$this->scraper_inprocess_item['content'] = $dom->saveHTML();
        $this->scraper_inprocess_item['content'] = substr(substr($dom->saveHTML(), 12), 0, -15);
    }
}
