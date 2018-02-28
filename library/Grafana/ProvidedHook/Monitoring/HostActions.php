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
use Icinga\Web\Navigation\Navigation;
use Icinga\Web\Navigation\NavigationItem;
use Icinga\Web\Url;
use Icinga\Application\Config;



class HostActions extends HostActionsHook
{
    public function getActionsForHost(Host $host)
    {
        $config = Config::module('grafana')->getSection('grafana');
        $timerange = $config->get('timerangeAll', '1w/w');
        $nav = new Navigation();
        $nav->addItem(new NavigationItem('Show all graphs', array(
            'url' => Url::fromPath('grafana/show', array('host' => $host->getName(), 'timerange' => $timerange)),
            'target' => '_next',
            'icon' => 'gauge',
        )));
        return $nav;
    }
}