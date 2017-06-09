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
            'grafana_host',
            array(
                'placeholder' => 'example server.name:3000',
                'label' => $this->translate('Host'),
                'description' => $this->translate('Host name of the Grafana server.'),
                'required' => true
            )
        );
        $this->addElement(
            'select',
            'grafana_protocol',
            array(
                'label' => 'Protocol',
                'multiOptions' => array(
                    'http' => $this->translate('Unsecure: http'),
                    'https' => $this->translate('Secure: https'),
                ),
                'description' => $this->translate('Protocol used to access Grafana.'),
             )
        );
        $this->addElement(
            'select',
            'grafana_timerange',
            array(
                'label' => $this->translate('Timerange'),
                'multiOptions' => array(
                    '' => $this->translate('Use default'),
                    '5m' => $this->translate('Last 5 minutes'),
                    '15m' => $this->translate('Last 15 minutes'),
                    '30m' => $this->translate('Last 30 minutes'),
                    '1h' => $this->translate('Last 1 hour'),
                    '3h' => $this->translate('Last 3 hours'),
                    '6h' => $this->translate('Last 6 hours'),
                    '8h' => $this->translate('Last 8 hours'),
                    '12h' => $this->translate('Last 12 hours'),
                    '24h' => $this->translate('Last 24 hours'),
                    '2d' => $this->translate('Last 2 days'),
                    '7d' => $this->translate('Last 7 days'),
                    '30d' => $this->translate('Last 30 days'),
                    '60d' => $this->translate('Last 60 days'),
                    '6M' => $this->translate('Last 6 months'),
                    '1y' => $this->translate('Last 1 year'),
                    '2y' => $this->translate('Last 2 years'),
                ),
                'description' => $this->translate('The default timerange to use for the graphs.')
            )
        );
        $this->addElement(
            'text',
            'grafana_custvardisable',
            array(
                'label' => $this->translate('Disable customvar'),
                'description' => $this->translate('Name of the custom variable that, if set, will disable the graph.'),
            )
        );
        $this->addElement(
            'text',
            'grafana_defaultdashboard',
            array(
                'value' => 'icinga2-default',
                'label' => $this->translate('Default dashboard'),
                'description' => $this->translate('Name of the default dashboard.'),
            )
        );
        $this->addElement(
            'text',
            'grafana_defaultorgid',
            array(
                'value' => '1',
                'label' => $this->translate('Default Organization Id'),
                'description' => $this->translate('Id of the default organization.'),
            )
        );
        $this->addElement(
            'checkbox',
            'grafana_shadows',
            array(
                'value'=> false,
                'label' => $this->translate('Show shadows'),
                'description' => $this->translate('Show shadows around the graph.'),
            )
        );
        $this->addElement(
            'select',
            'grafana_defaultdashboardstore',
            array(
                'label' => $this->translate('Datasource Backend'),
                'multiOptions' => array(
                    'db' => $this->translate('Database'),
                    'file' => $this->translate('File'),
                ),
                'description' => $this->translate('Grafana Backend Type.')
            )
        );
        $this->addElement(
            'select',
            'grafana_theme',
            array(
                'label' => $this->translate('Grafana theme'),
                'value' => 'light',
                'multiOptions' => array(
                    'light' => $this->translate('Light'),
                    'dark' => $this->translate('Dark'),
                ),
                'description' => $this->translate('Grafana theme that will be used.')
            )
        );
        $this->addElement(
            'select',
            'grafana_datasource',
            array(
                'label' => $this->translate('Datasource Type'),
                'multiOptions' => array(
                    'influxdb' => $this->translate('InfluxDB'),
                    'graphite' => $this->translate('Graphite'),
                    'pnp' => $this->translate('PNP'),
                ),
                'description' => $this->translate('Grafana Datasource Type.')
            )
        );
        $this->addElement(
            'select',
            'grafana_accessmode',
            array(
                'label' => $this->translate('Grafana access'),
                'multiOptions' => array(
                    'direct' => $this->translate('Direct'),
                    'proxy' => $this->translate('Proxy'),
                    'iframe' => $this->translate('iFrame'),
                ),
                'description' => $this->translate('User access Grafana directly or module proxies graphs.'),
                'class' => 'autosubmit',
                'required' => true
            )
        );

        if (isset($formData['grafana_accessmode']) && $formData['grafana_accessmode'] === 'proxy') {
            $this->addElement(
                'number',
                'grafana_proxytimeout',
                array(
                    'label' => $this->translate('Proxy Timeout'),
                    'placeholder' => '5',
                    'description' => $this->translate('Timeout in seconds for proxy mode to fetch images.')
                )
            );
            $this->addElement(
                'select',
                'grafana_authanon',
                array(
                    'label' => $this->translate('Anonymous Access'),
                    'value' => 'yes',
                    'multiOptions' => array(
                        'yes' => $this->translate('Yes'),
                        'no' => $this->translate('No'),
                    ),
                    'description' => $this->translate('Anonymous or username/password access to Grafana server.'),
                    'class' => 'autosubmit'
                )
            );
            if (isset($formData['grafana_authanon']) && $formData['grafana_authanon'] === 'no' ) {
                $this->addElement(
                    'text',
                    'grafana_username',
                    array(
                        'label' => $this->translate('Username'),
                        'description' => $this->translate('The HTTP Basic Auth user name used to access Grafana.'),
                        'required' => true
                    )
                );
                $this->addElement(
                    'password',
                    'grafana_password',
                    array(
                        'renderPassword' => true,
                        'label' => $this->translate('Password'),
                        'description' => $this->translate('The HTTP Basic Auth password used to access Grafana.'),
                        'required' => true
                    )
                );
            }
        }

        if (isset($formData['grafana_accessmode']) && $formData['grafana_accessmode'] === 'direct') {
            $this->addElement(
                'select',
                'grafana_directrefresh',
                array(
                    'label' => $this->translate('Refresh on direct'),
                    'value' => 'no',
                    'multiOptions' => array(
                        'yes' => $this->translate('Yes'),
                        'no' => $this->translate('No'),
                    ),
                    'description' => $this->translate('Refresh fraphs on direct access.')
                )
            );
        }

        if (isset($formData['grafana_accessmode']) && ( $formData['grafana_accessmode'] != 'iframe' )) {
            $this->addElement(
                'text',
                'grafana_height',
                array(
                    'value' => '280',
                    'label' => $this->translate('Graph height'),
                    'description' => $this->translate('The default graph height in pixels.')
                )
            );
            $this->addElement(
                'text',
                'grafana_width',
                array(
                    'value' => '640',
                    'label' => $this->translate('Graph width'),
                    'description' => $this->translate('The default graph width in pixels.')
                )
            );
            $this->addElement(
                'select',
                'grafana_enableLink',
                array(
                    'label' => $this->translate('Enable link'),
                    'value' => 'no',
                    'multiOptions' => array(
                        'yes' => $this->translate('Yes'),
                        'no' => $this->translate('No'),
                    ),
                    'description' => $this->translate('Image is an link to the dashboard on the Grafana server.'),
                    'class' => 'autosubmit'
                )
            );
        }
        if (isset($formData['grafana_enableLink']) && ( $formData['grafana_enableLink'] === 'yes') && ( $formData['grafana_accessmode'] != 'iframe' )) {
            $this->addElement(
                'select',
                'grafana_usepublic',
                array(
                    'label' => $this->translate('Use public links'),
                    'value' => 'no',
                    'multiOptions' => array(
                        'yes' => $this->translate('Yes'),
                        'no' => $this->translate('No'),
                    ),
                    'description' => $this->translate('Use public url that is different from host above.'),
                    'class' => 'autosubmit'
                )
            );
        }
        if (isset($formData['grafana_usepublic']) && ( $formData['grafana_usepublic'] === 'yes' ) && ( $formData['grafana_accessmode'] != 'iframe' )) {
            $this->addElement(
                'text',
                'grafana_publichost',
                array(
                    'placeholder' => 'example server.name:3000',
                    'label' => $this->translate('Public host'),
                    'description' => $this->translate('Public host name of the Grafana server.'),
                    'required' => true
                )
            );
            $this->addElement(
                'select',
                'grafana_publicprotocol',
                array(
                    'label' => 'Public protocol',
                    'multiOptions' => array(
                        'http' => $this->translate('Unsecure: http'),
                        'https' => $this->translate('Secure: https'),
                    ),
                    'description' => $this->translate('Public protocol used to access Grafana.'),
                )
            );
        }
    }
}

