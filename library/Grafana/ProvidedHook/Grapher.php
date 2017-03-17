<?php

namespace Icinga\Module\Grafana\ProvidedHook;

use Icinga\Application\Config;
use Icinga\Exception\ConfigurationError;
use Icinga\Application\Hook\GrapherHook;
use Icinga\Module\Monitoring\Object\MonitoredObject;
use Icinga\Module\Monitoring\Object\Host;
use Icinga\Module\Monitoring\Object\Service;
use Icinga\Web\Url;

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
    protected $datasource = null;

    protected function init()
    {
        $config = Config::module('grafana')->getSection('grafana');
	$this->config = $config;
        $this->username = $this->config->get('username', $this->username);
        $this->grafanaHost = $this->config->get('host', $this->grafanaHost);
	if ( $this->grafanaHost == null)
        {
           return "<p>No Grafana host configured!</p>";
	}
        $this->password = $this->config->get('password', $this->password);
        $this->protocol = $this->config->get('protocol', $this->protocol);
        $this->timerange = $this->config->get('timerange', $this->timerange);
	$this->height = $this->config->get('height', $this->height);
        $this->width = $this->config->get('width', $this->width);
	$this->enableLink = $this->config->get('enableLink', $this->enableLink);
        $this->defaultDashboard = $this->config->get('defaultdashboard', $this->defaultDashboard);
	$this->datasource = $this->config->get('datasource', $this->datasource);

        if($this->username != null){
            if($this->password != null){
                $this->auth = $this->username.":".$this->password."@";
            } else {
                $this->auth = $this->username."@";
            }
        } else {
            $this->auth = "";
        }
    }

    private function getGraphConf($serviceName)
    {

        $graphconfig = Config::module('grafana', 'graphs');
        $this->graphconfig = $graphconfig;
        if ($this->graphconfig->hasSection(strtok($serviceName, ' ')) && ($this->graphconfig->hasSection($serviceName) == False )) {
           $serviceName = strtok($serviceName, ' ');
        }
	
      $this->dashboard = $this->graphconfig->get($serviceName, 'dashboard', $this->defaultDashboard);
      $this->panelId = $this->graphconfig->get($serviceName, 'panelId', '1');
      $this->timerange = $this->graphconfig->get($serviceName, 'timerange', $this->timerange);

      return $this;
    }

    private function getPreviewImage($serviceName, $hostName)
    {
	$pngUrl = sprintf(
			'%s://%s%s/render/dashboard-solo/db/%s?var-hostname=%s&var-service=%s&panelId=%s&width=%s&height=%s&theme=light&from=now-%s&to=now',
			$this->protocol,
			$this->auth,
			$this->grafanaHost,
			$this->dashboard,
			urlencode($hostName),
			rawurlencode($serviceName),
			$this->panelId,
			$this->width,
			$this->height,
			$this->timerange
	);

        $ctx = stream_context_create(array('ssl' => array("verify_peer"=>false, "verify_peer_name"=>false), 'http' => array('method' => 'GET', 'timeout' => 5)));
        $imgBinary = @file_get_contents($pngUrl, false, $ctx);
        $error = error_get_last();
        if ($error !== null) {
            return "Graph currently unavailable: ".$error["message"];
        }

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
        if (($object instanceof Host)||($object instanceof Service)) {
            return true;
        } else {
            return false;
        }
    }

    public function getPreviewHtml(MonitoredObject $object)
    {
	// enable_perfdata = true ?
        if (! $object->process_perfdata) {
            return '';
        }

        if ($object instanceof Host) {
            $serviceName = $object->check_command;
	    $hostName = $object->host_name;
        } elseif ($object instanceof Service) {
            $serviceName = $object->service_description;
            $hostName = $object->host->getName();
        }

	$this->getGraphConf($serviceName);

	if ($this->datasource == "graphite") {
            $serviceName = preg_replace('/[^a-zA-Z0-9\*\-:]/', '_', $serviceName);
            $hostName = preg_replace('/[^a-zA-Z0-9\*\-:]/', '_', $hostName);
        }
	$html = "";
        foreach(explode(',' , $this->panelId) as $panelid) {
            $this->panelId = $panelid;

	    if ($this->enableLink == "no") 
            {
		return $this->getPreviewImage($serviceName, $hostName);
	    }

	    $html .= '<a href="%s://%s/dashboard/db/%s?var-hostname=%s&var-service=%s&from=now-%s&to=now';

            if ( $this->dashboard != $this->defaultDashboard )
	    {
		$html .= '&panelId=' . $this->panelId .'&fullscreen';
	    }
	
	    $html .= '"target="_blank">%s</a>';

            $html = sprintf(
		$html,
		$this->protocol,
		$this->grafanaHost,
		$this->dashboard,
		urlencode($hostName),
		rawurlencode($serviceName),
                $this->timerange,
		$this->getPreviewImage($serviceName, $hostName)
	   );
        }
        return $html;
    }
}
