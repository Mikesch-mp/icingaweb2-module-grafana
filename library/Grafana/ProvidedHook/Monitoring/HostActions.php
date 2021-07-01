<?php

/**
 * Created by PhpStorm.
 * User: carst
 * Date: 21.05.2017
 * Time: 09:13
 */

namespace Icinga\Module\Grafana\ProvidedHook\Monitoring;

use Icinga\Authentication\Auth;
use Icinga\Module\Monitoring\Hook\HostActionsHook;
use Icinga\Module\Monitoring\Object\Host;
use Icinga\Web\Navigation\Navigation;
use Icinga\Web\Navigation\NavigationItem;
use Icinga\Web\Url;
use Icinga\Application\Config;

class HostActions extends HostActionsHook
{
    protected $defaultTimerange = '1w/w';

    public function getActionsForHost(Host $host)
    {
        if (! Auth::getInstance()->hasPermission('grafana/showall')) {
            return [];
        }

        $config = Config::module('grafana')->getSection('grafana');
        $timerange = $config->get('timerangeAll', $this->defaultTimerange);
        $nav = new Navigation();
        $nav->addItem(new NavigationItem(t('Show all graphs'), array(
            'url' => Url::fromPath('grafana/show', array('host' => $host->getName(), 'timerange' => $timerange)),
            'target' => '_next',
            'icon' => 'gauge',
        )));
        return $nav;
    }
}
