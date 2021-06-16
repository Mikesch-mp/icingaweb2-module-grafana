#
# spec file for package icingaweb2-module-grafana
# You need to define version and release externally to build this RPM, e.g.
# with rpmbuild --define "version ${VERSION}" --define "release ${RELEASE}"

Name:           icingaweb2-module-grafana
Version:        %{version}
Release:        %{release}
Summary:        Icingaweb2 module to embed Graphana graphs into Icingaweb2
License:        GPLv2
Group:          Applications/System
Vendor:         Carsten Köbke Consulting
Packager:       Joern Ott
Url:            https://github.com/Mikesch-mp/icingaweb2-module-grafana
Source0:        icingaweb2-module-grafana-%{version}.tar.gz
BuildArch:      noarch
Requires:       icingaweb2

%description
Add Grafana graphs into Icinga Web 2 to display performance metrics.

%install
rm -rf $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT/usr/share/icingaweb2/modules/grafana
tar -xzf %{SOURCE0} -C $RPM_BUILD_ROOT/usr/share/icingaweb2/modules/grafana --strip-components 1

%files
%defattr(644,root,root,755)
/usr/share/icingaweb2/modules/grafana

%changelog
* Fri Nov 06 2020 Joern Ott <joern.ott@schufa.de>
- Fix spec
* Wed May 06 2020 Joern Ott <joern.ott@schufa.de>
- Initial RPM spec
