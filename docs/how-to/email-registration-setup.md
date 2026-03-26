# Регистрация по e-mail: настройка почты

> Актуально для текущей реализации: письмо отправляется через почтовое событие Битрикса `REBIT_AUTH_REGISTRATION_CONFIRMATION`.
> Реальный текст письма берётся из почтового шаблона Битрикса, а фактическая доставка выполняется через SMTP-транспорт контейнера (`msmtp`).

## Локальная разработка

Для dev используется `Mailpit`.

Что уже предусмотрено в проекте:
- в `docker-compose.yml` добавлен сервис `mailpit`;
- web-интерфейс писем доступен на `http://localhost:8025`.

Минимальные dev-параметры приложения и транспорта:

```dotenv
REBIT_AUTH_MAIL_EVENT_SITE_ID=s1
REBIT_SMTP_HOST=mailpit
REBIT_SMTP_PORT=1025
REBIT_SMTP_ENCRYPTION=none
REBIT_SMTP_USERNAME=
REBIT_SMTP_PASSWORD=
REBIT_SMTP_FROM_EMAIL=noreply@rebit-p2p.loc
REBIT_SMTP_FROM_NAME="Rebit P2P"
REBIT_SMTP_TLS_CERTCHECK=off
```

`Mailpit` подключается к PHP-контейнеру как SMTP-цель для `msmtp`, а Bitrix mail event использует стандартную отправку через `sendmail_path`.

## Production: какие данные нужны от стороннего почтового сервера

Для production письмо формирует Bitrix mail event, а реальная доставка зависит от настройки почтового транспорта. Поэтому у провайдера нужно получить:

1. `SMTP host`
   - пример для Яндекса: `smtp.yandex.ru`
2. `SMTP port`
   - обычно `465` для SSL или `587` для TLS
3. `Тип шифрования`
   - `ssl`, `tls` или `none`
4. `SMTP username`
   - чаще всего полный e-mail ящика, например `noreply@domain.ru`
5. `SMTP password`
   - лучше отдельный пароль приложения / app password
6. `From email`
   - адрес отправителя, который разрешён у провайдера
7. `From name`
   - отображаемое имя отправителя, например `Rebit P2P`
8. Подтверждение, что домен отправителя настроен корректно:
   - SPF
   - DKIM
   - желательно DMARC

## Пример для Яндекс 360

Обычно нужны такие значения:

```dotenv
REBIT_SMTP_HOST=smtp.yandex.ru
REBIT_SMTP_PORT=465
REBIT_SMTP_ENCRYPTION=ssl
REBIT_SMTP_USERNAME=noreply@your-domain.ru
REBIT_SMTP_FROM_EMAIL=noreply@your-domain.ru
REBIT_SMTP_FROM_NAME="Rebit P2P"
```

Пароль лучше хранить не в `.env`, а в Docker secret `rebit_smtp_password`.

## Что положить в production backend env

В backend env/config должны быть:

```dotenv
REBIT_AUTH_REGISTRATION_CODE_TTL_MINUTES=15
REBIT_AUTH_REGISTRATION_RESEND_COOLDOWN_SECONDS=60
REBIT_AUTH_REGISTRATION_MAX_ATTEMPTS=5
REBIT_AUTH_MAIL_EVENT_SITE_ID=s1
REBIT_SMTP_HOST=smtp.yandex.ru
REBIT_SMTP_PORT=465
REBIT_SMTP_ENCRYPTION=ssl
REBIT_SMTP_USERNAME=noreply@your-domain.ru
REBIT_SMTP_FROM_EMAIL=noreply@your-domain.ru
REBIT_SMTP_FROM_NAME="Rebit P2P"
REBIT_SMTP_TLS_CERTCHECK=on
```

Также в Битриксе должны существовать:

- тип почтового события `REBIT_AUTH_REGISTRATION_CONFIRMATION`
- почтовый шаблон для нужного сайта (`LID`)

Шаблон письма лежит в:

- `docs/instruction/bitrix-email-registration-template.md`

Пароль SMTP передаётся отдельным Docker secret:

- `rebit_smtp_password`

## Что важно проверить после подключения

- письма реально доходят, а не только принимаются SMTP-сервером;
- письмо не попадает в спам;
- `From email` совпадает с доменом, для которого настроены SPF/DKIM;
- на сервере открыт исходящий трафик на SMTP-порт провайдера;
- cooldown повторной отправки и TTL кода соответствуют бизнес-ожиданиям.
