FROM php:fpm

# Install system dependencies and Composer
RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libzip-dev libpng-dev \
    && docker-php-ext-install gd pdo pdo_mysql \
    && curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && printf "upload_max_filesize=10M\npost_max_size=10M\n" > /usr/local/etc/php/conf.d/uploads.ini \
    && rm composer-setup.php \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Allow running Composer as root within the container
ENV COMPOSER_ALLOW_SUPERUSER=1

# On container start, install dependencies if vendor is missing, then start php-fpm
CMD ["sh", "-lc", "[ -f vendor/autoload.php ] || composer install --no-interaction --no-progress; exec php-fpm"]
