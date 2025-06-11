FROM php:8.4-cli

ARG WWWGROUP

ENV DEBIAN_FRONTEND=noninteractive \
    TZ=UTC \
    SUPERVISOR_PHP_COMMAND="/usr/local/bin/php -d variables_order=EGPCS /var/www/html/artisan serve --host=0.0.0.0 --port=80" \
    SUPERVISOR_PHP_USER="sail"

# Установка системных зависимостей
RUN apt-get update && apt-get install -y \
    gosu git unzip curl zip \
    libpq-dev libzip-dev libonig-dev \
    cron supervisor postgresql-client \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip \
    && ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Создание рабочего пользователя
RUN groupadd --force -g $WWWGROUP sail && useradd -ms /bin/bash --no-user-group -g $WWWGROUP -u 1337 sail

# Установка рабочего каталога
WORKDIR /var/www/html

# Копируем конфиги
COPY start-container /usr/local/bin/start-container
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY cron-artisan-schedule /etc/cron.d/artisan-schedule
COPY php.ini /etc/php/8.4/cli/conf.d/99-sail.ini
RUN chmod +x /usr/local/bin/start-container

EXPOSE 80

ENTRYPOINT ["start-container"]
