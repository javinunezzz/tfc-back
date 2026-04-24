FROM php:8.2-cli

# Instalar dependencias necesarias
RUN apt-get update -y && \
    apt-get install -y libonig-dev git unzip zip libpng-dev libjpeg-dev libfreetype6-dev libzip-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install pdo pdo_mysql mbstring gd zip

# Configurar tamaño máximo de subida y post
RUN echo "upload_max_filesize=20M" > /usr/local/etc/php/conf.d/uploads.ini \
 && echo "post_max_size=20M" >> /usr/local/etc/php/conf.d/uploads.ini

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establecer el directorio de trabajo
WORKDIR /app

# Copiar el código de la aplicación al contenedor
COPY . /app

# Instalar las dependencias de Composer
RUN composer install

# Exponer el puerto de la aplicación
EXPOSE 8000

# Comando para ejecutar el servidor de desarrollo de Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
