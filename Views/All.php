<?php

/**
 * Generating html output of event list.
 * 
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package lw_events
 */

namespace LwEvents\Views;

class All
{

    /**
     * The html template will be rendered and return.
     * 
     * @param array $data
     * @param bool $admin
     * @return string
     */
    public function render($data, $admin = false, $baseUrl = false)
    {
        $language = $data["lang"];
        $oid = $data["oid"];
        $uploadpath = $data["upload_path"];
        $uploadurl = $data["upload_url"];
        unset($data["lang"]);
        unset($data["upload_url"]);
        unset($data["upload_path"]);
        unset($data["oid"]);

        
        $view = new \lw_view(dirname(__FILE__) . '/Templates/EventsList.phtml');
        $view->admin = $admin;
        #$view->baseUrl = \LwEvents\Services\Page::getUrl()."&oid=".$oid;
        $view->baseUrl = $baseUrl."&oid=".$oid;
        $view->baseUrlWithoutIndex = substr($baseUrl, 0, strpos($baseUrl, "index=") + strlen("index="));
        $view->data = $this->addLogoUrlToDataArrayIfExists($data, $uploadpath, $uploadurl);
        $view->lang = $language;
        $view->oid = $oid;

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
                $data[$i]["logoUrl"] = $uploadurl."events_logo_".$entry["id"].$idArray[$entry["id"]];
                $data[$i]["logoPath"] = $uploadpath."events_logo_".$entry["id"].$idArray[$entry["id"]];
            }
            $i++;
        }
        
     return $data;   
    }

}