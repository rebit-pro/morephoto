# Документация Bybit P2P API

> Полная документация по API Bybit P2P, используемому в платформе Rebit.
> Перевод и адаптация оригинальной документации Bybit на русский язык.

---

## Содержание

1. [Аутентификация](#1-аутентификация)
2. [Общий формат ответа](#2-общий-формат-ответа)
3. [Пользователь и аккаунт](#3-пользователь-и-аккаунт)
   - 3.1. [Получить информацию об аккаунте](#31-получить-информацию-об-аккаунте)
   - 3.2. [Получить информацию о контрагенте](#32-получить-информацию-о-контрагенте)
4. [Кошелёк и балансы](#4-кошелёк-и-балансы)
   - 4.1. [Получить баланс монет](#41-получить-баланс-монет)
5. [Стакан (объявления)](#5-стакан-объявления)
   - 5.1. [Получить список объявлений (стакан)](#51-получить-список-объявлений-стакан)
6. [Управление объявлениями](#6-управление-объявлениями)
   - 6.1. [Создать объявление](#61-создать-объявление)
   - 6.2. [Обновить / переопубликовать объявление](#62-обновить--переопубликовать-объявление)
   - 6.3. [Получить мои объявления](#63-получить-мои-объявления)
   - 6.4. [Получить детали моего объявления](#64-получить-детали-моего-объявления)
   - 6.5. [Удалить объявление](#65-удалить-объявление)
7. [Сделки (ордера)](#7-сделки-ордера)
   - 7.1. [Получить все ордера](#71-получить-все-ордера)
   - 7.2. [Получить активные ордера](#72-получить-активные-ордера)
   - 7.3. [Получить детали ордера](#73-получить-детали-ордера)
   - 7.4. [Отметить ордер как оплаченный](#74-отметить-ордер-как-оплаченный)
   - 7.5. [Выпустить активы (подтвердить получение)](#75-выпустить-активы-подтвердить-получение)
8. [Чат сделки](#8-чат-сделки)
   - 8.1. [Отправить сообщение в чат](#81-отправить-сообщение-в-чат)
   - 8.2. [Загрузить файл для чата](#82-загрузить-файл-для-чата)
9. [Ограничения Bybit P2P API](#9-ограничения-bybit-p2p-api)
10. [Маппинг: Bybit API → Rebit-модули](#10-маппинг-bybit-api--rebit-модули)

---

## 1. Аутентификация

### Требования к доступу

> ⚠️ **Важно:** P2P API доступен только пользователям со статусом **General Advertiser** и выше. Пользователи без статуса P2P-рекламодателя не могут использовать P2P API.

### Базовые URL

| Среда | URL |
|-------|-----|
| **Testnet** | `https://api-testnet.bybit.com` |
| **Mainnet** | `https://api.bybit.com` или `https://api.bytick.com` |

**Региональные ограничения:**

| Регион | URL |
|--------|-----|
| Нидерланды | `https://api.bybit.nl` |
| Гонконг | `https://api.byhkbit.com` |
| Турция | `https://api.bybit-tr.com` |
| Казахстан | `https://api.bybit.kz` |

### Типы API-ключей

| Тип | Шифрование | Описание |
|-----|-----------|----------|
| **Системно-сгенерированные** | HMAC (SHA256) | Bybit генерирует пару публичного и приватного ключей |
| **Самогенерируемые** | RSA (SHA256) | Пользователь создаёт RSA-ключи, передаёт Bybit только публичный ключ |

### HTTP-заголовки для аутентификации

Все аутентифицированные запросы должны содержать следующие заголовки:

| Заголовок | Описание |
|-----------|----------|
| `X-BAPI-API-KEY` | API-ключ |
| `X-BAPI-TIMESTAMP` | UTC-метка времени в миллисекундах |
| `X-BAPI-SIGN` | Подпись, вычисленная на основе параметров запроса |
| `X-BAPI-RECV-WINDOW` | Окно допустимого времени запроса (мс, по умолчанию `5000`) |

### Алгоритм формирования подписи

#### Шаг 1: Сформировать строку для подписи

**Для GET-запросов:**
```
timestamp + api_key + recv_window + queryString
```

**Для POST-запросов:**
```
timestamp + api_key + recv_window + jsonBodyString
```

#### Шаг 2: Подписать строку

- **HMAC:** вычислить `HMAC_SHA256`, конвертировать в HEX (lowercase)
- **RSA:** вычислить `RSA_SHA256`, конвертировать в Base64

#### Шаг 3: Добавить подпись в заголовок `X-BAPI-SIGN`

#### Пример (HMAC)

```
# Исходные данные:
timestamp = "1658384314791"
api_key = "XXXXXXXXXX"
recv_window = "5000"
queryString = "category=option&symbol=BTC-29JUL22-25000-C"

# Строка для подписи:
"1658384314791XXXXXXXXXX5000category=option&symbol=BTC-29JUL22-25000-C"

# Результат подписи:
"410e0f387bafb7afd0f1722c068515e09945610124fa11774da1da857b72f30b"
```

### Правило валидации временной метки

```
server_time - recv_window <= timestamp < server_time + 1000
```

> 💡 **Рекомендация:** используйте локальное время устройства, синхронизированное по NTP.

---

## 2. Общий формат ответа

Все ответы Bybit API имеют единую обёртку:

```json
{
    "ret_code": 0,
    "ret_msg": "SUCCESS",
    "result": { ... },
    "ext_code": "",
    "ext_info": {},
    "time_now": "1741763625.212991"
}
```

| Параметр | Тип | Описание |
|----------|-----|----------|
| `ret_code` | `number` | Код результата. `0` = успех |
| `ret_msg` | `string` | Сообщение. `OK`, `SUCCESS`, `""` — успех |
| `result` | `object` | Данные ответа |
| `ext_code` | `string` | Дополнительный код |
| `ext_info` | `object` | Дополнительная информация |
| `time_now` | `string` | Текущая метка времени сервера |

> ℹ️ Некоторые эндпоинты (v5/asset) используют поле `retCode`/`retMsg`/`retExtInfo`/`time` вместо `ret_code`/`ret_msg`/`ext_code`/`time_now`.

---

## 3. Пользователь и аккаунт

### 3.1. Получить информацию об аккаунте

Возвращает полную информацию о P2P-аккаунте текущего пользователя.

**Используется в:** `rebit.identity` — при подключении API-ключей, верификации.

```
POST /v5/p2p/user/personal/info
```

#### Параметры запроса

Нет (тело запроса — пустой объект `{}`).

#### Параметры ответа

| Параметр | Тип | Описание |
|----------|-----|----------|
| `nickName` | `string` | Никнейм пользователя |
| `defaultNickName` | `boolean` | Является ли никнейм автогенерированным |
| `isOnline` | `boolean` | Пользователь онлайн на Bybit |
| `kycLevel` | `string` | Уровень KYC-верификации |
| `email` | `string` | Email (маскированный) |
| `mobile` | `string` | Телефон (маскированный) |
| `lastLogoutTime` | `string` | Время последнего выхода (timestamp) |
| `recentRate` | `string` | Процент завершённых сделок за 30 дней |
| `totalFinishCount` | `integer` | Общее кол-во завершённых сделок |
| `totalFinishSellCount` | `integer` | Завершённых сделок продажи |
| `totalFinishBuyCount` | `integer` | Завершённых сделок покупки |
| `recentFinishCount` | `integer` | Кол-во завершённых сделок за 30 дней |
| `averageReleaseTime` | `string` | Среднее время выпуска токенов (мин) |
| `averageTransferTime` | `string` | Среднее время оплаты (мин) |
| `accountCreateDays` | `integer` | Дней с момента создания аккаунта |
| `firstTradeDays` | `integer` | Дней с первой сделки |
| `realName` | `string` | Реальное имя |
| `recentTradeAmount` | `string` | Объём сделок в USDT за 30 дней |
| `totalTradeAmount` | `string` | Общий объём сделок в USDT |
| `registerTime` | `string` | Время регистрации (timestamp) |
| `authStatus` | `integer` | Статус VA. `1`: VA, `2`: не VA |
| `kycCountryCode` | `string` | Код страны KYC (ISO 3) |
| `blocked` | `string` | Статус блокировки |
| `goodAppraiseRate` | `string` | Рейтинг положительных отзывов |
| `goodAppraiseCount` | `integer` | Кол-во положительных отзывов |
| `badAppraiseCount` | `integer` | Кол-во отрицательных отзывов |
| `accountId` | `integer` | ID спотового аккаунта |
| `paymentCount` | `integer` | Кол-во добавленных платёжных методов |
| `contactCount` | `integer` | Кол-во контактов |
| `vipLevel` | `integer` | VIP-уровень |
| `userCancelCountLimit` | `integer` | Лимит отмен ордеров |
| `paymentRealNameUneditable` | `boolean` | Запрет на изменение имени в платёжных методах |
| `userId` | `string` | ID пользователя |
| `realNameEn` | `string` | Реальное имя (англ.) |

#### Пример запроса

```http
POST /v5/p2p/user/personal/info HTTP/1.1
Host: api-testnet.bybit.com
X-BAPI-SIGN: XXXXXX
X-BAPI-API-KEY: xxxxxxxxxxxxxxxxxx
X-BAPI-TIMESTAMP: 1741595134312
X-BAPI-RECV-WINDOW: 5000
Content-Type: application/json

{}
```

#### Пример ответа

```json
{
    "ret_code": 0,
    "ret_msg": "SUCCESS",
    "result": {
        "nickName": "Saaaul",
        "defaultNickName": false,
        "isOnline": true,
        "email": "w********g@jn.com",
        "mobile": "",
        "kycLevel": 1,
        "lastLogoutTime": "1741246087",
        "recentRate": 0,
        "totalFinishCount": 0,
        "recentFinishCount": 0,
        "averageReleaseTime": "0",
        "averageTransferTime": "0",
        "accountCreateDays": 698,
        "firstTradeDays": 0,
        "realName": "TEST",
        "recentTradeAmount": "0",
        "totalTradeAmount": "0",
        "registerTime": "1681266699",
        "authStatus": 2,
        "kycCountryCode": "MYS",
        "blocked": "",
        "goodAppraiseRate": "0",
        "goodAppraiseCount": 0,
        "badAppraiseCount": 0,
        "accountId": "1448940",
        "paymentCount": 2,
        "vipLevel": 1,
        "userId": "1448939",
        "realNameEn": "TEST WOOD"
    }
}
```

---

### 3.2. Получить информацию о контрагенте

Возвращает публичную информацию о контрагенте в рамках конкретного ордера.

**Используется в:** `rebit.exchange` — отображение профиля контрагента на странице сделки.

```
POST /v5/p2p/user/order/personal/info
```

#### Параметры запроса

| Параметр | Обязательный | Тип | Описание |
|----------|:---:|-----|----------|
| `originalUid` | ✅ | `string` | ID пользователя-контрагента |
| `orderId` | ✅ | `string` | ID ордера |

#### Параметры ответа

| Параметр | Тип | Описание |
|----------|-----|----------|
| `nickName` | `string` | Никнейм |
| `defaultNickName` | `boolean` | Автогенерированный никнейм |
| `isOnline` | `boolean` | Онлайн на Bybit |
| `kycLevel` | `string` | Уровень KYC |
| `email` | `string` | Email (маскированный) |
| `mobile` | `string` | Телефон (маскированный) |
| `lastLogoutTime` | `string` | Время последнего выхода |
| `recentRate` | `integer` | Процент завершённых за 30 дней |
| `totalFinishCount` | `integer` | Всего завершённых сделок |
| `totalFinishSellCount` | `integer` | Завершённых продаж |
| `totalFinishBuyCount` | `integer` | Завершённых покупок |
| `recentFinishCount` | `integer` | Завершённых за 30 дней |
| `averageReleaseTime` | `string` | Среднее время выпуска (мин) |
| `averageTransferTime` | `string` | Среднее время оплаты (мин) |
| `accountCreateDays` | `integer` | Дней с регистрации |
| `firstTradeDays` | `integer` | Дней с первой сделки |
| `realName` | `string` | Реальное имя |
| `recentTradeAmount` | `string` | Объём USDT за 30 дней |
| `totalTradeAmount` | `string` | Общий объём USDT |
| `registerTime` | `string` | Время регистрации |
| `authStatus` | `integer` | VA-статус. `1`: VA, `2`: не VA |
| `kycCountryCode` | `string` | Код страны KYC |
| `blocked` | `string` | Статус блокировки |
| `goodAppraiseRate` | `string` | Рейтинг положительных отзывов |
| `goodAppraiseCount` | `integer` | Кол-во положительных отзывов |
| `badAppraiseCount` | `integer` | Кол-во отрицательных отзывов |
| `vipLevel` | `integer` | VIP-уровень |
| `userId` | `string` | ID пользователя |
| `realNameEn` | `string` | Имя (англ.) |

#### Пример запроса

```http
POST /v5/p2p/user/order/personal/info HTTP/1.1
Host: api-testnet.bybit.com
X-BAPI-SIGN: XXXXXX
X-BAPI-API-KEY: xxxxxxxxxxxxxxxxxx
X-BAPI-TIMESTAMP: 1741831548391
X-BAPI-RECV-WINDOW: 5000
Content-Type: application/json

{
    "originalUid": "290118",
    "orderId": "1900004704665923584"
}
```

#### Пример ответа

```json
{
    "ret_code": 0,
    "ret_msg": "SUCCESS",
    "result": {
        "nickName": "cjmtest",
        "defaultNickName": false,
        "isOnline": false,
        "kycLevel": 2,
        "lastLogoutTime": "1741778675",
        "recentRate": 0,
        "totalFinishCount": 0,
        "averageReleaseTime": "0",
        "averageTransferTime": "0",
        "accountCreateDays": 1241,
        "firstTradeDays": 2,
        "realName": "1**** ",
        "recentTradeAmount": "0",
        "totalTradeAmount": "0",
        "registerTime": "1634552356",
        "authStatus": 2,
        "kycCountryCode": "MYS",
        "blocked": "N",
        "goodAppraiseRate": "0",
        "goodAppraiseCount": 0,
        "badAppraiseCount": 0,
        "vipLevel": 0,
        "userType": "ORG",
        "userId": ""
    }
}
```

---

## 4. Кошелёк и балансы

### 4.1. Получить баланс монет

Возвращает балансы по всем типам аккаунтов мастер-аккаунта или саб-аккаунта.

**Используется в:** `rebit.wallet` — синхронизация балансов (`SyncBalancesAgent`).

```
GET /v5/asset/transfer/query-account-coins-balance
```

#### Параметры запроса

| Параметр | Обязательный | Тип | Описание |
|----------|:---:|-----|----------|
| `memberId` | ❌ | `string` | ID пользователя. Обязателен при проверке баланса саб-аккаунта через мастер-ключ |
| `accountType` | ✅ | `string` | Тип аккаунта (например, `FUND`, `UNIFIED`) |
| `coin` | ❌ | `string` | Код монеты (uppercase). Несколько монет через запятую: `USDT,USDC,ETH`. Обязательно для `accountType=UNIFIED` (макс. 10 монет) |
| `withBonus` | ❌ | `integer` | `0` (по умолчанию): без бонусов. `1`: включить бонусы |

#### Параметры ответа

| Параметр | Тип | Описание |
|----------|-----|----------|
| `accountType` | `string` | Тип аккаунта |
| `memberId` | `string` | ID пользователя |
| `balance` | `array` | Массив балансов |
| `balance[].coin` | `string` | Код валюты |
| `balance[].walletBalance` | `string` | Баланс кошелька |
| `balance[].transferBalance` | `string` | Баланс, доступный для перевода |
| `balance[].bonus` | `string` | Бонус |

#### Пример запроса

```http
GET /v5/asset/transfer/query-account-coins-balance?accountType=FUND&coin=USDC HTTP/1.1
Host: api-testnet.bybit.com
X-BAPI-SIGN: XXXXX
X-BAPI-API-KEY: xxxxxxxxxxxxxxxxxx
X-BAPI-TIMESTAMP: 1675866354698
X-BAPI-RECV-WINDOW: 5000
```

#### Пример ответа

```json
{
    "retCode": 0,
    "retMsg": "success",
    "result": {
        "memberId": "XXXX",
        "accountType": "FUND",
        "balance": [
            {
                "coin": "USDC",
                "transferBalance": "0",
                "walletBalance": "0",
                "bonus": ""
            }
        ]
    },
    "retExtInfo": {},
    "time": 1675866354913
}
```

---

## 5. Стакан (объявления)

### 5.1. Получить список объявлений (стакан)

Возвращает список активных P2P-объявлений (стакан ордеров).

**Используется в:** `rebit.exchange` — синхронизация стакана (`SyncOrderBookAgent`), отображение в UI.

```
POST /v5/p2p/item/online
```

#### Параметры запроса

| Параметр | Обязательный | Тип | Описание |
|----------|:---:|-----|----------|
| `tokenId` | ✅ | `string` | ID токена: `USDT`, `ETH`, `BTC` |
| `currencyId` | ✅ | `string` | ID валюты: `RUB`, `USD`, `EUR` |
| `side` | ✅ | `string` | Направление: `0` — покупка, `1` — продажа |
| `page` | ❌ | `string` | Номер страницы (по умолчанию `1`) |
| `size` | ❌ | `string` | Размер страницы (по умолчанию `10`, макс. `30`) |

#### Параметры ответа

| Параметр | Тип | Описание |
|----------|-----|----------|
| `count` | `int` | Общее количество объявлений |
| `items` | `array<object>` | Массив объявлений |
| `items[].id` | `string` | ID объявления |
| `items[].userId` | `int` | ID пользователя |
| `items[].nickName` | `string` | Никнейм автора |
| `items[].tokenId` | `string` | ID токена |
| `items[].currencyId` | `string` | ID валюты |
| `items[].side` | `string` | `0`: покупка, `1`: продажа |
| `items[].price` | `string` | Цена объявления |
| `items[].lastQuantity` | `string` | Доступный объём токена |
| `items[].minAmount` | `string` | Мин. сумма сделки (фиат) |
| `items[].maxAmount` | `string` | Макс. сумма сделки (фиат) |
| `items[].payments` | `array[string]` | ID платёжных методов |
| `items[].recentOrderNum` | `string` | Кол-во недавних ордеров |
| `items[].recentExecuteRate` | `string` | Процент исполнения |
| `items[].isOnline` | `boolean` | Пользователь онлайн |
| `items[].lastLogoutTime` | `string` | Время последнего выхода |
| `items[].authTag` | `array[string]` | Теги: `GA` (General Advertiser), `VA` (Verified Advertiser), `BA` (Block Advertiser) |
| `items[].paymentPeriod` | `int` | Время на оплату (минуты) |

#### Пример запроса

```http
POST /v5/p2p/item/online HTTP/1.1
Host: api-testnet.bybit.com
X-BAPI-SIGN: XXXXX
X-BAPI-API-KEY: xxxxxxxxxxxxxxxxxx
X-BAPI-TIMESTAMP: 1675866354698
X-BAPI-RECV-WINDOW: 5000
Content-Type: application/json

{
    "tokenId": "USDT",
    "currencyId": "EUR",
    "side": "0"
}
```

#### Пример ответа

```json
{
    "ret_code": 0,
    "ret_msg": "SUCCESS",
    "result": {
        "count": 3,
        "items": [
            {
                "id": "1899658238346616832",
                "userId": "290118",
                "nickName": "cjmtest",
                "tokenId": "USDT",
                "currencyId": "EUR",
                "side": 0,
                "priceType": 0,
                "price": "0.93",
                "lastQuantity": "10000",
                "quantity": "10000",
                "minAmount": "200",
                "maxAmount": "9300",
                "remark": "1111121212",
                "status": 10,
                "payments": ["14"],
                "recentOrderNum": 0,
                "recentExecuteRate": 0,
                "isOnline": true,
                "lastLogoutTime": "1741749194",
                "authTag": ["BA"],
                "paymentPeriod": 15
            }
        ]
    }
}
```

---

## 6. Управление объявлениями

### 6.1. Создать объявление

Создаёт новое P2P-объявление.

**Используется в:** `rebit.exchange` — `CreateAdvertisementUseCase`.

```
POST /v5/p2p/item/create
```

#### Параметры запроса

| Параметр | Обязательный | Тип | Описание |
|----------|:---:|-----|----------|
| `tokenId` | ✅ | `string` | ID токена: `USDT`, `ETH`, `BTC` |
| `currencyId` | ✅ | `string` | ID валюты: `RUB`, `USD`, `EUR` |
| `side` | ✅ | `string` | `0`: покупка, `1`: продажа |
| `priceType` | ✅ | `string` | Модель цены. `0`: фиксированная, `1`: плавающая |
| `premium` | ✅ | `string` | Надбавка к рыночной цене (%). Например, `130` = 130% от рыночной цены |
| `price` | ✅ | `string` | Цена за единицу токена в валюте |
| `minAmount` | ✅ | `string` | Мин. сумма сделки (фиат) |
| `maxAmount` | ✅ | `string` | Макс. сумма сделки (фиат) |
| `remark` | ✅ | `string` | Описание объявления (макс. 900 символов) |
| `tradingPreferenceSet` | ✅ | `object` | Торговые ограничения (см. ниже) |
| `paymentIds` | ✅ | `array[string]` | ID платёжных методов (макс. 5) |
| `quantity` | ✅ | `string` | Объём токенов |
| `paymentPeriod` | ✅ | `string` | Время на оплату (минуты) |
| `itemType` | ✅ | `string` | `ORIGIN`: обычное, `BULK`: массовое |

**Объект `tradingPreferenceSet`:**

| Параметр | Обязательный | Тип | Описание |
|----------|:---:|-----|----------|
| `hasUnPostAd` | ❌ | `string` | Контрагент не должен иметь объявлений. `0`: нет, `1`: да |
| `isKyc` | ❌ | `string` | Обязательна верификация. `0`: нет, `1`: да |
| `isEmail` | ❌ | `string` | Обязательна привязка email. `0`: нет, `1`: да |
| `isMobile` | ❌ | `string` | Обязательна привязка телефона. `0`: нет, `1`: да |
| `hasRegisterTime` | ❌ | `string` | Мин. время регистрации. `0`: нет, `1`: да |
| `registerTimeThreshold` | ❌ | `string` | Порог регистрации (дни) |
| `orderFinishNumberDay30` | ❌ | `string` | Мин. кол-во завершённых ордеров за 30 дней |
| `completeRateDay30` | ❌ | `string` | Мин. процент завершения за 30 дней |
| `nationalLimit` | ❌ | `string` | Ограничение по странам KYC (ISO 3) |
| `hasOrderFinishNumberDay30` | ❌ | `string` | Включить лимит ордеров. `0`: нет, `1`: да |
| `hasCompleteRateDay30` | ❌ | `string` | Включить лимит процента. `0`: нет, `1`: да |
| `hasNationalLimit` | ❌ | `string` | Включить ограничение по странам. `0`: нет, `1`: да |

#### Параметры ответа

| Параметр | Тип | Описание |
|----------|-----|----------|
| `itemId` | `string` | ID созданного объявления |
| `securityRiskToken` | `string` | Токен безопасности |
| `riskTokenType` | `string` | Тип токена безопасности |
| `riskVersion` | `string` | Версия |
| `needSecurityRisk` | `boolean` | Требуется ли подтверждение безопасности |

#### Пример запроса

```http
POST /v5/p2p/item/create HTTP/1.1
Host: api-testnet.bybit.com
X-BAPI-SIGN: XXXXX
X-BAPI-API-KEY: xxxxxxxxxxxxxxxxxx
X-BAPI-TIMESTAMP: 1675866354698
X-BAPI-RECV-WINDOW: 5000
Content-Type: application/json

{
    "tokenId": "USDT",
    "currencyId": "EUR",
    "side": "0",
    "priceType": "0",
    "premium": "",
    "price": "0.92",
    "minAmount": "20",
    "maxAmount": "45000",
    "paymentIds": ["7110"],
    "remark": "test",
    "tradingPreferenceSet": {
        "isKyc": "1",
        "hasCompleteRateDay30": "1",
        "completeRateDay30": "95",
        "hasOrderFinishNumberDay30": "1",
        "orderFinishNumberDay30": "60"
    },
    "quantity": "20000",
    "paymentPeriod": "15",
    "itemType": "ORIGIN"
}
```

#### Пример ответа

```json
{
    "ret_code": 0,
    "ret_msg": "SUCCESS",
    "result": {
        "itemId": "1899659847717838848",
        "securityRiskToken": "",
        "riskTokenType": "",
        "riskVersion": "",
        "needSecurityRisk": false
    }
}
```

---

### 6.2. Обновить / переопубликовать объявление

Обновляет существующее объявление или переводит его в статус «онлайн».

**Используется в:** `rebit.exchange` — `UpdateAdvertisementUseCase`.

```
POST /v5/p2p/item/update
```

#### Параметры запроса

| Параметр | Обязательный | Тип | Описание |
|----------|:---:|-----|----------|
| `id` | ✅ | `string` | ID объявления |
| `priceType` | ✅ | `string` | `0`: фиксированная, `1`: плавающая |
| `premium` | ✅ | `string` | Надбавка к рыночной цене (%) |
| `price` | ✅ | `string` | Цена за токен в валюте |
| `minAmount` | ✅ | `string` | Мин. сумма (фиат) |
| `maxAmount` | ✅ | `string` | Макс. сумма (фиат) |
| `remark` | ✅ | `string` | Описание (макс. 900) |
| `tradingPreferenceSet` | ✅ | `object` | Торговые ограничения (аналогично `create`) |
| `paymentIds` | ✅ | `array[string]` | ID платёжных методов (макс. 5) |
| `actionType` | ✅ | `string` | `MODIFY`: обновить, `ACTIVE`: переопубликовать |
| `quantity` | ✅ | `string` | Объём токенов |
| `paymentPeriod` | ✅ | `string` | Время на оплату (минуты) |

#### Параметры ответа

| Параметр | Тип | Описание |
|----------|-----|----------|
| `securityRiskToken` | `string` | Токен безопасности |
| `riskTokenType` | `string` | Тип токена |
| `riskVersion` | `string` | Версия |
| `needSecurityRisk` | `boolean` | Требуется ли подтверждение |

#### Пример запроса

```http
POST /v5/p2p/item/update HTTP/1.1
Host: api-testnet.bybit.com
X-BAPI-SIGN: XXXXXX
X-BAPI-API-KEY: xxxxxxxxxxxxxxxxxx
X-BAPI-TIMESTAMP: 1741769463827
X-BAPI-RECV-WINDOW: 5000
Content-Type: application/json

{
    "priceType": "0",
    "premium": "",
    "quantity": "1000",
    "minAmount": "25",
    "maxAmount": "5000",
    "paymentPeriod": "15",
    "remark": "",
    "price": "0.914",
    "paymentIds": ["-1"],
    "tradingPreferenceSet": {},
    "actionType": "ACTIVE",
    "id": "1898988222063644672"
}
```

#### Пример ответа

```json
{
    "ret_code": 0,
    "ret_msg": "SUCCESS",
    "result": {
        "securityRiskToken": "",
        "riskTokenType": "",
        "riskVersion": "",
        "needSecurityRisk": false
    }
}
```

---

### 6.3. Получить мои объявления

Возвращает список объявлений текущего пользователя.

**Используется в:** `rebit.exchange` — `ListAdvertisementsUseCase`.

```
POST /v5/p2p/item/personal/list
```

#### Параметры запроса

| Параметр | Обязательный | Тип | Описание |
|----------|:---:|-----|----------|
| `itemId` | ❌ | `string` | ID объявления |
| `status` | ❌ | `string` | `1`: распроданы, `2`: доступны |
| `side` | ❌ | `string` | `0`: покупка, `1`: продажа |
| `tokenId` | ❌ | `string` | ID токена |
| `page` | ❌ | `string` | Страница (по умолчанию `1`) |
| `size` | ❌ | `string` | Размер (по умолчанию `10`, макс. `30`) |
| `currencyId` | ❌ | `string` | ID валюты |

#### Параметры ответа

| Параметр | Тип | Описание |
|----------|-----|----------|
| `count` | `integer` | Общее количество |
| `items` | `array<object>` | Массив объявлений |
| `items[].id` | `string` | ID объявления |
| `items[].userId` | `string` | ID пользователя |
| `items[].nickName` | `string` | Никнейм |
| `items[].tokenId` | `string` | ID токена |
| `items[].currencyId` | `string` | ID валюты |
| `items[].side` | `integer` | `0`: покупка, `1`: продажа |
| `items[].priceType` | `integer` | `0`: фиксированная, `1`: плавающая |
| `items[].price` | `string` | Цена |
| `items[].premium` | `string` | Надбавка (%) |
| `items[].lastQuantity` | `string` | Оставшийся объём |
| `items[].quantity` | `string` | Начальный объём |
| `items[].frozenQuantity` | `string` | Замороженный объём |
| `items[].executedQuantity` | `string` | Исполненный объём |
| `items[].minAmount` | `string` | Мин. сумма |
| `items[].maxAmount` | `string` | Макс. сумма |
| `items[].remark` | `string` | Описание |
| `items[].status` | `integer` | `10`: онлайн, `20`: офлайн, `30`: завершено |
| `items[].createDate` | `string` | Дата создания (timestamp) |
| `items[].payments` | `array<string>` | ID платёжных методов |
| `items[].hiddenReason` | `string` | Причина скрытия (пустая строка — не скрыто) |
| `items[].tradingPreferenceSet` | `object` | Торговые ограничения |
| `items[].updateDate` | `string` | Дата обновления |
| `items[].feeRate` | `string` | Ставка комиссии |
| `items[].paymentPeriod` | `integer` | Время на оплату (мин) |
| `items[].itemType` | `string` | `ORIGIN` / `BULK` |
| `items[].paymentTerms` | `array<object>` | Детали платёжных методов |

#### Пример запроса

```http
POST /v5/p2p/item/personal/list HTTP/1.1
Host: api-testnet.bybit.com
X-BAPI-SIGN: XXXXXX
X-BAPI-API-KEY: xxxxxxxxxxxxxxxxxx
X-BAPI-TIMESTAMP: 1741761792313
X-BAPI-RECV-WINDOW: 5000
Content-Type: application/json

{}
```

---

### 6.4. Получить детали моего объявления

Возвращает полную информацию о конкретном объявлении.

**Используется в:** `rebit.exchange` — детальная страница объявления.

```
POST /v5/p2p/item/info
```

#### Параметры запроса

| Параметр | Обязательный | Тип | Описание |
|----------|:---:|-----|----------|
| `itemId` | ✅ | `string` | ID объявления |

#### Параметры ответа

| Параметр | Тип | Описание |
|----------|-----|----------|
| `id` | `string` | ID объявления |
| `accountId` | `string` | ID аккаунта |
| `userId` | `string` | ID пользователя |
| `nickName` | `string` | Никнейм |
| `tokenId` | `string` | ID токена |
| `currencyId` | `string` | ID валюты |
| `side` | `integer` | `0`: покупка, `1`: продажа |
| `priceType` | `integer` | `0`: фиксированная, `1`: плавающая |
| `price` | `string` | Цена |
| `premium` | `string` | Надбавка |
| `lastQuantity` | `string` | Оставшийся объём |
| `quantity` | `string` | Начальный объём |
| `frozenQuantity` | `string` | Замороженный объём |
| `executedQuantity` | `string` | Исполненный объём |
| `minAmount` | `string` | Мин. сумма |
| `maxAmount` | `string` | Макс. сумма |
| `remark` | `string` | Описание |
| `hiddenReason` | `string` | Причина скрытия |
| `status` | `integer` | `10`: онлайн, `20`: офлайн, `30`: завершено |
| `createDate` | `string` | Дата создания |
| `payments` | `array<string>` | ID платёжных методов |
| `tradingPreferenceSet` | `object` | Торговые ограничения |
| `updateDate` | `string` | Дата обновления |
| `feeRate` | `string` | Ставка комиссии |
| `version` | `integer` | Версия объявления. `1`: с предзаморозкой, `2`: без |
| `paymentPeriod` | `integer` | Время на оплату (мин) |
| `itemType` | `string` | `ORIGIN` / `BULK` |
| `paymentTerms` | `array<object>` | Детали платёжных методов |
| `paymentTerms[].id` | `string` | ID платёжного метода |
| `paymentTerms[].paymentType` | `string` | Тип платёжного метода |

#### Пример запроса

```http
POST /v5/p2p/item/info HTTP/1.1
Host: api-testnet.bybit.com
X-BAPI-SIGN: XXXXXX
X-BAPI-API-KEY: xxxxxxxxxxxxxxxxxx
X-BAPI-TIMESTAMP: 1741767097117
X-BAPI-RECV-WINDOW: 5000
Content-Type: application/json

{
    "itemId": "1898988222063644672"
}
```

---

### 6.5. Удалить объявление

Удаляет (отменяет) объявление.

**Используется в:** `rebit.exchange` — `DeactivateAdvertisementUseCase`.

```
POST /v5/p2p/item/cancel
```

#### Параметры запроса

| Параметр | Обязательный | Тип | Описание |
|----------|:---:|-----|----------|
| `itemId` | ✅ | `string` | ID объявления |

#### Параметры ответа

Нет (поле `result` = `null`).

#### Пример запроса

```http
POST /v5/p2p/item/cancel HTTP/1.1
Host: api-testnet.bybit.com
X-BAPI-SIGN: XXXXXX
X-BAPI-API-KEY: xxxxxxxxxxxxxxxxxx
X-BAPI-TIMESTAMP: 1741769960147
X-BAPI-RECV-WINDOW: 5000
Content-Type: application/json

{
    "itemId": "1899667660027793408"
}
```

#### Пример ответа

```json
{
    "ret_code": 0,
    "ret_msg": "SUCCESS",
    "result": null
}
```

---

## 7. Сделки (ордера)

### 7.1. Получить все ордера

Возвращает список всех P2P-ордеров пользователя (по умолчанию за 90 дней, доступны до 180 дней).

**Используется в:** `rebit.exchange` — `ListTradesUseCase`, `SyncTradeHistoryAgent`.

```
POST /v5/p2p/order/simplifyList
```

#### Параметры запроса

| Параметр | Обязательный | Тип | Описание |
|----------|:---:|-----|----------|
| `page` | ✅ | `integer` | Номер страницы |
| `size` | ✅ | `integer` | Количество записей (макс. `30`) |
| `status` | ❌ | `integer` | Статус ордера (см. ниже) |
| `beginTime` | ❌ | `string` | Начало периода |
| `endTime` | ❌ | `string` | Конец периода |
| `tokenId` | ❌ | `string` | ID токена |
| `side` | ❌ | `integer` | `0`: покупка, `1`: продажа |

**Статусы ордеров:**

| Код | Описание |
|-----|----------|
| `5` | Ожидание подтверждения в блокчейне (только web3) |
| `10` | Ожидание оплаты покупателем |
| `20` | Ожидание выпуска активов продавцом |
| `30` | Апелляция (арбитраж) |
| `40` | Ордер отменён |
| `50` | Ордер завершён |
| `60` | Оплата в процессе (только онлайн-оплата) |
| `70` | Ошибка оплаты (только онлайн-оплата) |
| `80` | Исключительная отмена (конвертация монеты, только hotswap) |
| `90` | Ожидание выбора tokenId покупателем |
| `100` | Возражение |
| `110` | Ожидание подачи возражения |

#### Параметры ответа

| Параметр | Тип | Описание |
|----------|-----|----------|
| `count` | `integer` | Общее количество ордеров |
| `items` | `array<object>` | Массив ордеров |
| `items[].id` | `string` | ID ордера |
| `items[].side` | `integer` | `0`: покупка, `1`: продажа |
| `items[].tokenId` | `string` | ID токена |
| `items[].orderType` | `string` | `ORIGIN` / `SMALL_COIN` / `WEB3` |
| `items[].amount` | `string` | Сумма сделки |
| `items[].currencyId` | `string` | ID валюты |
| `items[].price` | `string` | Цена |
| `items[].fee` | `string` | Комиссия |
| `items[].targetNickName` | `string` | Никнейм контрагента |
| `items[].targetUserId` | `string` | UID контрагента |
| `items[].status` | `integer` | Статус ордера |
| `items[].createDate` | `string` | Время создания |
| `items[].transferLastSeconds` | `string` | Оставшееся время на оплату (сек) |
| `items[].userId` | `string` | UID текущего пользователя |
| `items[].sellerRealName` | `string` | Реальное имя продавца |
| `items[].buyerRealName` | `string` | Реальное имя покупателя |
| `items[].extension` | `object` | Дополнительная информация |
| `items[].extension.isDelayWithdraw` | `boolean` | Задержка вывода |
| `items[].extension.delayTime` | `string` | Время задержки |
| `items[].extension.startTime` | `string` | Начало задержки |

#### Пример запроса

```http
POST /v5/p2p/order/simplifyList HTTP/1.1
Host: api-testnet.bybit.com
X-BAPI-SIGN: XXXXXX
X-BAPI-API-KEY: xxxxxxxxxxxxxxxxxx
X-BAPI-TIMESTAMP: 1741774253544
X-BAPI-RECV-WINDOW: 5000
Content-Type: application/json

{
    "page": 1,
    "size": 10
}
```

---

### 7.2. Получить активные ордера

Возвращает список ордеров в активных (незавершённых) статусах.

**Используется в:** `rebit.exchange` — отображение текущих сделок, `CheckExpiredTradesAgent`.

```
POST /v5/p2p/order/pending/simplifyList
```

#### Параметры запроса

| Параметр | Обязательный | Тип | Описание |
|----------|:---:|-----|----------|
| `page` | ✅ | `integer` | Номер страницы |
| `size` | ✅ | `integer` | Количество записей (макс. `30`) |
| `status` | ❌ | `integer` | Фильтр по статусу (см. статусы выше) |
| `beginTime` | ❌ | `string` | Начало периода |
| `endTime` | ❌ | `string` | Конец периода |
| `tokenId` | ❌ | `string` | ID токена |
| `side` | ❌ | `integer` | `0`: покупка, `1`: продажа |

#### Параметры ответа

Аналогичны [7.1. Получить все ордера](#71-получить-все-ордера).

#### Пример запроса

```http
POST /v5/p2p/order/pending/simplifyList HTTP/1.1
Host: api-testnet.bybit.com
X-BAPI-SIGN: XXXXXX
X-BAPI-API-KEY: xxxxxxxxxxxxxxxxxx
X-BAPI-TIMESTAMP: 1741831424861
X-BAPI-RECV-WINDOW: 5000
Content-Type: application/json

{
    "page": 1,
    "size": 10
}
```

---

### 7.3. Получить детали ордера

Возвращает полную информацию о конкретном P2P-ордере.

**Используется в:** `rebit.exchange` — `GetTradeUseCase`, страница сделки.

```
POST /v5/p2p/order/info
```

#### Параметры запроса

| Параметр | Обязательный | Тип | Описание |
|----------|:---:|-----|----------|
| `orderId` | ✅ | `string` | ID ордера |

#### Параметры ответа

| Параметр | Тип | Описание |
|----------|-----|----------|
| `id` | `string` | ID ордера |
| `side` | `int` | `0`: покупка, `1`: продажа |
| `itemId` | `string` | ID объявления |
| `userId` | `string` | UID текущего пользователя |
| `nickName` | `string` | Никнейм текущего пользователя |
| `makerUserId` | `string` | UID владельца объявления |
| `targetUserId` | `string` | UID контрагента |
| `targetNickName` | `string` | Никнейм контрагента |
| `targetConnectInformation` | `string` | Контактная информация контрагента |
| `sellerRealName` | `string` | Реальное имя продавца |
| `buyerRealName` | `string` | Реальное имя покупателя |
| `tokenId` | `string` | ID токена |
| `currencyId` | `string` | ID валюты |
| `price` | `string` | Цена ордера |
| `quantity` | `string` | Объём токена |
| `amount` | `string` | Сумма в фиате |
| `paymentType` | `int` | Используемый платёжный метод |
| `transferDate` | `string` | Время оплаты покупателем |
| `status` | `int` | Статус ордера |
| `createDate` | `string` | Дата создания |
| `paymentTermList` | `array<object>` | Платёжные методы продавца |
| `paymentTermList[].id` | `string` | ID платёжного метода |
| `paymentTermList[].realName` | `string` | Реальное имя |
| `paymentTermList[].paymentType` | `int` | Тип платежа |
| `paymentTermList[].bankName` | `string` | Название банка |
| `paymentTermList[].branchName` | `string` | Название отделения |
| `paymentTermList[].accountNo` | `string` | Номер счёта |
| `paymentTermList[].qrcode` | `string` | URL QR-кода |
| `remark` | `string` | Описание |
| `transferLastSeconds` | `string` | Оставшееся время на оплату (сек) |
| `appealContent` | `string` | Содержание апелляции |
| `appealType` | `int` | Тип апелляции |
| `appealNickName` | `string` | Никнейм подавшего апелляцию |
| `canAppeal` | `string` | Может ли пользователь подать апелляцию |
| `confirmedPayTerm` | `object` | Выбранный покупателем платёжный метод |
| `makerFee` | `string` | Комиссия мейкера |
| `takerFee` | `string` | Комиссия тейкера |
| `extension` | `object` | Дополнительная информация |
| `orderType` | `string` | `ORIGIN` / `SMALL_COIN` / `WEB3` |
| `appealUserId` | `string` | UID подавшего апелляцию |
| `notifyTokenId` | `string` | Токен получения покупателем |
| `notifyTokenQuantity` | `string` | Объём токена для покупателя |
| `cancelReason` | `string` | Причина отмены |
| `usedCoupon` | `bool` | Использован ли купон |
| `couponTokenId` | `string` | ID токена купона |
| `couponQuantity` | `string` | Объём купона |
| `targetUserType` | `string` | Тип контрагента: `PERSONAL` / `ORG` |

#### Пример запроса

```http
POST /v5/p2p/order/info HTTP/1.1
Host: api-testnet.bybit.com
X-BAPI-SIGN: XXXXXX
X-BAPI-API-KEY: xxxxxxxxxxxxxxxxxx
X-BAPI-TIMESTAMP: 1741832094881
X-BAPI-RECV-WINDOW: 5000
Content-Type: application/json

{
    "orderId": "1900004704665923584"
}
```

---

### 7.4. Отметить ордер как оплаченный

Уведомляет продавца о том, что покупатель совершил оплату.

**Используется в:** `rebit.exchange` — `ConfirmPaymentUseCase`.

> ⚠️ **Ограничение:** на данный момент платёжный метод «Balance» не поддерживается Bybit P2P API.

```
POST /v5/p2p/order/pay
```

#### Параметры запроса

| Параметр | Обязательный | Тип | Описание |
|----------|:---:|-----|----------|
| `orderId` | ✅ | `string` | ID ордера |
| `paymentType` | ✅ | `string` | ID типа платёжного метода |
| `paymentId` | ✅ | `string` | ID конкретного платёжного метода |

#### Параметры ответа

Нет (поле `result` = `null`).

#### Пример запроса

```http
POST /v5/p2p/order/pay HTTP/1.1
Host: api-testnet.bybit.com
X-BAPI-SIGN: XXXXX
X-BAPI-API-KEY: xxxxxxxxxxxxxxxxxx
X-BAPI-TIMESTAMP: 1675866354698
X-BAPI-RECV-WINDOW: 5000
Content-Type: application/json

{
    "orderId": "1899736339155943424",
    "paymentType": "14",
    "paymentId": "7110"
}
```

#### Пример ответа

```json
{
    "ret_code": 0,
    "ret_msg": "SUCCESS",
    "result": null
}
```

---

### 7.5. Выпустить активы (подтвердить получение)

Продавец подтверждает получение оплаты и выпускает криптовалюту покупателю.

**Используется в:** `rebit.exchange` — `ConfirmReceiptUseCase`.

```
POST /v5/p2p/order/finish
```

#### Параметры запроса

| Параметр | Обязательный | Тип | Описание |
|----------|:---:|-----|----------|
| `orderId` | ✅ | `string` | ID ордера |

#### Параметры ответа

Нет (поле `result` = `null`).

#### Пример запроса

```http
POST /v5/p2p/order/finish HTTP/1.1
Host: api-testnet.bybit.com
X-BAPI-SIGN: XXXXX
X-BAPI-API-KEY: xxxxxxxxxxxxxxxxxx
X-BAPI-TIMESTAMP: 1675866354698
X-BAPI-RECV-WINDOW: 5000
Content-Type: application/json

{
    "orderId": "1899736339155943424"
}
```

#### Пример ответа

```json
{
    "ret_code": 0,
    "ret_msg": "SUCCESS",
    "result": null
}
```

---

## 8. Чат сделки

### 8.1. Отправить сообщение в чат

Отправляет текстовое сообщение или файл в чат P2P-ордера.

**Используется в:** `rebit.exchange` — `SendMessageUseCase`, `ExecuteChatScriptUseCase`.

```
POST /v5/p2p/order/message/send
```

#### Параметры запроса

| Параметр | Обязательный | Тип | Описание |
|----------|:---:|-----|----------|
| `message` | ✅ | `string` | Текст сообщения или URL файла (полученный через Upload) |
| `contentType` | ✅ | `string` | Тип содержимого: `str` (текст), `pic` (изображение), `pdf` (PDF), `video` (видео) |
| `orderId` | ✅ | `string` | ID ордера |
| `msgUuid` | ✅ | `string` | UUID сообщения (для дедупликации, генерируется клиентом) |
| `fileName` | ❌ | `string` | Имя файла (для изображений/PDF/видео) |

#### Параметры ответа

Нет (поле `result` = `null`).

#### Пример запроса

```http
POST /v5/p2p/order/message/send HTTP/1.1
Host: api-testnet.bybit.com
X-BAPI-SIGN: XXXXX
X-BAPI-API-KEY: xxxxxxxxxxxxxxxxxx
X-BAPI-TIMESTAMP: 1675866354698
X-BAPI-RECV-WINDOW: 5000
Content-Type: application/json

{
    "message": "Здравствуйте! Готов к оплате.",
    "contentType": "str",
    "orderId": "1898976123321221120",
    "msgUuid": "cf016044-c754-4be0-b2dc-b403ecce150d"
}
```

#### Пример ответа

```json
{
    "ret_code": 0,
    "ret_msg": "SUCCESS",
    "result": null
}
```

---

### 8.2. Загрузить файл для чата

Загружает файл (изображение, PDF, видео) для отправки в чат.

**Используется в:** `rebit.exchange` — загрузка медиа перед отправкой в чат сделки.

```
POST /v5/p2p/oss/upload_file
```

> 💡 **Порядок работы с файлами:**
> 1. Загрузить файл через этот эндпоинт → получить URL
> 2. Отправить сообщение через [Send Chat Message](#81-отправить-сообщение-в-чат) с `contentType` = `pic`/`pdf`/`video` и полученным URL в `message`

#### Параметры запроса

| Параметр | Обязательный | Тип | Описание |
|----------|:---:|-----|----------|
| `upload_file` | ✅ | `MultipartFile` | Файл. Поддерживаемые форматы: `jpg`, `png`, `jpeg`, `pdf`, `mp4` |

#### Параметры ответа

| Параметр | Тип | Описание |
|----------|-----|----------|
| `url` | `string` | URL загруженного файла |
| `type` | `string` | Тип файла (`IMAGE`, и др.) |

#### Пример запроса

```http
POST /v5/p2p/oss/upload_file HTTP/1.1
Host: api-testnet.bybit.com
Content-Type: multipart/form-data; boundary=boundary-for-file
X-BAPI-API-KEY: xxxxxxxxxxxxxxxxxx
X-BAPI-SIGN: XXXXX
X-BAPI-TIMESTAMP: 1742311654006
X-BAPI-RECV-WINDOW: 5000

--boundary-for-file
Content-Disposition: form-data; name="upload_file"; filename="screenshot.png"
Content-Type: image/png

<бинарное содержимое>
--boundary-for-file--
```

#### Пример ответа

```json
{
    "ret_code": 0,
    "ret_msg": "成功",
    "result": {
        "uploadId": null,
        "type": "IMAGE",
        "url": "/fiat/p2p/oss/showObj/otc/9001/100571889JXxfevzu0QqH93PeleC7F4y89zX2U9Qu-lq5Gt3JJxg.png?e=1742570854&token=..."
    }
}
```

---

## 9. Ограничения Bybit P2P API

При сопоставлении пользовательских сценариев (`scenario.md`) с доступными эндпоинтами Bybit P2P API выявлены операции, **для которых Bybit не предоставляет API-эндпоинтов**. Эти ограничения необходимо учитывать при проектировании архитектуры.

### 9.1. Создание ордера (сделки) по объявлению

**Сценарий:** Пользователь кликает на предложение в стакане и создаёт сделку (Сценарий 3).

**Статус:** ❌ Эндпоинт **не существует** в Bybit P2P API.

**Влияние на архитектуру:**
- `CreateTradeUseCase` не может автоматически создавать сделку через API
- **Решение:** создание сделки происходит на стороне Bybit (через UI биржи или партнёрские механизмы). Rebit **отслеживает появление новых ордеров** через `POST /v5/p2p/order/pending/simplifyList` и синхронизирует их в локальную БД

### 9.2. Отмена ордера

**Сценарий:** Покупатель отменяет сделку до истечения таймера (Сценарий 4).

**Статус:** ❌ Эндпоинт **не существует** в Bybit P2P API.

**Влияние на архитектуру:**
- `CancelTradeUseCase` и `CheckExpiredTradesAgent` не могут программно отменить сделку
- **Решение:** отмена происходит автоматически по таймеру на стороне Bybit. Rebit **отслеживает смену статуса** через polling `order/info` или `order/pending/simplifyList` и обновляет локальный статус

### 9.3. Получение истории сообщений чата

**Сценарий:** Загрузка истории чата при открытии страницы сделки (Сценарий 11).

**Статус:** ❌ Эндпоинт **не существует** в Bybit P2P API (или не документирован).

**Влияние на архитектуру:**
- `GetChatHistoryUseCase` не может подгружать историю из Bybit
- **Решение:** все сообщения, отправленные через `POST /v5/p2p/order/message/send`, **дублируются в локальную БД** (`rebit_trade_message`). История чата читается из локального хранилища. Сообщения, отправленные контрагентом через UI Bybit, **не будут отображаться** в Rebit

### 9.4. Получение платёжных методов пользователя

**Сценарий:** Список привязанных платёжных методов пользователя для создания объявления.

**Статус:** ❌ Отдельный эндпоинт **не существует** в Bybit P2P API.

**Влияние на архитектуру:**
- Платёжные методы можно получить **косвенно** из:
  - `POST /v5/p2p/item/personal/list` → поле `paymentTerms` в каждом объявлении
  - `POST /v5/p2p/order/info` → поле `paymentTermList`
  - `POST /v5/p2p/user/personal/info` → поле `paymentCount` (только количество)
- **Решение:** при подключении аккаунта — извлечь `paymentTerms` из существующих объявлений пользователя. При создании объявления — передавать `paymentIds` (ID типов платёжных методов), которые пользователь знает из UI Bybit

### 9.5. Открытие арбитража (апелляция)

**Сценарий:** Пользователь открывает спор при неподтверждённой оплате (Сценарий 4).

**Статус:** ❌ Эндпоинт **не существует** в Bybit P2P API.

**Влияние на архитектуру:**
- `OpenDisputeUseCase` не может программно инициировать арбитраж
- **Решение:** при необходимости арбитража — **перенаправлять пользователя в UI Bybit**. Статус `disputed` (status=30) будет определён через polling `order/info`

### 9.6. Справочник платёжных методов

**Сценарий:** Получение глобального списка платёжных методов для валютной пары.

**Статус:** ❌ Отдельный эндпоинт **не существует** в Bybit P2P API.

**Влияние на архитектуру:**
- **Решение:** справочник `rebit_payment_method` заполняется **вручную или из ответов других эндпоинтов** (поля `payments`, `paymentTerms` из объявлений и ордеров). Данные по `symbolInfo` в ответе `POST /v5/p2p/item/online` содержат косвенные данные о валютных парах, но не о платёжных методах

---

### Сводная таблица ограничений

| Операция | Статус в API | Решение в Rebit |
|----------|:---:|---|
| Создание сделки | ❌ | Polling новых ордеров через `order/pending/simplifyList` |
| Отмена ордера | ❌ | Polling статуса, таймер на стороне Bybit |
| История чата | ❌ | Локальное хранение отправленных сообщений в `rebit_trade_message` |
| Платёжные методы пользователя | ❌ | Извлечение из `paymentTerms` существующих объявлений |
| Открытие арбитража | ❌ | Редирект в UI Bybit, polling статуса `disputed` |
| Справочник платёжных методов | ❌ | Ручное заполнение + парсинг из ответов API |

> ⚠️ **Вывод:** Bybit P2P API — **read-heavy**: предоставляет полноценные средства для чтения стаканов, ордеров, информации о пользователях, а также позволяет управлять объявлениями, отмечать оплату, выпускать активы и отправлять сообщения. Но **создание сделок, их отмена и арбитраж** остаются за пределами API.

---

## 10. Маппинг: Bybit API → Rebit-модули

Таблица соответствия доступных эндпоинтов Bybit API и внутренних модулей/юзкейсов Rebit.

### Identity

| Bybit API | Rebit UseCase | Описание |
|-----------|---------------|----------|
| `POST /v5/p2p/user/personal/info` | `VerifyApiUseCase` | Тестовый запрос при подключении ключей |

### Exchange — Стакан

| Bybit API | Rebit UseCase / Agent | Описание |
|-----------|----------------------|----------|
| `POST /v5/p2p/item/online` | `GetOrderBookUseCase`, `SyncOrderBookAgent` | Загрузка/синхронизация стакана |

### Exchange — Объявления

| Bybit API | Rebit UseCase | Описание |
|-----------|---------------|----------|
| `POST /v5/p2p/item/create` | `CreateAdvertisementUseCase` | Создание объявления |
| `POST /v5/p2p/item/update` | `UpdateAdvertisementUseCase` | Обновление/переопубликация |
| `POST /v5/p2p/item/personal/list` | `ListAdvertisementsUseCase` | Список моих объявлений |
| `POST /v5/p2p/item/info` | — | Детали объявления |
| `POST /v5/p2p/item/cancel` | `DeactivateAdvertisementUseCase` | Удаление объявления |

### Exchange — Сделки

| Bybit API | Rebit UseCase / Agent | Описание |
|-----------|----------------------|----------|
| `POST /v5/p2p/order/simplifyList` | `ListTradesUseCase`, `SyncTradeHistoryAgent` | Список/синхронизация ордеров |
| `POST /v5/p2p/order/pending/simplifyList` | `ListTradesUseCase`, `CheckExpiredTradesAgent` | Активные ордера |
| `POST /v5/p2p/order/info` | `GetTradeUseCase` | Детали ордера |
| `POST /v5/p2p/order/pay` | `ConfirmPaymentUseCase` | Отметка «Я оплатил» |
| `POST /v5/p2p/order/finish` | `ConfirmReceiptUseCase` | Выпуск активов |

### Exchange — Чат

| Bybit API | Rebit UseCase | Описание |
|-----------|---------------|----------|
| `POST /v5/p2p/order/message/send` | `SendMessageUseCase`, `ExecuteChatScriptUseCase` | Отправка сообщения |
| `POST /v5/p2p/oss/upload_file` | — | Загрузка файла для чата |

### Exchange — Контрагент

| Bybit API | Rebit UseCase | Описание |
|-----------|---------------|----------|
| `POST /v5/p2p/user/order/personal/info` | — | Информация о контрагенте |

### Wallet

| Bybit API | Rebit UseCase / Agent | Описание |
|-----------|----------------------|----------|
| `GET /v5/asset/transfer/query-account-coins-balance` | `SyncBalancesUseCase`, `SyncBalancesAgent` | Синхронизация балансов |

### Операции без API (реализация на стороне Rebit)

| Операция | Rebit UseCase / Agent | Реализация |
|----------|----------------------|------------|
| Создание сделки | `CreateTradeUseCase` | Polling `order/pending/simplifyList` |
| Отмена сделки | `CancelTradeUseCase`, `CheckExpiredTradesAgent` | Polling статуса через `order/info` |
| История чата | `GetChatHistoryUseCase` | Чтение из `rebit_trade_message` |
| Арбитраж | `OpenDisputeUseCase` | Редирект в UI Bybit |

---

## Приложение А. Маппинг статусов: Bybit → Rebit

| Bybit `status` | Описание Bybit | Rebit `TradeStatusEnum` |
|:-:|---|---|
| `10` | Ожидание оплаты покупателем | `pending_payment` |
| `20` | Ожидание выпуска продавцом | `payment_sent` → `payment_confirmed` |
| `30` | Апелляция | `disputed` |
| `40` | Отменён | `cancelled` |
| `50` | Завершён | `completed` |
| `60` | Оплата в процессе | `pending_payment` |
| `70` | Ошибка оплаты | `pending_payment` |

---

## Приложение Б. Справочник кодов ошибок

Все ответы Bybit содержат `ret_code`. При `ret_code !== 0` следует обрабатывать ошибку:

| `ret_code` | Описание |
|:---:|---|
| `0` | Успех |
| `10001` | Ошибка параметров |
| `10003` | Ошибка подписи |
| `10004` | Ошибка IP-адреса |
| `10005` | Недействительный API-ключ |
| `33004` | Недостаточно средств |

> ℹ️ Полный список кодов см. в [официальной документации Bybit](https://bybit-exchange.github.io/docs/).
