FROM php:8.2-apache

# Ensure only mpm_prefork is active (base image may load mpm_event, causing
# "More than one MPM loaded" errors), then enable mod_rewrite for URL routing
RUN a2dismod mpm_event || true \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

# Install PDO MySQL extension required by the application
RUN docker-php-ext-install pdo pdo_mysql

# Allow .htaccess overrides in the document root
RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

# Copy all application files to the Apache document root
COPY . /var/www/html/

# Ensure the uploads directory exists and is writable by the web server
RUN mkdir -p /var/www/html/uploads/avatars \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 775 /var/www/html/uploads

# Expose the default HTTP port
EXPOSE 80

# Apache is started automatically by the base image via apache2-foreground
CMD ["apache2-foreground"]
