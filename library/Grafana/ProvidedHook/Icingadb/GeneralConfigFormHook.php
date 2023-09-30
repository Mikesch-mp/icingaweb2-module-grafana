<?php

namespace Icinga\Module\Grafana\ProvidedHook\Icingadb;

use Icinga\Application\Hook\ConfigFormEventsHook;
use Icinga\Module\Grafana\Forms\Config\GeneralConfigForm;
use Icinga\Web\Form;
use Icinga\Module\Grafana\Helpers\JwtToken;

class GeneralConfigFormHook extends ConfigFormEventsHook
{

    public function appliesTo(Form $form)
    {
        return $form instanceof GeneralConfigForm;
    }

    public function onSuccess(Form $form)
    {
        if($form->getElement('grafana_jwtEnable')->getValue()) {
            JwtToken::generateRsaKeys();
        }
    }
}
