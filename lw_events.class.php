<?php

/**
 * This plugin supports the creation of a tag cloud.
 * 
 * @author Michael Mandt <michael.mandt@logic-works.de>
 * @package lw_events
 */
class lw_events extends lw_plugin
{

    protected $repository;

    /**
     * For the functionality of this plugin is it necessary to include
     * the "Autoloader" and the instances of "in_auth" and "auth"
     * objects.
     */
    public function __construct()
    {
        parent::__construct();
        include_once(dirname(__FILE__) . '/Services/Autoloader.php');
        $autoloader = new \LwEvents\Services\Autoloader();
        $this->auth = \lw_registry::getInstance()->getEntry("auth");
    }

    /**
     * The HTML frontend output will be build.
     * 
     * @return string
     */
    public function buildPageOutput()
    {
        //[PLUGIN:bionrw_events2?teaserview=1&teaserelements=3&language=de&targetid=9&targetoid=11]
        if (array_key_exists("teaserview", $this->params) && array_key_exists("teaserelements", $this->params) && array_key_exists("targetid", $this->params)) {
            if ($this->params ["teaserview"] < 0) {
                $plugindata["parameter"]["teaserview"] = 0;
            }
            elseif ($this->params ["teaserview"] > 1) {
                $plugindata["parameter"]["teaserview"] = 1;
            }
            else {
                $plugindata["parameter"]["teaserview"] = $this->params["teaserview"];
            }

            if ($this->params["teaserelements"] < 1) {
                $plugindata["parameter"]["teaserelements"] = 1;
            }
            elseif ($this->params["teaserelements"] > 10) {
                $plugindata["parameter"]["teaserelements"] = 10;
            }
            else {
                $plugindata["parameter"]["teaserelements"] = $this->params["teaserelements"];
            }

            if (array_key_exists("language", $this->params)) {
                $plugindata["parameter"]["language"] = $this->params["language"];
            }
            else {
                $plugindata["parameter"]["language"] = "de";
            }
            $plugindata["parameter"]["targetid"] = $this->params["targetid"];
            $plugindata["parameter"]["oid"] = $this->params["targetoid"];
            $plugindata["parameter"]["calendar"] = $this->params["calendar"];
        }
        else {
            $plugindata = $this->repository->plugins()->loadPluginData($this->getPluginName(), $this->params['oid']);
            $plugindata["parameter"]["oid"] = $this->params['oid'];
        }
        if (!empty($plugindata["parameter"])) {
            $response = new \LwEvents\Services\Response();
            $response->setDbObject($this->db);
            $response->setDataByKey("plugindata", $plugindata["parameter"]);
            $response->setDataByKey("c_media", $this->config["url"]["media"]);

            $controller = new \LwEvents\Controller\EventsController($response, $this->request, $this->auth->isLoggedIn());
            $controller->execute();

            return $response->getOutputByKey("lw_events_".$plugindata["parameter"]["oid"]);
        }
        else {
            return "Das Plugin wurde noch nicht konfiguriert. <br> Bitte in der Backendkonfiguration auf speichern klicken.";
        }
    }

    /**
     * The HTML backend output will be build.
     * 
     * @return string
     */
    public function getOutput()
    {
        $backend = new \LwEvents\Controller\Backend($this->config, $this->request, $this->repository, $this->getPluginName(), $this->getOid());
        if ($this->request->getAlnum("pcmd") == "save") {
            $backend->backend_save();
        }
        return $backend->backend_view();
    }

    /**
     * Here will be set if the plugin-conetentbox is deleteable from a page.
     * 
     * @return boolean
     */
    function deleteEntry()
    {
        return true;
    }

}