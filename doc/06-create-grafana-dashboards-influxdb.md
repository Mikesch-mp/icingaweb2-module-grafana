# Create Grafana dashboards for InfluxDB backend

To create your own graphs you need to create a new [dashboard](http://docs.grafana.org/guides/basic_concepts/#dashboard) with a [panel](http://docs.grafana.org/guides/basic_concepts/#panel) in Grafana.
In this example we create a dashboard for the command ping6, open the menu and go to `Dashboards` -> `New`.

![Create new Dashboard](images/06-create-new.png)

First go to the dashboard settings (![Settings](images/06-cog.png)) and change the name to `ITL-ping6`.
Also if you want add description, row names, links etc.
To save your changes, just hit the :floppy_disk: icon.

## Templating

We use templateing to get a map from var-[hostname|servicename|command] to a Grafana variable. We will set this as a query to InfluxDB and later if you want we change it to an constant.
Open the `Templating` settings and hit the ![Templating](images/06-new-button.png) button.
The variable name has to be `hostname`, `service` or `command` for the use with the grafana module.
The `Datasource` should point to your InfluxDB datasource.
Set the `Type` to `Query`, Refresh to `On Dashboard Load` and if you want, set `Sort` to what preferred sort order.

 * Hostname query
```
 SHOW TAG VALUES WITH KEY = "hostname"
```

 * Service query
```
 SHOW TAG VALUES WITH KEY = "service" where hostname =~ /^$hostname$/
```

 * Command query
```
 SHOW MEASUREMENTS where hostname =~ /^$hostname$/
```

The complete templating should now look like this

![Complete Templating](images/06-templating-influx.png)

Close the templateing and hit the :floppy_disk: icon to save the dashboard we made so far.

![New Dashboard with templating](images/06-new-dashboard-with-templating.png)

## Add Panel (Graph)

Now we add a new `Panel` to our dashboard, click on the 3 points in the left side of the row and choose `Add Panel`

![Add Panel](images/06-dashboard-add-panel.png)

We want to add `Graph`

![Empty Graph](images/06-dashboard-panel.png)

To edit the graph, move your mouse over the graph title (Panel Title) and click on it.
Choose `Edit` from the opened menu. Now you will see a new menu below the graph.

![Edit Graph](images/06-dashboard-panel-influx-edit.png)

## Edit Panel Metrics (Metrics Tab)

 1. Change the `Data Source` to your InfluxDB data source if it is not your default data source.
 2. Choose a __fixed__ measurement or change it to `/^$command$/` if you want it a more dynamically.
 3. Add to where clause the `hostname =~ /^$hostname$/`.
 4. If you have more then one service for your host that uses __ping6__ as command you should also add another where clause for the service with `service =~ /^$service$/`.
 5. Change `fill(null)` to `fill(none)`.
 6. Add `tag(metric)` in the same line where `fill(none)` is. This will give us all metric (short) names to use in the legend.
 7. Insert `$tag_metric` at **ALIAS BY**.

![Metrics Value](images/06-dashboard-panel-metrics.png)

### Add Critical & Warning (optional)

Adding thresholds will change the Y-axis range, so you will not see as much details of your metric as without them.

 1. For __critical__ duplicate the **A** query to **B**.
 2. Change `field(value)` to `field(crit)` on query **B**.
 3. Remove `mean()` and add `last()` instead on query **B**.
 4. Remove `tag(metric) on query **B**.
 5. Change `fill(none)` to `fill(last)` on query **B**.
 6. Change __ALIAS BY__ to `critical` on query **B**.
 7. For __warning__ duplicate tge **B** query to **C**.
 8. Change `field(crit)` to `field(warn)` on query **C**.
 9. Change **ALIAS BY** to `warning` on query **C**.

![Critical and Warning](images/06-dashboard-panel-metrics-crit-warn.png)

## Change axis for second metric (pl)

As ping6 has `rta`and `pl` metrics and they have different units, we can use both Y axis.
Change the `pl` axis by clicking on the small colored line and choose `Right` as `Y Axis`.

![Set Y axis](images/06-dashboard-panel-yaxis.png)

## Change colors for values/tresholds

To change the colors the quick way just click on the small colored line infront of the metric/thresholds in the legend.

![Colors for metrics/threshold](images/06-dashboard-panel-colors.png)

The default Icinga2 color for critical is `#ff5566` and for warning it is `#ffaa44`

Dont forget to hit the :floppy_disk: from time to timee to save your dashboard.

## Change graph title (General tab)

Here enter a title for your graph or leave it empty. If you want to set it to the service name, enter `$service` in the `Title` field.
To make your colleagues happy, enter also a short description (optional), the field support markdown :smiley:.

![Panel title](images/06-dashboard-panel-title.png)

## Axis Units (Axes tab)

Set the left Y axis unit to `seconds` (Icinga2 stores all time based metrics in seconds) for the `rta` metric.
The left Y axis we set to `percent: (0-100)`, the `Y-Min` and `Decimals` to **0**, because pl is a percentage without decimals.

![Axis](images/06-dashboard-panel-axis.png)

## Legend (Legend tab)

Activate `As Table`, `Min`, `Max`, `Avg` and `Current` to have some more information shown.
If you want to hide metrics with only **0** in the choosen time range, activate __Hide series__ `With only zeros` too.

![Legend](images/06-dashboard-panel-legend.png)

## Display Series overrides for thresholds (Display tab)

For thresholds we remove the the line filling, so they will only be a thin line.
To add a new override click first on `Series overrides` and then on the `+ Add overrides`
Insert `/critical|warning/`(the alias we used in the query) into `alias or regex`, add `Line fill:0` and `Legend: false` to it.

![Overrides](images/06-dashboard-panel-display-overrides.png)


## Final dashboard

### With thresholds

![With thresholds](images/06-final-dashboard-threshold-influx.png)

### Without thresholds

![Without thresholds](images/06-final-dashboard-without-threshold-influx.png)
