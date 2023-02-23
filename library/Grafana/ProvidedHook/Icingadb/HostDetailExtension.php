<?php

namespace Icinga\Module\Grafana\ProvidedHook\Icingadb;

use Icinga\Module\Icingadb\Hook\HostDetailExtensionHook;
use Icinga\Module\Icingadb\Model\Host;
use ipl\Html\Html;
use ipl\Html\HtmlString;
use ipl\Html\ValidHtml;

class HostDetailExtension extends HostDetailExtensionHook
{
    use IcingaDbGrapher;

    public function getHtmlForObject(Host $host): ValidHtml
    {
        $graphs = $this->getPreviewHtml($host);

        if (! empty($graphs)) {
          return HtmlString::create($graphs);
        }

        return HtmlString::create('');
    }
}
