# Grafana module for Icinga Web 2

## General Information

Insert Grafana graphs into Icinga Web 2 to display performance metrics.

## Requirements

Icinga Web 2 (>= 2.4.0)

Grafana (>= 4.1)

InfluxDB or any other valid data source

## Installation

Just extract this to your Icinga Web 2 module folder in a folder called grafana.

(Configuration -> Modules -> grafana -> enable).

Import the 2 json files into your Grafana server. The default dashboard must be named 'icinga2-default'!.

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

**Timerange now-**
The global time range for the graphs, default: *6h'

## Graph configuration

**Name of Service**
The name (not display name) of the service which you want to configure a graph for.

**Dashboard name**
The name of the Grafana dashboard to use.

**PanelId**
The panelId of the graph. You can get if if you click on "share" at the graph title.

**Timerange now-**
The time range for this service graph only.

**Enable link**
Enables the perfdata image as an link to the Grafana dashboard.

## Hats off to

This module borrows a lot from https://github.com/Icinga/icingaweb2-module-generictts & https://github.com/Icinga/icingaweb2-module-pnp.

