<?php

use Icinga\Authentication\Auth;
$auth = Auth::getInstance();

$this->providePermission('grafana/graphconfig', $this->translate('Allow to configure graphs.'));
$this->providePermission('grafana/graph', $this->translate('Allow to view graphs in dashboards.'));
$this->providePermission('grafana/debug', $this->translate('Allow to see module debug informations.'));
$this->providePermission('grafana/showall', $this->translate('Allow access to see all graphs of a host.'));
$this->providePermission('grafana/enablelink', $this->translate('Allow to follow links to Grafana'));

$this->provideConfigTab('config', array(
    'title' => 'Configuration',
    'label' => 'Configuration',
    'url' => 'config'
));

if ($auth->hasPermission('grafana/graphconfig'))
{
   $this->menuSection(N_('Configuration'))->add('Grafana Graphs')->setUrl('grafana/graph')->setPriority(900);
   $this->provideConfigTab('graph', array(
       'title' => 'Graphs',
       'label' => 'Graphs',
       'url' => 'graph'
   ));
}
