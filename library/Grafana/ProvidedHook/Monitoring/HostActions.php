<?php
/**
 * Created by PhpStorm.
 * User: carst
 * Date: 21.05.2017
 * Time: 09:13
 */
namespace Icinga\Module\Grafana\ProvidedHook\Monitoring;

use Icinga\Module\Monitoring\Hook\HostActionsHook;
use Icinga\Module\Monitoring\Object\Host;

class HostActions extends HostActionsHook
{
    public function getActionsForHost(Host $host)
    {
        $label = mt('grafana', 'Grafana Graphs');
        return array(
            $label => 'grafana/show?host='
                . rawurlencode($host->getName())
        );
    }
}