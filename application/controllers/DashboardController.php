<?php

namespace Icinga\Module\Grafana\Controllers;

use Icinga\Module\Grafana\ProvidedHook\Grapher;
use Icinga\Module\Monitoring\Object\Host;
use Icinga\Module\Monitoring\Controller;
use Icinga\Module\Monitoring\Object\Service;

class DashboardController extends Controller
{
    public function init()
    {
        $this->assertPermission('grafana/graph');
        $this->setAutorefreshInterval(15);
    }

    public function indexAction()
    {
        $this->getTabs()->add('graphs', array(
            'active' => true,
            'label' => $this->translate('Graphs'),
            'url' => $this->getRequest()->getUrl()
        ));

        $hostname = urldecode($this->params->getRequired('host'));
        $servicename = urldecode($this->params->get('service'));

        if ($servicename != null) {
            $object = new Service($this->backend, $hostname, $servicename);
        } else {
            $object = new Host($this->backend, $this->params->getRequired('host'));
        }

        $this->applyRestriction('monitoring/filter/objects', $object);
        if ($object->fetch() === false) {
            $this->httpNotFound($this->translate('Host or Service not found'));
        }

        $graph = new Grapher();
        $this->view->graph = $graph->getPreviewHtml($object);
    }

}
