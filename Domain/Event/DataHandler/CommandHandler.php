<?php

/**
 * Creation, update ,deletion of news entries.
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package lw_events
 */

namespace LwEvents\Domain\Event\DataHandler;

class CommandHandler
{

    private $db;
    private $uploadpath;

    /**
     * An instance of lw_db is needed.
     * 
     * @param object $db
     */
    public function __construct($db, $uploadpath)
    {
        $this->db = $db;
        $this->uploadpath = $uploadpath;
    }

    /**
     * A new news entry will be added.
     * 
     * @param array $array
     * @return boolean
     */
    public function addEntry($array)
    {
        if (empty($array["date2"])) {
            $array["date2"] = $array["date"];
        }
        $this->db->setStatement("INSERT INTO t:lw_master ( lw_object, language, opt1text, opt2text, opt3text, opt4text, opt1number, opt2number, opt3number, opt4number, lw_first_date, lw_last_date) VALUES ( :lw_object, :language, :opt1text, :opt2text, :opt3text, :opt4text, :opt1number, :opt2number, :opt3number, :opt4number, :date, :date ) ");
        $this->db->bindParameter("lw_object", "s", "lw_events");
        $this->db->bindParameter("language", "s", $array["language"]);
        $this->db->bindParameter("opt1text", "s", $array["headline1"]);
        $this->db->bindParameter("opt2text", "s", $array["place"]);
        $this->db->bindParameter("opt3text", "s", $array["exturl"]);
        $this->db->bindParameter("opt4text", "s", $array["teasertext"]);
        $this->db->bindParameter("opt1number", "i", $array["pageid"]);
        $this->db->bindParameter("opt2number", "i", $array["date"]);
        $this->db->bindParameter("opt3number", "i", substr($array["date"], 0, 4));
        $this->db->bindParameter("opt4number", "i", $array["date2"]);
        $this->db->bindParameter("date", "i", date("YmdHis"));
        $id = $this->db->pdbinsert($this->db->gt("lw_master"));

        if ($id > 0) {
            $this->saveLogo($id, $array["uploadfield"]);
            return $this->db->saveClob($this->db->gt("lw_master"), "opt1clob", $this->db->quote($array["maintext"]), $id);
        }
        else {
            return false;
        }
    }

    /**
     * An existing entry will be updated.
     * 
     * @param int $id
     * @param array $array
     * @return boolean
     */
    public function saveEntry($id, $array)
    {
        if (empty($array["date2"])) {
            $array["date2"] = $array["date"];
        }
        $this->db->setStatement("UPDATE t:lw_master SET opt1text = :opt1text, opt2text = :opt2text, opt3text = :opt3text, opt4text = :opt4text, opt1number = :opt1number, opt2number = :opt2number, opt3number = :opt3number, opt4number = :opt4number, lw_last_date = :date  WHERE id = :id ");
        $this->db->bindParameter("id", "i", $id);
        $this->db->bindParameter("opt1text", "s", $array["headline1"]);
        $this->db->bindParameter("opt2text", "s", $array["place"]);
        $this->db->bindParameter("opt3text", "s", $array["exturl"]);
        $this->db->bindParameter("opt4text", "s", $array["teasertext"]);
        $this->db->bindParameter("opt1number", "i", $array["pageid"]);
        $this->db->bindParameter("opt2number", "i", $array["date"]);
        $this->db->bindParameter("opt3number", "i", substr($array["date"], 0, 4));
        $this->db->bindParameter("opt4number", "i", $array["date2"]);
        $this->db->bindParameter("date", "i", date("YmdHis"));
        $this->db->pdbquery();

        if ($id > 0) {
            if(!empty($array["uploadfield"]["name"])) {
                $this->saveLogo($id, $array["uploadfield"]);
            }
            return $this->db->saveClob($this->db->gt("lw_master"), "opt1clob", $this->db->quote($array["maintext"]), $id);
        }
        else {
            return false;
        }
    }

    /**
     * An entry with certain id will be deleted.
     * 
     * @param int $id
     * @return bool
     */
    public function deleteEntry($id)
    {
        $this->db->setStatement("DELETE FROM t:lw_master WHERE id = :id ");
        $this->db->bindParameter("id", "i", $id);
        return $this->db->pdbquery();
    }

    private function saveLogo($id, $fileDataArray)
    {        
        $extension = "." . \lw_io::getFileExtension($fileDataArray["name"]);
        $filename = "events_logo_" . $id . $extension;

        $uploadDir = \lw_directory::getInstance($this->uploadpath);
        $files = $uploadDir->getDirectoryContents("file");

        foreach ($files as $file) {
            $name = $file->getName();
            $explodeName = explode(".", $name);
            $nameWithoutExtention = $explodeName[0];

            if ($nameWithoutExtention == "events_logo_" . $id) {
                $uploadDir->deleteFile($file->getName());
            }
        }

        $uploadDir->addFile($fileDataArray['tmp_name'], $filename);
        
        $image = new \LwEvents\Services\LogoResizer($this->uploadpath.$filename);
        $image->setParams(200, 100);
        $image->resize();
        $image->crop();
        $image->saveImage();
        
        #$image = new \LwEvents\Services\LogoResizer($fileDataArray["tmp_name"]);
        
        #$image = new \lw_image($fileDataArray['tmp_name']);
        #$image->scaleImage(200,150,$this->uploadpath.$filename, true, false);
        #$image->cropImage($this->uploadpath.$filename, $this->uploadpath.$filename, "center", 200, 150);
        #print_r($image->getImageType());die();
        
    }
    
    public function deleteLogo($id)
    {
        $uploadDir = \lw_directory::getInstance($this->uploadpath);
        $files = $uploadDir->getDirectoryContents("file");

        foreach ($files as $file) {
            $name = $file->getName();
            $explodeName = explode(".", $name);
            $nameWithoutExtention = $explodeName[0];

            if ($nameWithoutExtention == "events_logo_" . $id) {
                $uploadDir->deleteFile($file->getName());
            }
        }
        
        return true;
    }

}