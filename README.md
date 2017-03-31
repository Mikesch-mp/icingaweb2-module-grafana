# Grafana module for Icinga Web 2

## General Information

Insert Grafana graphs into Icinga Web 2 to display performance metrics.

## Requirements

Icinga Web 2 (>= 2.4.0)

Grafana (>= 4.1)

InfluxDB, Graphite and PNP(untested)

## Installation

* **Enable basic auth or anonymous access in your Grafana configuration.**
* Extract this to your Icinga Web 2 module folder in a folder called grafana.
* Enable the module (Configuration -> Modules -> grafana -> enable).
* Configure the module and save configuration (Configuration -> Modules -> grafana -> Configuration). 
* Depending on your datasource import the 2 json files into your Grafana server. 
  The default dashboard name is 'icinga2-default', but you can configure it now too.

__*If you use PNP datasources, you have to edit the 2 dashboards metric queries, or create new dashboards!__

## Configuration

There are various configuration settings to tweak how the module behaves.

**Username**
*(Optional)* The HTTP Basic Auth user name used to access Grafana. 

**Password**
*(Optional)* The HTTP Basic Auth password used to access Grafana. You need to set username too.

**Host**
*(Required)* The Grafana server host name (and port).

**Protocol**
The protocol used to access the Grafana server, default: *http*

**Graph height**
The graph height in pixel, default: *280*

**Graph width**
The graph width in pixel, default : *640*

**Timerange**
The global time range for the graphs, default: *6h*

**Enable Link**
Enables/disable the graph as a link to Grafana dashboard, default: *yes*

**Default Dashboard**
The name of the defaut dashboard that should be used for not configured graphs. Important, panelID must be 1! Default: *icinga2-default*

**Datasource backend**
The backend of Grafana (file or  database),default: *Database*

**Datasource type**
The type of your Grafana datasource (InfluxDB,Graphite or PNP),default: *InfluxDB*


Example (/etc/icingaweb2/modules/grafana/)config.ini
```
[grafana]
username = "you grafana username"
host = "hostname:3000"
protocol = "https"
password = "123456"
height = "280"
width = "640"
timerange = "3h"
enableLink = "yes"
defaultdashboard = "icinga2-default"
datasource = "influxdb"
defaultdashboardstore = "db"
``` 

## Graph configuration

**Name**
The name (not display name) of the service or check_command which you want to configure a graph for.

**Dashboard name**
The name of the Grafana dashboard to use.

**PanelId**
The panelId of the graph. You can get if if you click on "share" at the graph title.

**Timerange**
The time range for this service graph only.

**Height**
The graph height in pixel, if default should not be used.

**width**
The width in pixel, if default should not be used.


## FAQ
**Search Order**
The Module will search first for the service name, then for parametrized service name and last for service check_command name.

If none of the above is configured, it will use the default dashboard.

Example: Service = "MySQL Users", check_command = mysql_health 

First it will search for a configration with Name = "MySQL Usage", 

then for "MySQL", last for the check_command used like "mysql_health"

## Hats off to

This module borrows a lot from https://github.com/Icinga/icingaweb2-module-generictts & https://github.com/Icinga/icingaweb2-module-pnp.

