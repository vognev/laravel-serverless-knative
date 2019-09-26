#!/bin/bash

set -e

. ./php-modules.sh

REQUEST=("$@")
OPTIONS=()

contains() {
  local NEEDLE="$1"
  local -n HAYSTACK="$2"
  for v in ${HAYSTACK[@]}; do
    if [[ "$v" == "$NEEDLE" ]]; then
      return 0
    fi
  done
  return 1;
}

# collect module deps
for req in ${REQUEST[@]}; do
  hash depmod_${req} > /dev/null 2>&1 && depmod_${req} REQUEST
done

for mod in ${MODULES[@]}; do
  if contains "$mod" REQUEST; then
    enable_${mod} OPTIONS
  fi
done

echo "MODULES: ${REQUEST[*]}"

./buildconf --force

  CFLAGS="-fstack-protector-strong -fpic -fpie -O3 -ffunction-sections -fdata-sections" \
CPPFLAGS="-fstack-protector-strong -fpic -fpie -O3 -ffunction-sections -fdata-sections" \
 LDFLAGS="-Wl,-O1 -Wl,--strip-all -Wl,--hash-style=both -pie" \
./configure \
    --build=x86_64-pc-linux-gnu \
    --prefix=${INSTALL_DIR} \
    --enable-option-checking=fatal \
    --disable-all \
    --disable-fpm \
    --disable-cgi \
    --disable-phpdbg \
    --disable-phpdbg-webhelper \
    --without-pear \
    --enable-cli \
    --with-pcre-jit \
    --with-config-file-path=${INSTALL_DIR}/etc/php \
    --with-config-file-scan-dir=${INSTALL_DIR}/etc/php/conf.d:/app/.php.conf.d \
    "${OPTIONS[@]}"
