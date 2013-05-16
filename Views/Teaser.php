<?php

/**
 * Generating html output of teaser list.
 * 
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package lw_events
 */

namespace LwEvents\Views;

class Teaser
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
        $calements = $data["calelements"];
        $targetid = $data["targetid"]; 
        $oid = $data["oid"];
        $calendar = $data["calendar"];
        unset($data["lang"]);
        unset($data["calelements"]);
        unset($data["targetid"]);
        unset($data["oid"]);
        unset($data["calendar"]);

        $url = substr($baseUrl, 0, strpos($baseUrl, "index=") + strlen("index="));

        $view = new \lw_view(dirname(__FILE__) . '/Templates/TeaserList.phtml');
        $view->baseUrl = $url.$targetid."&oid=".$oid;
        $view->baseUrlWithoutIndex = $url;
        $view->data = $data;
        $view->calelements = $this->prepareCalArray($calements);
        $view->lang = $language;
        $view->calendar = $calendar;

        return $view->render();
    }

    private function prepareCalArray($array)
    {
        if (!empty($array) == is_array($array)) {
            $temp = array();
            foreach ($array as $key => $value) {
                foreach ($value as $k => $v) {
                    if ($k == "id" || $k == "opt2number" || $k == "opt4number" || $k == "opt1text" || $k == "opt1number" || $k == "opt3text") {
                        $temp[$key][$k] = $v;
                    }
                }
            }
            return $temp;
        }
    }

}