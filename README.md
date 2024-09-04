[![code style](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/code-style.yml/badge.svg)](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/code-style.yml)
[![type coverage](https://shepherd.dev/github/kaivladimirv/insurance-contracts-laravel/coverage.svg)](https://shepherd.dev/github/kaivladimirv/insurance-contracts-laravel)
[![psalm level](https://shepherd.dev/github/kaivladimirv/insurance-contracts-laravel/level.svg)](https://psalm.dev/)
[![tests](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/tests.yml/badge.svg)](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/tests.yml)
![Codecov](https://img.shields.io/codecov/c/github/kaivladimirv/insurance-contracts-laravel?token=PBI5E8fvQm)
[![unused dependencies](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/unused-dependencies.yml/badge.svg)](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/unused-dependencies.yml)
[![outdated dependencies](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/oudated-dependencies.yml/badge.svg)](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/oudated-dependencies.yml)
[![sast](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/sast.yml/badge.svg)](https://github.com/kaivladimirv/insurance-contracts-laravel/actions/workflows/sast.yml)
![license](https://img.shields.io/badge/license-MIT-green)
<a href="https://php.net"><img src="https://img.shields.io/badge/php-8.3%2B-%238892BF" alt="PHP Programming Language"></a>

## Сервис для работы с договорами страхования
Сервис позволяет страховым компания
работать с договорами, застрахованными лицами,
с возможностью регистрировать услуги,
которые были оказаны застрахованным лицам,
в медицинских организациях.
***

## Возможности
- Единая база договоров, застрахованных лиц и услуг;
- Определение лимитов на услуги: по сумме или количеству;
- Возможность регистрировать и отменять регистрацию оказанных услуг; 
- Исключены случаи превышения лимитов по договорам
  при оказании услуг застрахованным лицам;
- Возможность разрешать, индивидуально для застрахованных лиц, превышать лимит на услуги; 
- Получение остатков по застрахованному лицу;
- Быстрое получение списка должников;
- Уведомление застрахованных лиц (по электронной почте или телеграм) об изменении остатков по лимитам;
***

## Требования
* PHP 8.3+
* Composer 2.6.5+
* PostgreSQL 15+
* RabbitMQ 3.13+
***

## Установка

1. Клонировать репозиторий
   ```
   git clone https://github.com/kaivladimirv/insurance-contracts-laravel.git
   ```
2. Перейти в директорию проекта
   ```
   cd insurance-contracts-laravel
   ```
3. Установить зависимости
   ```
   composer install
   ```
4. Сконфигурировать .env файл
   ```
   cp .env.example .env
   ```
   
   Указать параметры подключения к базе данных:
   ```
   DB_HOST
   DB_PORT
   DB_USERNAME
   DB_PASSWORD
   ```
   
   Определить параметры подключения к RabbitMQ:
   ```
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
   ```
   MAIL_MAILER
   MAIL_HOST
   MAIL_PORT
   MAIL_USERNAME
   MAIL_PASSWORD
   ```
5. Сгенерировать ключ приложения
   ```
   php artisan key:generate
   ```
6. Создать базу данных  
   Базу данных можно создать вручную (см. DB_DATABASE в файле .env).
   Или использовать следующую команду:
   ```
   php artisan db:create
   ```   
7. Выполнить миграции
   ```
   php artisan migrate
   ```
8. Запуск веб-сервера
   ```
   php artisan serve --host=0.0.0.0 --port=80
   ```
9. Запуск обработки очередей
   ```
   composer run-script run-queue-workers
   ```
***

## Docker

1. Клонировать репозиторий
   ```
   git clone https://github.com/kaivladimirv/insurance-contracts-laravel.git
   ```
2. Перейти в директорию проекта
   ```
   cd insurance-contracts-laravel
   ```
3. Запуск проекта  
   При первоначальном запуске проекта выполнить команду
   ```
   make init
   ```
   Данная команда установит зависимости, создаст базу данных и запустит проект.  
   В дальнейшем для запуска проекта достаточно выполнять команду:
   ```
   make up
   ```

   Для остановки проекта нужно выполнить
   ```
   make down
   ```

   Для перезапуска проекта выполнить
   ```
   make restart
   ```
***

## Уведомления
   Для работы уведомлений в телеграм, необходимо определить переменные в файле .env:
   ```
   TELEGRAM_BOT_TOKEN
   TELEGRAM_BOT_URL
   ```

   Если в карте персоны указано, что уведомления должны происходить через Telegram, 
   то после добавления персоны система отправляет приглашение на подключение к чат-боту по электронной почте.

   После того как пользователь подключится к чат-боту, 
   система должна сопоставить его с соответствующей персоной в базе данных. 
   Чтобы это происходило автоматически, 
   нужно добавить в cron выполнение команды 
   ```
   php artisan schedule:run
   ```
   Или настроить её выполнение через Supervisor.

   Альтернативно, можно вручную запускать команду 
   ```
   php artisan app:process-incoming-telegram-updates
   ```
   Но в этом случае её придётся регулярно выполнять, 
   чтобы произвести сопоставление новых пользователей чат-бота с персонами в базе данных.
***

## Тестирование
   Запуск тестов:
   ```
   composer run-script test
   ```
   
   Запуск тестов через Docker:
   ```
   make test
   ```
***

## Дополнительные команды для управления сервисом
   - Пересчет остатков по лимитам услуги в договоре:
      ```   
      php artisan app:recalc-balances-for-service <contractId> <serviceId>
      ```
      Остатки будут пересчитаны по всем застрахованным лица указанного договора.      


   - Сопоставление новых пользователей чат-бота с персонами в базе данных:
      ```
      php artisan app:process-incoming-telegram-updates
      ```
***

## Мониторинг и отладка
Для мониторинга и отладки приложения вы можете использовать Laravel Telescope.
Получить доступ к интерфейсу Telescope можно перейдя по адресу [http://localhost/telescope](http://localhost/telescope).

## Документация
Вся документация по API будет доступна после запуска проекта по адресу [http://localhost/](http://localhost/).
***

## Лицензия
Проект "Insurance contracts" лицензирован для использования в соответствии с лицензией MIT (MIT). 
Дополнительную информацию см. в разделе [LICENSE](/LICENSE).
