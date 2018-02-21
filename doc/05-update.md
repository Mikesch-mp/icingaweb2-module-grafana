# Update the module

## Using Git

If you used [git clone](02-installation.md) to install the module, just do


```bash
git pull
```

inside the module directory (usaly /usr/share/icingaweb2/modules/grafana)

## Using latest tarball

The steps taken to update are the same as installation, but to make sure old files are removed
we will delete the old module directory first. Get the [latest version number](https://github.com/Mikesch-mp/icingaweb2-module-grafana/releases/latest) from git and put
into the `MODULE_VERSION` variable

```
MODULE_VERSION="1.2.0"
ICINGAWEB_MODULEPATH="/usr/share/icingaweb2/modules"
REPO_URL="https://github.com/Mikesch-mp/icingaweb2-module-grafana"
TARGET_DIR="${ICINGAWEB_MODULEPATH}/grafana"
URL="${REPO_URL}/archive/v${MODULE_VERSION}.tar.gz"
rm -rf ${TARGET_DIR}
install -d -m 0755 "${TARGET_DIR}"
wget -q -O - "$URL" | tar xfz - -C "${TARGET_DIR}" --strip-components 1
```

## Dashboards

Dont forget to check the dashboard directories for newer versions of existing ones 
or new dashboards. 
