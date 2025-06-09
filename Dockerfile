FROM php:8.2-fpm

# Argumentos
ARG user=laravel
ARG uid=1000

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev

# Limpar cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Obter o Composer mais recente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Criar usuário do sistema para executar comandos Composer e Artisan
RUN useradd -G www-data,root -u $uid -d /home/$user $user
RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

# Configurar diretório de trabalho
WORKDIR /var/www

# Copiar arquivos de configuração
COPY . .
COPY .env.example .env
RUN mkdir -p storage/framework/cache \
    mkdir -p storage/framework/sessions \
    mkdir -p storage/framework/views \
    mkdir -p bootstrap/cache

# Instalar dependências do Composer
RUN composer install --optimize-autoloader --no-dev

# Configurar permissões
RUN chown -R $user:www-data /var/www/storage
RUN chmod -R 775 /var/www/storage
RUN mkdir -p /var/www/bootstrap/cache && \
    chown -R www-data:www-data /var/www/bootstrap/cache && \
    chmod -R 775 /var/www/bootstrap/cache

# Expor porta 9000 e iniciar PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]