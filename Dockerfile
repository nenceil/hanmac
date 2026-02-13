# Gunakan PHP dengan server Apache
FROM php:8.2-apache

# Copy semua file project kamu ke dalam folder website di server
COPY . /var/www/html/

# Beritahu server untuk membuka port 80
EXPOSE 80