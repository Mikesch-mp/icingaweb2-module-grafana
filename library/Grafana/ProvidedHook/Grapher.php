<?php

namespace Icinga\Module\Grafana\ProvidedHook;

use Icinga\Application\Icinga;
use Icinga\Application\Config;
use Icinga\Exception\ConfigurationError;
use Exception;
use Icinga\Application\Hook\GrapherHook;
use Icinga\Module\Monitoring\Object\MonitoredObject;
use Icinga\Module\Monitoring\Object\Host;
use Icinga\Module\Monitoring\Object\Service;
use Icinga\Web\Url;
use Icinga\Web\View;
use Icinga\Module\Grafana\Util;

class Grapher extends GrapherHook
{
    protected $config;
    protected $graphConfig;
    protected $auth;
    protected $grafanaHost           = null;
    protected $grafanaTheme          = 'light';
    protected $protocol              = "http";
    protected $usePublic             = "no";
    protected $publicHost            = null;
    protected $publicProtocol        = "http";
    protected $timerange             = "6h";
    protected $username              = null;
    protected $password              = null;
    protected $width                 = 640;
    protected $height                = 280;
    protected $enableLink            = true;
    protected $defaultDashboard      = "icinga2-default";
    protected $defaultOrgId          = "1";
    protected $shadows               = false;
    protected $defaultDashboardStore = "db";
    protected $dataSource            = null;
    protected $accessMode            = "proxy";
    protected $proxyTimeout          = "5";
    protected $refresh               = "no";
    protected $title                 = "<h2>Performance Graph</h2>";
    protected $custvardisable        = "grafana_graph_disable";
    protected $repeatable            = "no";
    protected $numberMetrics         = "1";
    protected $debug                 = false;
    protected $timeRanges = array(
        'Minutes' => array(
            '5m' => '5 minutes',
            '15m' => '15 minutes',
            '30m' => '30 minutes',
            '45m' => '45 minutes'
        ),
        'Hours' => array(
            '1h' => '1 hour',
            '3h' => '3 hours',
            '6h' => '6 hours',
            '8h' => '8 hours',
            '12h' => '12 hours',
            '24h' => '24 hours'
        ),
        'Days' => array (
            '2d' => '2 days',
            '7d' => '7 days',
            '14d' => '14 days',
            '30d' => '30 days',
        ),
        'Months' => array (
            '2M' => '2 month',
            '6M' => '6 months',
            '9M' => '9 months'
        ),
        'Years' => array(
            '1y' => '1 year',
            '2y' => '2 years',
            '3y' => '3 years'
        )
    );

    protected function init()
    {

        $this->config = Config::module('grafana')->getSection('grafana');
        $this->grafanaHost = $this->config->get('host', $this->grafanaHost);
            if ($this->grafanaHost == null) {
                throw new ConfigurationError(
                    'No Grafana host configured!'
            );
        }
        $this->enableLink = $this->config->get('enableLink', $this->enableLink);
        if ( $this->enableLink == "yes" ) {
            $this->usePublic = $this->config->get('usepublic', $this->usePublic);
            if ( $this->usePublic == "yes" ) {
                $this->publicHost = $this->config->get('publichost', $this->publicHost);
                if ($this->publicHost == null) {
                    throw new ConfigurationError(
                        'No Grafana public host configured!'
                    );
                }
                $this->publicProtocol = $this->config->get('publicprotocol', $this->publicProtocol);
            } else {
                $this->publicHost = $this->grafanaHost;
                $this->publicProtocol = $this->protocol;
            }
        }

        $this->protocol = $this->config->get('protocol', $this->protocol);

        // Confid needed for Grafana
        $this->defaultDashboard = $this->config->get('defaultdashboard', $this->defaultDashboard);
        $this->defaultOrgId = $this->config->get('defaultorgid', $this->defaultOrgId);
        $this->grafanaTheme = $this->config->get('theme', $this->grafanaTheme);
        $this->defaultDashboardStore = $this->config->get('defaultdashboardstore', $this->defaultDashboardStore);
        $this->height = $this->config->get('height', $this->height);
        $this->width = $this->config->get('width', $this->width);

        // Check if there is a timerange in url params
        $this->timerange = Url::fromRequest()->hasParam('timerange') ? Url::fromRequest()->getParam('timerange') : $this->config->get('timerange', $this->timerange);


        $this->accessMode = $this->config->get('accessmode', $this->accessMode);
        $this->proxyTimeout = $this->config->get('proxytimeout', $this->proxyTimeout);

        /**
         * Direct mode refresh graphs trick
         */
        $this->refresh = $this->config->get('directrefresh', $this->refresh);
        $this->refresh = ($this->refresh == "yes" && $this->accessMode == "direct" ? time() : 'now');

        /**
         * Datasource needed to regex special chars
         */
        $this->dataSource = $this->config->get('datasource', $this->dataSource);

        /**
         * Display shadows around graph
         */
        $this->shadows = $this->config->get('shadows', $this->shadows);
        /**
         * Name of the custom varibale to disable graph
         */
        $this->custvardisable = ($this->config->get('custvardisable', $this->custvardisable));

        $this->debug = ($this->config->get('debug', $this->debug));

        /**
         * Username & Password
         */
        $this->username = $this->config->get('username', $this->username);
        $this->password = $this->config->get('password', $this->password);
        if ($this->username != null) {
            if ($this->password != null) {
                $this->auth = $this->username . ":" . $this->password;
            } else {
                $this->auth = $this->username;
            }
        } else {
            $this->auth = "";
        }
    }

    private function getGraphConf($serviceName, $serviceCommand = NULL)
    {

        $this->graphConfig = Config::module('grafana', 'graphs');

        if ($serviceCommand != NULL) {
            if ($this->graphConfig->hasSection(strtok($serviceName, ' ')) && ($this->graphConfig->hasSection($serviceName) == False)) {
                $serviceName = strtok($serviceName, ' ');
            }
            if ($this->graphConfig->hasSection(strtok($serviceName, ' ')) == False && ($this->graphConfig->hasSection($serviceName) == False)) {
                $serviceName = $serviceCommand;
                if($this->graphConfig->hasSection($serviceCommand) == False && $this->defaultDashboard == 'none') {
                    return NULL;
                }
            }
        }

        $this->dashboard = str_replace(" ", "-", $this->getGraphConfigOption($serviceName, 'dashboard', $this->defaultDashboard));
        $this->dashboardstore = $this->getGraphConfigOption($serviceName, 'dashboardstore', $this->defaultDashboardStore);
        $this->panelId = $this->getGraphConfigOption($serviceName, 'panelId', '1');
        $this->orgId = $this->getGraphConfigOption($serviceName, 'orgId', $this->defaultOrgId);
        $this->customVars = $this->getGraphConfigOption($serviceName, 'customVars', '');
        $this->timerange = Url::fromRequest()->hasParam('timerange') ? Url::fromRequest()->getParam('timerange') : $this->getGraphConfigOption($serviceName, 'timerange', $this->timerange);
        $this->height = $this->getGraphConfigOption($serviceName, 'height', $this->height);
        $this->width = $this->getGraphConfigOption($serviceName, 'width', $this->width);
        $this->repeatable = $this->getGraphConfigOption($serviceName, 'repeatable', $this->repeatable);
        $this->numberMetrics = $this->getGraphConfigOption($serviceName, 'numberMetrics', $this->numberMetrics);

        return $this;
    }

    private function getGraphConfigOption($section, $option, $default = NULL) {
        $value = $this->graphConfig->get($section, $option, $default);
        if(empty($value)) {
            return $default;
        }
        return $value;
    }

    private function getTimerangeLink($object, $rangeName, $timeRange)
    {
        $this->view = Icinga::app()->getViewRenderer()->view;
        if ($object instanceof Host) {
            $array = array(
                'host' => $object->host_name,
                'timerange' => $timeRange
            );
            $link = 'monitoring/host/show';
        } else {
            $array = array(
                'host' => $object->host->getName(),
                'service' => $object->service_description,
                'timerange' => $timeRange
            );
            $link = 'monitoring/service/show';
        }

        return $this->view->qlink(
            $rangeName,
            $link,
            $array,
            array(
                'class' => 'action-link',
                'data-base-target' => '_self',
                'title' => 'Set timerange for graph to ' . $rangeName
            )
        );
    }

    //returns false on error, previewHTML is passed as reference
    private function getMyPreviewHtml($serviceName, $hostName, &$previewHtml)
    {
        $imgClass = $this->shadows ? "grafana-img grafana-img-shadows" : "grafana-img";
	$hostName = rawurlencode($hostName);
	$serviceName = rawurlencode($serviceName);

        if ($this->accessMode == "proxy") {

            // Test whether curl is loaded
            if (extension_loaded('curl') === false) {
                $previewHtml = "<b>CURL extension is missing. Please install CURL for PHP and ensure it is loaded.</b>";
                return false;
            }

            $pngUrl = sprintf(
                '%s://%s/render/dashboard-solo/%s/%s?var-hostname=%s&var-service=%s%s&panelId=%s&orgId=%s&width=%s&height=%s&theme=%s&from=now-%s&to=now',
                $this->protocol,
                $this->grafanaHost,
                $this->dashboardstore,
                $this->dashboard,
                $hostName,
                $serviceName,
                $this->customVars,
                $this->panelId,
                $this->orgId,
                $this->width,
                $this->height,
                $this->grafanaTheme,
                $this->timerange
            );

            // fetch image with curl
            $curl_handle = curl_init();
            $curl_opts = array(
                CURLOPT_URL => $pngUrl,
                CURLOPT_CONNECTTIMEOUT => 2,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false, //TODO: config option
                CURLOPT_SSL_VERIFYHOST => 0, //TODO: config option
                CURLOPT_TIMEOUT => $this->proxyTimeout,
                CURLOPT_USERPWD => "$this->auth",
                CURLOPT_HTTPAUTH, CURLAUTH_ANY
            );

            curl_setopt_array($curl_handle, $curl_opts);
            $res = curl_exec($curl_handle);

            $statusCode = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

            if($this->debug && $res === false) {
                $previewHtml .= "<b>ImgageURL:</b> ". $pngUrl ."</br>";
            }

            if ($res === false) {
                $previewHtml .= "<b>Cannot fetch graph with curl:</b> '" . curl_error($curl_handle) . "'.";

                //provide a hint for 'Failed to connect to ...: Permission denied'
                if (curl_errno($curl_handle) == 7) {
                    $previewHtml .= " Check SELinux/Firewall.";
                }
                return false;
            }

            if ($statusCode > 299) {
                $error = @json_decode($res);
                $previewHtml .= "<b>Cannot fetch Grafana graph: " . Util::httpStatusCodeToString($statusCode) .
                    " ($statusCode)</b>: " . ($error !== null && property_exists($error, 'message') ? $error->message : "");
                return false;
            }

            curl_close($curl_handle);

            $img = 'data:image/png;base64,' . base64_encode($res);
            $imghtml = '<img src="%s" alt="%s" width="%d" height="%d" class="'. $imgClass .'"/>';
            $previewHtml = sprintf(
                $imghtml,
                $img,
                $serviceName,
                $this->width,
                $this->height
            );
        } elseif ($this->accessMode == "direct") {
            $imghtml = '<img src="%s://%s/render/dashboard-solo/%s/%s?var-hostname=%s&var-service=%s%s&panelId=%s&orgId=%s&width=%s&height=%s&theme=%s&from=now-%s&to=now&trickrefresh=%s" alt="%s" width="%d" height="%d" class="'. $imgClass .'"/>';
            $previewHtml = sprintf(
                $imghtml,
                $this->protocol,
                $this->grafanaHost,
                $this->dashboardstore,
                $this->dashboard,
                $hostName,
                $serviceName,
                $this->customVars,
                $this->panelId,
                $this->orgId,
                $this->width,
                $this->height,
                $this->grafanaTheme,
                $this->timerange,
                $this->refresh,
                rawurlencode($serviceName),
                $this->width,
                $this->height
            );
        } elseif ($this->accessMode == "iframe") {
            $iframehtml = '<iframe src="%s://%s/dashboard-solo/%s/%s?var-hostname=%s&var-service=%s%s&panelId=%s&orgId=%s&theme=%s&from=now-%s&to=now" alt="%s" height="%d" frameBorder="0" style="width: 100%%;"></iframe>';
            $previewHtml = sprintf(
                $iframehtml,
                $this->protocol,
                $this->grafanaHost,
                $this->dashboardstore,
                $this->dashboard,
                $hostName,
                $serviceName,
                $this->customVars,
                $this->panelId,
                $this->orgId,
                $this->grafanaTheme,
                $this->timerange,
                $serviceName,
                $this->height
            );
        }
        return true;
    }

    public function has(MonitoredObject $object)
    {
        if (($object instanceof Host) || ($object instanceof Service)) {
            return true;
        } else {
            return false;
        }
    }

    public function getPreviewHtml(MonitoredObject $object)
    {
        // enable_perfdata = true ?  || disablevar == true
        if (!$object->process_perfdata || isset($object->customvars[$this->custvardisable])) {
            return '';
        }

        if ($object instanceof Host) {
            $serviceName = $object->check_command;
            $hostName = $object->host_name;
        } elseif ($object instanceof Service) {
            $serviceName = $object->service_description;
            $hostName = $object->host->getName();
        }

        if (isset($object->customvars['grafana_graph_confname'])) {
            if($this->getGraphConf($object->customvars['grafana_graph_confname']) == NULL) {
                return ;
            }
        } else {
            if($this->getGraphConf($serviceName, $object->check_command) == NULL) {
                return;
            }
        }

        if($this->repeatable == "yes" ) {
            $this->panelId = implode(',', range($this->panelId, ($this->panelId -1) + intval(substr_count($object->perfdata, '=')/$this->numberMetrics)));
        }

        // replace special chars for graphite
        if ($this->dataSource == "graphite") {
            $serviceName = preg_replace('/[^a-zA-Z0-9\*\-:]/', '_', $serviceName);
            $hostName = preg_replace('/[^a-zA-Z0-9\*\-:]/', '_', $hostName);
        }

        $customVars = $object->fetchCustomvars()->customvars;

        // replace template to customVars from Icinga2
        foreach ($customVars as $k => $v) {
            $search[] = "\$$k\$";
            $replace[] = is_string($v) ? rawurlencode($v)  : null;
            $this->customVars = str_replace($search, $replace, $this->customVars);
        }

        // build the menu
        $return_html = "";
        $menu = '<table class="grafana-table"><tr>';
        $menu .= '<td><div class="grafana-icon"><div class="grafana-clock"></div></div></td>';
        foreach ($this->timeRanges as $key => $mainValue) {
            $menu .= '<td><ul class="grafana-menu-navigation"><a class="main" href="#">' . $key . '</a>';
            $counter = 1;
            foreach ($mainValue as $subkey => $value) {
                $menu .= '<li class="grafana-menu-n'. $counter .'">' . $this->getTimerangeLink($object, $value, $subkey) . '</li>';
                $counter++;
            }
            $menu .= '</ul></td>';
        }
        $menu .= '</tr></table>';

        foreach (explode(',', $this->panelId) as $panelid) {

            $html = "";
            $this->panelId = $panelid;

            //image value will be returned as reference
            $previewHtml = "";
            $res = $this->getMyPreviewHtml($serviceName, $hostName, $previewHtml);

            //do not render URLs on error or if disabled
            if (!$res || $this->enableLink == "no") {
                $html .= $previewHtml;
            } else {
                $html .= '<a href="%s://%s/dashboard/%s/%s?var-hostname=%s&var-service=%s%s&from=now-%s&to=now&orgId=%s';

                if ($this->dashboard != $this->defaultDashboard) {
                    $html .= '&panelId=' . $this->panelId . '&fullscreen';
                }

                $html .= '"target="_blank">%s</a>';

                $html = sprintf(
                    $html,
                    $this->publicProtocol,
                    $this->publicHost,
                    $this->dashboardstore,
                    $this->dashboard,
                    urlencode($hostName),
                    rawurlencode($serviceName),
                    $this->customVars,
                    $this->timerange,
                    $this->orgId,
                    $previewHtml
                );
            }
            $return_html .= $html;
        }
        return '<div class="icinga-module module-grafana">'.$this->title.$menu.$return_html.'</div>';
    }
}
