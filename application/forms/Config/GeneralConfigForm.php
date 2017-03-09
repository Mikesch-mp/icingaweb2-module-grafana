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
                'placeholder'         	=> 'server.name:3000',
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
        $this->addElement(
            'select',
            'grafana_timerange',
            array(
                'label'                 => $this->translate('Timerange'),
                'multiOptions' => array(
                                 ''    => $this->translate('Use default'),
                                 '5m'  => $this->translate('Last 5 minutes'),
                                 '15m'  => $this->translate('Last 15 minutes'),
                                 '30m'  => $this->translate('Last 30 minutes'),
                                 '1h'  => $this->translate('Last 1 hour'),
                                 '3h'  => $this->translate('Last 3 hours'),
                                 '6h'  => $this->translate('Last 6 hours'),
                                 '8h'  => $this->translate('Last 8 hours'),
                                 '12h' => $this->translate('Last 12 hours'),
                                 '24h' => $this->translate('Last 24 hours'),
                                 '2d'  => $this->translate('Last 2 days'),
                                 '7d'  => $this->translate('Last 7 days'),
                                 '30d'  => $this->translate('Last 30 days'),
                                 '60d'  => $this->translate('Last 60 days'),
                                 '6M'  => $this->translate('Last 6 months'),
                                 '1y'  => $this->translate('Last 1 year'),
                                 '2y'  => $this->translate('Last 2 years'),
                ),
                'description'           => $this->translate('Timerange to use for the graphs.')
            )
        );
        $this->addElement(
            'select',
            'grafana_enableLink',
            array(
                'label'                 => $this->translate('Enable link'),
		'multiOptions' => array(
                                'yes' => $this->translate('Yes'),
                                'no' => $this->translate('No'),
                ),
                'description'           => $this->translate('Image is an link to the dashboard on the Grafana server.')
            )
        );
        $this->addElement(
            'text',
            'grafana_defaultdashboard',
            array(
                'value'                 => 'icinga2-default',
                'label'                 => $this->translate('Default dashboard'),
                'description'           => $this->translate('Name of the default dashboard.'),
            )
        );
        $this->addElement(
            'select',
            'grafana_datasource',
            array(
                'label'                 => $this->translate('Datasource Type'),
                'multiOptions' => array(
                                 'influxdb'   => $this->translate('InfluxDB'),
                                 'graphite'   => $this->translate('Graphite'),
                                 'pnp'        => $this->translate('PNP'),
                ),
                'description'           => $this->translate('Grafana Datasource Type.')
            )
        );
        $this->addElement(
            'text',
            'grafana_excludes',
            array(
                'value'                 => '',
                'label'                 => $this->translate('Exclude services'),
                'description'           => $this->translate('Define your excludes separated by ","')
            )
        );

    }
}
