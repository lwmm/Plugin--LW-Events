<?php

/**
 * Preparations for the list all view.
 * 
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package lw_events
 */

namespace LwEvents\Domain\Event;

class All
{

    private $response;
    private $request;

    /**
     * @param object $response
     * @param object $request
     */
    public function __construct($response, $request)
    {
        $this->response = $response;
        $this->request = $request;
    }

    /**
     * Informations will be collected that the template can be rendered.
     * 
     * @return array
     */
    public function execute()
    {
        $plugindata = $this->response->getDataByKey("plugindata");

        $queryHandler = new \LwEvents\Domain\Event\DataHandler\QueryHandler($this->response->getDbObject());

        $array = $queryHandler->selectAllActualEvents($plugindata["language"]);
        $array["lang"] = $plugindata["language"];
        $array["oid"] = $plugindata["oid"];

        return $array;
    }

}