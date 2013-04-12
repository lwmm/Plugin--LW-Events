<?php

/**
 * Preparations for the detail view.
 * 
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package lw_events
 */

namespace LwEvents\Domain\Event;

class Detail
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
        $result = $queryHandler->selectEntry($this->request->getInt("id"));

        return array("formData" => $result, "lang" => $plugindata["language"], "oid" => $plugindata["oid"]);
    }

}