<?php

namespace Icinga\Module\Grafana\Controllers;

use Icinga\Exception\NotFoundError;
use Icinga\Module\Grafana\ProvidedHook\Icingadb\HostDetailExtension;
use Icinga\Module\Grafana\ProvidedHook\Icingadb\ServiceDetailExtension;
use Icinga\Module\Grafana\Web\Controller\IcingadbGrafanaController;
use Icinga\Module\Icingadb\Model\Host;
use Icinga\Module\Icingadb\Model\Service;
use ipl\Stdlib\Filter;
use ipl\Web\Url;

class IcingadbdashboardController extends IcingadbGrafanaController
{
    public function init()
    {
        $this->assertPermission('grafana/graph');
        $this->setAutorefreshInterval(15);
    }

    public function indexAction()
    {
        if (! $this->useIcingadbAsBackend) {
            $this->redirectNow(Url::fromPath('grafana/dashboard')->setQueryString($this->params));
        }

        $this->getTabs()->add(
            'graphs',
            [
                'active' => true,
                'label' => $this->translate('Graphs'),
                'url' => $this->getRequest()->getUrl()
            ]
        );

        $hostName = $this->params->getRequired('host');
        $serviceName = $this->params->get('service');

        if ($serviceName != null) {
            $query = Service::on($this->getDb())->with([
                'state',
                'icon_image',
                'host',
                'host.state'
            ]);
            $query->filter(
                Filter::all(
                    Filter::equal('service.name', $serviceName),
                    Filter::equal('host.name', $hostName)
                )
            );
        } else {
            $query = Host::on($this->getDb())->with(['state', 'icon_image']);
            $query->filter(Filter::equal('host.name', $hostName));
        }

        $this->applyRestrictions($query);
        $object = $query->first();
        if ($object === null) {
            throw new NotFoundError(t('Host or Service not found'));
        }

        if ($object instanceof Host) {
            $graph = new HostDetailExtension();
        } else {
            $graph = new ServiceDetailExtension();
        }

        $this->addContent($graph->getPreviewHtml($object));
    }
}
