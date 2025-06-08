# Se você estiver usando uma imagem PHP base, como php:8.x-apache ou php:8.x-fpm
FROM php:8.3-apache 

# Instale as dependências necessárias para as extensões PHP
# Use apt-get update e apt-get install para sistemas baseados em Debian/Ubuntu
RUN apt-get update && apt-get install -y \
    libpq-dev \ 
    libzip-dev \ 
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/* &&  docker-php-ext-install \
    pdo \
    pdo_mysql \ 
    mysqli \
    zip \
    opcache

EXPOSE 80

# Comando para iniciar o servidor (geralmente não necessário se usar php:apache ou php:fpm)
# CMD ["apache2-foreground"]