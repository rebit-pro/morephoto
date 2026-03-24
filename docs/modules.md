# Модули Bitrix: P2P-платформа Rebit

> Модули лежат в `api/public/local/modules/rebit.<name>/`.
> Каждый бизнес-модуль соответствует одному Bounded Context из [domain.md](domain.md).
> Общая инфраструктура и межмодульные контракты — в `rebit.share`.
> Архитектурные правила описаны в [architecture-guide](architecture-guide/README.md).

---

## 1. Карта модулей

| # | Модуль | Назначение | Namespace | Зависимости | Статус |
|---|--------|------------|-----------|-------------|--------|
| 1 | `rebit.share` | Общая инфраструктура, контракты | `Rebit\Share` | — | ✅ |
| 2 | `rebit.auth` | Аутентификация, токены | `Rebit\Auth` | `rebit.share` | ✅ |
| 3 | `rebit.bybit` | Клиент Bybit API | `Rebit\Bybit` | `rebit.share` | ✅ |
| 4 | `rebit.identity` | Управление API-ключами Bybit | `Rebit\Identity` | `rebit.share` | ✅ |
| 5 | `rebit.wallet` | Балансы, транзакции | `Rebit\Wallet` | `rebit.share` | ✅ |
| 6 | `rebit.dev` | Инструменты разработки | `Rebit\Dev` | — | ✅ |
| 7 | `rebit.exchange` | P2P-торговля, сделки, чат | `Rebit\Exchange` | `rebit.share` | 🔜 запланирован |
| 8 | `rebit.notification` | Уведомления, каналы доставки | `Rebit\Notification` | `rebit.share` | 🔜 запланирован |
| 9 | `rebit.security` | Сессии, 2FA, аудит | `Rebit\Security` | `rebit.share` | 🔜 запланирован |

### Правило зависимостей

Все бизнес-модули зависят **только** от `rebit.share`.
Межмодульное взаимодействие идёт через контракты в `rebit.share/lib/Application/Contract/`.
Подробнее: [06. Кросс-доменное взаимодействие](architecture-guide/06_кросс-доменное-взаимодействие.md).

### Граф зависимостей

```
rebit.share
    ▲
    ├── rebit.auth           (реализует TokenResolverInterface)
    ├── rebit.bybit          (реализует BybitClientInterface)
    ├── rebit.identity       (реализует BybitConnectionResolverInterface)
    ├── rebit.wallet         (потребляет BybitClientInterface, BybitConnectionResolverInterface; реализует BalanceQueryInterface)
    ├── rebit.exchange       (потребляет BybitClientInterface, BybitConnectionResolverInterface, BalanceQueryInterface)
    ├── rebit.notification   (запланирован)
    └── rebit.security       (запланирован)
```

---

## 2. Межмодульные контракты (`rebit.share`)

Контракты определяют границы между модулями. Каждый контракт — интерфейс и набор DTO в `rebit.share`.
Модуль-поставщик реализует контракт в `Infrastructure/Adapter/`, модуль-потребитель зависит только от интерфейса.

### Существующие контракты

| Контракт | Путь в `rebit.share` | Реализация | Потребители |
|----------|----------------------|------------|-------------|
| `TokenResolverInterface` | `Application/Contract/Auth/` | `rebit.auth` → `TokenResolver` | Middleware аутентификации |
| `BybitClientInterface` | `Application/Contract/Bybit/` | `rebit.bybit` → `BybitApiClient` | `rebit.identity`, `rebit.wallet` |
| `BybitConnectionResolverInterface` | `Application/Contract/Bybit/` | `rebit.identity` → `BybitConnectionResolver` | `rebit.wallet` |
| `CacheCleanerInterface` | `Application/Contract/Cache/` | `rebit.share` → Infrastructure | Все модули |
| `FileServiceInterface` | `Application/Contract/File/` | ⚠️ не реализован | — |

### Планируемые контракты (для `rebit.exchange`)

| Контракт | Путь в `rebit.share` | Поставщик | Потребитель | Назначение |
|----------|----------------------|-----------|-------------|------------|
| `BalanceQueryInterface` | `Application/Contract/Wallet/` | `rebit.wallet` | `rebit.exchange` | Проверка достаточности баланса перед созданием объявления |
| `NotificationDispatcherInterface` | `Application/Contract/Notification/` | `rebit.notification` | `rebit.exchange` | Отправка уведомлений о событиях сделки (контракт создаётся заранее, реализация — при создании модуля) |

### Структура контракта (пример: Bybit)

```
rebit.share/lib/Application/Contract/Bybit/
├── BybitClientInterface.php
├── BybitConnectionResolverInterface.php
├── BybitConnectionDto.php
├── BybitCredentials.php
├── BybitEnvironmentEnum.php
├── BybitResponseDto.php
└── BybitApiException.php
```

### Как это работает в DI

Модуль-поставщик регистрирует реализацию контракта по ключу-интерфейсу:

```php
// rebit.identity/di/connection.php
BybitConnectionResolverInterface::class => [
    'constructor' => static function(): BybitConnectionResolverInterface {
        $sl = ServiceLocator::getInstance();

        return new BybitConnectionResolver(
            $sl->get(ApiConnectionRepository::class),
            $sl->get(ApiKeyEncryptor::class),
        );
    },
],
```

Модуль-потребитель получает зависимость через контейнер:

```php
// rebit.wallet/di/balance.php
BybitBalanceGatewayInterface::class => [
    'constructor' => static function(): BybitBalanceGatewayInterface {
        $sl = ServiceLocator::getInstance();

        return new BybitBalanceGateway(
            $sl->get(BybitConnectionResolverInterface::class),
            $sl->get(BybitClientInterface::class),
        );
    },
],
```

---

## 3. Структура модуля

Все модули следуют единой структуре из [03. Структура модуля и DI](architecture-guide/03_структура-модуля-и-di.md):

```
rebit.<name>/
├── .settings.php          # Точка входа Bitrix для DI, подключает файлы из di/
├── include.php            # Bootstrap: проверка зависимостей, регистрация событий
├── routes.php             # HTTP-маршруты (если есть API)
├── install/
│   └── index.php          # Установщик модуля
├── di/
│   ├── Layers/            # DI по техническим слоям (Infrastructure, Presentation, Shared)
│   └── <Domain>.php       # DI по предметным зонам
└── lib/
    ├── Application/       # Сценарии, DTO, порты
    │   └── <Domain>/
    │       ├── UseCase/
    │       ├── Dto/
    │       │   ├── Request/
    │       │   └── Result/
    │       └── Port/
    │           └── Outgoing/
    ├── Domain/            # Предметная модель
    │   └── <Domain>/
    │       ├── Entity/
    │       │   ├── Table/
    │       │   ├── <Name>.php
    │       │   └── <Name>Collection.php
    │       ├── Repository/
    │       ├── Service/
    │       ├── Enum/
    │       ├── Event/
    │       └── ValueObject/
    ├── Infrastructure/    # Адаптеры, интеграции
    │   ├── Adapter/
    │   ├── Bridge/
    │   └── <Integration>/
    └── Presentation/      # Контроллеры, команды
        ├── Controller/
        └── Command/
```

### Ключевые правила

- Контроллеры — в `Presentation/Controller/`, наследуют `BaseJsonController`.
- UseCase — `final readonly class`, принимает Request DTO, возвращает Result DTO.
- Репозитории — в `Domain/<Domain>/Repository/`, возвращают Entity, скалярные значения или DTO.
- Адаптеры межмодульных контрактов — в `Infrastructure/Adapter/`.
- `declare(strict_types=1)` во всех файлах.
- Все классы `final readonly` где возможно (Entity и Collection — `final`, но не `readonly`, т.к. наследуют Bitrix ORM).
- DI разбит по доменам и слоям в `di/`, собирается в `.settings.php`.

---

## 4. Модуль `rebit.share`

**Назначение:** общая инфраструктура, базовые классы, межмодульные контракты, хелперы.

Не содержит бизнес-логики. Предоставляет фундамент, от которого зависят все остальные модули.

### Структура

```
rebit.share/
├── .settings.php
├── include.php
├── routes.php
├── di/
│   ├── Layers/
│   │   └── Infrastructure.php
│   └── file.php
└── lib/
    ├── Application/
    │   ├── Contract/                          # Межмодульные контракты
    │   │   ├── Auth/
    │   │   │   └── TokenResolverInterface.php
    │   │   ├── Bybit/
    │   │   │   ├── BybitClientInterface.php
    │   │   │   ├── BybitConnectionResolverInterface.php
    │   │   │   ├── BybitConnectionDto.php
    │   │   │   ├── BybitCredentials.php
    │   │   │   ├── BybitEnvironmentEnum.php
    │   │   │   ├── BybitResponseDto.php
    │   │   │   └── BybitApiException.php
    │   │   ├── Cache/
    │   │   │   └── CacheCleanerInterface.php
    │   │   └── File/
    │   │       └── FileServiceInterface.php
    │   ├── Interface/
    │   │   ├── RequestDtoInterface.php
    │   │   └── ResultDtoInterface.php
    │   ├── Collection/
    │   │   └── AbstractRequestCollection.php
    │   └── UseCase/
    │       └── UploadFileUseCase.php
    ├── Domain/
    │   └── File/
    ├── Infrastructure/
    │   ├── Bitrix/
    │   │   ├── Module/
    │   │   │   ├── ModuleHelper.php
    │   │   │   ├── ModuleRoutingTrait.php
    │   │   │   └── ModuleComponentInstallerTrait.php
    │   │   ├── Cache/
    │   │   ├── ControllerJson.php
    │   │   └── ControllerBuilder.php
    │   ├── Controller/
    │   │   ├── BaseJsonController.php
    │   │   ├── AbstractJsonController.php
    │   │   ├── AbstractController.php
    │   │   ├── Auth/
    │   │   ├── Request/
    │   │   ├── Responses/
    │   │   ├── Normalizer/
    │   │   ├── Serializers/
    │   │   ├── Filters/
    │   │   └── Attribute/
    │   ├── Repository/
    │   │   ├── AbstractHLBlockRepository.php
    │   │   ├── AbstractRepository.php
    │   │   └── RepositoryExceptionTrait.php
    │   ├── Helpers/
    │   │   ├── DtoMapper.php
    │   │   ├── ValidationHelper.php
    │   │   ├── RequestHelper.php
    │   │   ├── NormalizerHelper.php
    │   │   └── JsonSerializerHelper.php
    │   ├── HttpClient/
    │   ├── Serializer/
    │   ├── Logger/
    │   ├── Factory/
    │   ├── Dto/
    │   └── Exception/
    ├── Presentation/
    │   ├── Controller/
    │   │   └── FileController.php
    │   └── Command/
    │       └── RebitCommand.php
    └── Shared/
        ├── Facade/
        │   ├── Log.php
        │   └── Cache.php
        ├── Enum/
        │   ├── LogChannelEnum.php
        │   └── HttpMethodEnum.php
        ├── Exception/
        │   ├── HttpException.php
        │   ├── RebitException.php
        │   └── RepositoryException.php
        ├── Helper/
        ├── Dto/
        ├── Interface/
        └── ValueObject/
```

### Что предоставляет

| Категория | Что | Пример |
|-----------|-----|--------|
| Базовые контроллеры | HTTP-слой для всех модулей | `BaseJsonController`, `AbstractJsonController` |
| Базовые репозитории | ORM-обёртки | `AbstractHLBlockRepository`, `AbstractRepository` |
| Контракты | Межмодульные интерфейсы | `BybitClientInterface`, `TokenResolverInterface` |
| DTO-интерфейсы | Стандарт для входа/выхода | `RequestDtoInterface`, `ResultDtoInterface` |
| Module-трейты | Установка/маршруты модулей | `ModuleRoutingTrait`, `ModuleComponentInstallerTrait` |
| Хелперы | Маппинг, валидация, сериализация | `DtoMapper`, `ValidationHelper` |
| Фасады | Логирование, кэширование | `Log`, `Cache` |
| CLI-команды | Базовый класс команд | `RebitCommand` |

---

## 5. Модуль `rebit.auth`

**Домен:** Auth — аутентификация пользователей, управление токенами.

### Структура

```
rebit.auth/
├── .settings.php
├── include.php
├── routes.php
├── di/
│   └── auth.php
└── lib/
    ├── Application/
    │   └── Auth/
    │       ├── UseCase/
    │       │   ├── LoginUseCase.php
    │       │   └── LogoutUseCase.php
    │       └── Dto/
    │           ├── Request/
    │           └── Result/
    ├── Domain/
    │   └── User/
    │       ├── Entity/
    │       │   ├── UserToken.php
    │       │   └── UserCredentials.php
    │       ├── Repository/
    │       │   └── UserRepository.php
    │       └── Service/
    │           └── TokenGenerator.php
    ├── Infrastructure/
    │   └── Adapter/
    │       └── TokenResolver.php              # реализует TokenResolverInterface
    └── Presentation/
        └── Controller/
            └── AuthController.php
```

### Реализуемые контракты

| Контракт из `rebit.share` | Адаптер |
|----------------------------|---------|
| `TokenResolverInterface` | `Infrastructure/Adapter/TokenResolver` |

### Маршруты

```
POST   /api/v1/auth/login     → LoginUseCase
POST   /api/v1/auth/logout    → LogoutUseCase
```

### DI (di/auth.php)

```php
UserRepository::class
TokenGenerator::class
TokenResolverInterface::class → TokenResolver
LoginUseCase::class
LogoutUseCase::class
AuthController::class
```

---

## 6. Модуль `rebit.bybit`

**Назначение:** инфраструктурный модуль — HTTP-клиент для Bybit API.

Не содержит доменной логики. Реализует контракт `BybitClientInterface` из `rebit.share`.

### Структура

```
rebit.bybit/
├── .settings.php
├── include.php
├── di/
│   └── client.php
└── lib/
    └── Infrastructure/
        ├── Client/
        │   ├── BybitApiClient.php
        │   └── BybitApiClientFactory.php
        └── Auth/
            └── HmacSignatureGenerator.php
```

### Реализуемые контракты

| Контракт из `rebit.share` | Адаптер |
|----------------------------|---------|
| `BybitClientInterface` | `Infrastructure/Client/BybitApiClient` |

### DI (di/client.php)

```php
BybitClientInterface::class => [
    'constructor' => static function(): BybitClientInterface {
        return BybitApiClientFactory::create(
            Log::channel(LogChannelEnum::bybit),
        );
    },
],
```

---

## 7. Модуль `rebit.identity`

**Домен:** Identity — управление API-ключами Bybit, статус подключения.

### Структура

```
rebit.identity/
├── .settings.php
├── include.php
├── routes.php
├── orm_annotation.php
├── di/
│   └── connection.php
└── lib/
    ├── Application/
    │   └── ApiConnection/
    │       ├── UseCase/
    │       │   ├── ConnectApiUseCase.php
    │       │   ├── DisconnectApiUseCase.php
    │       │   ├── VerifyApiUseCase.php
    │       │   └── GetConnectionStatusUseCase.php
    │       └── Dto/
    │           ├── Request/
    │           │   └── ConnectApiRequestDto.php
    │           └── Result/
    │               └── ApiConnectionResultDto.php
    ├── Domain/
    │   └── ApiConnection/
    │       ├── Entity/
    │       │   ├── Table/
    │       │   │   └── ApiConnectionTable.php
    │       │   ├── ApiConnection.php
    │       │   └── ApiConnectionCollection.php
    │       ├── Repository/
    │       │   └── ApiConnectionRepository.php
    │       ├── Enum/
    │       │   ├── ConnectionStatusEnum.php
    │       │   └── ConnectionModeEnum.php
    │       ├── Event/
    │       │   ├── ApiConnectionCreated.php
    │       │   ├── ApiConnectionRevoked.php
    │       │   └── ApiConnectionFailed.php
    │       └── Service/
    │           ├── ApiKeyEncryptor.php
    │           └── ApiKeyMasker.php
    ├── Infrastructure/
    │   ├── Adapter/
    │   │   └── BybitConnectionResolver.php    # реализует BybitConnectionResolverInterface
    │   └── Controller/
    │       └── BaseIdentityController.php
    └── Presentation/
        └── Controller/
            └── ApiConnectionController.php
```

### Реализуемые контракты

| Контракт из `rebit.share` | Адаптер |
|----------------------------|---------|
| `BybitConnectionResolverInterface` | `Infrastructure/Adapter/BybitConnectionResolver` |

### Потребляемые контракты

| Контракт из `rebit.share` | Где используется |
|----------------------------|------------------|
| `BybitClientInterface` | `ConnectApiUseCase`, `VerifyApiUseCase` |

### Маршруты

```
POST   /api/v1/identity/connection          → ConnectApiUseCase
DELETE /api/v1/identity/connection          → DisconnectApiUseCase
POST   /api/v1/identity/connection/verify   → VerifyApiUseCase
GET    /api/v1/identity/connection/status   → GetConnectionStatusUseCase
```

### HL-блоки

| HL-блок | Таблица |
|---------|---------|
| `RebitApiConnection` | `rebit_api_connection` |

### DI (di/connection.php)

```php
ApiKeyEncryptor::class
ApiKeyMasker::class
ApiConnectionRepository::class
ConnectApiUseCase::class
DisconnectApiUseCase::class
VerifyApiUseCase::class
GetConnectionStatusUseCase::class
BybitConnectionResolverInterface::class → BybitConnectionResolver
ApiConnectionController::class
```

---

## 8. Модуль `rebit.wallet`

**Домен:** Wallet — балансы пользователя, блокировки средств, транзакции.

> **Связь с Bybit API:** Bybit предоставляет **только чтение** балансов (`GET /v5/asset/transfer/query-account-coins-balance`).
> При создании объявления Bybit **сам замораживает** средства (`frozenQuantity`).
> `LockFundsUseCase` / `UnlockFundsUseCase` — это локальная предварительная проверка и учёт.
> `SyncBalancesUseCase` синхронизирует данные Bybit → локальную БД, при расхождении — приоритет Bybit.

### Структура

```
rebit.wallet/
├── .settings.php
├── include.php
├── routes.php
├── orm_annotation.php
├── di/
│   ├── balance.php
│   └── transaction.php
└── lib/
    ├── Application/
    │   ├── Balance/
    │   │   ├── UseCase/
    │   │   │   ├── GetBalancesUseCase.php
    │   │   │   ├── SyncBalancesUseCase.php
    │   │   │   ├── LockFundsUseCase.php
    │   │   │   └── UnlockFundsUseCase.php
    │   │   ├── Dto/
    │   │   │   ├── Request/
    │   │   │   │   └── LockFundsDto.php
    │   │   │   └── Result/
    │   │   │       ├── BalanceResultDto.php
    │   │   │       └── BalanceListResultDto.php
    │   │   └── Port/
    │   │       └── BybitBalanceGatewayInterface.php
    │   └── Transaction/
    │       ├── UseCase/
    │       │   ├── ListTransactionsUseCase.php
    │       │   └── ExportTransactionsUseCase.php
    │       └── Dto/
    │           ├── Request/
    │           │   └── TransactionFilterDto.php
    │           └── Result/
    │               ├── TransactionResultDto.php
    │               └── TransactionListResultDto.php
    ├── Domain/
    │   ├── Balance/
    │   │   ├── Entity/
    │   │   │   ├── Table/
    │   │   │   │   └── BalanceTable.php
    │   │   │   ├── Balance.php
    │   │   │   └── BalanceCollection.php
    │   │   ├── Repository/
    │   │   │   └── BalanceRepository.php
    │   │   ├── Event/
    │   │   │   ├── BalanceSynced.php
    │   │   │   ├── FundsLocked.php
    │   │   │   ├── FundsUnlocked.php
    │   │   │   ├── FundsTransferred.php
    │   │   │   └── BalanceDiscrepancyDetected.php
    │   │   └── Service/
    │   │       └── BalanceCalculator.php
    │   └── Transaction/
    │       ├── Entity/
    │       │   ├── Table/
    │       │   │   └── TransactionTable.php
    │       │   ├── Transaction.php
    │       │   └── TransactionCollection.php
    │       ├── Repository/
    │       │   └── TransactionRepository.php
    │       └── Enum/
    │           └── TransactionTypeEnum.php
    ├── Infrastructure/
    │   ├── Bybit/
    │   │   └── BybitBalanceGateway.php         # реализует BybitBalanceGatewayInterface
    │   └── Bridge/
    │       └── SyncBalancesBridge.php
    └── Presentation/
        ├── Controller/
        │   ├── BalanceController.php
        │   └── TransactionController.php
        └── Command/
            └── SyncBalancesCommand.php
```

### Потребляемые контракты

| Контракт из `rebit.share` | Где используется |
|----------------------------|------------------|
| `BybitClientInterface` | `BybitBalanceGateway` |
| `BybitConnectionResolverInterface` | `BybitBalanceGateway`, `SyncBalancesCommand` |

### Внутренние порты

| Порт | Адаптер |
|------|---------|
| `BybitBalanceGatewayInterface` | `Infrastructure/Bybit/BybitBalanceGateway` |

### Маршруты

```
GET    /api/v1/wallet/balances               → GetBalancesUseCase
POST   /api/v1/wallet/balances/sync          → SyncBalancesUseCase
GET    /api/v1/wallet/transactions            → ListTransactionsUseCase
GET    /api/v1/wallet/transactions/export     → ExportTransactionsUseCase
```

### HL-блоки

| HL-блок | Таблица |
|---------|---------|
| `RebitBalance` | `rebit_balance` |
| `RebitTransaction` | `rebit_transaction` |

### DI

**di/balance.php:**

```php
BalanceCalculator::class
BalanceRepository::class
GetBalancesUseCase::class
LockFundsUseCase::class
UnlockFundsUseCase::class
BybitBalanceGatewayInterface::class → BybitBalanceGateway
SyncBalancesUseCase::class
SyncBalancesCommand::class
BalanceController::class
```

**di/transaction.php:**

```php
TransactionRepository::class
ListTransactionsUseCase::class
ExportTransactionsUseCase::class
TransactionController::class
```

---

## 9. Модуль `rebit.dev`

**Назначение:** инструменты разработки. Не устанавливается в production.

### Структура

```
rebit.dev/
├── install/
│   └── index.php
└── lib/
    ├── PhpCsFixer/
    └── Migration/
```

---

## 10. Запланированные модули

Следующие модули описаны в [domain.md](domain.md) и будут реализованы по мере развития платформы.
Каждый из них будет зависеть **только** от `rebit.share`.

### 10.1. `rebit.exchange` — P2P-торговля

**Домен:** Exchange — стаканы ордеров, объявления, сделки, чат сделки, скрипты автосообщений.

Самый крупный модуль. Фичи сгруппированы по поддоменам.

> ⚠️ **Важно:** Bybit P2P API имеет существенные ограничения — ряд операций невозможен через API
> и реализуется через polling или локальное хранение.
> Подробнее: [api.md § 9. Ограничения Bybit P2P API](api.md#9-ограничения-bybit-p2p-api).

**Поддомены:**

| Поддомен | Ответственность |
|----------|----------------|
| Currency | Валюты и валютные пары (локальный справочник) |
| PaymentMethod | Способы оплаты (локальный справочник, заполняется из ответов Bybit) |
| OrderBook | Стакан P2P-ордеров (кэш из Bybit, `POST /v5/p2p/item/online`) |
| Advertisement | Объявления пользователя (полноценный CRUD через Bybit API) |
| Trade | Жизненный цикл сделки (ограничен: чтение + confirm/release, без создания и отмены) |
| TradeChat | Чат внутри сделки (отправка через Bybit API, история — только локальная) |
| ChatScript | Скрипты автосообщений трейдера (полностью локальный функционал) |

### Маршруты и маппинг на Bybit API

```
# Стакан → Bybit: POST /v5/p2p/item/online
GET    /api/v1/exchange/orderbook

# Справочники → локальные данные (без Bybit API)
GET    /api/v1/exchange/currencies
GET    /api/v1/exchange/currency-pairs
GET    /api/v1/exchange/payment-methods

# Объявления → Bybit: item/create, item/update, item/personal/list, item/info, item/cancel
GET    /api/v1/exchange/advertisements
POST   /api/v1/exchange/advertisements
PATCH  /api/v1/exchange/advertisements/{id}
DELETE /api/v1/exchange/advertisements/{id}

# Сделки → Bybit: order/simplifyList, order/pending/simplifyList, order/info, order/pay, order/finish
GET    /api/v1/exchange/trades
GET    /api/v1/exchange/trades/{id}
POST   /api/v1/exchange/trades/{id}/confirm-payment    ← Bybit: order/pay
POST   /api/v1/exchange/trades/{id}/confirm-receipt     ← Bybit: order/finish

# Контрагент → Bybit: POST /v5/p2p/user/order/personal/info
GET    /api/v1/exchange/trades/{id}/counterparty

# Чат сделки → Bybit: order/message/send + oss/upload_file; история — только из локальной БД
GET    /api/v1/exchange/trades/{id}/chat                ← local-only (rebit_trade_message)
POST   /api/v1/exchange/trades/{id}/chat                ← Bybit: order/message/send + локальное сохранение
POST   /api/v1/exchange/trades/{id}/chat/read           ← local-only
POST   /api/v1/exchange/trades/{id}/chat/upload         ← Bybit: oss/upload_file

# Скрипты автосообщений → полностью локальный функционал
GET    /api/v1/exchange/chat-scripts
POST   /api/v1/exchange/chat-scripts
PATCH  /api/v1/exchange/chat-scripts/{id}
DELETE /api/v1/exchange/chat-scripts/{id}
```

### Ограничения Bybit P2P API и архитектурные решения

| Операция | Bybit API | Решение в Rebit |
|----------|:---------:|-----------------|
| Создание сделки | ❌ нет эндпоинта | Сделки создаются на Bybit (UI биржи). Rebit обнаруживает новые ордера через polling `order/pending/simplifyList` |
| Отмена сделки | ❌ нет эндпоинта | Отмена по таймеру на стороне Bybit. Rebit обновляет статус через polling `order/info` |
| Открытие арбитража | ❌ нет эндпоинта | Редирект пользователя в UI Bybit. Статус `disputed` определяется через polling |
| История чата | ❌ нет эндпоинта | Все отправленные сообщения дублируются в `rebit_trade_message`. Сообщения контрагента из UI Bybit **не** отображаются |
| Платёжные методы пользователя | ❌ нет прямого эндпоинта | Извлекаются из `paymentTerms` в ответах `item/personal/list` и `order/info` |
| Справочник платёжных методов | ❌ нет прямого эндпоинта | Локальная таблица `rebit_payment_method`, заполняется из ответов API + вручную |

### Потребляемые контракты

| Контракт из `rebit.share` | Где используется |
|----------------------------|------------------|
| `BybitClientInterface` | Синхронизация стакана, CRUD объявлений, управление сделками, чат |
| `BybitConnectionResolverInterface` | Определение подключения пользователя при каждом запросе к Bybit |
| `BalanceQueryInterface` | Проверка баланса перед созданием объявления (планируемый контракт) |

### Планируемые DI-файлы

```
di/
├── currency.php           # Currency, CurrencyPair
├── payment-method.php     # PaymentMethod
├── orderbook.php          # OrderBook sync
├── advertisement.php      # Advertisement CRUD
├── trade.php              # Trade lifecycle
├── trade-chat.php         # TradeChat + message sending
└── chat-script.php        # ChatScript CRUD
```

### Планируемые HL-блоки

| HL-блок | Таблица |
|---------|---------|
| `RebitCurrency` | `rebit_currency` |
| `RebitCurrencyPair` | `rebit_currency_pair` |
| `RebitPaymentMethod` | `rebit_payment_method` |
| `RebitOrderBook` | `rebit_order_book` |
| `RebitAdvertisement` | `rebit_advertisement` |
| `RebitTrade` | `rebit_trade` |
| `RebitTradeMessage` | `rebit_trade_message` |
| `RebitTradeChatScript` | `rebit_trade_chat_script` |
| `RebitTradeChatScriptStep` | `rebit_trade_chat_script_step` |

### 10.2. `rebit.notification` — Уведомления

**Домен:** Notification — формирование, хранение и доставка уведомлений.

**Поддомены:**

| Поддомен | Ответственность |
|----------|----------------|
| Notification | Хранение и отображение уведомлений |
| Preference | Настройки каналов по категориям |
| Channel | Каналы доставки: in_app, push, email, telegram |

**Планируемые маршруты:**

```
GET    /api/v1/notifications
GET    /api/v1/notifications/unread-count
POST   /api/v1/notifications/{id}/read
POST   /api/v1/notifications/read-all
GET    /api/v1/notifications/preferences
PATCH  /api/v1/notifications/preferences
```

**Планируемые HL-блоки:**

| HL-блок | Таблица |
|---------|---------|
| `RebitNotification` | `rebit_notification` |
| `RebitNotificationPreference` | `rebit_notification_preference` |

### 10.3. `rebit.security` — Безопасность

**Домен:** Security — сессии, 2FA, аудит, мониторинг подозрительной активности.

**Поддомены:**

| Поддомен | Ответственность |
|----------|----------------|
| Session | Управление активными сессиями |
| TwoFactor | Двухфакторная аутентификация (TOTP / SMS / Email) |
| Audit | Журнал действий пользователя (append-only) |
| Alert | Подозрительная активность, алерты |

**Планируемые маршруты:**

```
# Сессии
GET    /api/v1/security/sessions
DELETE /api/v1/security/sessions/{id}
DELETE /api/v1/security/sessions

# 2FA
POST   /api/v1/security/2fa/enable
POST   /api/v1/security/2fa/disable
POST   /api/v1/security/2fa/verify

# Аудит
GET    /api/v1/security/audit-log

# Алерты
GET    /api/v1/security/alerts
POST   /api/v1/security/alerts/{id}/resolve
```

**Планируемые HL-блоки:**

| HL-блок | Таблица |
|---------|---------|
| `RebitUserSession` | `rebit_user_session` |
| `RebitAuditLog` | `rebit_audit_log` |
| `RebitSecurityAlert` | `rebit_security_alert` |
| `RebitTwoFactorAuth` | `rebit_two_factor_auth` |

---

## 11. Фоновые процессы

Фоновые процессы размещаются в `Presentation/Command/` модуля, к чьему домену они относятся.

### Существующие

| Команда | Модуль | Класс | Описание |
|---------|--------|-------|----------|
| Синхронизация балансов | `rebit.wallet` | `Presentation/Command/SyncBalancesCommand` | Синхронизация балансов активных пользователей с Bybit |

### Запланированные

| Команда | Модуль | Описание | Интервал |
|---------|--------|----------|----------|
| Синхронизация стакана | `rebit.exchange` | Polling `POST /v5/p2p/item/online` → обновление `rebit_order_book` | 10 сек |
| Очистка стакана | `rebit.exchange` | Удаление записей `rebit_order_book` старше 5 мин | 1 мин |
| Синхронизация сделок | `rebit.exchange` | Polling `order/pending/simplifyList` → обнаружение новых ордеров и обновление статусов (включая отмену по таймеру Bybit) | 10 сек |
| Синхронизация истории сделок | `rebit.exchange` | Polling `order/simplifyList` → дозагрузка завершённых ордеров в `rebit_trade` | 10 мин |
| Выполнение шагов скриптов | `rebit.exchange` | Отправка отложенных сообщений из `rebit_trade_chat_script_step` через `order/message/send` | 5 сек |
| Мониторинг активности | `rebit.security` | Анализ паттернов подозрительной активности | 5 мин |

---

## 12. Шаблоны файлов

### 12.1. `include.php`

```php
<?php

declare(strict_types=1);

use Bitrix\Main\Loader;
use Rebit\Share\Infrastructure\Bitrix\Module\ModuleHelper;

Loader::includeModule('highloadblock');

ModuleHelper::validateModuleInstalled('rebit.share');

ModuleHelper::compileHLEntities(['RebitEntityName']);
```

### 12.2. `.settings.php`

```php
<?php

declare(strict_types=1);

return [
    'services' => [
        'value' => array_merge(
            require __DIR__ . '/di/domain-a.php',
            require __DIR__ . '/di/domain-b.php',
        ),
        'readonly' => true,
    ],
];
```

### 12.3. `install/index.php`

```php
<?php

declare(strict_types=1);

use Rebit\Share\Infrastructure\Bitrix\Module\ModuleRoutingTrait;

class Rebit_Example extends CModule
{
    use ModuleRoutingTrait;

    public $MODULE_ID = 'rebit.example';
    public $MODULE_NAME = 'rebit.example — Описание';
    public $MODULE_DESCRIPTION = 'Описание модуля';
    public $MODULE_VERSION = '1.0.0';
    public $MODULE_VERSION_DATE = '2026-03-20 12:00:00';
    public $PARTNER_NAME = 'rebit';
    public $PARTNER_URI = 'https://rebit-pro.ru';

    public function __construct()
    {
        $this->initModuleRouting();
    }

    public function DoInstall(): bool
    {
        RegisterModule($this->MODULE_ID);
        $this->installModuleRouting();

        return true;
    }

    public function DoUninstall(): bool
    {
        $this->uninstallModuleRouting();
        UnRegisterModule($this->MODULE_ID);

        return true;
    }
}
```

### 12.4. `routes.php`

```php
<?php

declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;
use Rebit\Example\Presentation\Controller\ExampleController;

return static function(RoutingConfigurator $routes) {
    $routes->get('/api/v1/example/items', [ExampleController::class, 'listAction']);
    $routes->post('/api/v1/example/items', [ExampleController::class, 'createAction']);
};
```

### 12.5. DI-файл домена

```php
<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Example\Application\Feature\UseCase\GetFeatureUseCase;
use Rebit\Example\Domain\Feature\Repository\FeatureRepository;
use Rebit\Example\Presentation\Controller\FeatureController;

return [
    FeatureRepository::class => [
        'className' => FeatureRepository::class,
    ],
    GetFeatureUseCase::class => [
        'constructor' => static function(): GetFeatureUseCase {
            return new GetFeatureUseCase(
                ServiceLocator::getInstance()->get(FeatureRepository::class),
            );
        },
    ],
    FeatureController::class => [
        'constructor' => static function(): FeatureController {
            return new FeatureController(
                ServiceLocator::getInstance()->get(GetFeatureUseCase::class),
            );
        },
    ],
];
```

### 12.6. UseCase

```php
<?php

declare(strict_types=1);

namespace Rebit\Example\Application\Feature\UseCase;

use Rebit\Example\Application\Feature\Dto\Request\FeatureRequestDto;
use Rebit\Example\Application\Feature\Dto\Result\FeatureResultDto;
use Rebit\Example\Domain\Feature\Repository\FeatureRepository;

final readonly class GetFeatureUseCase
{
    public function __construct(
        private FeatureRepository $repository,
    ) {}

    public function execute(FeatureRequestDto $dto): FeatureResultDto
    {
        // ...
    }
}
```

### 12.7. Controller

```php
<?php

declare(strict_types=1);

namespace Rebit\Example\Presentation\Controller;

use Rebit\Example\Application\Feature\UseCase\GetFeatureUseCase;
use Rebit\Example\Application\Feature\Dto\Request\FeatureRequestDto;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;
use Rebit\Share\Infrastructure\Controller\BaseJsonController;

final class FeatureController extends BaseJsonController
{
    public function __construct(
        private readonly GetFeatureUseCase $getFeatureUseCase,
    ) {
        parent::__construct();
    }

    public function listAction(FeatureRequestDto $dto): ControllerJson
    {
        return $this->json($this->getFeatureUseCase->execute($dto));
    }
}
```

---

## 13. Сводная статистика

### Реализованные модули

| Модуль | Контроллеры | UseCases | Entities | Repositories | Enums | Events | Services | Ports |
|--------|:-----------:|:--------:|:--------:|:------------:|:-----:|:------:|:--------:|:-----:|
| `rebit.share` | 1 | 1 | — | — | 2 | — | — | — |
| `rebit.auth` | 1 | 2 | 2 | 1 | — | — | 1 | — |
| `rebit.bybit` | — | — | — | — | — | — | — | — |
| `rebit.identity` | 1 | 4 | 1+coll | 1 | 2 | 3 | 2 | — |
| `rebit.wallet` | 2 | 5 | 2+coll | 2 | 1 | 5 | 1 | 1 |
| **Итого** | **5** | **12** | **5** | **4** | **5** | **8** | **4** | **1** |

### Межмодульные контракты

| Контракт | Поставщик | Потребители |
|----------|-----------|-------------|
| `TokenResolverInterface` | `rebit.auth` | Middleware |
| `BybitClientInterface` | `rebit.bybit` | `rebit.identity`, `rebit.wallet`, `rebit.exchange` |
| `BybitConnectionResolverInterface` | `rebit.identity` | `rebit.wallet`, `rebit.exchange` |
| `CacheCleanerInterface` | `rebit.share` | Все модули |
| `FileServiceInterface` | ⚠️ не реализован | — |
| `BalanceQueryInterface` | `rebit.wallet` | `rebit.exchange` (планируется) |
| `NotificationDispatcherInterface` | `rebit.notification` | `rebit.exchange` (планируется) |
