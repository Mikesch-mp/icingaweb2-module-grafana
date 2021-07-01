<?php

namespace Icinga\Module\Grafana\Forms\Graph;

//namespace Icinga\Module\Grafana\Forms\Config;

use Icinga\Module\Grafana\Forms\Config\GeneralConfigForm;
use Icinga\Module\Grafana\Helpers\Timeranges;
use Icinga\Exception\AlreadyExistsException;
use Icinga\Exception\IcingaException;
use Icinga\Exception\NotFoundError;
use Icinga\Forms\ConfigForm;
use Icinga\Web\Notification;

/**
 * Form for managing Grafana graphs with premade dashboards
 */
class GraphForm extends ConfigForm
{
    /**
     * Name of the graph if the form is bound to one
     *
     * @var string
     */
    protected $boundGraph;
    protected $grafanaVersion;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setName('form_config_grafana_graph');
    }

    /**
     * {@inheritdoc}
     */
    public function createElements(array $formData)
    {
        $this->addElement(
            'text',
            'name',
            array(
                'description'   => $this->translate(
                    'The name of the service or check_command which should use a premade dashboard'
                ),
                'label'         => $this->translate('Name'),
                'required'      => true
            )
        );

        $this->addElement(
            'text',
            'dashboard',
            array(
                'placeholder'   => 'DashboardName',
                'label'         => $this->translate('Dashboard name'),
                'description'   => $this->translate('Name of the Grafana dashboard that will be used.'),
                'required'      => true
            )
        );

        $this->addElement(
            'text',
            'dashboarduid',
            array(
                'label' => $this->translate('Dashboard UID'),
                'description' => $this->translate('UID of the dashboard.'),
                'required' => true,
            )
        );

        $this->addElement(
            'text',
            'panelId',
            array(
                'placeholder'   => 'example 1 or 1,4,10 ...',
                'label'         => $this->translate('PanelId(s)'),
                'description'   => $this->translate(
                    'Single panelId or comma seperated list of panelIds that will be used to show the graph(s).'
                ),
                'required'      => true
            )
        );

        $this->addElement(
            'number',
            'orgId',
            array(
                'placeholder'   => 'example 1 ...',
                'label'         => $this->translate('OrganizationId'),
                'description'   => $this->translate('Organization id where the dashboard is located.'),
                'required'      => false
            )
        );

        $this->addElement(
            'text',
            'customVars',
            array(
                'placeholder'   => '&var-example=$my_variable$',
                'label'         => $this->translate('Custom Variables'),
                'description'   => $this->translate('Custom variables from monitoring'),
                'required'      => false
            )
        );

        $this->addElement(
            'select',
            'timerange',
            array(
                'label'         => $this->translate('Timerange'),
                'multiOptions'  => array_merge(array('' => 'Use default'), Timeranges::getTimeranges()),
                'description'  => $this->translate('Timerange to use for the graph.'),
                'required'     => false
            )
        );
        $this->addElement(
            'number',
            'height',
            array(
                'label'         => $this->translate('Graph height'),
                'description'   => $this->translate('The graph height in pixel.'),
                'required'      => false
            )
        );
        $this->addElement(
            'number',
            'width',
            array(
                'label'         => $this->translate('Graph width'),
                'description'   => $this->translate('The graph width in pixel.'),
                'required'      => false
            )
        );
        $this->addElement(
            'select',
            'repeatable',
            array(
                'label' => $this->translate('Repeating Panel'),
                'value' => 'no',
                'multiOptions' => array(
                    'yes' => $this->translate('Yes'),
                    'no' => $this->translate('No'),
                ),
                'description' => $this->translate('Is this an auto repeating panel dashboard?'),
            )
        );
        $this->addElement(
            'number',
            'nmetrics',
            array(
                'label'         => $this->translate('Metrics per panel'),
                'description'   => $this->translate('The number of metrics in an auto repeating panel'),
                'required'      => false
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getSubmitLabel()
    {
        if (($submitLabel = parent::getSubmitLabel()) === null) {
            if ($this->boundGraph === null) {
                $submitLabel = $this->translate('Add graph');
            } else {
                $submitLabel = $this->translate('Update graph');
            }
        }
        return $submitLabel;
    }

    /**
     * {@inheritdoc}
     */
    public function onRequest()
    {
        // The base class implementation does not make sense here. We're not populating the whole configuration but
        // only a section
        return;
    }

    /**
     * {@inheritdoc}
     */
    public function onSuccess()
    {
        $name = $this->getElement('name')->getValue();
        $values = array(
            'dashboard'   => $this->getElement('dashboard')->getValue(),
            'panelId'     => $this->getElement('panelId')->getValue(),
            'orgId'     => $this->getElement('orgId')->getValue(),
            'customVars'  => $this->getElement('customVars')->getValue(),
            'timerange'   => $this->getElement('timerange')->getValue(),
            'height'      => $this->getElement('height')->getValue(),
            'width'       => $this->getElement('width')->getValue(),
            'repeatable'  => $this->getElement('repeatable')->getValue(),
            'nmetrics'    => $this->getElement('nmetrics')->getValue(),
            'dashboarduid' => $this->getElement('dashboarduid')->getValue()
        );

        if (empty($values['timerange'])) {
            $values['timerange'] = null;
        }
        if (empty($values['customVars'])) {
            $values['customVars'] = null;
        }
        if (empty($values['height'])) {
            $values['height'] = null;
        }
        if (empty($values['width'])) {
            $values['width'] = null;
        }
        if (empty($values['nmetrics']) && ($values['repeatable'] === "yes")) {
            $values['nmetrics'] = "1";
        }
        if (empty($values['nmetrics']) || ($values['repeatable'] === "no")) {
            $values['nmetrics'] = null;
        }
        if ($this->boundGraph === null) {
            $successNotification = $this->translate('Graph saved');
            try {
                $this->add($name, $values);
            } catch (AlreadyExistsException $e) {
                $this->addError($e->getMessage());
                return false;
            }
        } else {
            $successNotification = $this->translate('Graph updated');
            try {
                $this->update($name, $values, $this->boundGraph);
            } catch (IcingaException $e) {
                // Exception may be AlreadyExistsException or NotFoundError
                $this->addError($e->getMessage());
                return false;
            }
        }
        if ($this->save()) {
            Notification::success($successNotification);
            return true;
        }
        return false;
    }

    /**
     * Add a Grafana graph
     *
     * @param   string  $name           The name of the service
     * @param   array   $values
     *
     * @return  $this
     *
     * @throws  AlreadyExistsException  If the graph to add already exists
     */
    public function add($name, array $values)
    {
        if ($this->config->hasSection($name)) {
            throw new AlreadyExistsException(
                $this->translate('Can\'t add graph \'%s\'. Graph already exists'),
                $name
            );
        }
        $this->config->setSection($name, $values);
        return $this;
    }

    /**
     * Bind graph to this form
     *
     * @param   string  $name   The name of the graph
     *
     * @return  $this
     *
     * @throws  NotFoundError   If the given graph does not exist
     */
    public function bind($name)
    {
        if (! $this->config->hasSection($name)) {
            throw new NotFoundError(
                $this->translate('Can\'t load graph \'%s\'. Graph does not exist'),
                $name
            );
        }
        $this->boundGraph = $name;
        $graphs = $this->config->getSection($name)->toArray();
        $graphs['name'] = $name;
        $this->populate($graphs);
        return $this;
    }

    /**
     * Remove a graph
     *
     * @param   string  $name   The name of the graph
     *
     * @return  $this
     *
     * @throws  NotFoundError   If the role does not exist
     */
    public function remove($name)
    {
        if (! $this->config->hasSection($name)) {
            throw new NotFoundError(
                $this->translate('Can\'t remove graph \'%s\'. Graph does not exist'),
                $name
            );
        }
        $this->config->removeSection($name);
        return $this;
    }

    /**
     * Update a graph
     *
     * @param   string  $name       The possibly new name of the graph
     * @param   array   $values
     * @param   string  $oldName    The name of the graph to update
     *
     * @return  $this
     *
     * @throws  NotFoundError       If the graph to update does not exist
     */
    public function update($name, array $values, $oldName)
    {
        if ($name !== $oldName) {
            // The graph got a new name
            $this->remove($oldName);
            $this->add($name, $values);
        } else {
            if (! $this->config->hasSection($name)) {
                throw new NotFoundError(
                    $this->translate('Can\'t update graph \'%s\'. Graph does not exist'),
                    $name
                );
            }
            $this->config->setSection($name, $values);
        }
        return $this;
    }
}
