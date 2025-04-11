FROM php:8.2-apache

# Installe les extensions nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Active mod_rewrite (si besoin de .htaccess)
RUN a2enmod rewrite

# Copie tes fichiers backend dans le dossier Apache
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
