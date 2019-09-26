#!/bin/bash

MODULES=(
"bcmath"
"bz2"
"calendar"
"ctype"
"curl"
"dba"
"dom"
"enchant"
"exif"
"fileinfo"
"filter"
"ftp"
"gd"
"gmp"
"gettext"
"hash"
"iconv"
"imap"
"intl"
"json"
"ldap"
"libxml"
"mbstring"
"mysqli"
"mysqlnd"
"opcache"
"openssl"
"pcntl"
"pdo"
"pdo_mysql"
"pdo_pgsql"
"pdo_sqlite"
"pgsql"
"phar"
"posix"
"pspell"
"readline"
"recode"
"session"
"shmop"
"simplexml"
"snmp"
"soap"
"sockets"
"sodium"
"sqlite3"
"sysvmsg"
"sysvsem"
"sysvshm"
"tidy"
"tokenizer"
"xml"
"xmlreader"
"xmlrpc"
"xmlwriter"
"xsl"
"wddx"
"zip"
"zlib"
)

function enable_libxml() {
  apk add --no-cache libxml2-dev
  local -n OPTS=$1; OPTS+=("--enable-libxml")
}

function depmod_dom() {
  local -n DEPS=$1; DEPS+=("libxml")
}

function enable_dom() {
  local -n OPTS=$1; OPTS+=("--enable-dom")
}

function depmod_simplexml() {
    local -n DEPS=$1; DEPS+=("libxml")
}

function enable_simplexml() {
  local -n OPTS=$1; OPTS+=("--enable-simplexml")
}

function depmod_xml() {
  local -n DEPS=$1; DEPS+=("libxml")
}

function enable_xml() {
  local -n OPTS=$1; OPTS+=("--enable-xml")
}

function depmod_xmlreader() {
  local -n DEPS=$1; DEPS+=("libxml")
}

function enable_xmlreader() {
  local -n OPTS=$1; OPTS+=("--enable-xmlreader")
}

function depmod_xmlwriter() {
  local -n DEPS=$1; DEPS+=("libxml")
}

function enable_xmlwriter() {
  local -n OPTS=$1; OPTS+=("--enable-xmlwriter")
}

function enable_openssl() {
  apk add --no-cache openssl-dev
  local -n OPTS=$1; OPTS+=("--with-openssl")
}

function enable_ctype() {
  local -n OPTS=$1; OPTS+=("--enable-ctype")
}

function enable_fileinfo() {
  local -n OPTS=$1; OPTS+=("--enable-fileinfo")
}

function enable_filter() {
  local -n OPTS=$1; OPTS+=("--enable-filter")
}

function enable_hash() {
  local -n OPTS=$1; OPTS+=("--enable-hash")
}

function enable_iconv() {
  local -n OPTS=$1; OPTS+=("--with-iconv")
}

function enable_json() {
  local -n OPTS=$1; OPTS+=("--enable-json")
}

function enable_tokenizer() {
  local -n OPTS=$1; OPTS+=("--enable-tokenizer")
}

function enable_sqlite3() {
  local -n OPTS=$1; OPTS+=("--with-sqlite3")
}

function depmod_pdo_sqlite() {
    local -n DEPS=$1; DEPS+=("pdo" "sqlite3")
}

function enable_pdo_sqlite() {
  local -n OPTS=$1; OPTS+=("--with-pdo-sqlite")
}

function enable_pdo() {
  local -n OPTS=$1; OPTS+=("--enable-pdo")
}

function enable_session() {
  local -n OPTS=$1; OPTS+=("--enable-session")
}

function enable_posix() {
  local -n OPTS=$1; OPTS+=("--enable-posix")
}

function enable_phar() {
  local -n OPTS=$1; OPTS+=("--enable-phar")
}

function enable_opcache() {
  local -n OPTS=$1; OPTS+=("--enable-opcache")
}

function enable_bcmath() {
  local -n OPTS=$1; OPTS+=("--enable-bcmath")
}

function enable_gd() {
  apk add --no-cache {zlib,libwebp,libjpeg-turbo,libpng,libxpm,freetype}-dev
  local -n OPTS=$1;
  OPTS+=("--with-gd")
  OPTS+=("--with-webp-dir")
  OPTS+=("--with-jpeg-dir")
  OPTS+=("--with-png-dir")
  OPTS+=("--with-zlib-dir")
  OPTS+=("--with-xpm-dir")
  OPTS+=("--with-freetype-dir")
  OPTS+=("--enable-gd-jis-conv")
}

function depmod_mysqli() {
  local -n DEPS=$1; DEPS+=("mysqlnd")
}

function enable_mysqli() {
  local -n OPTS=$1; OPTS+=("--with-mysqli")
}

function enable_mysqlnd() {
  local -n OPTS=$1; OPTS+=("--enable-mysqlnd")
}

function enable_readline() {
  apk add --no-cache readline-dev
  local -n OPTS=$1; OPTS+=("--with-readline")
}

function enable_shmop() {
  local -n OPTS=$1; OPTS+=("--enable-shmop")
}

function enable_pgsql() {
  apk add --no-cache postgresql-dev
  local -n OPTS=$1; OPTS+=("--with-pgsql")
}

function depmod_soap() {
  local -n DEPS=$1; DEPS+=("libxml")
}

function enable_soap() {
  local -n OPTS=$1; OPTS+=("--enable-soap")
}

function enable_sysvshm() {
  local -n OPTS=$1; OPTS+=("--enable-sysvshm")
}

function enable_xsl() {
  apk add --no-cache libxslt-dev
  local -n OPTS=$1; OPTS+=("--with-xsl")
}

function depmod_xsl() {
  local -n DEPS=$1; DEPS+=("libxml" "dom")
}

function enable_bz2() {
  apk add --no-cache bzip2-dev
  local -n OPTS=$1; OPTS+=("--with-bz2")
}

function enable_curl() {
  apk add --no-cache curl-dev
  local -n OPTS=$1; OPTS+=("--with-curl")
}

function enable_enchant() {
  apk add --no-cache enchant-dev
  local -n OPTS=$1; OPTS+=("--with-enchant")
}

function enable_gettext() {
  local -n OPTS=$1; OPTS+=("--with-gettext")
}

function depmod_imap() {
  local -n DEPS=$1; DEPS+=("openssl")
}

function enable_imap() {
  apk add --no-cache krb5-dev imap-dev
  local -n OPTS=$1; OPTS+=("--with-imap" "--with-imap-ssl" "--with-kerberos")
}

function enable_ldap() {
  apk add --no-cache openldap-dev
  local -n OPTS=$1; OPTS+=("--with-ldap")
}

function enable_recode() {
  apk add --no-cache recode-dev
  local -n OPTS=$1; OPTS+=("--with-recode")
}

function enable_sockets() {
  local -n OPTS=$1; OPTS+=("--enable-sockets")
}

function enable_tidy() {
  apk add --no-cache tidyhtml-dev
  local -n OPTS=$1; OPTS+=("--with-tidy")
}

function enable_calendar() {
  local -n OPTS=$1; OPTS+=("--enable-calendar")
}

function enable_exif() {
  local -n OPTS=$1; OPTS+=("--enable-exif")
}

function enable_gmp() {
  apk add --no-cache gmp-dev
  local -n OPTS=$1; OPTS+=("--with-gmp")
}

function enable_pcntl() {
  local -n OPTS=$1; OPTS+=("--enable-pcntl")
}

function enable_pdo_pgsql() {
  apk add --no-cache postgresql-dev
  local -n OPTS=$1; OPTS+=("--with-pdo-pgsql")
}

function depmod_pdo_pgsql() {
  local -n DEPS=$1; DEPS+=("pdo")
}

function enable_sodium() {
  apk add --no-cache libsodium-dev
  local -n OPTS=$1; OPTS+=("--with-sodium")
}

function enable_sysvmsg() {
  local -n OPTS=$1; OPTS+=("--enable-sysvmsg")
}

function enable_xmlrpc() {
  local -n OPTS=$1; OPTS+=("--with-xmlrpc")
}

function depmod_xmlrpc() {
  local -n DEPS=$1; DEPS+=("libxml")
}

function enable_zip() {
  apk add --no-cache libzip-dev
  local -n OPTS=$1; OPTS+=("--enable-zip" "--with-libzip")
}

function depmod_zip() {
  local -n DEPS=$1; DEPS+=("zlib")
}

function enable_zlib() {
  apk add --no-cache zlib-dev
  local -n OPTS=$1; OPTS+=("--with-zlib")
}

function enable_ftp() {
  local -n OPTS=$1; OPTS+=("--enable-ftp")
}

function enable_intl() {
  apk add --no-cache icu-dev
  local -n OPTS=$1; OPTS+=("--enable-intl")
}

function enable_mbstring() {
  local -n OPTS=$1; OPTS+=("--enable-mbstring")
}

function enable_pdo_mysql() {
  local -n OPTS=$1; OPTS+=("--with-pdo-mysql")
}

function depmod_pdo_mysql() {
  local -n DEPS=$1; DEPS+=("pdo")
}

function enable_snmp() {
  apk add --no-cache net-snmp-dev
  local -n OPTS=$1; OPTS+=("--with-snmp")
}

function enable_sysvsem() {
  local -n OPTS=$1; OPTS+=("--enable-sysvsem")
}

function enable_wddx() {
  apk add --no-cache expat-dev
  local -n OPTS=$1; OPTS+=("--enable-wddx" "--with-libexpat-dir=/usr/lib")
}

function depmod_wddx() {
  local -n DEPS=$1; DEPS+=("libxml")
}

function enable_pspell() {
  apk add --no-cache aspell-dev
  local -n OPTS=$1; OPTS+=("--with-pspell")
}

function enable_dba() {
  local -n OPTS=$1; OPTS+=("--enable-dba")
}
