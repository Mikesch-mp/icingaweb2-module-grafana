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
    protected $graphconfig;
    protected $auth;
    protected $grafana = array();
    protected $height = 280;
    protected $grafanaHost = null;
    protected $password = null;
    protected $protocol = "http";
    protected $timerange = "6h";
    protected $username = null;
    protected $width = 640;
    protected $enableLink = "yes";
    protected $defaultDashboard = "icinga2-default";
    protected $defaultDashboardStore = "db";
    protected $datasource = null;
    protected $accessmode = "proxy";
    protected $timeout = "5";
    protected $refresh = "no";
    protected $timeranges = [
                          '5m'   => '5 minutes',
                          '15m'  => '15 minutes',
                          '30m'  => '30 minutes',
                          '1h'   => '1 hour',
                          '3h'   => '3 hours',
                          '6h'   => '6 hours',
                          '8h'   => '8 hours',
                          '12h'  => '12 hours',
                          '24h'  => '24 hours',
                          '2d'   => '2 days',
                          '7d'   => '7 days',
                          '30d'  => '30 days',
                          '60d'  => '60 days',
                          '6M'   => '6 months',
                          '1y'   => '1 year',
                          '2y'   => '2 years',
    ];



    protected function init()
    {
	$this->config = Config::module('grafana')->getSection('grafana');
        $this->username = $this->config->get('username', $this->username);
        $this->grafanaHost = $this->config->get('host', $this->grafanaHost);
	if ( $this->grafanaHost == null)
        {
            throw new ConfigurationError(
                'No Grafana host configured!'
            );
	}
        $this->password = $this->config->get('password', $this->password);
        $this->protocol = $this->config->get('protocol', $this->protocol);

	// Check if there is a timerange in url params
	if ( Url::fromRequest()->hasParam('timerange') ) {
           $this->timerange = Url::fromRequest()->getParam('timerange');
	} else {
           $this->timerange = $this->config->get('timerange', $this->timerange);
        }
	$this->timeout = $this->config->get('timeout', $this->timeout);
	$this->height = $this->config->get('height', $this->height);
        $this->width = $this->config->get('width', $this->width);
	$this->enableLink = $this->config->get('enableLink', $this->enableLink);
        $this->defaultDashboard = $this->config->get('defaultdashboard', $this->defaultDashboard);
        $this->defaultDashboardStore = $this->config->get('defaultdashboardstore', $this->defaultDashboardStore);
	$this->datasource = $this->config->get('datasource', $this->datasource);
        $this->accessmode = $this->config->get('accessmode', $this->accessmode);
        $this->refresh = $this->config->get('directrefresh', $this->refresh);
        $this->refresh = ($this->refresh == "yes" && $this->accessmode == "direct" ? time() : 'now');
        if($this->username != null)
        {
            if($this->password != null)
            {
                $this->auth = $this->username.":".$this->password;
            }
            else
            {
                $this->auth = $this->username;
            }
        }
        else
        {
            $this->auth = "";
        }
    }

    private function getGraphConf($serviceName, $serviceCommand)
    {

        $graphconfig = Config::module('grafana', 'graphs');
        $this->graphconfig = $graphconfig;
        if ($this->graphconfig->hasSection(strtok($serviceName, ' ')) && ($this->graphconfig->hasSection($serviceName) == False )) 
        {
           $serviceName = strtok($serviceName, ' ');
        }
	if ($this->graphconfig->hasSection(strtok($serviceName, ' ')) == False && ($this->graphconfig->hasSection($serviceName) == False )) 
        {
           $serviceName = $serviceCommand;
        }

	
      $this->dashboard = $this->graphconfig->get($serviceName, 'dashboard', $this->defaultDashboard);
      $this->dashboardstore = $this->graphconfig->get($serviceName, 'dashboardstore', $this->defaultDashboardStore);
      $this->panelId = $this->graphconfig->get($serviceName, 'panelId', '1');
      $this->customVars = $this->graphconfig->get($serviceName, 'customVars', '');
      if ( Url::fromRequest()->hasParam('timerange') ) {
         $this->timerange = Url::fromRequest()->getParam('timerange');
      } else {
         $this->timerange = $this->graphconfig->get($serviceName, 'timerange', $this->timerange);
      }
      $this->height = $this->graphconfig->get($serviceName, 'height', $this->height);
      $this->width = $this->graphconfig->get($serviceName, 'width', $this->width);

      return $this;
    }

    private function getTimerangeLink($object, $rangeName, $timeRange)
    {
        $this->view = Icinga::app()->getViewRenderer()->view;
        if ($object instanceof Host)
        {
	    $array = [
                  'host'       => $object->host_name,
                  'timerange'  => $timeRange
            ];
	    $link = 'monitoring/host/show';
        } else {
            $array = [
                  'host'       => $object->host->getName(),
                  'service'    => $object->service_description,
                  'timerange'  => $timeRange
            ];
	     $link = 'monitoring/service/show';
        }

        return $this->view->qlink(
                                $rangeName,
                                $link,
                                $array,
                                array(
                                        'class'             => 'action-link',
                                        'data-base-target'  => '_self',
                                        'title'             => 'Set timerange for graph to '. $rangeName
                                )
        );
    }

    //returns false on error, previewHTML is passed as reference
    private function getMyPreviewHtml($serviceName, $hostName, &$previewHtml)
    {
        if ($this->accessmode == "proxy") {
	    $pngUrl = sprintf(
			'%s://%s/render/dashboard-solo/%s/%s?var-hostname=%s&var-service=%s%s&panelId=%s&width=%s&height=%s&theme=light&from=now-%s&to=now',
			$this->protocol,
			$this->grafanaHost,
			$this->dashboardstore,
			$this->dashboard,
			urlencode($hostName),
			rawurlencode($serviceName),
			$this->customVars,
			$this->panelId,
			$this->width,
			$this->height,
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
                CURLOPT_TIMEOUT => $this->timeout, 
                CURLOPT_USERPWD => "$this->auth",
                CURLOPT_HTTPAUTH, CURLAUTH_ANY
            );

            curl_setopt_array($curl_handle, $curl_opts);

            $res = curl_exec($curl_handle);

            if ($res === false) {
                $previewHtml = "<b>Cannot fetch graph with curl:</b> '" . curl_error($curl_handle) . "'.";

                //provide a hint for 'Failed to connect to ...: Permission denied'
                if (curl_errno($curl_handle) == 7) {
                    $previewHtml .= " Check SELinux/Firewall.";
                }
                return false;
            }

            $statusCode = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);

            if ($statusCode > 299) {
                $error = @json_decode($res);
                $previewHtml = "<b>Cannot fetch Grafana graph: ". Util::httpStatusCodeToString($statusCode) .
                       " ($statusCode)</b>: " . (property_exists($error, 'message') ? $error->message : "");
                return false;
            }

            curl_close($curl_handle);

            $img = 'data:image/png;base64,'.base64_encode($res);
            $imghtml = '<img src="%s" alt="%s" width="%d" height="%d" />';
            $previewHtml = sprintf(
                $imghtml,
                $img,
                rawurlencode($serviceName),
                $this->width,
                $this->height
            );
        } else {
            $iframehtml = '<iframe src="%s://%s/dashboard-solo/%s/%s?var-hostname=%s&var-service=%s%s&panelId=%s&theme=light&from=now-%s&to=now&trickrefresh=%s" alt="%s" height="%d" frameBorder="0" style="width: 100%%;"></iframe>';
            $previewHtml = sprintf(
                $iframehtml,
                $this->protocol,
                $this->grafanaHost,
                $this->dashboardstore,
                $this->dashboard,
                urlencode($hostName),
                rawurlencode($serviceName),
                $this->customVars,
                $this->panelId,
                $this->timerange,
                $this->refresh,
                rawurlencode($serviceName),
                $this->height
            );
        }

        return true;
    }

    public function has(MonitoredObject $object)
    {
        if (($object instanceof Host)||($object instanceof Service)) 
        {
            return true;
        } 
        else
        {
            return false;
        }
    }

    public function getPreviewHtml(MonitoredObject $object)
    {
	// enable_perfdata = true ?  || no perfdata into service
        if (! $object->process_perfdata || ! $object->perfdata)
        {
            return '';
        }

        if ($object instanceof Host) 
        {
            $serviceName = $object->check_command;
            $hostName = $object->host_name;
        } 
        elseif ($object instanceof Service) 
        {
            $serviceName = $object->service_description;
            $hostName = $object->host->getName();
        }
        $customVars = $object->fetchCustomvars()->customvars;

	$this->getGraphConf($serviceName, $object->check_command);

        if ($this->datasource == "graphite")
        {
            $serviceName = preg_replace('/[^a-zA-Z0-9\*\-:]/', '_', $serviceName);
            $hostName = preg_replace('/[^a-zA-Z0-9\*\-:]/', '_', $hostName);
        }

	$return_html = "";
        
        // replace template to customVars from Icinga2
	foreach($customVars as $k => $v){
		$search[] = "\$$k\$";
		$replace[] = is_string($v) ? $v : null;
		$this->customVars = str_replace($search, $replace, $this->customVars);
	}

        $menu = '<div class="scrollmenu" style="overflow: auto; white-space: nowrap; padding: 8px">';
        foreach ($this->timeranges as $key => $value) {
             $menu .=  $this->getTimerangeLink($object, $value, $key) .'  :  ';
	}
	$menu = substr($menu, 0, -3);
	$menu .= '</div>';

        foreach(explode(',' , $this->panelId) as $panelid) {

            $html = "";
            $this->panelId = $panelid;

            //image value will be returned as reference
            $previewHtml = "";
            $res = $this->getMyPreviewHtml($serviceName, $hostName, $previewHtml);

            //do not render URLs on error or if disabled
	    if (!$res || $this->enableLink == "no") 
            {
		$html .= $previewHtml;
            } else if ($this->accessmode == "direct") {
            $extra = "";
            if ($this->dashboard != $this->defaultDashboard) {
                $extra = '&panelId=' . $this->panelId . '&fullscreen';
            }
            $html .= '<a href="%s://%s/dashboard/%s/%s?var-hostname=%s&var-service=%s%s&from=now-%s&to=now%s" target="_blank">' . $this->getView()->translate('View in Grafana') . '</a>';
            $html = sprintf(
                    $html,
                    $this->protocol,
                    $this->grafanaHost,
                    $this->dashboardstore,
                    $this->dashboard,
                    urlencode($hostName),
                    rawurlencode($serviceName),
                    $this->customVars,
                    $this->timerange,
                    $extra
                ) . $previewHtml;
	    }
            else 
            {
	        $html .= '<a href="%s://%s/dashboard/%s/%s?var-hostname=%s&var-service=%s%s&from=now-%s&to=now';

                if ( $this->dashboard != $this->defaultDashboard )
	        {
		    $html .= '&panelId=' . $this->panelId .'&fullscreen';
	        }
	
	        $html .= '"target="_blank">%s</a>';

                $html = sprintf(
		    $html,
		    $this->protocol,
		    $this->grafanaHost,
		    $this->dashboardstore,
		    $this->dashboard,
		    urlencode($hostName),
		    rawurlencode($serviceName),
		    $this->customVars,
                    $this->timerange,
                    $previewHtml
	       );
           }
	   $return_html .= $html;
        }
        return $menu.$return_html;
    }
}
