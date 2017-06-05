# Release Workflow

Print this document.

Specify the release version.

    VERSION=1.1.7

## Issues

Check issues at https://github.com/Mikesch-mp/icingaweb2-module-grafana/milestones

## Version

Update the version number in the following file:

* [module.info](module.info): Version: (.*)

Example:

    sed -i "s/Version: .*/Version: $VERSION/g" module.info

## Git Tag

Commit these changes to the "master" branch:

    $ git commit -v -a -m "Release version $VERSION"

Create a signed tag (tags/v<VERSION>) on the "master" branch.

    $ git tag m "Version $VERSION" v$VERSION

Push the tag.

    $ git push --tags

# External Dependencies

## Release Tests

* Provision the vagrant boxes and pull the master in `/usr/share/icingaweb2/modules/grafana`

Example:

    $ git clone https://github.com/Icinga/icinga-vagrant.git
    $ cd icinga-vagrant/icinga2x
    $ vagrant up
    $ vagrant ssh -c "cd /usr/share/icingaweb2/modules/grafana && sudo git pull"

## GitHub Release

Create a new release for the newly created Git tag.
https://github.com/Mikesch-mp/icingaweb2-module-grafana/releases

## Icinga Exchange Release

Update [icingaexchange.yml](icingaexchange.yml) with the new release version.

## Announcement

* Twitter (highlight @icinga)
* Forum: https://monitoring-portal.org/index.php?thread/39830-grafana-module/

# After the release

* Close the released version at https://github.com/Mikesch-mp/icingaweb2-module-grafana/milestones
