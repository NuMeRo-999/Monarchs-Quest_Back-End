# Utiliza la imagen de PHP 8.2 con Apache como servidor web
FROM php:8.2-apache

# Instala las extensiones de PHP necesarias
RUN docker-php-ext-install pdo_mysql

# Copia los archivos de la aplicación al contenedor
COPY . /var/www/html

# Cambia el propietario de los archivos de la aplicación al usuario de Apache
RUN chown -R www-data:www-data /var/www/html

# Habilita el módulo de reescritura de Apache para Symfony
RUN a2enmod rewrite
