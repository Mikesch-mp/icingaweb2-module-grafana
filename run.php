<?php
use Icinga\Authentication\Auth;
$auth = Auth::getInstance();

$this->provideHook('grapher');

if ($auth->hasPermission('grafana/showall')) {
    $this->provideHook('monitoring/HostActions');
}