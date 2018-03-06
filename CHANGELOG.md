# Change Log

## [1.2.0](https://github.com/mikesch-mp/icingaweb2-module-grafana/tree/1.2.0) (2018-03-06)
[Full Changelog](https://github.com/mikesch-mp/icingaweb2-module-grafana/compare/v1.1.10...1.2.0)

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
