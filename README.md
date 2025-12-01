# Микросервис ролей и разрешений пользователей

## О проекте

Проект представляет собой систему rbac ролей с rest api. Добавлять\получать пользователей справами и разрешениями в\из сервисы\ов.

## Технологический стек

- **Backend**: PHP 8.3, Yii2
- **Database**: PostgreSQL 15
- **Caching**: Redis
- **Web Server**: Nginx
- **Containerization**: Docker + Docker Compose

## Развертывание и установка
Клонируйте репозиторий и перейдите в папку проекта

В папке с проектом выполнить команды по очереди:

```bash
# Запускает все сервисы: nginx, PHP-FPM, PostgreSQL, Redis, в фоновом режиме
docker-compose up -d

# Входим в контейнер с PHP-приложением для выполнения следующих команд
docker-compose exec rbac bash

# Устанавливаем PHP-зависимости через Composer
composer install

# Применяем миграции базы данных для создания таблиц
php php yii migrate/up

# Выходим из контейнера обратно в хост-систему
exit
```
В папке config создать: db_local.php и params_local.php по примеру с db.php и params.php

После приложение будет доступно по адресу: http://localhost:8081