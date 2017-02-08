<?php

use Icinga\Authentication\Auth;
$auth = Auth::getInstance();

$this->providePermission('grafana/graph', $this->translate('Allow to configure graphs'));

$this->provideConfigTab('config', array(
    'title' => 'Configuration',
    'label' => 'Configuration',
    'url' => 'config'
));

if ($auth->hasPermission('grafana/graph'))
{
   $this->provideConfigTab('graph', array(
       'title' => 'Graphs',
       'label' => 'Graphs',
       'url' => 'graph'
   ));
}
