<?php
/**
 * Created by PhpStorm.
 * User: carst
 * Date: 17.02.2018
 * Time: 20:09
 */

namespace Icinga\Module\Grafana\Controllers;

use Icinga\Exception\NotFoundError;
use Icinga\Module\Grafana\ProvidedHook\Icingadb\HostDetailExtension;
use Icinga\Module\Grafana\ProvidedHook\Icingadb\ServiceDetailExtension;
use Icinga\Module\Grafana\Web\Controller\IcingadbGrafanaController;
use Icinga\Module\Grafana\Web\Widget\PrintAction;
use Icinga\Module\Icingadb\Model\CustomvarFlat;
use Icinga\Module\Icingadb\Model\Host;
use Icinga\Module\Icingadb\Model\Service;
use Icinga\Module\Grafana\Helpers\Timeranges;
use Icinga\Application\Config;
use ipl\Html\HtmlDocument;
use ipl\Html\HtmlElement;
use ipl\Html\HtmlString;
use ipl\Stdlib\Filter;
use ipl\Web\Url;

class IcingadbshowController extends IcingadbGrafanaController
{
    /** @var bool */
    protected $showFullscreen;
    protected $host;
    protected $custvardisable = "grafana_graph_disable";

    public function init()
    {
        $this->assertPermission('grafana/showall');
        $this->view->showFullscreen
            = $this->showFullscreen
            = (bool)$this->_helper->layout()->showFullscreen;
        $this->host = $this->getParam('host');
        $this->config = Config::module('grafana')->getSection('grafana');
        /**
         * Name of the custom varibale to disable graph
         */
        $this->custvardisable = ($this->config->get('custvardisable', $this->custvardisable));
    }

    public function indexAction()
    {
        if (! $this->useIcingadbAsBackend) {
            $this->redirectNow(Url::fromPath('grafana/show')->setQueryString($this->params));
        }
        $this->disableAutoRefresh();

        if (!$this->showFullscreen) {
            $this->getTabs()->add(
                'graphs',
                [
                    'label' => $this->translate('Grafana Graphs'),
                    'url' => $this->getRequest()->getUrl()
                ]
            )->activate('graphs');

            $this->getTabs()->extend(new PrintAction());
        }

        $this->addControl(
            HtmlElement::create(
                'h1',
                null,
                sprintf($this->translate('Performance graphs for %s'), $this->host)
            )
        );

        // Preserve timerange if selected
        $parameters = ['host' => $this->host];
        if ($this->hasParam('timerange')) {
            $parameters['timerange'] = $this->getParam('timerange');
        }

        /* The timerange menu */
        $menu = new Timeranges($parameters, 'grafana/show');
        $this->addControl(new HtmlString($menu->getTimerangeMenu()));

        /* first host object for host graph */
        $this->object = $this->getHostObject($this->host);
        $varsFlat = CustomvarFlat::on($this->getDb());
        $this->applyRestrictions($varsFlat);

        $varsFlat
            ->columns(['flatname', 'flatvalue'])
            ->orderBy('flatname');
        $varsFlat->filter(Filter::equal('host.id', $this->object->id));
        $customVars = $this->getDb()->fetchAll($varsFlat->assembleSelect());
        if ($this->object->perfdata_enabled
            || !(isset($customVars[$this->custvardisable])
                && json_decode(strtolower($customVars[$this->custvardisable])) !== false)
        ) {
            $object = (new HtmlDocument())
                ->addHtml(HtmlElement::create('h2', null, $this->object->checkcommand));
            $this->addContent($object);
            $this->addContent((new HostDetailExtension())->getPreviewHtml($this->object, true));
        }
        /* Get all services for this host */
        $query = Service::on($this->getDb());
        $query->filter(Filter::equal('host.name', $this->host));

        $this->applyRestrictions($query);

        foreach ($query as $service) {
            $this->object = $this->getServiceObject($service->name, $this->host);
            $varsFlat = CustomvarFlat::on($this->getDb());
            $this->applyRestrictions($varsFlat);

            $varsFlat
                ->columns(['flatname', 'flatvalue'])
                ->orderBy('flatname');
            $varsFlat->filter(Filter::equal('service.id', $service->id));
            $customVars = $this->getDb()->fetchAll($varsFlat->assembleSelect());
            if ($this->object->perfdata_enabled
                && !(isset($customVars[$this->custvardisable])
                    && json_decode(strtolower($customVars[$this->custvardisable])) !== false)
            ) {
                $object = (new HtmlDocument())
                    ->addHtml(HtmlElement::create('h2', null, $service->name));
                $this->addContent($object);
                $this->addContent((new ServiceDetailExtension())->getPreviewHtml($service, true));
            }
        }

        unset($this->object);
        unset($customVars);
    }


    public function getHostObject($host)
    {
        $query = Host::on($this->getDb());
        $query->filter(Filter::equal('name', $host));
        $this->applyRestrictions($query);
        $host = $query->first();

        if ($host === null) {
            throw new NotFoundError(t('Host not found'));
        }

        return $host;
    }

    public function getServiceObject($service, $host)
    {
        $query = Service::on($this->getDb());

        $query->filter(Filter::equal('name', $service));
        $query->filter(Filter::equal('host.name', $host));
        $this->applyRestrictions($query);

        $service = $query->first();

        if ($service === null) {
            throw new NotFoundError(t('Service not found'));
        }

        return $service;
    }
}