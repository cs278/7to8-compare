FROM ubuntu:24.04

WORKDIR /tmp
RUN apt-get update && apt-get install -y wget build-essential
RUN wget -Ophp.tar.bz2 https://www.php.net/distributions/php-7.4.33.tar.bz2

WORKDIR /tmp/php

RUN tar --strip-components=1 -xf ../php.tar.bz2

RUN apt-get install -y bison libxml2-dev libsqlite3-dev libsodium-dev locales-all libzip-dev libxslt1-dev re2c

RUN apt-get install -y libcurl4-openssl-dev libargon2-dev
RUN apt-get install -y pkg-config libicu-dev
RUN apt-get install -y libssl-dev default-libmysqlclient-dev
RUN apt-get install -y libffi-dev libpng-dev libwebp-dev libjpeg-dev
RUN apt-get install -y libgmp3-dev libonig-dev
RUN apt-get install -y libedit-dev

RUN ./configure --disable-cgi --disable-fpm \
    --with-zip --with-xsl --enable-zend-test \
    --enable-sysvmsg --enable-sysvsem --enable-sysvshm \
    --with-password-argon2 --with-sodium \
    --with-libedit --enable-pcntl \
    --with-pdo-mysql --with-mysqli --enable-mbstring \
    --enable-intl --with-gettext \
    --enable-gd --with-ffi --enable-exif \
    --with-curl --enable-calendar --enable-bcmath --with-gmp \
    --with-zlib --enable-debug CFLAGS="-DDEBUG_ZEND=2 -ggdb3"

RUN make -j $(nproc)

# RUN cat -n Zend/zend_operators.c | grep -5 compare_function && false

# COPY insert.c /tmp/insert.c

COPY zend_operators.patch /tmp/zend_operators.patch

RUN patch Zend/zend_operators.c < /tmp/zend_operators.patch


# RUN mv Zend/zend_operators.c Zend/zend_operators.c.orig && (head -n2026 Zend/zend_operators.c.orig; cat /tmp/insert.c; tail -n+2027 Zend/zend_operators.c.orig) > Zend/zend_operators.c

# RUN cat -n Zend/zend_operators.c | grep 202 && false
RUN sed -i 's/CG(compiler_options) & ZEND_COMPILE_NO_BUILTINS/1/' Zend/zend_compile.c

RUN make -j $(nproc)

RUN make test || true

# RUN sapi/cli/php -r 'function cheeze() { main(); } function main () {var_dump(in_array(0.1, ["y"]), "y" == "x");} cheeze();'
RUN cat /tmp/comparisons.log | sort | uniq -c | sort && false
# RUN sapi/cli/php -r 'function main () {var_dump(in_array(0.1, ["y"]), "y" == "x");} main(); $x = range(1, 30); sort($x);' && false
# RUN ./configure --help && false