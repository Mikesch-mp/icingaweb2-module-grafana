<?php

namespace Icinga\Module\Grafana\Web\Widget;

use Icinga\Web\Url;
use Icinga\Web\Widget\Tabextension\Tabextension;
use Icinga\Web\Widget\Tabs;

/**
 * Tabextension that allows to print the current page
 *
 * Displayed as a dropdown field in the tabs
 */
class PrintAction implements Tabextension
{
    /**
     * Applies the dashboard actions to the provided tabset
     *
     * @param   Tabs $tabs The tabs object to extend with
     */
    public function apply(Tabs $tabs)
    {
        $tabs->addAsDropdown(
            'print',
            [
                'icon'      => 'print',
                'target'=> '_blank',
                'label'     => t('Print'),
                'url'       => (htmlspecialchars_decode(Url::fromRequest()->getAbsoluteUrl())). '&showFullscreen',
            ]
        );
    }
}