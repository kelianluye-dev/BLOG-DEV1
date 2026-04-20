FROM php:8.2-apache                                                                                                               
  RUN rm -f /etc/apache2/mods-enabled/mpm_event.conf \                                                                                    && rm -f /etc/apache2/mods-enabled/mpm_event.load \
      && rm -f /etc/apache2/mods-enabled/mpm_worker.conf \
      && rm -f /etc/apache2/mods-enabled/mpm_worker.load \
      && ln -sf /etc/apache2/mods-available/mpm_prefork.conf /etc/apache2/mods-enabled/mpm_prefork.conf \
      && ln -sf /etc/apache2/mods-available/mpm_prefork.load /etc/apache2/mods-enabled/mpm_prefork.load \
      && a2enmod rewrite \
      && docker-php-ext-install pdo pdo_mysql

  RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

  COPY . /var/www/html/

  RUN mkdir -p /var/www/html/uploads/avatars \
      && chown -R www-data:www-data /var/www/html/uploads \
      && chmod -R 775 /var/www/html/uploads

  EXPOSE 80
  CMD ["apache2-foreground"]
