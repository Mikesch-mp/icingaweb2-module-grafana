# Grafana Module for Icinga Web 2

#### Table of Contents

1. [About](#about)
2. [Requirements](#requirements)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [FAQ](#faq)
6. [Thanks](#thanks)


## About

Add Grafana graphs into Icinga Web 2 to display performance metrics.

![Icinga Web 2 Grafana Integration](https://github.com/Mikesch-mp/icingaweb2-module-grafana/raw/master/doc/images/icingaweb2_grafana_screenshot_01.png "Grafana")
![Icinga Web 2 Grafana Integration](https://github.com/Mikesch-mp/icingaweb2-module-grafana/raw/master/doc/images/icingaweb2_grafana_screenshot_02.png "Grafana")

## Requirements

* [Icinga Web 2](https://www.icinga.com/products/icinga-web-2/) (>= 2.4.1)
* [Grafana](https://grafana.com/) (>= 4.1)
* [InfluxDB](https://docs.influxdata.com/influxdb/), [Graphite](https://graphiteapp.org) or [PNP](https://docs.pnp4nagios.org/) (untested) as backend for Grafana
* [PHP](https://www.php.net) with curl enabled (for proxy mode)

## Installation

Extract this module to your Icinga Web 2 modules directory as `grafana` directory.

Git clone:

```
cd /usr/share/icingaweb2/modules
git clone https://github.com/Mikesch-mp/icingaweb2-module-grafana.git grafana
```

Tarball download (latest [release](https://github.com/Mikesch-mp/icingaweb2-module-grafana/releases/latest)):

```
cd /usr/share/icingaweb2/modules
wget https://github.com/Mikesch-mp/icingaweb2-module-grafana/archive/v1.1.0.zip
unzip v1.1.0.zip
mv icingaweb2-module-grafana-1.1.0 grafana
```

Enable the module in the Icinga Web 2 frontend in `Configuration -> Modules -> grafana -> enable`.
You can also enable the module by using the `icingacli` command:

```
icingacli module enable grafana
```

### Grafana Preparations

Enable basic auth or anonymous access in your Grafana configuration.

Choose which datasource to use (InfluxDB, Graphite). Import the JSON files from the `dashboards`
directory.

* `base-metrics.json`
* `icinga2-default.json`

The default dashboard name is 'icinga2-default'. You can also configure it inside the module.

There are currently no default dashboards for PNP available. Please create them on your own and send a PR.


## Configuration

### Global Configuration

You can edit global configuration settings in Icinga Web 2 in `Configuration -> Modules -> grafana -> Configuration`.

Setting            | Description
-------------------|-------------------
host               | **Required.** Grafana server host name (and port).
protocol           | **Optional.** Protocol used to access the Grafana server. Defaults to `http`.
graph height       | **Optional.** Graph height in pixel. Defaults to `280`.
graph width        | **Optional.** Graph width in pixel. Defaults to `640`.
timerange          | **Optional.** Global time range for graphs. Defaults to `6h`.
enableLink         | **Optional.** Enable/disable graph with a rendered URL to the Grafana dashboard. Defaults to `yes`.
datasource         | **Required.** Type of the Grafana datasource (`influxdb`, `graphite` or `pnp`). Defaults to `influxdb`.
defaultdashboard   | **Required.** Name of the default dashboard which will be shown for unconfigured graphs. **Important: `panelID` must be set to `1`!** Defaults to `icinga2-default`.
defaultdashboardstore | **Optional.** Grafana backend (file or database). Defaults to `Database`.
accessmode         | **Optional.** Controls whether graphs are fetched with curl (`proxy`), are embedded (`direct`) or in iframe ('iframe'. Direct access is faster and needs `auth.anonymous` enabled in Grafana. Defaults to `proxy`.
timeout            | **Proxy only** **Optional.** Timeout in seconds for proxy mode to fetch images. Defaults to `5`.
username           | **Proxy non anonymous only** **Required** HTTP Basic Auth user name to access Grafana.
password           | **Proxy non anonymous only** **Required** HTTP Basic Auth password to access Grafana. Requires the username setting.
directrefresh      | **Direct Only** **Optional.** Refresh graphs on direct access. Defaults to `no`.
usepublic          | **Optional** Enable usage of publichost/protocol. Defaults to `no`.
publichost         | **Optional** Use a diffrent host for the graph links.
publicprotocol     | **Optional** Use a diffrent protocol for the graph links.

**IMPORTANT**
Be warned on 'iframe' access mode the auto refresh will hit you!

Example:
```
vim /etc/icingaweb2/modules/grafana/config.ini

[grafana]
username = "your grafana username"
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
accessmode = "proxy"
timeout = "5"
directrefresh = "no"
usepublic = "no"
publichost = "otherhost:3000"
publicprotocol = "http"
```

### Graph Configuration

You can add specific graph configuration settings in Icinga Web 2 in `Configuration -> Grafana Graphs`.

Setting            | Description
-------------------|-------------------
name               | **Optional.** The name (not the `display_name`) of the service or check command where a graph should be rendered.
dashboard          | **Optional.** Name of the Grafana dashboard to use.
panelId            | **Optional.** Graph panelId. Open Grafana and select to share your dashboard to extract the value.
customVars         | **Optional.** Set additional custom variables used for Grafana.
timerange          | **Optional.** Specify the time range for this graph.
height             | **Optional.** Graph height in pixel. Overrides global default.
width              | **Optional.** Graph width in pixel. Overrides global default.

Example:
```
vim /etc/icingaweb2/modules/grafana/graphs.ini

[check_command]
dashboard = "my-own"
panelId = "42"
customVars = "&os=$os$"
timerange = "3h"
height = "100"
width = "150"

```


## FAQ

### Search order

This module prefers the `service name`, then looks for an optional `parametrized service name` and for the `service check command name`.

If there is no match, it will use the default dashboard as fallback.

Example:

```
Service = "MySQL Users", check_command = mysql_health
```
At first glance `Name = "MySQL Usage"` must provide a match. Then `MySQL` and last but not least any service
`check_command` attribute which is set to `mysql_health`.

## Thanks

This module borrows a lot from https://github.com/Icinga/icingaweb2-module-generictts & https://github.com/Icinga/icingaweb2-module-pnp.
