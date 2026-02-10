FROM php:8.2-apache

# Enable Apache rewrite (optional but useful)
RUN a2enmod rewrite

# Set public folder as document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf

# Copy project files
COPY . /var/www/html

EXPOSE 10000