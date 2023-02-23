<?php

namespace Icinga\Module\Grafana\ProvidedHook\Icingadb;

use Icinga\Module\Icingadb\Hook\HostDetailExtensionHook;
use Icinga\Module\Icingadb\Model\Host;
use ipl\Html\ValidHtml;

class HostDetailExtension extends HostDetailExtensionHook
{
    use IcingaDbGrapher;

    public function getHtmlForObject(Host $host): ValidHtml
    {
        $this->object = $host;
        return $this->getPreviewHtml($host);
    }
}
