<?php

namespace Icinga\Module\Grafana\ProvidedHook;

use Icinga\Application\Icinga;
use Icinga\Authentication\Auth;
use Icinga\Application\Config;
use Icinga\Exception\ConfigurationError;
use Icinga\Application\Hook\GrapherHook;
use Icinga\Module\Monitoring\Object\MonitoredObject;
use Icinga\Module\Monitoring\Object\Host;
use Icinga\Module\Monitoring\Object\Service;
use Icinga\Web\Url;
use Icinga\Module\Grafana\Helpers\Util;
use Icinga\Module\Grafana\Helpers\Timeranges;


class Grapher extends GrapherHook
{
    protected $config;
    protected $graphConfig;
    protected $auth;
    protected $authentication;
    protected $grafanaHost = null;
    protected $grafanaTheme = 'light';
    protected $protocol = "http";
    protected $usePublic = "no";
    protected $publicHost = null;
    protected $publicProtocol = "http";
    protected $timerange = "6h";
    protected $timerangeto = "now";
    protected $username = null;
    protected $password = null;
    protected $apiToken = null;
    protected $width = 640;
    protected $height = 280;
    protected $enableLink = true;
    protected $defaultDashboard = "icinga2-default";
    protected $defaultDashboardPanelId = "1";
    protected $defaultOrgId = "1";
    protected $shadows = false;
    protected $defaultDashboardStore = "db";
    protected $dataSource = null;
    protected $accessMode = "proxy";
    protected $proxyTimeout = "5";
    protected $refresh = "no";
    protected $title = "<h2>Performance Graph</h2>";
    protected $custvardisable = "grafana_graph_disable";
    protected $custvarconfig = "grafana_graph_config";
    protected $repeatable = "no";
    protected $numberMetrics = "1";
    protected $debug = false;
    protected $SSLVerifyPeer = false;
    protected $SSLVerifyHost = "0";
    protected $cacheTime = 300;
    protected $grafanaVersion = "0";
    protected $defaultdashboarduid;

    protected function init()
    {
        $this->permission = Auth::getInstance();
        $this->config = Config::module('grafana')->getSection('grafana');
        $this->grafanaVersion = $this->config->get('version', $this->grafanaVersion);
        $this->grafanaHost = $this->config->get('host', $this->grafanaHost);
        if ($this->grafanaHost == null) {
            throw new ConfigurationError(
                'No Grafana host configured!'
            );
        }
        $this->protocol = $this->config->get('protocol', $this->protocol);
        $this->enableLink = $this->config->get('enableLink', $this->enableLink);
        if ($this->enableLink == "yes" && $this->permission->hasPermission('grafana/enablelink')) {
            $this->usePublic = $this->config->get('usepublic', $this->usePublic);
            if ($this->usePublic == "yes") {
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

        // Confid needed for Grafana
        $this->defaultDashboard = $this->config->get('defaultdashboard', $this->defaultDashboard);
        $this->defaultdashboarduid = $this->config->get('defaultdashboarduid', null);
        if ($this->grafanaVersion == "1" && is_null($this->defaultdashboarduid)) {
            throw new ConfigurationError(
                'Usage of Grafana 5 is configured but no UID for default dashboard found!'
            );
        }
        $this->defaultDashboardPanelId = $this->config->get('defaultdashboardpanelid', $this->defaultDashboardPanelId);
        $this->defaultOrgId = $this->config->get('defaultorgid', $this->defaultOrgId);
        $this->grafanaTheme = $this->config->get('theme', $this->grafanaTheme);
        $this->defaultDashboardStore = $this->config->get('defaultdashboardstore', $this->defaultDashboardStore);
        $this->height = $this->config->get('height', $this->height);
        $this->width = $this->config->get('width', $this->width);

        $this->accessMode = $this->config->get('accessmode', $this->accessMode);
        $this->proxyTimeout = $this->config->get('proxytimeout', $this->proxyTimeout);
        /**
         * Read the global default timerange
         */
        $this->timerange = $this->config->get('timerange', $this->timerange);
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
        /**
         * Name of the custom varibale for graph config
         */
        $this->custvarconfig = ($this->config->get('custvarconfig', $this->custvarconfig));

        /**
         * Show some debug informations?
         */
        $this->debug = ($this->config->get('debug', $this->debug));
        /**
         * Verify the certificate's name against host
         */
        $this->SSLVerifyHost = ($this->config->get('ssl_verifyhost', $this->SSLVerifyHost));
        /**
         * Verify the peer's SSL certificate
         */
        $this->SSLVerifyPeer = ($this->config->get('ssl_verifypeer', $this->SSLVerifyPeer));

        /**
         * Username & Password or token
         */

        $this->apiToken = $this->config->get('apitoken', $this->apiToken);
        $this->authentication = $this->config->get('authentication');
        if ($this->apiToken == null && $this->authentication == "token") {
            throw new ConfigurationError(
                'API token usage configured, but no token given!'
            );
        } else {
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
    }

    private function getGraphConf($serviceName, $serviceCommand = null)
    {

        $this->graphConfig = Config::module('grafana', 'graphs');

        if ($this->graphConfig->hasSection(strtok($serviceName,
                ' ')) && ($this->graphConfig->hasSection($serviceName) == false)) {
            $serviceName = strtok($serviceName, ' ');
        }
        if ($this->graphConfig->hasSection(strtok($serviceName,
                ' ')) == false && ($this->graphConfig->hasSection($serviceName) == false)) {
            $serviceName = $serviceCommand;
            if ($this->graphConfig->hasSection($serviceCommand) == false && $this->defaultDashboard == 'none') {
                return null;
            }
        }

        $this->dashboard = $this->getGraphConfigOption($serviceName, 'dashboard', $this->defaultDashboard);
        if ($this->grafanaVersion == "1") {
            $this->dashboarduid = $this->getGraphConfigOption($serviceName, 'dashboarduid', $this->defaultdashboarduid);
        } else {
            $this->dashboardstore = $this->getGraphConfigOption($serviceName, 'dashboardstore',
                $this->defaultDashboardStore);
        }
        $this->panelId = $this->getGraphConfigOption($serviceName, 'panelId', $this->defaultDashboardPanelId);
        $this->orgId = $this->getGraphConfigOption($serviceName, 'orgId', $this->defaultOrgId);
        $this->customVars = $this->getGraphConfigOption($serviceName, 'customVars', '');

        if(Url::fromRequest()->hasParam('tr-from') && Url::fromRequest()->hasParam('tr-to')) {
            $this->timerange = urldecode(Url::fromRequest()->getParam('tr-from'));
            $this->timerangeto = urldecode(Url::fromRequest()->getParam('tr-to'));
        } else {
            $this->timerange = Url::fromRequest()->hasParam('timerange') ?
                'now-' . urldecode(Url::fromRequest()->getParam('timerange')) :
                'now-' . $this->getGraphConfigOption($serviceName, 'timerange', $this->timerange);
            $this->timerangeto = strpos($this->timerange, '/') ? $this->timerange : $this->timerangeto;
        }

        $this->height = $this->getGraphConfigOption($serviceName, 'height', $this->height);
        $this->width = $this->getGraphConfigOption($serviceName, 'width', $this->width);
        $this->repeatable = $this->getGraphConfigOption($serviceName, 'repeatable', $this->repeatable);
        $this->numberMetrics = $this->getGraphConfigOption($serviceName, 'nmetrics', $this->numberMetrics);

        return $this;
    }

    private function getGraphConfigOption($section, $option, $default = null)
    {
        $value = $this->graphConfig->get($section, $option, $default);
        if (empty($value)) {
            return $default;
        }
        return $value;
    }

    //returns false on error, previewHTML is passed as reference
    private function getMyPreviewHtml($serviceName, $hostName, &$previewHtml)
    {
        $imgClass = $this->shadows ? "grafana-img grafana-img-shadows" : "grafana-img";
        if ($this->accessMode == "proxy") {

            // Test whether curl is loaded
            if (extension_loaded('curl') === false) {
                $previewHtml = "<b>CURL extension is missing. Please install CURL for PHP and ensure it is loaded.</b>";
                return false;
            }
            if ($this->grafanaVersion == "1") {
                $this->pngUrl = sprintf(
                    '%s://%s/render/d-solo/%s/%s?var-hostname=%s&var-service=%s&var-command=%s%s&panelId=%s&orgId=%s&width=%s&height=%s&theme=%s&from=%s&to=%s',
                    $this->protocol,
                    $this->grafanaHost,
                    $this->dashboarduid,
                    $this->dashboard,
                    urlencode($hostName),
                    rawurlencode($serviceName),
                    $this->object->check_command,
                    $this->customVars,
                    $this->panelId,
                    $this->orgId,
                    $this->width,
                    $this->height,
                    $this->grafanaTheme,
                    urlencode($this->timerange),
                    urlencode($this->timerangeto)
                );
            } else {

                $this->pngUrl = sprintf(
                    '%s://%s/render/dashboard-solo/%s/%s?var-hostname=%s&var-service=%s&var-command=%s%s&panelId=%s&orgId=%s&width=%s&height=%s&theme=%s&from=%s&to=%s',
                    $this->protocol,
                    $this->grafanaHost,
                    $this->dashboardstore,
                    $this->dashboard,
                    urlencode($hostName),
                    rawurlencode($serviceName),
                    $this->object->check_command,
                    $this->customVars,
                    $this->panelId,
                    $this->orgId,
                    $this->width,
                    $this->height,
                    $this->grafanaTheme,
                    urlencode($this->timerange),
                    urlencode($this->timerangeto)
                );
            }
            // fetch image with curl
            $curl_handle = curl_init();
            $curl_opts = array(
                CURLOPT_URL => $this->pngUrl,
                CURLOPT_CONNECTTIMEOUT => 2,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => $this->SSLVerifyPeer,
                CURLOPT_SSL_VERIFYHOST => ($this->SSLVerifyHost) ? 2 : 0,
                CURLOPT_TIMEOUT => $this->proxyTimeout,
                CURLOPT_USERPWD => "$this->auth",
                CURLOPT_HTTPAUTH,
                CURLAUTH_ANY
            );

            if ($this->authentication == "token") {
                $curl_opts[CURLOPT_HTTPHEADER] = array(
                    'Content-Type: application/json',
                    "Authorization: Bearer " . $this->apiToken
                );
            } else {
                $curl_opts[CURLOPT_USERPWD] = "$this->auth";
            }

            curl_setopt_array($curl_handle, $curl_opts);
            $res = curl_exec($curl_handle);

            $statusCode = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

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
                    " ($statusCode)</b>: " . ($error !== null && property_exists($error,
                        'message') ? $error->message : "");
                return false;
            }

            curl_close($curl_handle);

            $img = 'data:image/png;base64,' . base64_encode($res);
            $imghtml = '<img src="%s" alt="%s" width="%spx" height="%spx" class="' . $imgClass . '"/>';
            $previewHtml = sprintf(
                $imghtml,
                $img,
                rawurlencode($serviceName),
                $this->width,
                $this->height
            );
        } elseif ($this->accessMode == "indirectproxy") {
            if ($this->object instanceof Service) {
                $this->pngUrl = Url::frompath('grafana/img', array(
                    'host' => urlencode($hostName),
                    'service' => rawurlencode($serviceName),
                    'panelid' => $this->panelId,
                    'timerange' => urlencode($this->timerange),
                    'timerangeto' => urlencode($this->timerangeto),
                    'cachetime' => $this->cacheTime
                ));
            } else {
                $this->pngUrl = Url::frompath('grafana/img', array(
                    'host' => urlencode($hostName),
                    'panelid' => $this->panelId,
                    'timerange' => urlencode($this->timerange),
                    'timerangeto' => urlencode($this->timerangeto),
                    'cachetime' => $this->cacheTime
                ));
            }
            $imghtml = '<div style="min-height: %spx;"><img src="%s%s" alt="%s" width="%spx" height="%spx" class="' .
                $imgClass . '" /></div>';
            $previewHtml = sprintf(
                $imghtml,
                $this->height,
                $this->getView()->serverUrl(),
                $this->pngUrl,
                $serviceName,
                $this->width,
                $this->height

            );
        } elseif ($this->accessMode == "direct") {
            if ($this->grafanaVersion == "1") {
                $imghtml = '<div style="min-height: %spx;"><img src="%s://%s/render/d-solo/%s/%s?var-hostname=%s&var-service=%s&var-command=%s%s&panelId=%s&orgId=%s&width=%s&height=%s&theme=%s&from=%s&to=%s&trickrefresh=%s" alt="%s" width="%spx" height="%spx" class="' . $imgClass . '" /></div>';
                $previewHtml = sprintf(
                    $imghtml,
                    $this->height,
                    $this->protocol,
                    $this->grafanaHost,
                    $this->dashboarduid,
                    $this->dashboard,
                    urlencode($hostName),
                    rawurlencode($serviceName),
                    $this->object->check_command,
                    $this->customVars,
                    $this->panelId,
                    $this->orgId,
                    $this->width,
                    $this->height,
                    $this->grafanaTheme,
                    urlencode($this->timerange),
                    urlencode($this->timerangeto),
                    $this->refresh,
                    rawurlencode($serviceName),
                    $this->width,
                    $this->height
                );
            } else {
                $imghtml = '<div style="min-height: %spx;"><img src="%s://%s/render/dashboard-solo/%s/%s?var-hostname=%s&var-service=%s&var-command=%s%s&panelId=%s&orgId=%s&width=%s&height=%s&theme=%s&from=%s&to=%s&trickrefresh=%s" alt="%s" width="%spx" height="%spx" class="' . $imgClass . '" /></div>';
                $previewHtml = sprintf(
                    $imghtml,
                    $this->height,
                    $this->protocol,
                    $this->grafanaHost,
                    $this->dashboardstore,
                    $this->dashboard,
                    urlencode($hostName),
                    rawurlencode($serviceName),
                    $this->object->check_command,
                    $this->customVars,
                    $this->panelId,
                    $this->orgId,
                    $this->width,
                    $this->height,
                    $this->grafanaTheme,
                    urlencode($this->timerange),
                    urlencode($this->timerangeto),
                    $this->refresh,
                    rawurlencode($serviceName),
                    $this->width,
                    $this->height
                );
            }
        } elseif ($this->accessMode == "iframe") {
            if ($this->grafanaVersion == "1") {
                $iframehtml = '<iframe src="%s://%s/d-solo/%s/%s?var-hostname=%s&var-service=%s&var-command=%s%s&panelId=%s&orgId=%s&theme=%s&from=%s&to=%s" alt="%s" height="%d" frameBorder="0" style="width: 100%%;"></iframe>';
                $previewHtml = sprintf(
                    $iframehtml,
                    $this->protocol,
                    $this->grafanaHost,
                    $this->dashboarduid,
                    $this->dashboard,
                    urlencode($hostName),
                    rawurlencode($serviceName),
                    $this->object->check_command,
                    $this->customVars,
                    $this->panelId,
                    $this->orgId,
                    $this->grafanaTheme,
                    urlencode($this->timerange),
                    urlencode($this->timerangeto),
                    rawurlencode($serviceName),
                    $this->height
                );
            } else {
                $iframehtml = '<iframe src="%s://%s/dashboard-solo/%s/%s?var-hostname=%s&var-service=%s&var-command=%s%s&panelId=%s&orgId=%s&theme=%s&from=%s&to=%s" alt="%s" height="%d" frameBorder="0" style="width: 100%%;"></iframe>';
                $previewHtml = sprintf(
                    $iframehtml,
                    $this->protocol,
                    $this->grafanaHost,
                    $this->dashboardstore,
                    $this->dashboard,
                    urlencode($hostName),
                    rawurlencode($serviceName),
                    $this->object->check_command,
                    $this->customVars,
                    $this->panelId,
                    $this->orgId,
                    $this->grafanaTheme,
                    urlencode($this->timerange),
                    urlencode($this->timerangeto),
                    rawurlencode($serviceName),
                    $this->height
                );
            }
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

    public function getPreviewHtml(MonitoredObject $object, $report = false)
    {
        $this->object = $object;
        // enable_perfdata = true ?  || disablevar == true
        if (!$this->object->process_perfdata || (( isset($this->object->customvars[$this->custvardisable]) && json_decode(strtolower($this->object->customvars[$this->custvardisable])) !== false)) ) {
            return '';
        }

        if ($this->object instanceof Host) {
            $this->cacheTime = $this->object->host_next_check - $this->object->host_last_check;
            $serviceName = $this->object->check_command;
            $hostName = $this->object->host_name;
            $parameters = array(
                'host' => $this->object->host_name,
            );
            $link = 'monitoring/host/show';
        } elseif ($this->object instanceof Service) {
            $this->cacheTime = $this->object->service_next_check - $this->object->service_last_check;
            $serviceName = $this->object->service_description;
            $hostName = $this->object->host->getName();
            $parameters = array(
                'host' => $this->object->host->getName(),
                'service' => $this->object->service_description,
            );
            $link = 'monitoring/service/show';
        }

        // Preserve timerange if set
        $parameters['timerange'] = $this->timerange;

        if (array_key_exists($this->custvarconfig,
                $this->object->customvars) && !empty($this->object->customvars[$this->custvarconfig])) {
            $graphConfiguation = $this->getGraphConf($object->customvars[$this->custvarconfig]);
        } else {
            $graphConfiguation = $this->getGraphConf($serviceName, $object->check_command);
        }
        if ($graphConfiguation == null) {
            return;
        }

        if ($this->repeatable == "yes") {
            $this->panelId = implode(',', range($this->panelId,
                ($this->panelId - 1) + intval(substr_count($object->perfdata, '=') / $this->numberMetrics)));
        }

        // replace special chars for graphite
        if ($this->dataSource == "graphite" && $this->accessMode != "indirectproxy") {
            $serviceName = preg_replace('/[^a-zA-Z0-9\*\-:]/', '_', $serviceName);
            $hostName = preg_replace('/[^a-zA-Z0-9\*\-:]/', '_', $hostName);
        }

        if (!empty($this->customVars)) {
            // replace template to customVars from Icinga2
            $customVars = $object->fetchCustomvars()->customvars;
            foreach ($customVars as $k => $v) {
                $search[] = "\$$k\$";
                $replace[] = is_string($v) ? $v : null;
                $this->customVars = str_replace($search, $replace, $this->customVars);
            }

            // urlencodee values
            $customVars = "";
            foreach (explode('&', $this->customVars) as $param) {
                $arr = explode("=", $param);
                if ($this->dataSource == "graphite") {
                    $k = preg_replace('/[^a-zA-Z0-9\*\-:]/', '_', $arr[0]);
                } else {
                    $k = $arr[0];
                }
                $customVars = $customVars . '&' . rawurlencode($k);
                if (count($arr) > 1) {
                    if ($this->dataSource == "graphite") {
                        $v = preg_replace('/[^a-zA-Z0-9\*\-:]/', '_', $arr[1]);
                    } else {
                        $v = $arr[1];
                    }
                    $customVars = $customVars . "=" . rawurlencode($v);
                }
            }
            $this->customVars = $customVars;
        }

        $return_html = "";

        // Hide menu if in reporting or compact mode
        $menu = "";
        if ($report === false && !$this->getView()->compact) {
            $timeranges = new Timeranges($parameters, $link);
            $menu = $timeranges->getTimerangeMenu($this->timerange, $this->timerangeto);
        } else {
            $this->title = '';
        }

        foreach (explode(',', $this->panelId) as $panelid) {

            $html = "";
            $this->panelId = $panelid;

            //image value will be returned as reference
            $previewHtml = "";
            $res = $this->getMyPreviewHtml($serviceName, $hostName, $previewHtml);

            //do not render URLs on error or if disabled
            if (!$res || $this->enableLink == "no" || !$this->permission->hasPermission('grafana/enablelink')) {
                $html .= $previewHtml;
            } else {
                if ($this->grafanaVersion == "1") {
                    $html .= '<a href="%s://%s/d/%s/%s?var-hostname=%s&var-service=%s&var-command=%s%s&from=%s&to=%s&orgId=%s&panelId=%s&fullscreen" target="_blank">%s</a>';

                    $html = sprintf(
                        $html,
                        $this->publicProtocol,
                        $this->publicHost,
                        $this->dashboarduid,
                        $this->dashboard,
                        ($this->dataSource == "graphite") ? rawurlencode(preg_replace('/[^a-zA-Z0-9\*\-:]/', '_',
                            $hostName)) : rawurlencode($hostName),
                        ($this->dataSource == "graphite") ? rawurlencode(preg_replace('/[^a-zA-Z0-9\*\-:]/', '_', $serviceName)) : rawurlencode($serviceName),
                        $this->object->check_command,
                        $this->customVars,
                        urlencode($this->timerange),
                        urlencode($this->timerangeto),
                        $this->orgId,
                        $this->panelId,
                        $previewHtml
                    );
                } else {
                    $html .= '<a href="%s://%s/dashboard/%s/%s?var-hostname=%s&var-service=%s&var-command=%s%s&from=%s&to=%s&orgId=%s&panelId=%s&fullscreen" target="_blank">%s</a>';

                    $html = sprintf(
                        $html,
                        $this->publicProtocol,
                        $this->publicHost,
                        $this->dashboardstore,
                        $this->dashboard,
                        ($this->dataSource == "graphite") ? rawurlencode(preg_replace('/[^a-zA-Z0-9\*\-:]/', '_',
                            $hostName)) : rawurlencode($hostName),
                        ($this->dataSource == "graphite") ? rawurlencode(preg_replace('/[^a-zA-Z0-9\*\-:]/', '_', $serviceName)) : rawurlencode($serviceName),
                        $this->object->check_command,
                        $this->customVars,
                        urlencode($this->timerange),
                        urlencode($this->timerangeto),
                        $this->orgId,
                        $this->panelId,
                        $previewHtml
                    );
                }
            }
            $return_html .= $html;
        }
        if ($this->debug && $this->permission->hasPermission('grafana/debug') && $report === false) {
            $usedUrl = "";
            if ($this->accessMode == "proxy" || $this->accessMode == "indirectproxy" ) {
                $usedUrl = $this->pngUrl;
            } else {
                $usedUrl = preg_replace('/.*?src\s*=\s*[\'\"](.*?)[\'\"].*/', "$1", $previewHtml);
            }
            if ($this->accessMode == "iframe") {
                $this->height = "100%";
            }

            $return_html .= "<h2>Performance Graph Debug</h2>";
            $return_html .= "<table class=\"name-value-table\"><tbody>";
            $return_html .= "<tr><th>Access mode</th><td>" . $this->accessMode . "</td>";
            $return_html .= "<tr><th>Authentication type</th><td>" . $this->authentication . "</td>";
            $return_html .= "<tr><th>Protocol</th><td>" . $this->protocol . "</td>";
            $return_html .= "<tr><th>Grafana Host</th><td>" . $this->grafanaHost . "</td>";
            if ($this->grafanaVersion == "1") {
                $return_html .= "<tr><th>Dashboard UID</th><td>" . $this->dashboarduid . "</td>";
            } else {
                $return_html .= "<tr><th>Dashboard Store</th><td>" . $this->defaultDashboardStore . "</td>";
            }
            $return_html .= "<tr><th>Dashboard Name</th><td>" . $this->dashboard . "</td>";
            $return_html .= "<tr><th>Panel ID</th><td>" . $this->panelId . "</td>";
            $return_html .= "<tr><th>Organization ID</th><td>" . $this->orgId . "</td>";
            $return_html .= "<tr><th>Theme</th><td>" . $this->grafanaTheme . "</td>";
            $return_html .= "<tr><th>Timerange</th><td>" . $this->timerange . "</td>";
            $return_html .= "<tr><th>Timerangeto</th><td>" . $this->timerangeto . "</td>";
            $return_html .= "<tr><th>Height</th><td>" . $this->height . "</td>";
            $return_html .= "<tr><th>Width</th><td>" . $this->width . "</td>";
            $return_html .= "<tr><th>Custom Variables</th><td>" . rawurldecode($this->customVars) . "</td>";
            $return_html .= "<tr><th>Graph URL</th><td>" . $usedUrl . "</td>";
            $return_html .= "<tr><th>Disable graph custom variable</th><td>" . $this->custvardisable . "</td>";
            $return_html .= "<tr><th>Graph config custom variable</th><td>" . $this->custvarconfig . "</td>";
            if (isset($object->customvars[$this->custvarconfig])) {
                $return_html .= "<tr><th>" . $this->custvarconfig . "</th><td>" . $object->customvars[$this->custvarconfig] . "</td>";
            }
            $return_html .= "<tr><th>Shadows</th><td>" . (($this->shadows) ? 'Yes' : 'No') . "</td>";
            if ($this->accessMode == "proxy") {
                $return_html .= "<tr><th>SSL Verify Peer</th><td>" . (($this->SSLVerifyPeer) ? 'Yes' : 'No') . "</td>";
                $return_html .= "<tr><th>SSL Verify Host</th><td>" . (($this->SSLVerifyHost) ? 'Yes' : 'No') . "</td>";
            }
            $return_html .= " </tbody></table>";

        }
        return '<div class="icinga-module module-grafana">' . $this->title . $menu . $return_html . '</div>';
    }
}
