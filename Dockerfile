FROM php:8.2-apache

# Disable ALL MPM modules first, then enable only mpm_prefork
RUN a2dismod mpm_event mpm_worker mpm_itk 2>/dev/null || true \
    && a2enmod mpm_prefork \
    && a2enmod rewrite \
    && docker-php-ext-install pdo pdo_mysql

# Allow .htaccess overrides
RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

# Fallback: strip any stray LoadModule directives for non-prefork MPMs
RUN sed -i '/^LoadModule mpm_event/d; /^LoadModule mpm_worker/d; /^LoadModule mpm_itk/d' \
        /etc/apache2/apache2.conf \
        /etc/apache2/mods-enabled/*.conf 2>/dev/null || true

COPY . /var/www/html/

RUN mkdir -p /var/www/html/uploads/avatars \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 775 /var/www/html/uploads

EXPOSE 80
CMD ["apache2-foreground"]
