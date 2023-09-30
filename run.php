<?php

use Icinga\Module\Grafana\ProvidedHook\Icingadb\IcingadbSupport;
use Icinga\Module\Grafana\ProvidedHook\Icingadb\GeneralConfigFormHook;

$this->provideHook('icingadb/HostActions');
$this->provideHook('icingadb/IcingadbSupport');
$this->provideHook('icingadb/HostDetailExtension');
$this->provideHook('icingadb/ServiceDetailExtension');
$this->provideHook('ConfigFormEvents', GeneralConfigFormHook::class);

require_once __DIR__ . '/vendor/autoload.php';
