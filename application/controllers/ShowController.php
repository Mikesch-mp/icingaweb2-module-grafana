<?php
/**
 * Created by PhpStorm.
 * User: carst
 * Date: 17.02.2018
 * Time: 20:09
 */

namespace Icinga\Module\Grafana\Controllers;

use Icinga\Module\Grafana\Web\Controller\MonitoringAwareController;
use Icinga\Module\Monitoring\Object\Service;
use Icinga\Module\Monitoring\Object\Host;
use Icinga\Web\Widget\Tab;
use Icinga\Web\Hook;
use Icinga\Module\Grafana\Helpers\Timeranges;



class ShowController extends MonitoringAwareController
{
    /** @var bool */
    protected $showFullscreen;

    public function init()
    {
        $this->assertPermission('grafana/showall');
        $this->view->showFullscreen
            = $this->showFullscreen
            = (bool)$this->_helper->layout()->showFullscreen;
    }

    public function indexAction()
    {
        $this->disableAutoRefresh();
        $host = $this->getParam('host');
        $this->view->host = $host;

        if (! $this->showFullscreen) {
            $tabs = $this->getTabs();
            $tabs->add('graphs', array(
                'label' => $this->translate('Grafana Graphs'),
                'url' => $this->getRequest()->getUrl()))->activate('graphs');

            $fullscreen = new Tab(array(
                'title' => $this->translate('Print'),
                'icon'  => 'print',
                'url'   => ((string) $this->getRequest()->getUrl()) . '&showFullscreen'
            ));
            $fullscreen->setTargetBlank();
            $tabs->addAsDropdown('fullscreen', $fullscreen);

        } else {
            $this->view->title = sprintf(
                $this->translate('Performance graphs for %s'),
                $host
            );
        }

        $menu = new Timeranges(array( 'host' => $host), 'grafana/show');
        $this->view->menu = $menu->getTimerangeMenu();

        $hostobject = new Host($this->backend, $host);
        $this->applyRestriction('monitoring/filter/objects', $hostobject);
        if ($hostobject->fetch() === false) {
            $this->httpNotFound($this->translate('Host not found'));
        }
        $objects[] = $hostobject;

        $query = $this->backend->select()->from('servicestatus', [
            'service_description',
        ]);
        $this->applyRestriction('monitoring/filter/objects', $query);

        foreach ($query->where('host_name', $host) as $service){
            $objects[] = $this->getServiceObject($service->service_description);
        }
        $this->view->objects = $objects;
        $this->view->grapher = Hook::first('grapher');
    }

    public function getServiceObject($serviceName)
    {
        $service = new Service(
            $this->backend,
            $this->view->host,
            $serviceName
        );
        $this->applyRestriction('monitoring/filter/objects', $service);
        if ($service->fetch() === false) {
            $this->httpNotFound($this->translate('Service not found'));
        }
        return $service;
    }
}