FROM php:7.0-apache
RUN mkdir -p /var/log/test
RUN chmod 777 /var/log/test
COPY files /var/www/html
EXPOSE 80
