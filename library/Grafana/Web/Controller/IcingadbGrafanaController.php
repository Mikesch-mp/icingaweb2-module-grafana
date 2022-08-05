<?php

/* Icinga Graphite Web | (c) 2022 Icinga GmbH | GPLv2 */

namespace Icinga\Module\Grafana\Web\Controller;

use Icinga\Application\Modules\Module;
use Icinga\Module\Grafana\ProvidedHook\Icingadb\IcingadbSupport;
use Icinga\Module\Icingadb\Common\Auth;
use Icinga\Module\Icingadb\Common\Database;
use ipl\Web\Compat\CompatController;

class IcingadbGrafanaController extends CompatController
{
    use Auth;
    use Database;

    /** @var bool Whether to use icingadb as the backend */
    protected $useIcingadbAsBackend;

    protected function moduleInit()
    {
        $this->useIcingadbAsBackend = Module::exists('icingadb') && IcingadbSupport::useIcingaDbAsBackend();
    }
}
