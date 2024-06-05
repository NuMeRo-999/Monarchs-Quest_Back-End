FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    curl \
    git \
    && apt-get clean

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Instala Symfony CLI
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash \
    && apt-get install -y symfony-cli

WORKDIR /var/www/html

# Copia los archivos necesarios para instalar dependencias
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-scripts --no-autoloader

# Copia el resto de los archivos del proyecto
COPY . .

# Configura permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Ejecuta composer dump-autoload y una segunda instalación para asegurarse de que todas las dependencias estén presentes
RUN rm -rf vendor && composer dump-autoload --optimize \
    && composer install --no-dev --optimize-autoloader --no-scripts
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilita el módulo de reescritura de Apache
RUN a2enmod rewrite

# Copia y da permisos al script de entrada
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["symfony", "server:start"]
