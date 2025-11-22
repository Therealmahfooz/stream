# Use a standard PHP image that includes the Apache web server
FROM php:8.1-apache

# Install PostgreSQL client libraries (pdo_pgsql)
# Needed for your config.php to connect to the Render Postgres DB
RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo pdo_pgsql

# Copy the custom Apache config (created above)
COPY 000-default.conf /etc/apache2/sites-available/

# Enable the custom config and disable the default one
RUN a2dissite 000-default.conf && \
    a2ensite 000-default.conf && \
    a2enmod rewrite

# Copy all project files from current directory into the web server root
COPY . /var/www/html/

# The base image automatically runs Apache in the foreground.
