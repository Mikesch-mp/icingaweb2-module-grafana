<?php

namespace Icinga\Module\Grafana\ProvidedHook\Icingadb;

use Icinga\Module\Icingadb\Hook\ServiceDetailExtensionHook;
use Icinga\Module\Icingadb\Model\Service;
use ipl\Html\ValidHtml;

class ServiceDetailExtension extends ServiceDetailExtensionHook
{
    use IcingaDbGrapher;

    public function getHtmlForObject(Service $service): ValidHtml
    {
        $this->object = $service;
        return $this->getPreviewHtml($service);
    }
}
