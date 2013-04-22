<?php

/**
 * Generating html output for detailed news view.
 * 
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package lw_events
 */

namespace LwEvents\Views;

class Detail
{

    /**
     * The html template will be rendered and return. 
     * 
     * @param array $data
     * @return string
     */
    public function render($data)
    {
        $view = new \lw_view(dirname(__FILE__) . '/Templates/Detail.phtml');
        $view->data = $data["formData"];
        $view->lang = $data["lang"];
        $view->baseUrl = \LwEvents\Services\Page::getUrl()."&oid=".$data["oid"];
        $view->logoUrl = $this->getLogoUrl($data["formData"]["id"], $data["upload_path"], $data["upload_url"]);
        
        return $view->render();
    }
    
    private function getLogoUrl($id, $uploadpath, $uploadurl)
    {
        $filename = false;
        $uploadDir = \lw_directory::getInstance($uploadpath);
        $files = $uploadDir->getDirectoryContents("file");
        
        foreach($files as $file){
            $name = $file->getName();
            $explodeName = explode(".", $name);
            $nameWithoutExtention = $explodeName[0];
            
            if($nameWithoutExtention == "events_logo_".$id){
                $filename = $name;
            }
        }
        
        if(!$filename) {
            return false;
        }
        else{
            return $uploadurl.$filename;
        }
    }
}
