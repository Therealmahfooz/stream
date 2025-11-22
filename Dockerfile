# Use a standard PHP image that includes the Apache web server
FROM php:8.1-apache

# Install PostgreSQL client libraries (pdo_pgsql)
# Necessary for your config.php to connect to the Render Postgres DB
RUN apt-get update && \
    apt-get install -y libpq-dev && \
    docker-php-ext-install pdo pdo_pgsql

# Copy all project files from current directory into the web server root
# (Replace the existing index.html/etc.)
COPY . /var/www/html/

# Ensure Apache web server is running. (This base image runs Apache by default.)
# Apache will automatically look for index.php
