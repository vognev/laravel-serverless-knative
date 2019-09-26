#!/bin/bash

set -e

INSTALL_DIR=$1

find ${INSTALL_DIR} -type f -name \*.a -delete

if [ -d "${INSTALL_DIR}/lib/php/extensions" ]; then
  exodus -t -o /tmp/bundle.tar.gz ${INSTALL_DIR}/bin/php \
         -a ${INSTALL_DIR}/lib/php/extensions
else
  exodus -t -o /tmp/bundle.tar.gz ${INSTALL_DIR}/bin/php
fi

rm -rf ${INSTALL_DIR}/*

tar zxf /tmp/bundle.tar.gz -C ${INSTALL_DIR} --strip-components=1

pushd "${INSTALL_DIR}"

mkdir -p etc/php/conf.d

[ -d bundles/*/${INSTALL_DIR}/lib ] && ln -s bundles/*/${INSTALL_DIR}/lib .

rm /tmp/bundle.tar.gz