# Grafana module for Icinga Web 2

## General Information

Insert Grafana graphs into Icinga Web 2 to display performance metrics.

## Requirements

Icinga Web 2 (>= 2.4.0)
Grafana (>= 4.1)
InfluxDB

## Installation

Just extract this to your Icinga Web 2 module folder in a folder called grafana.

(Configuration -> Modules -> grafana -> enable).

Import the 2 json files into your Grafana server. The default dashboard must be named 'icinga2-default'!.

## Configuration

There are various configuration settings to tweak how the module behaves.


## Hats off to

This module borrows a lot from https://github.com/Icinga/icingaweb2-module-generictts & https://github.com/Icinga/icingaweb2-module-pnp.

