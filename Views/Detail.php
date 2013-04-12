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

        return $view->render();
    }

}
