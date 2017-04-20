<?php

namespace Icinga\Module\Grafana\ProvidedHook;

use Icinga\Application\Icinga;
use Icinga\Application\Config;
use Icinga\Exception\ConfigurationError;
use Icinga\Application\Hook\GrapherHook;
use Icinga\Module\Monitoring\Object\MonitoredObject;
use Icinga\Module\Monitoring\Object\Host;
use Icinga\Module\Monitoring\Object\Service;
use Icinga\Web\Url;
use Icinga\Web\View;

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
    protected $enableLink = true;
    protected $defaultDashboard = "icinga2-default";
    protected $defaultDashboardStore = "db";
    protected $datasource = null;
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
           return "<p>No Grafana host configured!</p>";
	}
        $this->password = $this->config->get('password', $this->password);
        $this->protocol = $this->config->get('protocol', $this->protocol);

	// Check if there is a timerange in url params
	if ( Url::fromRequest()->hasParam('timerange') ) {
           $this->timerange = Url::fromRequest()->getParam('timerange');
	} else {
           $this->timerange = $this->config->get('timerange', $this->timerange);
        }

	$this->height = $this->config->get('height', $this->height);
        $this->width = $this->config->get('width', $this->width);
	$this->enableLink = $this->config->get('enableLink', $this->enableLink);
        $this->defaultDashboard = $this->config->get('defaultdashboard', $this->defaultDashboard);
        $this->defaultDashboardStore = $this->config->get('defaultdashboardstore', $this->defaultDashboardStore);
	$this->datasource = $this->config->get('datasource', $this->datasource);
        $this->view = Icinga::app()->getViewRenderer()->view;
        if($this->username != null)
        {
            if($this->password != null)
            {
                $this->auth = $this->username.":".$this->password."@";
            }
            else
           {
                $this->auth = $this->username."@";
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
      $this->timerange = $this->graphconfig->get($serviceName, 'timerange', $this->timerange);
      $this->height = $this->graphconfig->get($serviceName, 'height', $this->height);
      $this->width = $this->graphconfig->get($serviceName, 'width', $this->width);

      return $this;
    }

    private function getTimerangeLink($hostName, $serviceName, $rangeName, $timeRange)
    {
        return $this->view->qlink(
                                $rangeName,
                                'monitoring/service/show',
                                array(
                                    'host'       => $hostName,
                                    'service'    => $serviceName,
                                    'timerange'  => $timeRange
                                ),
                                array(
                                        'class'             => 'action-link',
                                        'data-base-target'  => '_self',
                                        'title'             => 'Set timerange for graph to '. $rangeName
                                )
        );
    }

    private function getPreviewImage($serviceName, $hostName)
    {
	$pngUrl = sprintf(
			'%s://%s%s/render/dashboard-solo/%s/%s?var-hostname=%s&var-service=%s%s&panelId=%s&width=%s&height=%s&theme=light&from=now-%s&to=now',
			$this->protocol,
			$this->auth,
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
        curl_setopt($curl_handle,CURLOPT_URL,$pngUrl);
        curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
        curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl_handle,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl_handle,CURLOPT_TIMEOUT,5);
        $imgBinary = curl_exec($curl_handle);
        if(curl_error($curl_handle))
        {
            return 'Graph currently unavailable: :' . curl_error($curl_handle);
        }
        curl_close($curl_handle);


        $img = 'data:image/png;base64,'.base64_encode($imgBinary);
        $imghtml = '<img src="%s" alt="%s" width="%d" height="%d" />';
        return sprintf(
            $imghtml,
            $img,
            rawurlencode($serviceName),
            $this->width,
            $this->height
      );
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
            $customVars = $object->fetchCustomvars()->customvars;
            $hostName = $object->host_name;
        } 
        elseif ($object instanceof Service) 
        {
            $serviceName = $object->service_description;
            $customVars = $object->fetchCustomvars()->customvars;
            $hostName = $object->host->getName();
        }

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
             $menu .=  $this->getTimerangeLink($hostName, $serviceName, $value, $key) .'  :  ';
	}
	$menu .= '</div>';
	$html = "";

        foreach(explode(',' , $this->panelId) as $panelid) {

            $this->panelId = $panelid;

	    if ($this->enableLink == "no") 
            {
		$html .= $this->getPreviewImage($serviceName, $hostName);
	    }
            else 
            {
	        $html .= '<a href="%s://%s/dashboard/%s/%s?var-hostname=%s&var-service=%s&from=now-%s&to=now';

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
                    $this->timerange,
		    $this->getPreviewImage($serviceName, $hostName)
	       );
           }
	   $return_html .= $html;
        }
        return $menu.$return_html;
    }
}
