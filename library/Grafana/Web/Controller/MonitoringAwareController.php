<?php
namespace Icinga\Module\Grafana\Web\Controller;
use Icinga\Module\Monitoring\Controller;
use Icinga\Module\Monitoring\DataView\DataView;
abstract class MonitoringAwareController extends Controller
{
    /**
     * Restrict the given monitored object query for the currently authenticated user
     *
     * @param   DataView    $dataView
     *
     * @return  DataView                The given data view
     */
    protected function applyMonitoringRestriction(DataView $dataView)
    {
        $this->applyRestriction('monitoring/filter/objects', $dataView);
        return $dataView;
    }
}