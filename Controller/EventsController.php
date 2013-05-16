<?php

/**
 * Tagcloud controller collects necessary informations and pass them
 * to the specific classes in case of which job has to be done.
 *
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package lw_events
 */

namespace LwEvents\Controller;

class EventsController extends \lw_object
{

    private $response;
    private $request;
    private $admin;

    /**
     * @param object $response
     * @param object $request
     * @param bool   $admin
     */
    public function __construct($response, $request, $admin)
    {
        $this->response = $response;
        $this->request = $request;
        $this->admin = $admin;
    }

    /**
     * 
     */
    public function execute()
    {
        $plugindata = $this->response->getDataByKey("plugindata");
        $teaserview = $plugindata["teaserview"];

        if (!$teaserview) {
            if($this->request->getInt("oid") == $plugindata["oid"]) {
                if ($this->request->getInt("delete")) {
                    $this->deleteEntryById($this->request->getInt("delete"));
                }


                if ($this->request->getAlnum("show")) {
                    $show = ucfirst($this->request->getAlnum("show"));
                }
                else {
                    $show = "All";
                }
            }
            else {
                $show = "All";
            }
            $class = "\\LwEvents\\Domain\\Event\\" . $show;
            $class_view = "\\LwEvents\\Views\\" . $show;

            $controller = new $class($this->response, $this->request);

            $view = new $class_view($this->request);
            $this->response->setOutputByKey("lw_events_".$plugindata["oid"], $view->render($controller->execute(), $this->admin, $this->response->getDataByKey("baseUrl")));
        }
        else {
            $controller = new \LwEvents\Domain\Event\Teaser($this->response, $this->request);
            $view = new \LwEvents\Views\Teaser();
            $this->response->setOutputByKey("lw_events_".$plugindata["oid"], $view->render($controller->execute(), false, $this->response->getDataByKey("baseUrl")));
        }
    }

    /**
     * A new entry will be deleted.
     * 
     * @param int $id
     */
    private function deleteEntryById($id)
    {
        $commandHandler = new \LwEvents\Domain\Event\DataHandler\CommandHandler($this->response->getDbObject(), $this->response->getDataByKey("upload_path"));
        $commandHandler->deleteEntry($id);
        $commandHandler->deleteLogo($id);

        \LwEvents\Services\Page::reload($this->response->getDataByKey("baseUrl"));
    }

}
