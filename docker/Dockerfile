FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libonig-dev \
    libzip-dev \
    unzip \
    git \
    curl \
    nano \
    && docker-php-ext-install zip mbstring

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN a2enmod rewrite

RUN sed -i 's/APACHE_RUN_USER=.*/APACHE_RUN_USER=www-data/' /etc/apache2/envvars && \
    sed -i 's/APACHE_RUN_GROUP=.*/APACHE_RUN_GROUP=www-data/' /etc/apache2/envvars

COPY docker/apache-config.conf /etc/apache2/sites-available/000-default.conf

RUN composer install --no-dev --optimize-autoloader

RUN php bin/console cache:clear \
    && cp .env.dev .env \
    && php bin/console cache:warmup

EXPOSE 80

CMD ["apache2-foreground"]