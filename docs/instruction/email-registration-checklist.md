# Чек-лист: регистрация по e-mail с подтверждением кода

## 1. Dev / локальная проверка

- [ ] В корневом `.env` заполнены параметры mail event и SMTP-транспорта:
  - `REBIT_AUTH_MAIL_EVENT_SITE_ID=s1`
  - `REBIT_SMTP_HOST=mailpit`
  - `REBIT_SMTP_PORT=1025`
  - `REBIT_SMTP_ENCRYPTION=none`
  - `REBIT_SMTP_FROM_EMAIL=noreply@rebit-p2p.loc`
  - `REBIT_SMTP_FROM_NAME="Rebit P2P"`
- [ ] Подняты контейнеры:
  - `api-php-fpm`
  - `api-php-cli`
  - `frontend-node`
  - `mailpit`
- [ ] Открывается интерфейс Mailpit: `http://localhost:8025`
- [ ] Почтовый транспорт dev-окружения действительно направлен в Mailpit (`msmtp`)
- [ ] В Битриксе существует почтовое событие `REBIT_AUTH_REGISTRATION_CONFIRMATION`

Команды:

```bash
cd "/home/user/rebit-p2p"
docker compose up -d api-php-fpm api-php-cli frontend-node mailpit
```

## 2. Миграция

- [ ] Применена миграция `Version20260326120008`
- [ ] Создана таблица `rebit_auth_registration_confirmation`
- [ ] Применена миграция `Version20260326120009`
- [ ] Создан тип почтового события `REBIT_AUTH_REGISTRATION_CONFIRMATION`
- [ ] Создан HTML-почтовый шаблон для этого события

Команда:

```bash
cd "/home/user/rebit-p2p"
make migrate
```

Проверка статуса миграций:

```bash
cd "/home/user/rebit-p2p"
docker compose run --rm api-php-cli php /app/public/local/modules/sprint.migration/tools/migrate.php ls
```

## 3. Smoke в dev

- [ ] На фронте открывается страница регистрации `/register`
- [ ] Первый шаг принимает `email` и `password`
- [ ] После отправки приходит письмо в Mailpit
- [ ] В письме есть 6-значный код
- [ ] Второй шаг принимает код и подтверждает регистрацию
- [ ] После подтверждения пользователь автоматически логинится
- [ ] Повторная отправка кода блокируется на время cooldown
- [ ] Истёкший код не подтверждается

## 4. Проверка качества

- [ ] Пройдены тесты модуля `rebit.auth`
- [ ] Пройден `phpstan`
- [ ] Фронтенд собирается без ошибок

Команды:

```bash
cd "/home/user/rebit-p2p"
docker compose run --rm api-php-cli sh -lc 'cd /app && vendor/bin/phpunit public/local/modules/rebit.auth/tests --colors=always'

docker compose run --rm api-php-cli sh -lc 'cd /app && composer phpstan -- --error-format=table'

docker compose run --rm frontend-node-cli sh -lc 'cd /app && npm run build'
```

## 5. Production: что должно быть подготовлено

### Backend env

- [ ] `REBIT_AUTH_REGISTRATION_CODE_TTL_MINUTES`
- [ ] `REBIT_AUTH_REGISTRATION_RESEND_COOLDOWN_SECONDS`
- [ ] `REBIT_AUTH_REGISTRATION_MAX_ATTEMPTS`
- [ ] `REBIT_AUTH_MAIL_EVENT_SITE_ID`
- [ ] `REBIT_SMTP_HOST`
- [ ] `REBIT_SMTP_PORT`
- [ ] `REBIT_SMTP_ENCRYPTION`
- [ ] `REBIT_SMTP_USERNAME`
- [ ] `REBIT_SMTP_FROM_EMAIL`
- [ ] `REBIT_SMTP_FROM_NAME`
- [ ] `REBIT_SMTP_TLS_CERTCHECK`

### Docker secrets

- [ ] На сервере существует source-файл `/srv/rebit-p2p/swarm/secrets/rebit_smtp_password`
- [ ] Создан versioned Swarm secret `rebit_smtp_password_<BUILD_NUMBER>`
- [ ] `rebit_encryption_key`
- [ ] `rebit_geetest_captcha_key`
- [ ] `rebit_mysql_password`
- [ ] `rebit_mysql_root_password`

## 6. Что нужно настроить для отправки почты

- [ ] В Битриксе создано почтовое событие `REBIT_AUTH_REGISTRATION_CONFIRMATION`
- [ ] В Битриксе создан HTML-шаблон письма для нужного сайта (`LID`)
- [ ] В PHP-контейнерах настроен SMTP-транспорт (`msmtp`)
- [ ] Для production получены SMTP-данные от провайдера или настроен локальный MTA
- [ ] Настроены SPF / DKIM / DMARC для домена отправителя

Пример данных, которые нужны от внешнего почтового провайдера:

- SMTP host: `smtp.yandex.ru`
- SMTP port: `465`
- Encryption: `ssl`
- Username: `rebit-2017@yandex.ru`
- Password: пароль приложения

## 7. Smoke после выката на production

- [ ] Запрос кода регистрации отрабатывает без 500
- [ ] Письмо реально приходит во внешний ящик
- [ ] Код подтверждается
- [ ] После подтверждения создаётся активный пользователь
- [ ] Выполняется автологин
- [ ] Письма не падают в спам массово
- [ ] На SMTP нет ошибок авторизации / TLS / connect timeout

## 8. Что учитывать отдельно

- [ ] Сейчас в проекте отправка регистрации реализована через почтовое событие Bitrix
- [ ] Для dev Mailpit подключён как SMTP-цель контейнерного транспорта `msmtp`
- [ ] Шаблон для админки подготовлен отдельно в файле `docs/instruction/bitrix-email-registration-template.md`
