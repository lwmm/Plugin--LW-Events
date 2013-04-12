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
    public function render($data, $admin = false)
    {
        $language = $data["lang"];
        $oid = $data["oid"];
        unset($data["lang"]);
        unset($data["oid"]);

        $view = new \lw_view(dirname(__FILE__) . '/Templates/EventsList.phtml');
        $view->admin = $admin;
        $view->baseUrl = \LwEvents\Services\Page::getUrl()."&oid=".$oid;
        $view->baseUrlWithoutIndex = substr(\LwEvents\Services\Page::getUrl(), 0, strpos(\LwEvents\Services\Page::getUrl(), "index=") + strlen("index="));
        $view->data = $data;
        $view->lang = $language;
        $view->oid = $oid;

        return $view->render();
    }

}