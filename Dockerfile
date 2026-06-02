FROM php:8.2-apache

# Installe les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Active mod_rewrite pour Apache
RUN a2enmod rewrite

# Copie la config Apache
COPY budget-app /var/www/html

# Défini le répertoire de travail
WORKDIR /var/www/html

# Expose le port 80
EXPOSE 80
