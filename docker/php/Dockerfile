# FieldOps (version nueva) — PHP 8.3 + Apache, docroot en public/
FROM php:8.3-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
        libicu-dev libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mysqli gd zip intl mbstring bcmath opcache \
    && a2enmod rewrite headers \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# docroot en /var/www/html/public (estandar Laravel)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf \
         /etc/apache2/conf-available/*.conf \
    && printf '<Directory /var/www/html/public>\n    Options -Indexes +FollowSymLinks\n    AllowOverride All\n    Require all granted\n</Directory>\n' \
         > /etc/apache2/conf-available/fieldops.conf \
    && a2enconf fieldops

RUN printf 'memory_limit=512M\nupload_max_filesize=64M\npost_max_size=64M\nmax_execution_time=120\ndate.timezone=America/Argentina/Buenos_Aires\nopcache.enable=1\n' \
    > /usr/local/etc/php/conf.d/fieldops.ini

WORKDIR /var/www/html
