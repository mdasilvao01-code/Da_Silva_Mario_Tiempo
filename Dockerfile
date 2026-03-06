FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

# Copiar los PHP de la subcarpeta PHP/ a la raíz del servidor
COPY PHP/*.php /var/www/html/

# Copiar también el index.php de la raíz (por si acaso)
COPY index.php /var/www/html/

# Configurar Apache
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
