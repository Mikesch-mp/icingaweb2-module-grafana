# Release Workflow

Print this document.

Specify the release version.

```
VERSION=1.2.0
```

## Issues

Check issues at https://github.com/Mikesch-mp/icingaweb2-module-grafana/milestones

## Version

Update the version number in the following file:

* [module.info](module.info): Version: (.*)

Example:

```
sed -i "s/Version: .*/Version: $VERSION/g" module.info
```

## Changelog

Ensure to have [github_changelog_generator](https://github.com/skywinder/github-changelog-generator)
installed and set the GitHub token to avoid rate limiting.

```
github_changelog_generator -u mikesch-mp -p icingaweb2-module-grafana --future-release=$VERSION
```

## Git Tag

Commit these changes to the "master" branch:

```
$ git commit -v -a -m "Release version $VERSION"
```

Create a signed tag (tags/v<VERSION>) on the "master" branch.

```
$ git tag -m "Version $VERSION" v$VERSION
```
Push the tag.

```
$ git push --tags
```

# External Dependencies

## Release Tests

* Provision the vagrant boxes and pull the master in `/usr/share/icingaweb2/modules/grafana`

Example:

```
$ git clone https://github.com/Icinga/icinga-vagrant.git
$ cd icinga-vagrant/influxdb
$ vagrant up
$ vagrant ssh -c "cd /usr/share/icingaweb2/modules/grafana && sudo git pull"
```

## GitHub Release

Create a new release for the newly created Git tag.
https://github.com/Mikesch-mp/icingaweb2-module-grafana/releases

Note: A new GitHub release will be synced by Icinga Exchange automatically.

## Announcement

* Twitter (highlight @icinga, use hashtags #icinga #grafana #monitoringlove)
* Forum: https://monitoring-portal.org/t/grafana-module-for-icinga-web-2/703

# After the release

* Close the released version at https://github.com/Mikesch-mp/icingaweb2-module-grafana/milestones
