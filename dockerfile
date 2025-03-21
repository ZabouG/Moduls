FROM php:8.2-cli

# Installer les extensions PDO et MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Définir le dossier de travail
WORKDIR /var/www/html

# Définir l'utilisateur
RUN chown -R www-data:www-data /var/www/html

# Commande par défaut pour exécuter le serveur PHP
CMD ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/html"]
