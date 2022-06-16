# Change Log

## [v1.3.6](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.3.6) (2019-09-07)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.3.5...v1.3.6)

**Closed issues:**

- Some services not shown in Icingaweb2 2.7.1 when grafana module enabled [\#221](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/221)
- 'No data points' on host objects with spaces in the name [\#219](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/219)
- Question concerning the 2 dashboards [\#216](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/216)
- Timelegend is shown UTC and not Browser / php time [\#215](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/215)
- Icinga2 not get all  the graphs from grafana , got only ping4 graph [\#197](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/197)

## [v1.3.5](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.3.5) (2019-05-12)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.3.4...v1.3.5)

**Implemented enhancements:**

- Disable autorefresh for grafana iframe [\#209](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/209) ([friesoft](https://github.com/friesoft))

**Fixed bugs:**

- Problems using spaces in command name [\#210](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/210)
- Problem with unknown Datasource [\#208](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/208)
- customVars in grafana URLs not working anymore [\#202](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/202)

**Closed issues:**

- var-hostname with newest files from repo not working with graphite [\#204](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/204)

## [v1.3.4](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.3.4) (2018-12-17)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.3.3...v1.3.4)

**Fixed bugs:**

- Icingaweb2-Dashlet with Grafana-Graphs needs permisison for grafana/graph, which doesn't exists [\#191](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/191)

**Closed issues:**

- Graphs broken with 1.3.3 version together with Grafana 5 and Enable link = Yes  [\#194](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/194)

## [v1.3.3](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.3.3) (2018-12-16)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.3.2...v1.3.3)

**Implemented enhancements:**

- support multiple custom vars [\#189](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/189) ([cxcv](https://github.com/cxcv))

**Fixed bugs:**

- Custom Vars don't get escaped when send over to grafana [\#186](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/186)

**Closed issues:**

- No Graph shown on master since commits from 10.12.2018 [\#192](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/192)
- support multiple custom variables [\#188](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/188)
- Help creating dashboard template parsing metrics [\#185](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/185)
- Time used by graphs in Icingaweb2 [\#107](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/107)

**Merged pull requests:**

- Update 02-installation.md [\#190](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/190) ([Eifoen](https://github.com/Eifoen))

## [v1.3.2](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.3.2) (2018-09-14)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.3.1...v1.3.2)

**Fixed bugs:**

- Selecting any "Special" timerange breaks graph in module but following link to Grafana works [\#182](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/182)
- Variable timerange is missing host on post request. [\#180](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/180)
- No graphs for unavailable services [\#177](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/177)

**Closed issues:**

- Changing timerange is missing the parameter host. [\#181](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/181)
- Is it possible to use Regex in Grafana Config [\#179](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/179)

## [v1.3.1](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.3.1) (2018-08-14)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.3.0...v1.3.1)

**Fixed bugs:**

- Indirectproxy multiple panels show one panel multiple times [\#178](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/178)

## [v1.3.0](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.3.0) (2018-08-14)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.2.5...v1.3.0)

**Implemented enhancements:**

- added field to configure timeranges by hand [\#176](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/176) ([neubi4](https://github.com/neubi4))
- Move permission test to the HostActions Hook [\#173](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/173) ([lippserd](https://github.com/lippserd))

**Fixed bugs:**

- View all graphs and grafana\_graph\_disable [\#172](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/172)
- The default timerange for graphs does not work [\#164](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/164)

**Closed issues:**

- Host Action "Show all Graphs" - No Data Ppoints [\#175](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/175)
- How to with "check\_by\_ssh" ???? [\#174](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/174)
- No data points even performance\_data shows data [\#169](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/169)
- Same graph for all checks with same name [\#163](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/163)

**Merged pull requests:**

- Graphs on dashboard [\#154](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/154) ([nbuchwitz](https://github.com/nbuchwitz))

## [v1.2.5](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.2.5) (2018-05-31)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.2.4...v1.2.5)

**Fixed bugs:**

- failure if grafana\_graph\_disable is not set [\#157](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/157)
- Indirect-proxy-mode not working when using FQDN as hostname [\#156](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/156)
- "Disable Graph" feature seems broken [\#111](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/111)

**Closed issues:**

- Config parsing inconsistency regarding 'indirectproxyrefresh'  [\#153](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/153)
- indirectproxy mode - default dashboard [\#152](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/152)
- indirect proxy mode uses http:// links [\#136](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/136)
- Indirect-Proxy mode crops redered images [\#127](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/127)

## [v1.2.4](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.2.4) (2018-04-30)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.2.3...v1.2.4)

**Fixed bugs:**

- Module fails if no cutom variable is set to disable graph [\#151](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/151)

## [v1.2.3](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.2.3) (2018-04-29)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.2.2...v1.2.3)

## [v1.2.2](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.2.2) (2018-04-29)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.2.1...v1.2.2)

**Implemented enhancements:**

- indirectproxy mode refreshes graphs [\#142](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/142)
- Fix behavior of disabling the Grafana Graph [\#140](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/140) ([mcktr](https://github.com/mcktr))

**Fixed bugs:**

- indirectproxy mode - custom variables [\#145](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/145)
- Error creating or updating Grafana Graphs on Icinga Web [\#138](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/138)

**Closed issues:**

- Passive checks performance counters not showed in grafana [\#144](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/144)
- Error Integrating Grafana to Icinga2 Host Overview [\#143](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/143)
- Group by value based on timeframe [\#139](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/139)
- graphs are not saved -\> uncaught error [\#137](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/137)

## [v1.2.1](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.2.1) (2018-03-21)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.2.0...v1.2.1)

**Implemented enhancements:**

- \[Feature request\] Indirect Proxy: Reserve space to prevent jumps when graphs appear [\#131](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/131)
- Idea: How to speed up proxy mode [\#126](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/126)
- Proposal: Issue Milestones, Labels, Styleguide, Contributing, Changelog [\#34](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/34)
- add permission for enableLink [\#135](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/135) ([xam-stephan](https://github.com/xam-stephan))

**Fixed bugs:**

- Undefined index: grafana\_version [\#134](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/134)
- Print link has nasty &amp in url which causes timerange not honored [\#130](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/130)
- Module does not load first graph \(panelId=1\) from dashboard [\#129](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/129)

**Closed issues:**

- Optional use host\_display\_name instead of host\_name in call to Grafana [\#133](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/133)
- Scope of Custom Variables [\#132](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/132)
- Indirect Proxy Mode: If one panel fails to render the previous gets duplicated [\#128](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/128)
- Grafana 5 compatibility [\#116](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/116)

## [v1.2.0](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.2.0) (2018-03-06)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.1.10...v1.2.0)

**Implemented enhancements:**

- Create new dashboards for ITL plugins [\#105](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/105)
- Support grafana API token [\#97](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/97)
- Feature request - grafana graphs names wildcards specifications [\#39](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/39)
- Feature request: Show all graphs from host [\#30](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/30)
- Fullscreen view for default dashboard panels [\#119](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/119) ([jonbulica99](https://github.com/jonbulica99))

**Fixed bugs:**

- Using a dashboard with spaces in between words [\#108](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/108)

**Closed issues:**

- Document host -\> all graphs action [\#125](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/125)
- Make panel id for default dashboard configurable [\#122](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/122)
- Grafana ApiKey and iFrame [\#121](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/121)
- custom variables in graphs [\#120](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/120)
- Using "hostdisplayname" as a parameter for graph generation [\#109](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/109)
- Service performance graph shows wrong graph of another service [\#103](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/103)
- Explain the purpose of Grafana dashboards & how to enhance them [\#75](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/75)

**Merged pull requests:**

- Add Changelog for 1.2.0; update release docs [\#124](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/124) ([dnsmichi](https://github.com/dnsmichi))
- Update GraphForm.php [\#123](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/123) ([nbuchwitz](https://github.com/nbuchwitz))
- 1.2.0 [\#118](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/118) ([Mikesch-mp](https://github.com/Mikesch-mp))
- Update 02-installation.md [\#117](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/117) ([nbuchwitz](https://github.com/nbuchwitz))

## [v1.1.10](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.1.10) (2017-12-17)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.1.8...v1.1.10)

**Implemented enhancements:**

- Feature request: All panels [\#42](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/42)

**Fixed bugs:**

- Possibility to define organization ID [\#84](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/84)
- Proxy Mode: Show a better error message if PHP CURL is not installed/loaded [\#89](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/89) ([dnsmichi](https://github.com/dnsmichi))

**Closed issues:**

- Update releases [\#104](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/104)
- Error after upgrading to icingaweb2 2.4.1 [\#101](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/101)
- Graph per metric [\#96](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/96)
- curl timeout [\#94](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/94)
- Error importing dashboard grafana [\#93](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/93)
- Error viewing host after enable grafana module [\#92](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/92)
- no upgrade instructions [\#87](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/87)
- Move installation, configuration, etc. to doc/ and link over there. [\#76](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/76)

**Merged pull requests:**

- Update 04-graph-configuration.md [\#91](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/91) ([nbuchwitz](https://github.com/nbuchwitz))
- Update module.info [\#90](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/90) ([nbuchwitz](https://github.com/nbuchwitz))
- Docs: Make it more clear to enable the module [\#86](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/86) ([dnsmichi](https://github.com/dnsmichi))

## [v1.1.8](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.1.8) (2017-07-17)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.1.7...v1.1.8)

**Implemented enhancements:**

- Add contribution guide & add license/support info [\#74](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/74) ([dnsmichi](https://github.com/dnsmichi))
- Add GitHub issue template [\#73](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/73) ([dnsmichi](https://github.com/dnsmichi))
- Add RELEASE.md for release workflow help [\#72](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/72) ([dnsmichi](https://github.com/dnsmichi))

**Fixed bugs:**

- Hard-Coded InfluxDB/HostAlive in icinga2-default.json [\#82](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/82)
- Graph is not displayed if service status UNKNOWN [\#71](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/71)
- Add support for multiple Grafana Organization [\#79](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/79) ([epinter](https://github.com/epinter))

## [v1.1.7](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.1.7) (2017-06-03)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.1.6...v1.1.7)

**Implemented enhancements:**

- Remove outline \(on-click\) from image [\#69](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/69) ([epinter](https://github.com/epinter))

**Merged pull requests:**

- Add the defaultdashboard option 'none' to README [\#70](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/70) ([epinter](https://github.com/epinter))

## [v1.1.6](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.1.6) (2017-06-01)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.1.5...v1.1.6)

**Implemented enhancements:**

- 1.1.5 - No more scaling of the graph size ? [\#67](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/67)
- Proposal/Wish: Add a configuration switch for the theme/style [\#50](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/50)
- Fix CSS menu on webkit browsers [\#65](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/65) ([epinter](https://github.com/epinter))
- Set image width and height to auto [\#64](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/64) ([epinter](https://github.com/epinter))
- Use icinga color for timerange menu [\#62](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/62) ([epinter](https://github.com/epinter))

**Fixed bugs:**

- 1.1.6 - Undefined variable: imgClass [\#68](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/68)
- No Graph while Status Unknown [\#66](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/66)
- Parse error: syntax error, unexpected '\[' in Grapher.php [\#55](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/55)

## [v1.1.5](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.1.5) (2017-05-23)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.1.4...v1.1.5)

**Implemented enhancements:**

- disable graphs at service manually or automatic? [\#9](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/9)
- Move styles to css file [\#53](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/53) ([epinter](https://github.com/epinter))

**Merged pull requests:**

- Remove executable bit from files [\#52](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/52) ([dnsmichi](https://github.com/dnsmichi))

## [v1.1.4](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.1.4) (2017-05-20)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.1.3...v1.1.4)

**Implemented enhancements:**

- Add theme selection, light by default [\#57](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/57) ([epinter](https://github.com/epinter))

**Fixed bugs:**

- master broken due to typo [\#43](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/43)

## [v1.1.3](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.1.3) (2017-05-01)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.1.2...v1.1.3)

## [v1.1.2](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.1.2) (2017-04-30)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.1.1...v1.1.2)

**Implemented enhancements:**

- Add a GitHub description [\#38](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/38)
- Proxy mode with curl might fail with SELinux enabled [\#35](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/35)
- Timeout for proxy mode [\#40](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/40)

**Fixed bugs:**

- Proxy mode error handling with curl is buggy and not verbose enough [\#32](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/32)
- Proxy mode always returns 401 [\#31](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/31)
- multiple panelid's give error [\#29](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/29)
- Graphs don't refresh when Grafana access mode is direct [\#41](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/41)
- custom vars missing in link url [\#36](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/36)

**Merged pull requests:**

- Fix curl error handling and format error string [\#37](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/37) ([dnsmichi](https://github.com/dnsmichi))
- Better error handling for proxy access mode [\#33](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/33) ([dnsmichi](https://github.com/dnsmichi))

## [v1.1.1](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.1.1) (2017-04-22)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.1.0...v1.1.1)

**Implemented enhancements:**

- Graph render performance slow [\#21](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/21)

## [v1.1.0](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.1.0) (2017-04-21)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.0.11...v1.1.0)

**Fixed bugs:**

- Missing Grafana host configuration results in Undefined property: Icinga\Module\Grafana\ProvidedHook\Grapher::$view [\#26](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/26)
- Timerange menu - Service not found [\#24](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/24)

**Merged pull requests:**

- Fix view exception if Grafana host is not configured [\#28](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/28) ([dnsmichi](https://github.com/dnsmichi))
- Enhance the documentation, add screenshots [\#27](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/27) ([dnsmichi](https://github.com/dnsmichi))

## [v1.0.11](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.0.11) (2017-04-20)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.0.10...v1.0.11)

## [v1.0.10](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.0.10) (2017-04-20)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.0.9...v1.0.10)

**Implemented enhancements:**

- Possibility to change time in the view [\#4](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/4)

## [v1.0.9](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.0.9) (2017-04-20)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.0.8...v1.0.9)

## [v1.0.8](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.0.8) (2017-04-05)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.0.7...v1.0.8)

**Implemented enhancements:**

- Change graph size when adding new Graphana Graphs [\#19](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/19)
- New features [\#20](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/20) ([xbulat](https://github.com/xbulat))

## [v1.0.7](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.0.7) (2017-03-31)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.0.6...v1.0.7)

**Merged pull requests:**

- Add file backend for dashboards [\#18](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/18) ([dh-harald](https://github.com/dh-harald))

## [v1.0.6](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.0.6) (2017-03-30)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.0.5...v1.0.6)

## [v1.0.5](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.0.5) (2017-03-27)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.0.4...v1.0.5)

**Implemented enhancements:**

- Possibility to add more then one graph [\#12](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/12)

**Closed issues:**

- Grafana did not show the configuration Menu [\#16](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/16)
- png preview files not generated... [\#13](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/13)

## [v1.0.4](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.0.4) (2017-03-17)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.0.3...v1.0.4)

**Implemented enhancements:**

- "default rendering" options in configuration [\#3](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/3)
- Sample dashboard works for InfluxDB but not Graphite [\#1](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/1)

**Merged pull requests:**

- fixed dashboard default value [\#8](https://github.com/Mikesch-mp/icingaweb2-module-grafana/pull/8) ([mkayontour](https://github.com/mkayontour))

## [v1.0.3](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.0.3) (2017-03-03)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.0.2...v1.0.3)

**Implemented enhancements:**

- Docs: Provide a code snippet for config.ini [\#2](https://github.com/Mikesch-mp/icingaweb2-module-grafana/issues/2)

## [v1.0.2](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.0.2) (2017-02-13)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.0.1...v1.0.2)

## [v1.0.1](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.0.1) (2017-02-08)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.0.0...v1.0.1)

## [v1.0.0](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/v1.0.0) (2017-02-07)


\* *This Change Log was automatically generated by [github_changelog_generator](https://github.com/skywinder/Github-Changelog-Generator)*
