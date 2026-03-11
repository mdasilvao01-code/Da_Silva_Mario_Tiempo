FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

# Copiar todos los PHP desde la carpeta php/
COPY php/*.php /var/www/html/

RUN echo '<Directory /var/www/html>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
    DirectoryIndex index.php index.html\n\
</Directory>' > /etc/apache2/conf-available/custom.conf \
    && a2enconf custom

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80
