#!/bin/bash
SCRIPT_DIR="$( cd "$(dirname "$0")" ; pwd -P )"
RPMBUILD_DIR=${SCRIPT_DIR}
SPECS_DIR=${RPMBUILD_DIR}/SPECS
RPMS_DIR=${RPMBUILD_DIR}/RPMS
SRPMS_DIR=${RPMBUILD_DIR}/SRPMS
BUILD_DIR=${RPMBUILD_DIR}/BUILD
SOURCES_DIR=${RPMBUILD_DIR}/SOURCES
LOGLEVEL=5
APP="icingaweb2-module-grafana"
VERSION=$1
RELEASE=$2
URL="https://github.com/Mikesch-mp/icingaweb2-module-grafana/archive/v${VERSION}.tar.gz"

function init() {
    for DIR in ${SPECS_DIR} ${RPMS_DIR} ${SRPMS_DIR} ${BUILD_DIR} ${SOURCES_DIR}; do
        if [ ! -d ${DIR} ]; then
            echo "$DIR not found. Creating it."
            mkdir -p ${DIR}
        fi
    done

    if [ -z "${VERSION}" ]; then
        cat <<EOF
Usage: $0 VERSION [RELEASE]

where VERSION is the version of RPM, RELEASE is the release-number for that version.
Omitting RELEASE will assume 1 as release number.
EOF
        exit 2
    fi
    if [ -z "${RELEASE}" ]; then
        RELEASE=1
    fi

    LOGLEVEL_TEXT[0]="Panic"
    LOGLEVEL_TEXT[1]="Fatal"
    LOGLEVEL_TEXT[2]="Error"
    LOGLEVEL_TEXT[3]="Warning"
    LOGLEVEL_TEXT[4]="Info"
    LOGLEVEL_TEXT[5]="Debug"
}

function log() {
    local LEVEL=$1
    if [ ${LEVEL} -le ${LOGLEVEL} ]; then
        shift 1
        local DATE=$(date +"%Y-%m-%d %H:%M:%S")
        printf "%s %s %s\n" "${DATE}" "${LOGLEVEL_TEXT[${LEVEL}]}" "$@"
    fi
}

function get_archive() {
    cd ${SOURCES_DIR}
    log 4 "Fetching ${APP}-${VERSION}.tar.gz"
    curl -sSjL "${URL}" -o ${APP}-${VERSION}.tar.gz
}

function build() {
    cd ${RPMBUILD_DIR}
    log 4 "Building ${APP} ${VERSION}-${RELEASE}"
    rpmbuild --define="_topdir ${RPMBUILD_DIR}" \
             --define "version ${VERSION}" \
             --define "release ${RELEASE}" \
             -ba ${SPECS_DIR}/${APP}.spec
    log 4 "${APP} RPM built successfully"
}

init
get_archive
build
