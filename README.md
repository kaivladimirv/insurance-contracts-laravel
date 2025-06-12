[![code style](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/code-style.yml/badge.svg)](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/code-style.yml)
[![type coverage](https://shepherd.dev/github/kaivladimirv/insurance-contracts-laravel/coverage.svg)](https://shepherd.dev/github/kaivladimirv/insurance-contracts-laravel)
[![psalm level](https://shepherd.dev/github/kaivladimirv/insurance-contracts-laravel/level.svg)](https://psalm.dev/)
[![tests](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/tests.yml/badge.svg)](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/tests.yml)
![Codecov](https://img.shields.io/codecov/c/github/kaivladimirv/insurance-contracts-laravel?token=PBI5E8fvQm)
[![outdated dependencies](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/oudated-dependencies.yml/badge.svg)](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/oudated-dependencies.yml)
[![sast](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/sast.yml/badge.svg)](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/sast.yml)
![license](https://img.shields.io/badge/license-MIT-green)
<a href="https://php.net"><img src="https://img.shields.io/badge/php-8.4%2B-%238892BF" alt="PHP Programming Language"></a>

## Сервис для работы с договорами страхования
Сервис позволяет страховым компаниям работать с договорами, 
застрахованными лицами и оказанными медицинскими услугами. 
Предусмотрен контроль лимитов, уведомления клиентов, учёт остатков и интеграция с Telegram.
***

## Возможности
- Ведение единой базы договоров, застрахованных лиц и оказанных услуг
- Определение лимитов на услуги (по сумме или количеству)
- Регистрация и отмена регистрации оказанных услуг
- Исключение превышения лимитов при оказании услуг
- Индивидуальное разрешение превышения лимита для конкретных лиц
- Получение остатков по застрахованному лицу
- Получение списка должников
- Уведомление застрахованных лиц по email или в Telegram при изменении лимитов
***

## Требования
* PHP 8.4+
* Composer 2.6.5+
* PostgreSQL 15+
* RabbitMQ 3.13+
***

## Установка и запуск

### Локально (без Docker)

1. Клонировать репозиторий
   ```bash
   git clone https://github.com/kaivladimirv/insurance-contracts-laravel.git
   ```
2. Перейти в директорию проекта
   ```bash
   cd insurance-contracts-laravel
   ```
3. Установить зависимости
   ```bash
   composer install
   ```
4. Сконфигурировать .env файл
   ```bash
   cp .env.example .env
   ```
   
   Указать параметры подключения к базе данных:
   ```env
   DB_HOST
   DB_PORT
   DB_USERNAME
   DB_PASSWORD
   ```
   
   Определить параметры подключения к RabbitMQ:
   ```env
   QUEUE_CONNECTION=rabbitmq
   RABBITMQ_DSN=amqp://
   RABBITMQ_HOST=localhost
   RABBITMQ_PORT=5672
   RABBITMQ_VHOST=/
   RABBITMQ_USER=guest
   RABBITMQ_PASSWORD=guest
   RABBITMQ_QUEUE=default
   ```
   
   Для того чтобы работал процесс регистрации компании и уведомлений необходимо настроить почтовую службу. 
   При локальной установке все письма отправляются в лог-файл storage/logs/mailer.log.
   Если проект запускается с использованием Docker, то по умолчанию используется mailpit (<a href="http://localhost:8025/" target="_blank">http://localhost:8025/</a>).
   Для указания собственных настроек почтовой службы необходимо определить следующие переменные:
   ```env
   MAIL_MAILER
   MAIL_HOST
   MAIL_PORT
   MAIL_USERNAME
   MAIL_PASSWORD
   ```
5. Сгенерировать ключ приложения
   ```bash
   php artisan key:generate
   ```
6. Создать базу данных  
   Базу данных можно создать вручную (см. DB_DATABASE в файле .env).
   Или использовать следующую команду:
   ```bash
   php artisan db:create
   ```   
7. Выполнить миграции
   ```bash
   php artisan migrate
   ```
8. Запуск веб-сервера
   ```bash
   php artisan serve --host=0.0.0.0 --port=80
   ```
9. Запуск обработки очередей
   ```bash
   composer run-script run-queue-workers
   ```
---

### Через Docker

1. Клонировать репозиторий
   ```bash
   git clone https://github.com/kaivladimirv/insurance-contracts-laravel.git
   ```
2. Перейти в директорию проекта
   ```bash
   cd insurance-contracts-laravel
   ```
3. Запуск проекта  
   При первоначальном запуске проекта выполнить команду
   ```bash
   make init
   ```
   Данная команда установит зависимости, создаст базу данных и запустит проект.  
   В дальнейшем для запуска проекта достаточно выполнять команду:
   ```bash
   make up
   ```

   Для остановки проекта нужно выполнить
   ```bash
   make down
   ```

   Для перезапуска проекта выполнить
   ```bash
   make restart
   ```

---

## Уведомления

### Email

По умолчанию письма отправляются в `storage/logs/mailer.log`. В Docker используется Mailpit: [http://localhost:8025](http://localhost:8025).

### Telegram

   В `.env` необходимо указать:
   
```env
   TELEGRAM_BOT_TOKEN=your_bot_token_here
   TELEGRAM_BOT_URL=https://t.me/your_bot_username
   ```
   - TELEGRAM_BOT_TOKEN — токен вашего Telegram-бота, полученный у @BotFather.
   - TELEGRAM_BOT_URL — ссылка на вашего Telegram-бота.

   #### Принцип работы:
   - Если в карте персоны указано, что уведомления должны происходить через Telegram, система отправляет приглашение на подключение к чат-боту по электронной почте после добавления персоны.

   - После того как пользователь подключится к чат-боту, система должна сопоставить его с соответствующей персоной в базе данных.

   #### Автоматическое сопоставление пользователей:
   - Для автоматического сопоставления новых пользователей чат-бота с персонами в базе данных, добавьте следующую команду в cron: 
      ```
      * * * * * root cd /путь/к/вашему/проекту && /usr/bin/php artisan schedule:run >> ./storage/logs/laravel-scheduler.log 2>> ./storage/logs/laravel-scheduler-errors.log
      ```
     Замените root на нужного пользователя (если требуется) и /путь/к/вашему/проекту на реальный путь к проекту.
   - Альтернативно, можно настроить выполнение этой команды через Supervisor.
   
   #### Ручное сопоставление пользователей:
   - Для ручного сопоставления новых пользователей чат-бота с персонами в базе данных можно запустить команду: 
      ```bash
      php artisan app:process-incoming-telegram-updates
      ```
      Обратите внимание, что при использовании этого метода команду придется регулярно выполнять для поддержания актуальности сопоставлений.
***

## Тестирование
   - Локально:
      ```bash
      composer run-script test
      ```
   
   - В Docker:
      ```bash
      make test
      ```
***

## Дополнительные команды
   - Пересчет остатков по лимитам услуги в договоре:
      ```bash   
      php artisan app:recalc-balances-for-service <contractId> <serviceId>
      ```
      Остатки будут пересчитаны по всем застрахованным лица указанного договора.      


   - Сопоставление новых пользователей чат-бота с персонами в базе данных:
      ```bash
      php artisan app:process-incoming-telegram-updates
      ```
***

## Мониторинг
Интерфейс Laravel Telescope доступен по адресу:  [http://localhost/telescope](http://localhost/telescope).

## Документация
Документация API будет доступна после запуска проекта по адресу [http://localhost/](http://localhost/).
***

## Лицензия
Проект лицензирован под MIT. Подробнее — см. [LICENSE](/LICENSE).
