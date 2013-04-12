<?php

/**
 * Generating html output for new/edit form.
 * 
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package lw_events
 */

namespace LwEvents\Views;

class Form
{

    /**
     * The html template will be rendered and return.
     * 
     * @param array $data
     * @param bool $admin
     * @return  string
     */
    public function render($data, $admin = false)
    {
        if (!$admin) {
            \LwEvents\Services\Page::reload(\LwEvents\Services\Page::getUrl());
        }
        else {
            if (array_key_exists("notvalid", $data)) {
                $temp = $data["c_media"];
                $data = $data["notvalid"];
                $data["c_media"] = $temp;
            }

            $view = new \lw_view(dirname(__FILE__) . '/Templates/EventForm.phtml');

            $view->admin = $admin;
            $view->baseUrl = \LwEvents\Services\Page::getUrl();
            $view->calendaricon = $data["c_media"] . "pics/fatcow_icons/16x16_0180/calendar.png";
            $view->mce = $data["c_media"] . "tinymce/jscripts/tiny_mce/tiny_mce.js";

            if ($data["cmd"] == "add") {
                $view->formAction = \LwEvents\Services\Page::getUrl() . "&show=form&cmd=add&oid=".$data["oid"];
                $view->formTitle = "neuen Termin anlegen";
            }

            if ($data["cmd"] == "edit") {
                $view->formAction = \LwEvents\Services\Page::getUrl() . "&show=form&cmd=edit&id=" . $data["id"]."&oid=".$data["oid"];
                $view->formTitle = "Termin bearbeiten";
            }

            if (array_key_exists("formData", $data)) {
                $view->formData = $data["formData"];

                $year = substr($data["formData"]['date'], 0, 4);
                $month = substr($data["formData"]['date'], 4, 2);
                $day = substr($data["formData"]['date'], 6, 2);

                $view->date = $day . '.' . $month . '.' . $year;

                if (!empty($data["formData"]['date2'])) {
                    $year2 = substr($data["formData"]['date2'], 0, 4);
                    $month2 = substr($data["formData"]['date2'], 4, 2);
                    $day2 = substr($data["formData"]['date2'], 6, 2);

                    $view->date2 = $day2 . '.' . $month2 . '.' . $year2;
                }
                else {
                    $view->date2 = "";
                }
            }
            else {
                $view->formData = false;
                $view->date = false;
            }

            if (array_key_exists("errors", $data)) {
                $view->errors = $data["errors"];
            }
            else {
                $view->errors = false;
            }

            return $view->render();
        }
    }

}