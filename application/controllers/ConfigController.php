<?php

namespace Icinga\Module\Grafana\Controllers;

use Icinga\Web\Controller;
use Icinga\Module\Grafana\Forms\Config\GeneralConfigForm;

class ConfigController extends Controller
{
    public function init()
    {
        $this->assertPermission('config/modules');
    }

    public function indexAction()
    {
        $form = new GeneralConfigForm();
        $form->setIniConfig($this->Config());
        $form->handleRequest();

        $this->view->form = $form;
        $this->view->tabs = $this->Module()->getConfigTabs()->activate('config');
    }
}
