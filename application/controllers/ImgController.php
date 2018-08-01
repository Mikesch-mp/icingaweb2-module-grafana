<?php
/**
 * Created by PhpStorm.
 * User: carst
 * Date: 11.03.2018
 * Time: 07:18
 */

namespace Icinga\Module\Grafana\Controllers;

use Icinga\Module\Grafana\Web\Controller\MonitoringAwareController;
use Icinga\Module\Monitoring\Object\Service;
use Icinga\Module\Monitoring\Object\Host;
use Icinga\Application\Config;
use Icinga\Exception\ConfigurationError;
use Icinga\Module\Grafana\Helpers\Util;


class ImgController extends MonitoringAwareController
{
    protected $host;
    protected $service;
    protected $timerange;
    protected $myConfig;
    protected $myAuth;
    protected $authentication;
    protected $grafanaHost             = null;
    protected $grafanaTheme            = 'light';
    protected $protocol                = "http";
    protected $username                = null;
    protected $password                = null;
    protected $apiToken                = null;
    protected $width                   = 640;
    protected $height                  = 280;
    protected $defaultDashboard        = "icinga2-default";
    protected $defaultDashboardPanelId = "1";
    protected $defaultOrgId            = "1";
    protected $shadows                 = false;
    protected $defaultDashboardStore   = "db";
    protected $dataSource              = null;
    protected $proxyTimeout            = "5";
    protected $custvarconfig           = "grafana_graph_config";
    protected $SSLVerifyPeer           = false;
    protected $SSLVerifyHost           = "0";
    protected $cacheTime;
    protected $grafanaVersion           = "0";
    protected $defaultdashboarduid;
    protected $refresh                 = "yes";


    public function init()
    {

        /* we need at least a host name */
        if (is_null($this->getParam('host')))
        {
            throw new \Error('No host given!');
        }

        /* save timerange from params for later use */
        $this->timerange = $this->hasParam('timerange') ? urldecode($this->getParam('timerange')) : null;
        if($this->hasParam('timerangeto')) {
            $this->timerangeto = urldecode($this->getParam('timerangeto'));
        } else {
            $this->timerangeto = strpos($this->timerange, '/') ? 'now-' . $this->timerange : "now";
        }
        $this->cacheTime = $this->hasParam('cachetime') ? $this->getParam('cachetime') : 300;

        /* load global configuration */
        $this->myConfig = Config::module('grafana')->getSection('grafana');
        $this->grafanaVersion = $this->myConfig->get('version', $this->grafanaVersion);
        $this->grafanaHost = $this->myConfig->get('host', $this->grafanaHost);
        if ($this->grafanaHost == null) {
            throw new ConfigurationError(
                'No Grafana host configured!'
            );
        }
        $this->protocol = $this->myConfig->get('protocol', $this->protocol);

        $this->defaultDashboard = $this->myConfig->get('defaultdashboard', $this->defaultDashboard);
        $this->defaultdashboarduid = $this->myConfig->get('defaultdashboarduid', NULL);
        if ($this->grafanaVersion == "1" && is_null($this->defaultdashboarduid)) {
            throw new ConfigurationError(
                'Usage of Grafana 5 is configured but no UID for default dashboard found!'
            );
        }
        $this->defaultDashboardPanelId = $this->myConfig->get('defaultdashboardpanelid', $this->defaultDashboardPanelId);
        $this->defaultOrgId = $this->myConfig->get('defaultorgid', $this->defaultOrgId);
        $this->grafanaTheme = $this->myConfig->get('theme', $this->grafanaTheme);
        $this->defaultDashboardStore = $this->myConfig->get('defaultdashboardstore', $this->defaultDashboardStore);
        $this->height = $this->myConfig->get('height', $this->height);
        $this->width = $this->myConfig->get('width', $this->width);
        $this->proxyTimeout = $this->myConfig->get('proxytimeout', $this->proxyTimeout);
        $this->refresh = $this->myConfig->get('indirectproxyrefresh', $this->refresh);
        /**
         * Read the global default timerange
         */
        if($this->timerange == null) {
            $this->timerange = $this->config->get('timerange', $this->timerange);
        }
        /**
         * Datasource needed to regex special chars
         */
        $this->dataSource = $this->myConfig->get('datasource', $this->dataSource);
        /**
         * Display shadows around graph
         */
        $this->shadows = $this->myConfig->get('shadows', $this->shadows);
        /**
         * Name of the custom varibale for graph config
         */
        $this->custvarconfig = ($this->myConfig->get('custvarconfig', $this->custvarconfig));
        /**
         * Verify the certificate's name against host
         */
        $this->SSLVerifyHost = ($this->myConfig->get('ssl_verifyhost', $this->SSLVerifyHost));
        /**
         * Verify the peer's SSL certificate
         */
        $this->SSLVerifyPeer = ($this->myConfig->get('ssl_verifypeer', $this->SSLVerifyPeer));

        /**
         * Username & Password or token
         */

        $this->apiToken = $this->myConfig->get('apitoken', $this->apiToken);
        $this->authentication = $this->myConfig->get('authentication');
        if ($this->apiToken == null && $this->authentication == "token") {
            throw new ConfigurationError(
                'API token usage configured, but no token given!'
            );
        } else {
            $this->username = $this->myConfig->get('username', $this->username);
            $this->password = $this->myConfig->get('password', $this->password);
            if ($this->username != null) {
                if ($this->password != null) {
                    $this->myAuth = $this->username . ":" . $this->password;
                } else {
                    $this->myAuth = $this->username;
                }
            } else {
                $this->myAuth = "";
            }
        }

    }

    public function indexAction()
    {
        if ($this->hasParam('service') && ! is_null($this->getParam('service')))
        {
            $service = $this->getServiceObject();
            $this->object = $service;
            $serviceName = $this->object->service_description;
            $hostName = $this->object->host_name;
        } else {
            $host = $this->getHostObject();
            $this->object = $host;
            $serviceName = $this->object->check_command;
            $hostName = $this->object->getName();
        }

        if (array_key_exists($this->custvarconfig, $this->object->customvars) && ! empty($this->object->customvars[$this->custvarconfig])) {
            $this->setGraphConf($this->object->customvars[$this->custvarconfig]);
        } else {
            $this->setGraphConf($serviceName, $this->object->check_command);
        }

        if (!empty($this->customVars)) {
            // replace template to customVars from Icinga2
            $myCustomVars = $this->object->fetchCustomvars()->customvars;
            foreach ($myCustomVars as $k => $v) {
                $search[] = "\$$k\$";
                $replace[] = is_string($v) ? $v : null;
                $this->customVars = str_replace($search, $replace, $this->customVars);
            }
            $this->customVars = explode('=', $this->customVars);
            $this->customVars = $this->customVars[0] . '=' . rawurlencode($this->customVars[1]);
        }
        // replace special chars for graphite
        if ($this->dataSource == "graphite") {
            $serviceName = preg_replace('/[^a-zA-Z0-9\*\-:]/', '_', $serviceName);
            $hostName = preg_replace('/[^a-zA-Z0-9\*\-:]/', '_', $hostName);
        }

        $imageHtml = "";
        $res = $this->getMyimageHtml($serviceName, $hostName, $imageHtml);
        header('Pragma: public');
        if($this->refresh == "yes") {
            header('Pragma: public');
            header("Expires: ".gmdate("D, d M Y H:i:s", time() + $this->cacheTime)." GMT");
            header('Cache-Control: max-age='.$this->cacheTime).', public';
        } else {
            header("Expires: ".gmdate("D, d M Y H:i:s", time() + 365*86440)." GMT");
            header('Cache-Control: max-age='. (365*86440));
        }
        header("Content-type: image/png");
        if (! $res)
        {
            $string = wordwrap($this->translate('Error'). ': ' . $imageHtml,40,"\n");
            $lines = explode("\n", $string);
            $im = @imagecreate ($this->width, $this->height);
            $background_color = imagecolorallocate ($im, 255, 255, 255); //white background
            $text_color = imagecolorallocate ($im, 255, 0,0);//black text
            foreach ($lines as $i => $line) {
                imagestring($im, 5, 0, 5 + $i * 15, $line, $text_color);
            }

            ob_start();
            imagepng($im);
            $img = ob_get_contents();
            ob_end_clean();
            imagedestroy($im);

            print $img;
        } else {
            print $imageHtml;
        }
        exit;
    }

    private function getHostObject()
    {
        $myHost = new Host(
            $this->backend,
            urldecode($this->getParam('host'))
        );
        $this->applyRestriction('monitoring/filter/objects', $myHost);
        if ($myHost->fetch() === false) {
            $this->httpNotFound($this->translate('Host not found'));
        }

        return $myHost;
    }

    private function getServiceObject()
    {
        $myService = new Service(
            $this->backend,
            urldecode($this->getParam('host')),
            rawurldecode($this->getParam('service'))
        );
        $this->applyRestriction('monitoring/filter/objects', $myService);
        if ($myService->fetch() === false) {
            $this->httpNotFound($this->translate('Service not found'));
        }
        return $myService;
    }

    private function setGraphConf($serviceName, $serviceCommand = NULL)
    {
        $graphConfig = Config::module('grafana', 'graphs');

        if ($graphConfig->hasSection(strtok($serviceName, ' ')) && ($graphConfig->hasSection($serviceName) == False)) {
            $serviceName = strtok($serviceName, ' ');
        }
        if ($graphConfig->hasSection(strtok($serviceName, ' ')) == False && ($graphConfig->hasSection($serviceName) == False)) {
            $serviceName = $serviceCommand;
            if($graphConfig->hasSection($serviceCommand) == False && $this->defaultDashboard == 'none') {
                return NULL;
            }
        }

        $this->dashboard = $graphConfig->get($serviceName, 'dashboard', $this->defaultDashboard);
        if ($this->grafanaVersion == "1")
        {
            $this->dashboarduid = $graphConfig->get($serviceName, 'dashboarduid', $this->defaultdashboarduid);
        } else {
            $this->dashboardstore = $graphConfig->get($serviceName, 'dashboardstore', $this->defaultDashboardStore);
        }
        $this->panelId = $this->hasParam('panelid') ? $this->getParam('panelid') : $graphConfig->get($serviceName, 'panelId', $this->defaultDashboardPanelId);
        $this->orgId = $graphConfig->get($serviceName, 'orgId', $this->defaultOrgId);
        $this->customVars = $graphConfig->get($serviceName, 'customVars', '');
        $this->height = $graphConfig->get($serviceName, 'height', $this->height);
        $this->width = $graphConfig->get($serviceName, 'width', $this->width);

    }
    private function getMyimageHtml($serviceName, $hostName, &$imageHtml)
    {
        $imgClass = $this->shadows ? "grafana-img grafana-img-shadows" : "grafana-img";
        // Test whether curl is loaded
        if (extension_loaded('curl') === false) {
            $imageHtml = $this->translate('CURL extension is missing. Please install CURL for PHP and ensure it is loaded.');
            return false;
        }
        if ($this->grafanaVersion == "1")
        {
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
        );

        if ($this->authentication == "token") {
            $curl_opts[CURLOPT_HTTPHEADER] = array('Content-Type: application/json' , "Authorization: Bearer ". $this->apiToken);
        } else {
            $curl_opts[CURLOPT_USERPWD] = "$this->myAuth";
        }

        curl_setopt_array($curl_handle, $curl_opts);
        $res = curl_exec($curl_handle);

        $statusCode = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

        if ($res === false) {
            $imageHtml .=$this->translate('Cannot fetch graph with curl') .': '. curl_error($curl_handle). '.';

            //provide a hint for 'Failed to connect to ...: Permission denied'
            if (curl_errno($curl_handle) == 7) {
                $imageHtml .= $this->translate(' Check SELinux/Firewall.');
            }
            return false;
        }

        if ($statusCode > 299) {
            $error = @json_decode($res);
            $imageHtml .= $this->translate('Cannot fetch Grafana graph'). ": " . Util::httpStatusCodeToString($statusCode) .
                " ". $statusCode .": " . ($error !== null && property_exists($error, 'message') ? $error->message : "");
            return false;
        }

        curl_close($curl_handle);
        $imageHtml = $res;
        return true;
    }
}