# ------------------------
FROM alpine:3.10.2 as base
# ------------------------

RUN apk add --no-cache bash unzip curl

SHELL ["/bin/bash", "-e", "-c"]

ENV BUILD_DIR=/tmp/buildroot
ENV INSTALL_DIR=/opt

# ------------------
FROM base as sources
# ------------------
RUN mkdir -p ${BUILD_DIR}/php

# download php
RUN curl -sL https://github.com/php/php-src/archive/php-7.2.21.tar.gz\
  | tar xz -C ${BUILD_DIR}/php --strip-components=1

WORKDIR ${BUILD_DIR}/php

# patch freetype
RUN apk add --no-cache patch
ADD storage/serverless/context/freetype.patch .
RUN patch -p1 < freetype.patch

# ---------------------
FROM sources as builder
# ---------------------
ARG SERVERLESS_PHP_MODULES
ARG SERVERLESS_CTX_PATH="storage/serverless/context"

RUN apk add --no-cache pkgconfig make autoconf bison gcc g++

WORKDIR ${BUILD_DIR}/php
ADD $SERVERLESS_CTX_PATH/php-configure.sh ./
ADD $SERVERLESS_CTX_PATH/php-modules.sh ./
RUN ./php-configure.sh "${SERVERLESS_PHP_MODULES[@]}"
RUN make -j `nproc` && make install && make clean

# pear modules (xdebug, redis etc)

# ---------------------
FROM builder as bundler
# ---------------------
ARG SERVERLESS_CTX_PATH="storage/serverless/context"

RUN apk add --no-cache musl musl-dev gcc py-pip \
 && pip install exodus_bundler\
 && ln -s /usr/bin/gcc /usr/bin/musl-gcc

ADD $SERVERLESS_CTX_PATH/php-package.sh ./
RUN ./php-package.sh ${INSTALL_DIR}

ADD $SERVERLESS_CTX_PATH/bootstrap ${INSTALL_DIR}/
ADD $SERVERLESS_CTX_PATH/php.ini   ${INSTALL_DIR}/etc/php/php.ini

# ---------------------------
FROM gcr.io/distroless/static
# ---------------------------
COPY --from=bundler /opt /opt
RUN ["/opt/bin/php", "-v"]

ADD . /app/
WORKDIR /app

CMD ["/opt/bootstrap"]
