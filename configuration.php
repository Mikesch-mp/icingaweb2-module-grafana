<?php

use Icinga\Authentication\Auth;
$auth = Auth::getInstance();

$this->providePermission('grafana/graphconfig', $this->translate('Allow to configure graphs.'));
$this->providePermission('grafana/debug', $this->translate('Allow to see module debug informations.'));
$this->providePermission('grafana/showall', $this->translate('Allow access to see all graphs of a host.'));
$this->providePermission('grafana/enableLink', $this->translate('Enable link to grafana'));

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
