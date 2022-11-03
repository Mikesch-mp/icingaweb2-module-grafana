<?php

namespace Icinga\Module\Grafana\Forms\Config;

use Icinga\Module\Grafana\Helpers\Timeranges;
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
                'class' => 'autosubmit',
             )
        );

        if (isset($formData['grafana_protocol']) && $formData['grafana_protocol'] === 'https' ) {
            $this->addElement(
                'checkbox',
                'grafana_ssl_verifypeer',
                array(
                    'value'=> false,
                    'label' => $this->translate('SSL verify peer'),
                    'description' => $this->translate('Verify the peer\'s SSL certificate.'),
                )
            );

            $this->addElement(
                'checkbox',
                'grafana_ssl_verifyhost',
                array(
                    'value'=> false,
                    'label' => $this->translate('SSL verify host'),
                    'description' => $this->translate('Verify the certificate\'s name against host.'),
                )
            );
        }
        $this->addElement(
            'select',
            'grafana_timerange',
            array(
                'label' => $this->translate('Timerange'),
                'multiOptions' => array_merge(array('' => 'Use default (6h)'), Timeranges::getTimeranges()),
                'description' => $this->translate('The default timerange to use for the graphs.')
            )
        );
        $this->addElement(
            'select',
            'grafana_timerangeAll',
            array(
                'label' => $this->translate('Timerange ShowAll'),
                'value' => '1w/w',
                'multiOptions' => Timeranges::getTimeranges(),
                'description' => $this->translate('The default timerange to use for show all graphs.')
            )
        );
        $this->addElement(
            'text',
            'grafana_custvardisable',
            array(
                'label' => $this->translate('Disable customvar'),
                'description' => $this->translate('Name of the custom variable that, if set to true, will disable the graph.'),
            )
        );
        $this->addElement(
            'text',
            'grafana_custvarconfig',
            array(
                'label' => $this->translate('Config customvar'),
                'description' => $this->translate('Name of the custom variable that, if set, hold the config name to be used.'),
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
            'grafana_defaultdashboarduid',
            array(
                'label' => $this->translate('Default dashboard UID'),
                'description' => $this->translate('UID of the default dashboard.'),
                'required' => true,
            )
        );
        $this->addElement(
            'number',
            'grafana_defaultdashboardpanelid',
            array(
                'value' => '1',
                'label' => $this->translate('Default panel id'),
                'description' => $this->translate('Id of the panel used in the default dashboard.'),
                'required' => true,
            )
        );
        $this->addElement(
            'number',
            'grafana_defaultorgid',
            array(
                'value' => '1',
                'label' => $this->translate('Default organization id'),
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
                    'indirectproxy' => $this->translate('Indirect Proxy'),
                    'iframe' => $this->translate('iFrame'),
                ),
                'description' => $this->translate('User access Grafana directly or module proxies graphs.'),
                'class' => 'autosubmit',
                'required' => true
            )
        );

        if (isset($formData['grafana_accessmode']) && $formData['grafana_accessmode'] === 'indirectproxy') {
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
                'grafana_authentication',
                array(
                    'label' => $this->translate('Authentication type'),
                    'value' => 'anon',
                    'multiOptions' => array(
                        'anon' => $this->translate('Anonymous'),
                        'token' => $this->translate('API Token'),
                        'basic' => $this->translate('Username & Password'),
                    ),
                    'description' => $this->translate('Authentication type used for Grafana access.'),
                    'class' => 'autosubmit'
                )
            );
            if (isset($formData['grafana_authentication']) && $formData['grafana_authentication'] === 'basic' ) {
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
            } elseif (isset($formData['grafana_authentication']) && $formData['grafana_authentication'] === 'token' ) {
                $this->addElement(
                    'text',
                    'grafana_apitoken',
                    array(
                        'label' => $this->translate('API Token'),
                        'description' => $this->translate('The API token used to access Grafana.'),
                        'required' => true
                    )
                );
            }
        }

        if (isset($formData['grafana_accessmode']) && $formData['grafana_accessmode'] === 'indirectproxy') {
            $this->addElement(
                'select',
                'grafana_indirectproxyrefresh',
                array(
                    'label' => $this->translate('Refresh on indirect proxy'),
                    'value' => 'yes',
                    'multiOptions' => array(
                        'yes' => $this->translate('Yes'),
                        'no' => $this->translate('No'),
                    ),
                    'description' => $this->translate('Refresh graphs on indirect proxy mode.')
                )
            );
        }

        if (isset($formData['grafana_accessmode'])) {
            $this->addElement(
                'number',
                'grafana_height',
                array(
                    'value' => '280',
                    'label' => $this->translate('Graph height'),
                    'description' => $this->translate('The default graph height in pixels.')
                )
            );
            if ( $formData['grafana_accessmode'] != 'iframe' ) {
	            $this->addElement(
	                'number',
	                'grafana_width',
	                array(
	                    'value' => '640',
	                    'label' => $this->translate('Graph width'),
	                    'description' => $this->translate('The default graph width in pixels.')
	                )
	            );
            }
        } 
 
        if (isset($formData['grafana_accessmode'])) {
            if ($formData['grafana_accessmode'] === 'indirectproxy') {
                $desc = 'Image is a link to the dashboard on the Grafana server.';
            }
            else {
                $desc = 'Above image is a link to the dashboard on the Grafana server.';
            }
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
                    'description' => $this->translate($desc),
                    'class' => 'autosubmit'
                )
            );
        }
        if (isset($formData['grafana_enableLink']) && ( $formData['grafana_enableLink'] === 'yes')) {
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
        if (isset($formData['grafana_usepublic']) && ( $formData['grafana_usepublic'] === 'yes' )) {
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
        $this->addElement(
            'checkbox',
            'grafana_debug',
            array(
                'value'=> false,
                'label' => $this->translate('Show debug'),
                'description' => $this->translate('Show debuging information.'),
            )
        );
    }
}
