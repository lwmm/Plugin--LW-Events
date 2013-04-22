<?php

/**
 * Generating html output of news list.
 * 
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package lw_events
 */

namespace LwEvents\Views;

class Archive
{

    /**
     * The html template will be rendered and return.
     * 
     * @param array $data
     * @param bool $admin
     * @return string
     */
    public function render($data, $admin = false)
    {
        $language = $data["lang"];
        $years = $data["uniqueYears"];


        $view = new \lw_view(dirname(__FILE__) . '/Templates/EventsListArchived.phtml');
        $view->admin = $admin;
        $view->baseUrl = \LwEvents\Services\Page::getUrl()."&oid=".$data["oid"];
        $view->baseUrlWithoutIndex = substr(\LwEvents\Services\Page::getUrl(), 0, strpos(\LwEvents\Services\Page::getUrl(), "index=") + strlen("index="));
        $view->years = $years;
        $view->selectedYear = $data["selectedYear"];

        if (array_key_exists("entries", $data)) {
            $view->data = $this->addLogoUrlToDataArrayIfExists($data["entries"], $data["upload_path"], $data["upload_url"]);
        }
        else {
            $view->data = "";
        }

        $view->lang = $language;

        return $view->render();
    }
    
     private function addLogoUrlToDataArrayIfExists($data, $uploadpath, $uploadurl)
    {
        $uploadDir = \lw_directory::getInstance($uploadpath);
        $files = $uploadDir->getDirectoryContents("file");
        
        $idArray = array();
        
        foreach($files as $file){
            $name = $file->getName();
            $explodeName = explode(".", $name);
            $nameWithoutExtention = $explodeName[0];
        
            $id = str_replace("events_logo_", "", $nameWithoutExtention);
            $idArray[$id] = ".".$explodeName[1];
        }
        
        $i = 0;
        foreach($data as $entry){
            if(array_key_exists($entry["id"], $idArray)) {
                #die("TRUE");
                $data[$i]["logoUrl"] = $uploadurl."events_logo_".$entry["id"].$idArray[$entry["id"]];
            }
            $i++;
        }
        
     return $data;   
    }

}