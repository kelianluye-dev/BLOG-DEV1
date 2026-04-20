 FROM php:8.2-apache                                                                                                               
  RUN a2dismod mpm_event || true \                                                                                                        && a2enmod mpm_prefork \
      && a2enmod rewrite \
      && docker-php-ext-install pdo pdo_mysql

  RUN sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

  COPY . /var/www/html/

  RUN mkdir -p /var/www/html/uploads/avatars \
      && chown -R www-data:www-data /var/www/html/uploads \
      && chmod -R 775 /var/www/html/uploads

  EXPOSE 80
  CMD ["apache2-foreground"]
