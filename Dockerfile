# Imagem base
FROM php:8.0-fpm-alpine as app

# Instala na imagem Docker as dependências SQLITE e SQLITE-DEV
RUN apk add --no-cache sqlite sqlite-dev

# Usa um helper para instalar as dependências do PDO e PDO_SQLITE
RUN docker-php-ext-install pdo pdo_sqlite

# Instala o executável do composer de outra imagem (composer:2.6)
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copia para a imagem o composer.json e composer.lock
COPY composer.* ./

# Usa composer para instalar as dependências do composer.json
RUN composer install --prefer-dist --no-dev --no-scripts --no-interaction

# Copia os arquivos do projeto
COPY ./db ./db
COPY ./src ./src
COPY ./public ./public

# Expoe a porta 8000 do container
EXPOSE 8000

# Roda o servidor embutido do PHP
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]