# Архитектура базы данных: P2P-платформа Rebit

> Все таблицы реализованы как **Highload-блоки Bitrix**.
> Пользователи хранятся в стандартной таблице `b_user`.

---

## Общие соглашения

- Имена таблиц: `rebit_<domain>_<entity>` (префикс Highload-блока).
- Первичный ключ: `ID` (int, auto_increment) — стандарт Highload-блоков.
- Внешние ключи: `UF_USER_ID`, `UF_TRADE_ID` и т.д. — на уровне приложения (Bitrix HL не поддерживает FK constraints).
- Временные метки: `UF_CREATED_AT`, `UF_UPDATED_AT` — тип `datetime`.
- Все строковые Enum-поля хранятся как `string` с ограниченным набором значений, валидация на уровне домена.
- Зашифрованные поля помечены 🔒.

---

## 1. Identity Domain

### 1.1. `rebit_api_connection` — Подключения к Bybit API

Хранит зашифрованные API-ключи пользователей для работы с Bybit.

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_USER_ID` | int | ✅ | ID пользователя (b_user.ID) |
| `UF_API_KEY_ENCRYPTED` | string(512) | ✅ | 🔒 Зашифрованный API Key |
| `UF_SECRET_KEY_ENCRYPTED` | string(512) | ✅ | 🔒 Зашифрованный Secret Key |
| `UF_MODE` | string(10) | ✅ | Режим: `testnet` / `mainnet` |
| `UF_STATUS` | string(30) | ✅ | Статус: `active`, `invalid`, `revoked`, `pending_verification` |
| `UF_LAST_VERIFIED_AT` | datetime | ❌ | Последняя успешная проверка ключей |
| `UF_ERROR_MESSAGE` | string(500) | ❌ | Последняя ошибка при проверке |
| `UF_CREATED_AT` | datetime | ✅ | Дата создания |
| `UF_UPDATED_AT` | datetime | ✅ | Дата обновления |

**Индексы:**
- `UF_USER_ID` — уникальный (одно активное подключение на пользователя)
- `UF_STATUS`

---

## 2. Exchange Domain

### 2.1. `rebit_currency` — Валюты

Справочник поддерживаемых валют и криптовалют.

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_CODE` | string(10) | ✅ | Код валюты: `USDT`, `BTC`, `RUB`, `USD` |
| `UF_NAME` | string(100) | ✅ | Название: `Tether`, `Bitcoin`, `Российский рубль` |
| `UF_TYPE` | string(10) | ✅ | Тип: `crypto` / `fiat` |
| `UF_DECIMALS` | int | ✅ | Количество знаков после запятой |
| `UF_ICON` | file | ❌ | Иконка валюты |
| `UF_IS_ACTIVE` | boolean | ✅ | Активна ли валюта |
| `UF_SORT` | int | ✅ | Сортировка |

**Индексы:**
- `UF_CODE` — уникальный
- `UF_IS_ACTIVE`

---

### 2.2. `rebit_currency_pair` — Валютные пары

Доступные пары для P2P-торговли.

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_TOKEN_CURRENCY_ID` | int | ✅ | FK → rebit_currency (криптовалюта) |
| `UF_FIAT_CURRENCY_ID` | int | ✅ | FK → rebit_currency (фиат) |
| `UF_CODE` | string(20) | ✅ | Код пары: `USDT_RUB`, `BTC_USDT` |
| `UF_IS_ACTIVE` | boolean | ✅ | Активна ли пара |
| `UF_IS_DEFAULT` | boolean | ✅ | Пара по умолчанию (USDT/RUB) |
| `UF_SORT` | int | ✅ | Сортировка |

**Индексы:**
- `UF_CODE` — уникальный
- `UF_TOKEN_CURRENCY_ID` + `UF_FIAT_CURRENCY_ID` — уникальный
- `UF_IS_ACTIVE`

---

### 2.3. `rebit_payment_method` — Способы оплаты

Справочник платёжных методов.

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_CODE` | string(50) | ✅ | Код: `tinkoff`, `sber`, `qiwi`, `bank_transfer` |
| `UF_NAME` | string(100) | ✅ | Название: `Тинькофф`, `Сбербанк` |
| `UF_ICON` | file | ❌ | Иконка |
| `UF_IS_ACTIVE` | boolean | ✅ | Активен |
| `UF_SORT` | int | ✅ | Сортировка |

**Индексы:**
- `UF_CODE` — уникальный

---

### 2.4. `rebit_order_book` — Стакан ордеров (кэш Bybit)

Кэшированные записи стакана P2P из Bybit API. Перезаписываются при каждой синхронизации.

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_BYBIT_ORDER_ID` | string(64) | ✅ | ID ордера в Bybit |
| `UF_CURRENCY_PAIR_ID` | int | ✅ | FK → rebit_currency_pair |
| `UF_SIDE` | string(4) | ✅ | Направление: `buy` / `sell` |
| `UF_PRICE` | double | ✅ | Цена за единицу токена в фиате |
| `UF_QUANTITY` | double | ✅ | Доступный объём токена |
| `UF_MIN_AMOUNT` | double | ✅ | Минимальная сумма сделки (фиат) |
| `UF_MAX_AMOUNT` | double | ✅ | Максимальная сумма сделки (фиат) |
| `UF_COUNTERPARTY_NAME` | string(100) | ✅ | Имя/никнейм контрагента |
| `UF_COUNTERPARTY_RATING` | double | ❌ | Рейтинг контрагента |
| `UF_COUNTERPARTY_TRADES` | int | ❌ | Количество завершённых сделок |
| `UF_COUNTERPARTY_COMPLETION_RATE` | double | ❌ | % завершённых сделок |
| `UF_PAYMENT_METHOD_IDS` | string(255) | ❌ | ID методов оплаты (JSON-массив) |
| `UF_PAYMENT_TIME_LIMIT` | int | ✅ | Лимит времени на оплату (минуты) |
| `UF_SYNCED_AT` | datetime | ✅ | Время последней синхронизации |

**Индексы:**
- `UF_BYBIT_ORDER_ID` — уникальный
- `UF_CURRENCY_PAIR_ID` + `UF_SIDE`
- `UF_SYNCED_AT`

---

### 2.5. `rebit_advertisement` — Объявления пользователей

Собственные P2P-объявления (маркет-мейкинг).

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_USER_ID` | int | ✅ | FK → b_user.ID |
| `UF_BYBIT_AD_ID` | string(64) | ❌ | ID объявления в Bybit (после публикации) |
| `UF_CURRENCY_PAIR_ID` | int | ✅ | FK → rebit_currency_pair |
| `UF_SIDE` | string(4) | ✅ | Тип: `buy` / `sell` |
| `UF_PRICE_TYPE` | string(10) | ✅ | Тип цены: `fixed` / `floating` |
| `UF_PRICE` | double | ✅ | Цена (фиксированная) или коэффициент (плавающая) |
| `UF_QUANTITY` | double | ✅ | Выставленный объём токена |
| `UF_QUANTITY_REMAINING` | double | ✅ | Оставшийся объём |
| `UF_MIN_AMOUNT` | double | ✅ | Мин. сумма сделки (фиат) |
| `UF_MAX_AMOUNT` | double | ✅ | Макс. сумма сделки (фиат) |
| `UF_PAYMENT_METHOD_IDS` | string(255) | ✅ | ID методов оплаты (JSON-массив) |
| `UF_CONDITIONS` | text | ❌ | Условия сделки (комментарий) |
| `UF_CHAT_SCRIPT_ID` | int | ❌ | FK → rebit_trade_chat_script (автоскрипт при открытии сделки) |
| `UF_STATUS` | string(20) | ✅ | Статус: `active`, `paused`, `completed`, `cancelled` |
| `UF_CREATED_AT` | datetime | ✅ | Дата создания |
| `UF_UPDATED_AT` | datetime | ✅ | Дата обновления |

**Индексы:**
- `UF_USER_ID` + `UF_STATUS`
- `UF_CURRENCY_PAIR_ID` + `UF_SIDE` + `UF_STATUS`
- `UF_BYBIT_AD_ID`

---

### 2.6. `rebit_trade` — Сделки

Основная таблица P2P-сделок.

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_BYBIT_ORDER_ID` | string(64) | ❌ | ID ордера в Bybit |
| `UF_BUYER_USER_ID` | int | ✅ | FK → b_user.ID (покупатель) |
| `UF_SELLER_USER_ID` | int | ❌ | FK → b_user.ID (продавец; null если контрагент внешний) |
| `UF_ADVERTISEMENT_ID` | int | ❌ | FK → rebit_advertisement (если по объявлению) |
| `UF_ORDER_BOOK_ENTRY_ID` | int | ❌ | FK → rebit_order_book (если по стакану) |
| `UF_CURRENCY_PAIR_ID` | int | ✅ | FK → rebit_currency_pair |
| `UF_SIDE` | string(4) | ✅ | Инициатор: `buy` / `sell` |
| `UF_PRICE` | double | ✅ | Цена за единицу токена |
| `UF_QUANTITY` | double | ✅ | Объём токена |
| `UF_FIAT_AMOUNT` | double | ✅ | Сумма в фиате |
| `UF_FEE` | double | ✅ | Комиссия Bybit |
| `UF_PAYMENT_METHOD_ID` | int | ✅ | FK → rebit_payment_method |
| `UF_PAYMENT_DETAILS` | text | ❌ | Реквизиты для оплаты (JSON) |
| `UF_COMMENT` | text | ❌ | Комментарий пользователя |
| `UF_STATUS` | string(20) | ✅ | Статус (см. State Machine в domain.md) |
| `UF_PAYMENT_DEADLINE` | datetime | ❌ | Дедлайн оплаты |
| `UF_PAID_AT` | datetime | ❌ | Время отметки «Я оплатил» |
| `UF_CONFIRMED_AT` | datetime | ❌ | Время подтверждения продавцом |
| `UF_COMPLETED_AT` | datetime | ❌ | Время завершения сделки |
| `UF_CANCELLED_AT` | datetime | ❌ | Время отмены |
| `UF_CANCEL_REASON` | string(50) | ❌ | Причина отмены: `timeout`, `user`, `insufficient_funds`, `dispute` |
| `UF_COUNTERPARTY_NAME` | string(100) | ❌ | Имя внешнего контрагента (если не наш пользователь) |
| `UF_CREATED_AT` | datetime | ✅ | Дата создания |
| `UF_UPDATED_AT` | datetime | ✅ | Дата обновления |

**Индексы:**
- `UF_BUYER_USER_ID` + `UF_STATUS`
- `UF_SELLER_USER_ID` + `UF_STATUS`
- `UF_BYBIT_ORDER_ID`
- `UF_STATUS` + `UF_PAYMENT_DEADLINE` (для фоновой проверки истекающих)
- `UF_CURRENCY_PAIR_ID`
- `UF_CREATED_AT`

---

### 2.7. `rebit_trade_message` — Чат сделки (real-time)

Сообщения в real-time чате сделки. Чат работает через WebSocket, пока сделка активна.

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_TRADE_ID` | int | ✅ | FK → rebit_trade |
| `UF_USER_ID` | int | ✅ | FK → b_user.ID (автор; для автосообщений — владелец скрипта) |
| `UF_MESSAGE` | text | ✅ | Текст сообщения |
| `UF_MESSAGE_TYPE` | string(10) | ✅ | Тип: `user` (ручное), `system` (смена статуса), `script` (автоскрипт) |
| `UF_SCRIPT_STEP_ID` | int | ❌ | FK → rebit_trade_chat_script_step (если отправлено скриптом) |
| `UF_IS_READ` | boolean | ✅ | Прочитано контрагентом |
| `UF_CREATED_AT` | datetime | ✅ | Время отправки |

**Индексы:**
- `UF_TRADE_ID` + `UF_CREATED_AT`
- `UF_TRADE_ID` + `UF_MESSAGE_TYPE`
- `UF_TRADE_ID` + `UF_IS_READ`

---

### 2.8. `rebit_trade_chat_script` — Скрипты чата

Заготовленные сценарии автоматических сообщений трейдера. Привязываются к объявлению — при открытии сделки система последовательно отправляет шаги скрипта в чат от имени трейдера.

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_USER_ID` | int | ✅ | FK → b_user.ID (владелец скрипта) |
| `UF_NAME` | string(255) | ✅ | Название скрипта (для UI: «Скрипт для покупки USDT», «Приветствие + реквизиты») |
| `UF_IS_ACTIVE` | boolean | ✅ | Активен ли скрипт |
| `UF_CREATED_AT` | datetime | ✅ | Дата создания |
| `UF_UPDATED_AT` | datetime | ✅ | Дата обновления |

**Индексы:**
- `UF_USER_ID` + `UF_IS_ACTIVE`

---

### 2.9. `rebit_trade_chat_script_step` — Шаги скрипта чата

Отдельные сообщения внутри скрипта: текст, порядок и опциональная задержка.

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_SCRIPT_ID` | int | ✅ | FK → rebit_trade_chat_script |
| `UF_SORT` | int | ✅ | Порядок шага (100, 200, 300…) |
| `UF_MESSAGE` | text | ✅ | Текст сообщения. Поддерживает плейсхолдеры: `{counterparty}`, `{amount}`, `{currency}` |
| `UF_DELAY_SECONDS` | int | ✅ | Задержка перед отправкой (сек). 0 = мгновенно |

**Индексы:**
- `UF_SCRIPT_ID` + `UF_SORT`

---

## 3. Wallet Domain

### 3.1. `rebit_balance` — Балансы пользователей

Текущие балансы пользователей по валютам.

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_USER_ID` | int | ✅ | FK → b_user.ID |
| `UF_CURRENCY_ID` | int | ✅ | FK → rebit_currency |
| `UF_AVAILABLE` | double | ✅ | Доступно |
| `UF_LOCKED` | double | ✅ | Заблокировано (под сделки) |
| `UF_TOTAL` | double | ✅ | Всего (available + locked) |
| `UF_SYNCED_AT` | datetime | ✅ | Последняя синхронизация с Bybit |
| `UF_UPDATED_AT` | datetime | ✅ | Дата обновления |

**Индексы:**
- `UF_USER_ID` + `UF_CURRENCY_ID` — уникальный
- `UF_USER_ID`

---

### 3.2. `rebit_transaction` — Транзакции

Журнал всех операций с балансами (append-only).

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_USER_ID` | int | ✅ | FK → b_user.ID |
| `UF_CURRENCY_ID` | int | ✅ | FK → rebit_currency |
| `UF_TYPE` | string(20) | ✅ | Тип: `deposit`, `withdrawal`, `trade_buy`, `trade_sell`, `lock`, `unlock`, `fee` |
| `UF_AMOUNT` | double | ✅ | Сумма операции (положительная для зачисления, отрицательная для списания) |
| `UF_BALANCE_AFTER` | double | ✅ | Баланс после операции |
| `UF_TRADE_ID` | int | ❌ | FK → rebit_trade (если связана со сделкой) |
| `UF_DESCRIPTION` | string(500) | ❌ | Описание операции |
| `UF_BYBIT_TX_ID` | string(64) | ❌ | ID транзакции в Bybit |
| `UF_CREATED_AT` | datetime | ✅ | Время операции |

**Индексы:**
- `UF_USER_ID` + `UF_CREATED_AT`
- `UF_USER_ID` + `UF_CURRENCY_ID` + `UF_TYPE`
- `UF_TRADE_ID`
- `UF_BYBIT_TX_ID`

---

## 4. Notification Domain

### 4.1. `rebit_notification` — Уведомления

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_USER_ID` | int | ✅ | FK → b_user.ID |
| `UF_CATEGORY` | string(20) | ✅ | Категория: `trade`, `system`, `security` |
| `UF_TYPE` | string(50) | ✅ | Тип: `trade_created`, `payment_received`, `new_device_login` и т.д. |
| `UF_TITLE` | string(255) | ✅ | Заголовок |
| `UF_MESSAGE` | text | ✅ | Текст уведомления |
| `UF_ENTITY_TYPE` | string(30) | ❌ | Связанная сущность: `trade`, `advertisement`, `balance` |
| `UF_ENTITY_ID` | int | ❌ | ID связанной сущности |
| `UF_IS_READ` | boolean | ✅ | Прочитано |
| `UF_CHANNELS` | string(100) | ✅ | Каналы доставки: JSON `["in_app","push","email"]` |
| `UF_CREATED_AT` | datetime | ✅ | Время создания |

**Индексы:**
- `UF_USER_ID` + `UF_IS_READ` + `UF_CREATED_AT`
- `UF_USER_ID` + `UF_CATEGORY`
- `UF_ENTITY_TYPE` + `UF_ENTITY_ID`

---

### 4.2. `rebit_notification_preference` — Настройки уведомлений

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_USER_ID` | int | ✅ | FK → b_user.ID |
| `UF_CATEGORY` | string(20) | ✅ | Категория уведомления |
| `UF_CHANNEL` | string(20) | ✅ | Канал: `in_app`, `push`, `email`, `telegram` |
| `UF_IS_ENABLED` | boolean | ✅ | Включен ли канал для категории |

**Индексы:**
- `UF_USER_ID` + `UF_CATEGORY` + `UF_CHANNEL` — уникальный

---

## 5. Security Domain

### 5.1. `rebit_user_session` — Сессии пользователей

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_USER_ID` | int | ✅ | FK → b_user.ID |
| `UF_SESSION_ID` | string(128) | ✅ | ID сессии Bitrix |
| `UF_IP_ADDRESS` | string(45) | ✅ | IP-адрес (IPv4/IPv6) |
| `UF_USER_AGENT` | string(500) | ✅ | User-Agent |
| `UF_DEVICE_FINGERPRINT` | string(64) | ❌ | Fingerprint устройства |
| `UF_IS_ACTIVE` | boolean | ✅ | Активна ли сессия |
| `UF_LAST_ACTIVITY_AT` | datetime | ✅ | Последняя активность |
| `UF_CREATED_AT` | datetime | ✅ | Время создания |

**Индексы:**
- `UF_USER_ID` + `UF_IS_ACTIVE`
- `UF_SESSION_ID` — уникальный

---

### 5.2. `rebit_audit_log` — Журнал аудита

Append-only лог всех значимых действий. Хранение 5+ лет.

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_USER_ID` | int | ✅ | FK → b_user.ID |
| `UF_ACTION` | string(50) | ✅ | Действие: `login`, `trade_create`, `api_key_change`, `withdrawal` и т.д. |
| `UF_ENTITY_TYPE` | string(30) | ❌ | Тип сущности: `trade`, `advertisement`, `api_connection` |
| `UF_ENTITY_ID` | int | ❌ | ID сущности |
| `UF_IP_ADDRESS` | string(45) | ✅ | IP-адрес |
| `UF_USER_AGENT` | string(500) | ❌ | User-Agent |
| `UF_PAYLOAD` | text | ❌ | Дополнительные данные (JSON) |
| `UF_CREATED_AT` | datetime | ✅ | Время действия |

**Индексы:**
- `UF_USER_ID` + `UF_CREATED_AT`
- `UF_ACTION` + `UF_CREATED_AT`
- `UF_ENTITY_TYPE` + `UF_ENTITY_ID`

---

### 5.3. `rebit_security_alert` — Алерты безопасности

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_USER_ID` | int | ✅ | FK → b_user.ID |
| `UF_ALERT_TYPE` | string(40) | ✅ | Тип: `frequent_cancellations`, `large_trade`, `multiple_accounts`, `new_device_login` |
| `UF_SEVERITY` | string(10) | ✅ | Уровень: `low`, `medium`, `high`, `critical` |
| `UF_STATUS` | string(20) | ✅ | Статус: `new`, `reviewing`, `resolved`, `dismissed` |
| `UF_DESCRIPTION` | text | ✅ | Описание инцидента |
| `UF_PAYLOAD` | text | ❌ | Дополнительные данные (JSON) |
| `UF_RESOLVED_AT` | datetime | ❌ | Время закрытия |
| `UF_CREATED_AT` | datetime | ✅ | Время создания |

**Индексы:**
- `UF_USER_ID` + `UF_STATUS`
- `UF_ALERT_TYPE` + `UF_STATUS`
- `UF_SEVERITY` + `UF_STATUS`

---

### 5.4. `rebit_two_factor_auth` — Двухфакторная аутентификация

| Поле | Тип | Обязательное | Описание |
|------|-----|:---:|----------|
| `ID` | int | ✅ | PK |
| `UF_USER_ID` | int | ✅ | FK → b_user.ID |
| `UF_METHOD` | string(10) | ✅ | Метод: `totp`, `sms`, `email` |
| `UF_SECRET_ENCRYPTED` | string(512) | ✅ | 🔒 Зашифрованный секрет (для TOTP) |
| `UF_IS_ENABLED` | boolean | ✅ | Активна ли 2FA |
| `UF_BACKUP_CODES_ENCRYPTED` | text | ❌ | 🔒 Резервные коды (JSON, зашифровано) |
| `UF_CREATED_AT` | datetime | ✅ | Дата подключения |
| `UF_UPDATED_AT` | datetime | ✅ | Дата обновления |

**Индексы:**
- `UF_USER_ID` — уникальный

---

## 6. ER-диаграмма (упрощённая)

```
b_user (Bitrix)
  │
  ├──1:1── rebit_api_connection
  ├──1:1── rebit_two_factor_auth
  ├──1:N── rebit_balance ──N:1── rebit_currency
  ├──1:N── rebit_transaction ──N:1── rebit_currency
  ├──1:N── rebit_advertisement
  ├──1:N── rebit_trade (buyer / seller)
  ├──1:N── rebit_trade_message
  ├──1:N── rebit_trade_chat_script
  ├──1:N── rebit_notification
  ├──1:1── rebit_notification_preference (per category+channel)
  ├──1:N── rebit_user_session
  ├──1:N── rebit_audit_log
  └──1:N── rebit_security_alert

rebit_currency_pair
  ├── UF_TOKEN_CURRENCY_ID ──► rebit_currency
  └── UF_FIAT_CURRENCY_ID  ──► rebit_currency

rebit_order_book
  └── UF_CURRENCY_PAIR_ID  ──► rebit_currency_pair

rebit_advertisement
  ├── UF_USER_ID            ──► b_user
  ├── UF_CURRENCY_PAIR_ID   ──► rebit_currency_pair
  └── UF_CHAT_SCRIPT_ID     ──► rebit_trade_chat_script

rebit_trade
  ├── UF_BUYER_USER_ID      ──► b_user
  ├── UF_SELLER_USER_ID     ──► b_user
  ├── UF_ADVERTISEMENT_ID   ──► rebit_advertisement
  ├── UF_ORDER_BOOK_ENTRY_ID──► rebit_order_book
  ├── UF_CURRENCY_PAIR_ID   ──► rebit_currency_pair
  └── UF_PAYMENT_METHOD_ID  ──► rebit_payment_method

rebit_trade_message
  ├── UF_TRADE_ID           ──► rebit_trade
  ├── UF_USER_ID            ──► b_user
  └── UF_SCRIPT_STEP_ID     ──► rebit_trade_chat_script_step

rebit_trade_chat_script
  └── UF_USER_ID            ──► b_user

rebit_trade_chat_script_step
  └── UF_SCRIPT_ID          ──► rebit_trade_chat_script

rebit_transaction
  ├── UF_USER_ID            ──► b_user
  ├── UF_CURRENCY_ID        ──► rebit_currency
  └── UF_TRADE_ID           ──► rebit_trade
```

---

## 7. Сводная таблица Highload-блоков

| # | Имя HL-блока | Таблица | Домен | Кол-во полей |
|---|-------------|---------|-------|:---:|
| 1 | `RebitApiConnection` | `rebit_api_connection` | Identity | 10 |
| 2 | `RebitCurrency` | `rebit_currency` | Exchange | 7 |
| 3 | `RebitCurrencyPair` | `rebit_currency_pair` | Exchange | 6 |
| 4 | `RebitPaymentMethod` | `rebit_payment_method` | Exchange | 5 |
| 5 | `RebitOrderBook` | `rebit_order_book` | Exchange | 15 |
| 6 | `RebitAdvertisement` | `rebit_advertisement` | Exchange | 17 |
| 7 | `RebitTrade` | `rebit_trade` | Exchange | 23 |
| 8 | `RebitTradeMessage` | `rebit_trade_message` | Exchange | 8 |
| 9 | `RebitTradeChatScript` | `rebit_trade_chat_script` | Exchange | 5 |
| 10 | `RebitTradeChatScriptStep` | `rebit_trade_chat_script_step` | Exchange | 5 |
| 11 | `RebitBalance` | `rebit_balance` | Wallet | 7 |
| 12 | `RebitTransaction` | `rebit_transaction` | Wallet | 10 |
| 13 | `RebitNotification` | `rebit_notification` | Notification | 10 |
| 14 | `RebitNotificationPreference` | `rebit_notification_preference` | Notification | 5 |
| 15 | `RebitUserSession` | `rebit_user_session` | Security | 8 |
| 16 | `RebitAuditLog` | `rebit_audit_log` | Security | 8 |
| 17 | `RebitSecurityAlert` | `rebit_security_alert` | Security | 8 |
| 18 | `RebitTwoFactorAuth` | `rebit_two_factor_auth` | Security | 7 |

**Итого: 18 Highload-блоков**

---

## 8. Рекомендации по реализации

1. **Типы double для денежных сумм:** в боевом коде все расчёты через `bcmath` (bcadd, bcsub, bcmul, bcdiv) с заданной точностью. В БД хранить как `double` (Bitrix HL не поддерживает `decimal`). Альтернатива — хранить в `string` и работать через bcmath.

2. **Шифрование:** API-ключи и 2FA-секреты шифровать через `sodium_crypto_secretbox` (libsodium). Ключ шифрования — в env-переменной, не в БД.

3. **Append-only таблицы** (`rebit_transaction`, `rebit_audit_log`): не допускать UPDATE/DELETE на уровне репозитория. Для высокой нагрузки рассмотреть партиционирование по дате.

4. **Кэширование стакана:** `rebit_order_book` перезаписывается полностью при синхронизации. Для отдачи на фронт использовать Redis/Memcache с TTL 10 сек.

5. **JSON-поля** (`UF_PAYMENT_METHOD_IDS`, `UF_PAYMENT_DETAILS`, `UF_CHANNELS`, `UF_PAYLOAD`): хранить как `string`/`text` с JSON-сериализацией. Валидация на уровне домена.

6. **Real-time чат:** доставка сообщений через WebSocket (Bitrix Pull & Push). Сообщения пишутся в `rebit_trade_message` синхронно, а затем пушатся в канал сделки. При реконнекте — клиент запрашивает сообщения с последнего известного `ID`. Антиспам: rate-limit 10 msg / 30 сек на уровне приложения.

7. **Скрипты чата:** шаги с `UF_DELAY_SECONDS > 0` выполняются через отложенные задачи (cron-агент или очередь). При отмене сделки до завершения скрипта — оставшиеся шаги не отправляются.
