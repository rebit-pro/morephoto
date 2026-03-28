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
