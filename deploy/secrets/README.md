# Production secrets templates

В этой папке лежат только шаблоны.

## SMTP пароль для регистрации по e-mail

1. Скопируйте шаблон:

```bash
cp deploy/secrets/rebit_smtp_password.example /srv/rebit-p2p/swarm/secrets/rebit_smtp_password
```

2. Откройте файл на production-сервере и замените содержимое на **пароль приложения Яндекса** для ящика `rebit-2017@yandex.ru`.
3. Файл должен содержать только пароль, без комментариев и без лишних данных.
4. После этого создайте versioned Docker Swarm secret командой `deploy/swarm-publish-runtime.sh` с тем же `VERSION`, что и `BUILD_NUMBER` деплоя.

Пример:

```bash
ssh rebit-pro 'VERSION=145 OUTPUT_ENV_FILE=/srv/rebit-p2p/swarm/runtime-objects.env /srv/rebit-p2p/swarm/swarm-publish-runtime.sh 145'
```

Важно:
- `rebit_smtp_password` — имя source-файла;
- `rebit_smtp_password_145` — имя versioned Swarm secret для `BUILD_NUMBER=145`.

## RabbitMQ пароль для очередей сообщений

1. Скопируйте шаблон:

```bash
cp deploy/secrets/rebit_rabbitmq_password.example /srv/rebit-p2p/swarm/secrets/rebit_rabbitmq_password
```

2. Откройте файл на production-сервере и замените содержимое на **надёжный пароль** для RabbitMQ.
3. Файл должен содержать только пароль, без комментариев и без лишних данных.
4. В `MESSENGER_TRANSPORT_DSN` в файле `backend.env` используйте плейсхолдер `__RABBITMQ_PASSWORD__` — entrypoint подставит реальный пароль из secret при старте контейнера:

```
MESSENGER_TRANSPORT_DSN=amqp://rebit:__RABBITMQ_PASSWORD__@rabbitmq:5672/rebit
```

5. Создайте versioned Swarm secret через `deploy/swarm-publish-runtime.sh`.

Важно:
- `rebit_rabbitmq_password` — имя source-файла;
- `rebit_rabbitmq_password_145` — имя versioned Swarm secret для `BUILD_NUMBER=145`.

## Telegram bot token для приёма заявок (POST /api/v1/lead)

1. Скопируйте шаблон:

```bash
cp deploy/secrets/rebit_telegram_bot_token.example /srv/rebit-p2p/swarm/secrets/rebit_telegram_bot_token
```

2. Откройте файл на production-сервере и замените содержимое на **токен бота от @BotFather**.
3. Файл должен содержать только токен, без комментариев и без лишних данных.
4. Остальные (несекретные) переменные заявок добавьте в `/srv/rebit-p2p/swarm/backend.env`:

```
REBIT_NOTIFICATION_TELEGRAM_CHAT_ID=123456789
REBIT_NOTIFICATION_LEAD_ALLOWED_ORIGIN=https://rebit-pro.ru
REBIT_NOTIFICATION_TELEGRAM_API_URL=https://api.telegram.org
```

5. Создайте versioned Swarm secret через `deploy/swarm-publish-runtime.sh` (скрипт сам находит
   новый файл в `secrets/`). `runtime-env.php` пробрасывает секрет в переменную
   `REBIT_NOTIFICATION_TELEGRAM_BOT_TOKEN`.

Важно:
- `rebit_telegram_bot_token` — имя source-файла;
- `rebit_telegram_bot_token_145` — имя versioned Swarm secret для `BUILD_NUMBER=145`.

## Охота за лидами (rebit.leadhunter, команда app:leadhunter:scan)

Секретов не требует: токен и прокси Telegram общие с приёмом заявок (см. выше).
Правила мониторинга добавьте в `/srv/rebit-p2p/swarm/backend.env` одной строкой:

```
REBIT_LEADHUNTER_RULES='[{"source":"flRu","keywords":["битрикс","bitrix"]},{"source":"flRu","params":{"category":2,"subcategory":27},"keywords":[]}]'
```

Формат правил и остальные переменные (`REBIT_LEADHUNTER_TELEGRAM_CHAT_ID` — если нужен
отдельный чат вместо общего с заявками; `REBIT_LEADHUNTER_FALLBACK_EMAIL` — резервная
доставка письмом при недоступном Telegram) — см. `.env.example` в корне репозитория.
Для fl.ru «Сайты под ключ» = `category=2, subcategory=27`.
