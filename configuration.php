<?php

use Icinga\Authentication\Auth;
$auth = Auth::getInstance();

$this->providePermission('grafana/graph', $this->translate('Allow to configure graphs'));
$this->providePermission('grafana/debug', $this->translate('Can see debuging informations, if enabled'));

$this->provideConfigTab('config', array(
    'title' => 'Configuration',
    'label' => 'Configuration',
    'url' => 'config'
));

if ($auth->hasPermission('grafana/graph'))
{
   $this->menuSection(N_('Configuration'))->add('Grafana Graphs')->setUrl('grafana/graph')->setPriority(900);
   $this->provideConfigTab('graph', array(
       'title' => 'Graphs',
       'label' => 'Graphs',
       'url' => 'graph'
   ));
}
