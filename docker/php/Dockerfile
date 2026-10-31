# Local test image mirroring zabiny.club production:
# PHP 8.3 php-fpm (prod: Remi php83 on AlmaLinux 8) + Grav core 1.7.x.
# Keep PHP_VERSION and GRAV_VERSION in sync with production.
ARG PHP_VERSION=8.3
FROM php:${PHP_VERSION}-fpm

ARG GRAV_VERSION=1.7.53.6
ARG USER_ID=1000
ARG GROUP_ID=1000

RUN apt-get update && apt-get install -y --no-install-recommends \
        unzip git rsync \
        libfreetype6-dev libjpeg62-turbo-dev libpng-dev libwebp-dev \
        libicu-dev libzip-dev libyaml-dev libmagickwand-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" gd intl zip opcache exif \
    && pecl install apcu yaml imagick \
    && docker-php-ext-enable apcu yaml imagick \
    && rm -rf /var/lib/apt/lists/* /tmp/pear

# Grav sends mail via `sendmail -bs`; in this setup mailpit plays sendmail and catches everything.
COPY --from=axllent/mailpit:latest /mailpit /usr/sbin/sendmail

# prod uses the distro php.ini (production defaults: display_errors Off, …)
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY zbm.ini /usr/local/etc/php/conf.d/zz-zbm.ini
COPY zz-pool.conf /usr/local/etc/php-fpm.d/zz-zbm.conf
COPY entrypoint.sh /usr/local/bin/zbm-entrypoint

# php-fpm workers run as www-data; match the host user so bind-mounted user/ stays writable on Linux.
RUN groupmod -o -g "${GROUP_ID}" www-data && usermod -o -u "${USER_ID}" www-data

# Grav core (without user/ — the repository is mounted there).
RUN curl -fsSL -o /tmp/grav.zip \
        "https://github.com/getgrav/grav/releases/download/${GRAV_VERSION}/grav-v${GRAV_VERSION}.zip" \
    && unzip -q /tmp/grav.zip -d /usr/src \
    && mv /usr/src/grav /usr/src/grav-core \
    && rm -rf /tmp/grav.zip /usr/src/grav-core/user \
    && echo "${GRAV_VERSION}" > /usr/src/grav-core/.grav-version

WORKDIR /var/www/html
ENTRYPOINT ["zbm-entrypoint"]
CMD ["php-fpm"]
