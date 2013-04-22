<?php

/**
 * Informations of the add/edit form will be checked if it's a valid input.
 * 
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package lw_events
 */

namespace LwEvents\Domain\Event\Service;

define("REQUIRED", "1");    # array( 1 => array( "error" => 1, "options" => "" ));
define("MAXLENGTH", "2");   # array( 2 => array( "error" => 1, "options" => array( "maxlength" => $maxlength, "actuallength" => $strlen ) ));
define("YEAR", "3");        # array( 3 => array( "error" => 1, "options" => array( "enteredyear" => $year ) ));
define("DATE", "4");        # array( 4 => array( "error" => 1, "options" => array( "entereddate" => $date ) ));  [$date = JJJJMMDD]
define("EMAIL", "5");       # array( 5 => array( "error" => 1, "options" => "" ));
define("DIGITFIELD", "6");  # array( 6 => array( "error" => 1, "options" => "" ));
define("ZIP", "7");         # array( 7 => array( "error" => 1, "options" => "" ));
define("PAYMENT", "8");     # array( 8 => array( "error" => 1, "options" => "" ));
define("BOOL", "9");        # array( 9 => array( "error" => 1, "options" => "" ));
define("MAINTEXTANDPAGEID", "10");# array( 10 => array( "error" => 1, "options" => "" ));
define("TODATELOWERFROMDATE", "11");# array( 11 => array( "error" => 1, "options" => "" ));
define("URL", "12");        # array( 12 => array( "error" => 1, "options" => "" ));
define("NOTALLOWEDFILEEXTENSION", "13");        # array( 12 => array( "error" => 1, "options" => "" ));

class isValid
{

    public function __construct()
    {
        $this->allowedKeys = array(
            "headline1",
            "place",
            "maintext",
            "pageid",
            "date",
            "date2",
            "exturl",
            "teasertext",
            "uploadfield");

        $this->errors = array();
    }

    public function setValues($array)
    {
        $this->array = $array;
    }

    public function validate()
    {
        $valid = true;
        foreach ($this->allowedKeys as $key) {
            $function = $key . "Validate";
            $result = $this->$function($this->array[$key]);
            if ($result == false) {
                $valid = false;
            }
        }

        if (!$this->checkEntryType()) {
            $valid = false;
        }

        return $valid;
    }

    private function addError($key, $number, $array = false)
    {
        $this->errors[$key][$number]['error'] = 1;
        $this->errors[$key][$number]['options'] = $array;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getErrorsByKey($key)
    {
        return $this->errors[$key];
    }

    private function headline1Validate($value)
    {
        $bool = true;

        if (!$this->requiredValidation("headline1", $value)) {
            $bool = false;
        }

        if (!$this->defaultValidation("headline1", $value, 255)) {
            $bool = false;
        }

        if (!$bool) {
            return false;
        }
        return true;
    }

    private function placeValidate($value)
    {
        if (!$this->defaultValidation("place", $value, 255)) {
            return false;
        }
        return true;
    }

    private function maintextValidate($value)
    {
        if (!$this->defaultValidation("mainttext", $value, 20000)) {
            return false;
        }
        return true;
    }

    private function pageidValidate($value)
    {
        if (ctype_digit($value) || empty($value)) {
            return true;
        }
        else {
            $this->addError("pageid", 6);
            return false;
        }
    }

    private function checkEntryType()
    {
        if (empty($this->array["maintext"]) && empty($this->array["pageid"]) && empty($this->array["exturl"])) {
            $this->addError("maintextANDpageid", 10);
            return false;
        }
        return true;
    }
    
    private function exturlValidate($value){
        if(empty($value)){
            return true;
        }
        $bool = true;
        
        if(!$this->defaultValidation("exturl", $value, 255)) {
            $bool = false;
        }
        
        if(!filter_var($value, FILTER_VALIDATE_URL)){
            $bool = false;
            $this->addError("exturl", 12);
        }
        
        if(!$bool) {
            return false;
        }
        return true;
    }
    
    private function teasertextValidate($value){
        if(empty($value)){
            return true;
        }
        if(!$this->defaultValidation("teasertext", $value, 255)){
            return false;
        }
        return true;
    }
    
    private function uploadfieldValidate($fileDataArray)
    {
        if(empty($fileDataArray["name"])) {
            return true;
        }
        $uploadExtension = ".".\lw_io::getFileExtension($fileDataArray["name"]);
        
        if($uploadExtension == ".jpeg" || $uploadExtension == ".jpg" || $uploadExtension == ".png" || $uploadExtension == ".gif") {
            return true;
        }
        
        $this->addError("uploadfield", 13);
        return false;
    }

    private function dateValidate($value)
    {
        $bool = true;

        if (!$this->requiredValidation("date", $value)) {
            $bool = false;
        }

        $year = substr($value, 0, 4);
        $month = substr($value, 4, 2);
        $day = substr($value, 6, 2);

        if (!checkdate($month, $day, $year)) {
            $bool = false;
        }

        if ($bool) {
            return true;
        }
        else {
            $this->addError("date", 4, array("entereddate" => $value));
            return false;
        }
    }

    private function date2Validate($value)
    {
        if (empty($value)) {
            return true;
        }

        $bool = true;
        $bool2 = true;

        $year = substr($value, 0, 4);
        $month = substr($value, 4, 2);
        $day = substr($value, 6, 2);

        if (!checkdate($month, $day, $year)) {
            $this->addError("date2", 4, array("entereddate" => $value));
            $bool = false;
        }

        if ($this->array["date"] > $value) {
            $this->addError("date2", 11);
            $bool2 = false;
        }

        if ($bool && $bool2) {
            return true;
        }

        if (!$bool || !$bool2) {
            return false;
        }
    }

    function defaultValidation($key, $value, $length)
    {
        $bool = true;

        if (strlen($value) > $length) {
            $this->addError($key, 2, array("maxlength" => $length, "actuallength" => strlen($value)));
            $bool = false;
        }

        if ($bool == false) {
            return false;
        }
        return true;
    }

    public function requiredValidation($key, $value)
    {
        if ($value == "") {
            $this->addError($key, 1);
            return false;
        }
        return true;
    }

}