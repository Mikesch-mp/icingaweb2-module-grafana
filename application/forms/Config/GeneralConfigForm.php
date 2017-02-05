<?php
namespace Icinga\Module\Grafana\Forms\Config;

use Icinga\Application\Config;
use Icinga\Forms\ConfigForm;

class GeneralConfigForm extends ConfigForm
{
    /**
     * Initialize this form
     */
    public function init()
    {
        $this->setName('form_config_grafana_general');
        $this->setSubmitLabel('Save Changes');
    }

    /**
     * {@inheritdoc}
     */
    public function createElements(array $formData)
    {
        $this->addElement(
            'text',
            'grafana_username',
            array(
                'placeholder'   	=> '***',
                'label'         	=> $this->translate('Username'),
                'description'   	=> $this->translate('The HTTP Basic Auth user name used to access Grafana.')
            )
        );
        $this->addElement(
            'password',
            'grafana_password',
            array(
		'renderPassword'	=> true,
                'placeholder'   	=> '***',
                'label'         	=> $this->translate('Password'),
                'description'   	=> $this->translate('The HTTP Basic Auth password used to access Grafana.')
            )
        );
        $this->addElement(
            'text',
            'grafana_host',
            array(
                'value'         	=> 'server.name:3000',
                'label'         	=> $this->translate('Host'),
                'description'   	=> $this->translate('Host name of the Grafana server.'),
                'required'              => true
            )
        );
        $this->addElement(
            'select',
            'grafana_protocol',
            array(
                'label'         	=> 'Protocol',
 		'multiOptions' => array(
                    		'http' => $this->translate('Unsecure: http'),
                    		'https' => $this->translate('Secure: https'),
            	),
                'description'   	=> $this->translate('Protocol used to access Grafana.'),
            'class' => 'autosubmit',
            )
        );

        $this->addElement(
            'text',
            'grafana_height',
            array(
                'value'           	=> '280',
                'label'                 => $this->translate('Graph height'),
                'description'           => $this->translate('Graph height in pixels.')
            )
        );
        $this->addElement(
            'text',
            'grafana_width',
            array(
                'value'           	=> '640',
                'label'                 => $this->translate('Graph width'),
                'description'           => $this->translate('Graph width in pixels.')
            )
        );
/**
        $this->addElement(
            'select',
            'grafana_use_nrpe_command',
            array(
                'label'                 => $this->translate('Use nrpe_command'),
		'multiOptions' => array(
                                'true' => $this->translate('True'),
                                'false' => $this->translate('False'),
                ),
                'description'           => $this->translate(
                        'Use nrpe_command instead of check_command as measurements.'
                        . 'See README on how to configure Icinga2 to use nrpe_command in InfluxdbWriter feature.'
                )
            )
        );
**/
    }
}

