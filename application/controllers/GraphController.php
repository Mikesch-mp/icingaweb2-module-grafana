<?php

namespace Icinga\Module\Grafana\Controllers;

use Icinga\Exception\NotFoundError;
use Icinga\Forms\ConfirmRemovalForm;
use Icinga\Module\Grafana\Forms\Graph\GraphForm;
use Icinga\Web\Controller;
use Icinga\Web\Notification;

class GraphController extends Controller
{
    public function init()
    {
        $this->assertPermission('grafana/graphconfig');
    }

    /**
     * List Grafana graphs
     */
    public function indexAction()
    {
/*
        $this->getTabs()->add('graphs', array(
            'active'    => true,
            'label'     => $this->translate('Graphs'),
            'url'       => $this->getRequest()->getUrl()
        ));
        $this->view->graphs = $this->Config('graphs');
*/
        $this->view->tabs = $this->Module()->getConfigTabs()->activate('graph');
        $this->view->graphs = $this->Config('graphs');
    }
    /**
     * Add a new graph
     */
    public function newAction()
    {
        $this->getTabs()->add('new-graph', array(
            'active'    => true,
            'label'     => $this->translate('New Graph'),
            'url'       => $this->getRequest()->getUrl()
        ));
        $graphs = new GraphForm();
        $graphs
            ->setIniConfig($this->Config('graphs'))
            ->setRedirectUrl('grafana/graph')
            ->handleRequest();
        $this->view->form = $graphs;
    }
    /**
     * Remove a graph
     */
    public function removeAction()
    {
        $graph = $this->params->getRequired('graph');
        $this->getTabs()->add('remove-graph', array(
            'active'    => true,
            'label'     => $this->translate('Remove Graph'),
            'url'       => $this->getRequest()->getUrl()
        ));
        $graphs = new GraphForm();
        try {
            $graphs
                ->setIniConfig($this->Config('graphs'))
                ->bind($graph);
        } catch (NotFoundError $e) {
            $this->httpNotFound($e->getMessage());
        }
        $confirmation = new ConfirmRemovalForm(array(
            'onSuccess' => function (ConfirmRemovalForm $confirmation) use ($graph, $graphs) {
                $graphs->remove($graph);
                if ($graphs->save()) {
                    Notification::success(mt('grafana', 'Graph removed'));
                    return true;
                }
                return false;
            }
        ));
        $confirmation
            ->setRedirectUrl('grafana/graph')
            ->setSubmitLabel($this->translate('Remove Graph'))
            ->handleRequest();
        $this->view->form = $confirmation;
    }
    /**
     * Update a graph
     */
    public function updateAction()
    {
        $graph = $this->params->getRequired('graph');
        $this->getTabs()->add('update-graph', array(
            'active'    => true,
            'label'     => $this->translate('Update Graph'),
            'url'       => $this->getRequest()->getUrl()
        ));
        $graphs = new GraphForm();
        try {
            $graphs
                ->setIniConfig($this->Config('graphs'))
                ->bind($graph);
        } catch (NotFoundError $e) {
            $this->httpNotFound($e->getMessage());
        }
        $graphs
            ->setRedirectUrl('grafana/graph')
            ->handleRequest();
        $this->view->form = $graphs;
    }
}
