# MorePhoto - Выбор фото и отправка заявки

Веб-приложение для выбора фотографий и отправки заявок с современным интерфейсом на Vue 3 и надежным бэкендом на Bitrix CMS.

## 🚀 Технологический стек

### Frontend
- **Vue 3** - фреймворк для пользовательского интерфейса
- **Node.js 19** (Alpine) - среда выполнения и сборка

### Backend
- **Bitrix CMS** - система управления контентом
- **PHP 8.4** - серверный язык программирования
- **PHP-DI** - контейнер зависимостей

### Базы данных и кеширование
- **Percona MySQL 8.0** - основная база данных
- **Redis** - кеширование приложения
- **RabbitMQ** - асинхронная обработка задач

### Инфраструктура
- **Nginx 1.25** - веб-сервер и шлюз
- **Docker & Docker Compose** - контейнеризация
- **PHP-FPM 8.4** - обработка PHP

## 📁 Структура проекта
```
morephoto/
├── frontend/ # Vue 3 приложение
├── api/
│ ├── public/
│ │ ├── bitrix/ # Ядро Bitrix CMS
│ │ └── local/ # Кастомные модули
├── mysql/ # Конфигурация БД
└── docker-compose.yml # Docker окружение
```
## 🛠️ Быстрый старт

### Предварительные требования
- Docker
- Docker Compose
- Git

### Установка и запуск

1. **Клонирование репозитория**
   ```bash
   git clone https://github.com/rebit-pro/morephoto.git
   cd morephoto

2. **Инициализация проекта**

   ```bash
   make init

3. Запуск окружения

   ```bash
   docker compose up -d
   Доступ к приложению
   ```
Frontend: [http://localhost:3000](https://api.morephoto.loc)

Backend API: [http://localhost:80](https://morephoto.loc)

База данных: localhost:3306

🔧 Разработка
Основные команды

```bash
      # Установка зависимостей
      make api-composer-install
   
      # Запуск тестов
      make test
      
      # Статический анализ кода
      make analyze
      
      # Проверка стиля кода
      make lint
```
Контейнеры разработки
frontend - Vue 3 dev server (порт 3000)

api-php-fpm - PHP обработчик

mysql - База данных (порт 3306)

redis - Кеш (порт 6379)

rabbitmq - Очереди (порты 5672, 15672)

🧪 Тестирование и качество кода
Проект использует современные инструменты для обеспечения качества:

PHPStan (уровень 6) - статический анализ

PHPUnit - модульное тестирование

PHP_CodeSniffer - проверка стиля кода

PHPLint - проверка синтаксиса

Процесс разработки

🚀 Деплой
Система использует версионирование образов Docker для zero-downtime деплоя:

```bash
# Сборка образов
make build

# Публикация в registry
make push

# Деплой на сервер
make deploy
```

📄 Лицензия
Этот проект является частной разработкой компании Rebit.

📞 Контакты
GitHub: <a href="https://github.com/rebit-pro" target="_blank" rel="noreferrer"><span>rebit-pro</span></a>

[![Ask DeepWiki](https://deepwiki.com/badge.svg)](https://deepwiki.com/rebit-pro/morephoto)
