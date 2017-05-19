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
    protected $grafanaHostLink = null;
    protected $grafanaTheme = 'light';
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
    protected $title = "<h2>Performance Graph</h2>";
    protected $timeranges = [
                          '5m'   => '5 minutes',
                          '30m'  => '30 minutes',
                          '3h'   => '3 hours',
                          '8h'   => '8 hours',
                          '12h'  => '12 hours',
                          '24h'  => '24 hours',
                          '2d'   => '2 days',
                          '7d'   => '7 days',
                          '30d'  => '30 days',
                          '60d'  => '60 days',
    ];



    protected function init()
    {
	$this->config = Config::module('grafana')->getSection('grafana');
        $this->username = $this->config->get('username', $this->username);
        $this->grafanaHost = $this->config->get('host', $this->grafanaHost);
        $this->grafanaHostLink = $this->config->get('hostlink', $this->grafanaHostLink);
        $this->grafanaTheme = $this->config->get('theme', $this->grafanaTheme);
	if ( $this->grafanaHost == null)
        {
            throw new ConfigurationError(
                'No Grafana host configured!'
            );
	}
        if ( $this->grafanaHostLink == null) {
            $this->grafanaHostLink = $this->grafanaHost;
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
        if ( Url::fromRequest()->hasParam('grafanaimage') ) {
            $imageurl = base64_decode(Url::fromRequest()->getParam('grafanaimage'));
            $this->getGrafanaImage($imageurl);
        }
    }

    private function getGraphConf($serviceName, $serviceCommand,$hostgroups,$hostname)
    {
        $graphconfig = Config::module('grafana', 'graphs');
        $this->graphconfig = $graphconfig;

        foreach($hostgroups as $key => $value) {
            if ($this->graphconfig->hasSection('hostgroup='.$key.'&service='.$serviceName))  {
                $serviceName='hostgroup='.$key.'&service='.$serviceName;
            }
        }

        if ($this->graphconfig->hasSection('hostname='.$hostname.'&service='.$serviceName))  {
                $serviceName='hostname='.$hostname.'&service='.$serviceName;
        }

        if ($this->graphconfig->hasSection(strtok($serviceName, ' ')) && ($this->graphconfig->hasSection($serviceName) == False ))
        {
           $serviceName = strtok($serviceName, ' ');
        }

       if ($this->graphconfig->hasSection(strtok($serviceName, ' ')) == False && ($this->graphconfig->hasSection($serviceName) == False ))
        {
            $serviceName = $serviceCommand;
            if($this->graphconfig->hasSection($serviceCommand) == False && $this->defaultDashboard == 'none') {
                return NULL;
            }
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

    private function getGrafanaImage($url) {
        $curl_handle = curl_init();
        $curl_opts = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_USERPWD => "$this->auth",
            CURLOPT_HTTPAUTH, CURLAUTH_ANY
        );

        curl_setopt_array($curl_handle, $curl_opts);
        $res = curl_exec($curl_handle);

        $info = curl_getinfo($curl_handle);

        curl_close($curl_handle);
        header('Content-Type: '.$info['content_type']);
        header('Content-Length: '.$info['download_content_length']);
        echo $res;
        exit;
    }

    //returns false on error, previewHTML is passed as reference
    private function getMyPreviewHtml($serviceName, $hostName, &$previewHtml,$object)
    {
        if ($this->accessmode == "proxy") {
            $pngUrl = sprintf(
                '%s://%s/render/dashboard-solo/%s/%s?var-hostname=%s&var-service=%s%s&panelId=%s&width=%s&height=%s&theme=%s&from=now-%s&to=now',
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
                $this->grafanaTheme,
                $this->timerange
            );

            $this->view = Icinga::app()->getViewRenderer()->view;
            if ($object instanceof Host)
            {
                $array = [
                      'host'       => $object->host_name,
                      'grafanaimage' => base64_encode($pngUrl),
                ];
                $link = 'monitoring/host/show';
            } else {
                $array = [
                      'host'       => $object->host->getName(),
                      'service'    => $object->service_description,
                      'grafanaimage' => base64_encode($pngUrl),
                ];
                $link = 'monitoring/service/show';
            }
            $previewHtml = sprintf('<img src="%s" style="display:block; width: auto; height: auto; max-width: 100%%; max-height: 100%%;box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.4), 0 6px 20px 0 rgba(0, 0, 0, 0.4) !important; border-radius: 5px; ">',$this->view->url($link, $array));
        } else {
            $iframehtml = '<iframe src="%s://%s/dashboard-solo/%s/%s?var-hostname=%s&var-service=%s%s&panelId=%s&theme=%s&from=now-%s&to=now&trickrefresh=%s" alt="%s" height="%d" frameBorder="0" style="width: 100%%;"></iframe>';
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
                $this->grafanaTheme,
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

        if($this->getGraphConf($serviceName, $object->check_command,$object->hostgroups,$hostName) == NULL) {
            return;
        }

        if ($this->datasource == "graphite")
        {
            $serviceName = preg_replace('/[^a-zA-Z0-9\*\-:]/', '_', $serviceName);
            $hostName = preg_replace('/[^a-zA-Z0-9\*\-:]/', '_', $hostName);
        }

	$return_html = "";
        
        // replace template to customVars from Icinga2
	foreach($customVars as $k => $v){
		$search[] = "\$$k\$";
		$replace[] = is_string($v) ? rawurlencode($v) : null;
		$this->customVars = str_replace($search, $replace, $this->customVars);
	}

        $menu = '<div class="scrollmenu" style="overflow: hidden; white-space: nowrap; padding: 8px">';
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
            $res = $this->getMyPreviewHtml($serviceName, $hostName, $previewHtml,$object);

            //do not render URLs on error or if disabled
	    if ($this->enableLink == "no") 
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
		    $this->grafanaHostLink,
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
        return $this->title.$menu.$return_html;
    }
}
