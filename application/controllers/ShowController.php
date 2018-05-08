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
    protected $host;

    public function init()
    {
        $this->assertPermission('grafana/showall');
        $this->view->showFullscreen
            = $this->showFullscreen
            = (bool)$this->_helper->layout()->showFullscreen;
        $this->host = $this->getParam('host');
    }

    public function indexAction()
    {
        $this->disableAutoRefresh();
        $this->view->host = $this->host;

        if (! $this->showFullscreen) {
            $tabs = $this->getTabs();
            $tabs->add('graphs', array(
                'label' => $this->translate('Grafana Graphs'),
                'url' => $this->getRequest()->getUrl()))->activate('graphs');

            $fullscreen = new Tab(array(
                'title' => $this->translate('Print'),
                'icon'  => 'print',
                'url'   => ((string) htmlspecialchars_decode($this->getRequest()->getUrl())) . '&showFullscreen'
            ));
            $fullscreen->setTargetBlank();
            $tabs->addAsDropdown('fullscreen', $fullscreen);

            $this->view->title = sprintf(
                $this->translate('Performance graphs for %s'),
                $this->host
            );
        }
        /* The timerange menu */
        $urlparams = http_build_query();
        $menu = new Timeranges(array( 'host' => $this->host), 'grafana/show');
        $this->view->menu = $menu->getTimerangeMenu();

        /* first host object for host graph */
        $objects[] = $this->getHostObject($this->host);

        /* Get all services for this host */
        $query = $this->backend->select()->from('servicestatus', [
            'service_description',
        ]);
        $this->applyRestriction('monitoring/filter/objects', $query);

        foreach ($query->where('host_name', $this->host) as $service){
            $objects[] = $this->getServiceObject($service->service_description, $this->host);
        }
        $this->view->objects = $objects;
        $this->view->grapher = Hook::first('grapher');
    }


    public function getHostObject($host)
    {

        $myHost = new Host($this->backend, $host);
        $this->applyRestriction('monitoring/filter/objects', $myHost);

        if ($myHost->fetch() === false) {
            $this->httpNotFound($this->translate('Host not found'));
        }

        return $myHost;
    }

    public function getServiceObject($service, $host)
    {
        $myService = new Service(
            $this->backend,
            $host,
            $service
        );
        $this->applyRestriction('monitoring/filter/objects', $myService);

        if ($myService->fetch() === false) {
            $this->httpNotFound($this->translate('Service not found'));
        }

        return $myService;
    }


}