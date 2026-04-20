FROM php:8.2-apache

# Aggressively remove ALL non-prefork MPM modules:
# 1. Use a2dismod to cleanly unregister known MPM variants
# 2. Physically delete every mpm_*.load and mpm_*.conf symlink in
#    mods-enabled/ EXCEPT mpm_prefork, so Apache cannot load them
# 3. Ensure mpm_prefork is enabled and rewrite is available
# 4. Validate that exactly one MPM remains before proceeding
RUN a2dismod mpm_event mpm_worker mpm_itk 2>/dev/null || true \
    && find /etc/apache2/mods-enabled/ \
         -name 'mpm_*.load' -o -name 'mpm_*.conf' \
       | grep -v 'mpm_prefork' \
       | xargs rm -f \
    && a2enmod mpm_prefork \
    && a2enmod rewrite \
    && docker-php-ext-install pdo pdo_mysql \
    && echo "--- MPM modules remaining in mods-enabled ---" \
    && ls /etc/apache2/mods-enabled/mpm_* \
    && echo "--- Validating single MPM ---" \
    && test "$(ls /etc/apache2/mods-enabled/mpm_*.load 2>/dev/null | wc -l)" -eq 1 \
    && echo "OK: exactly one MPM .load file present"

# Allow .htaccess overrides
RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

COPY . /var/www/html/

RUN mkdir -p /var/www/html/uploads/avatars \
    && chown -R www-data:www-data /var/www/html/uploads \
    && chmod -R 775 /var/www/html/uploads

EXPOSE 80
CMD ["apache2-foreground"]
