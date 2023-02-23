<?php

use Icinga\Module\Grafana\ProvidedHook\Icingadb\IcingadbSupport;

$this->provideHook('icingadb/HostActions');
$this->provideHook('icingadb/IcingadbSupport');
$this->provideHook('icingadb/HostDetailExtension');
$this->provideHook('icingadb/ServiceDetailExtension');
