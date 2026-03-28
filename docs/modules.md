# Модули Bitrix: P2P-платформа Rebit

> Модули лежат в `api/public/local/modules/rebit.<name>/`.
> Каждый бизнес-модуль соответствует одному Bounded Context из [domain.md](domain.md).
> Общая инфраструктура и межмодульные контракты — в `rebit.share`.
> Архитектурные правила описаны в [architecture-guide](architecture-guide/README.md).

> **Для фронтенда:** все работающие эндпоинты описаны в разделе [10. API-справочник для фронтенда](#10-api-справочник-для-фронтенда).

---

## 1. Карта модулей

| # | Модуль | Назначение | Namespace | Зависимости | Статус |
|---|--------|------------|-----------|-------------|--------|
| 1 | `rebit.share` | Общая инфраструктура, контракты | `Rebit\Share` | — | ✅ |
| 2 | `rebit.auth` | Аутентификация, e-mail логин, регистрация с подтверждением кода, токены | `Rebit\Auth` | `rebit.share` | ✅ |
| 3 | `rebit.bybit` | Клиент Bybit API | `Rebit\Bybit` | `rebit.share` | ✅ |
| 4 | `rebit.identity` | Управление API-ключами Bybit, платёжные методы пользователя | `Rebit\Identity` | `rebit.share` | ✅ |
| 5 | `rebit.wallet` | Балансы, транзакции | `Rebit\Wallet` | `rebit.share` | ✅ |
| 6 | `rebit.dev` | Инструменты разработки | `Rebit\Dev` | — | ✅ |
| 7 | `rebit.exchange` | P2P-торговля, сделки, чат, контрагенты | `Rebit\Exchange` | `rebit.share` | ✅ |
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
    ├── rebit.identity       (потребляет BybitClientInterface; реализует BybitConnectionResolverInterface)
    ├── rebit.wallet         (потребляет BybitClientInterface, BybitConnectionResolverInterface, CurrencyQueryInterface; реализует BalanceQueryInterface)
    ├── rebit.exchange       (потребляет BybitClientInterface, BybitConnectionResolverInterface, BalanceQueryInterface; реализует CurrencyQueryInterface)
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
| `BybitClientInterface` | `Application/Contract/Bybit/` | `rebit.bybit` → `BybitApiClient` | `rebit.identity`, `rebit.wallet`, `rebit.exchange` |
| `BybitConnectionResolverInterface` | `Application/Contract/Bybit/` | `rebit.identity` → `BybitConnectionResolver` | `rebit.wallet`, `rebit.exchange` |
| `CacheCleanerInterface` | `Application/Contract/Cache/` | `rebit.share` → `BitrixCacheCleaner` | Все модули |
| `BalanceQueryInterface` | `Application/Contract/Wallet/` | `rebit.wallet` → `BalanceQueryAdapter` | `rebit.exchange` |
| `CurrencyQueryInterface` | `Application/Contract/Exchange/` | `rebit.exchange` → `CurrencyQueryAdapter` | `rebit.wallet` (`SyncBalancesUseCase`) |
| `FileServiceInterface` | `Application/Contract/File/` | ⚠️ не реализован | — |

### Планируемые контракты

| Контракт | Путь в `rebit.share` | Поставщик | Потребитель | Назначение |
|----------|----------------------|-----------|-------------|------------|
| `NotificationDispatcherInterface` | `Application/Contract/Notification/` | `rebit.notification` | `rebit.exchange` | Отправка уведомлений о событиях сделки (реализация — при создании модуля) |

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
    │   │   ├── Exchange/
    │   │   │   └── CurrencyQueryInterface.php
    │   │   ├── File/
    │   │   │   └── FileServiceInterface.php
    │   │   └── Wallet/
    │   │       └── BalanceQueryInterface.php
    │   ├── Interface/
    │   │   ├── RequestDtoInterface.php
    │   │   └── ResultDtoInterface.php
    │   ├── Collection/
    │   │   └── AbstractRequestCollection.php
    │   └── UseCase/
    │       └── UploadFileUseCase.php
    ├── Domain/
    │   └── File/
    │       ├── Dto/
    │       │   ├── Request/
    │       │   │   └── UploadRequestFileRequestDto.php
    │       │   └── Result/
    │       │       └── UploadFileResultDto.php
    │       ├── Exception/
    │       │   ├── FileUploadFailedException.php
    │       │   └── InvalidFileException.php
    │       └── Service/
    │           └── FileUploadService.php
    ├── Infrastructure/
    │   ├── Bitrix/
    │   │   ├── Module/
    │   │   │   ├── ModuleHelper.php
    │   │   │   ├── ModuleRoutingTrait.php
    │   │   │   └── ModuleComponentInstallerTrait.php
    │   │   ├── Cache/
    │   │   │   └── BitrixCacheCleaner.php
    │   │   ├── ControllerJson.php
    │   │   └── ControllerBuilder.php
    │   ├── Controller/
    │   │   ├── BaseJsonController.php
    │   │   ├── AbstractJsonController.php
    │   │   ├── AbstractController.php
    │   │   ├── Auth/
    │   │   │   ├── AuthenticatedControllerInterface.php
    │   │   │   └── AuthenticatedControllerTrait.php
    │   │   ├── Request/
    │   │   │   ├── RequestFileToDtoMapper.php
    │   │   │   ├── RequestParameterFactory.php
    │   │   │   ├── RequestToCollectionMapper.php
    │   │   │   ├── RequestToDtoMapper.php
    │   │   │   └── RequestToEntityMapper.php
    │   │   ├── Responses/
    │   │   │   ├── AbstractResponse.php
    │   │   │   ├── JsonExceptionResponse.php
    │   │   │   └── JsonResponse.php
    │   │   ├── Normalizer/
    │   │   │   ├── CommonNormalizer.php
    │   │   │   ├── DateNormalizer.php
    │   │   │   ├── DateTimeNormalizer.php
    │   │   │   ├── EnumNormalizer.php
    │   │   │   ├── ObjectNormalizer.php
    │   │   │   └── ScalarNormalizer.php
    │   │   ├── Serializers/
    │   │   │   └── CommonSerializer.php
    │   │   ├── Filters/
    │   │   │   ├── BearerTokenFilter.php
    │   │   │   └── LoggerFilter.php
    │   │   └── Attribute/
    │   │       └── SkipWhenNull.php
    │   ├── Dto/
    │   │   └── Metadata/
    │   │       ├── DtoClassMetadata.php
    │   │       ├── DtoMetadataService.php
    │   │       ├── DtoParamTypeEnum.php
    │   │       └── DtoParameterMetadata.php
    │   ├── Exception/
    │   │   ├── DtoInterfaceNotImplementException.php
    │   │   ├── EntityNotFoundException.php
    │   │   ├── RequestParameterException.php
    │   │   └── ValidationHttpException.php
    │   ├── Factory/
    │   │   └── DtoFactory.php
    │   ├── Helpers/
    │   │   ├── DtoMapper.php
    │   │   ├── JsonSerializerHelper.php
    │   │   ├── MappingException.php
    │   │   ├── NormalizerHelper.php
    │   │   ├── RequestHelper.php
    │   │   └── ValidationHelper.php
    │   ├── HttpClient/
    │   │   ├── Exception/
    │   │   │   └── HttpClientException.php
    │   │   ├── RebitHttpClient.php
    │   │   └── RebitHttpClientFactory.php
    │   ├── Interface/
    │   │   ├── HasViewModelInterface.php
    │   │   ├── RequestMapperInterface.php
    │   │   └── SerializerInterface.php
    │   ├── Logger/
    │   │   ├── CommonLoggerProcessor.php
    │   │   └── RequestIdGenerator.php
    │   ├── Repository/
    │   │   ├── AbstractHLBlockRepository.php
    │   │   ├── AbstractRepository.php
    │   │   └── RepositoryExceptionTrait.php
    │   └── Serializer/
    │       └── NameConverter/
    │           └── CustomNameConverter.php
    ├── Presentation/
    │   ├── Controller/
    │   │   └── FileController.php
    │   └── Command/
    │       ├── Attribute/
    │       │   └── WithLock.php           # атрибут для команд с эксклюзивной блокировкой
    │       └── RebitCommand.php
    └── Shared/
        ├── Enum/
        │   ├── HttpMethodEnum.php
        │   └── LogChannelEnum.php
        ├── Exception/
        │   ├── HttpException.php
        │   ├── RebitException.php
        │   └── RepositoryException.php
        ├── Facade/
        │   ├── Cache.php
        │   └── Log.php
        ├── Helper/
        │   ├── ArrayToDtoMapper.php
        │   ├── DtoToArrayNormalizer.php
        │   ├── PathHelper.php
        │   └── StringHelper.php
        ├── Interface/
        │   ├── DtoInterface.php
        │   ├── NormalizerInterface.php
        │   └── RequestFileDtoInterface.php
        └── ValueObject/
            ├── AbstractDateRange.php
            ├── CacheKey.php
            ├── DateRange.php
            ├── DateTimeRange.php
            ├── Image.php
            └── Phone.php
```

### Что предоставляет

| Категория | Что | Пример |
|-----------|-----|--------|
| Базовые контроллеры | HTTP-слой для всех модулей | `BaseJsonController`, `AbstractJsonController` |
| Базовые репозитории | ORM-обёртки | `AbstractHLBlockRepository`, `AbstractRepository` |
| Контракты | Межмодульные интерфейсы | `BybitClientInterface`, `TokenResolverInterface`, `BalanceQueryInterface`, `CurrencyQueryInterface` |
| DTO-интерфейсы | Стандарт для входа/выхода | `RequestDtoInterface`, `ResultDtoInterface` |
| Module-трейты | Установка/маршруты модулей | `ModuleRoutingTrait`, `ModuleComponentInstallerTrait` |
| Хелперы | Маппинг, валидация, сериализация | `DtoMapper`, `ValidationHelper`, `ArrayToDtoMapper` |
| Фасады | Логирование, кэширование | `Log`, `Cache` |
| CLI-команды | Базовый класс команд + атрибут блокировки | `RebitCommand`, `WithLock` |
| Value Objects | Общие объекты-значения | `Phone`, `Image`, `DateRange`, `CacheKey` |

---

## 5. Модуль `rebit.auth`

**Домен:** Auth — аутентификация пользователей, e-mail логин, двухшаговая регистрация с подтверждением кода и управление токенами.

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
    │       ├── Contract/
    │       ├── UseCase/
    │       │   ├── LoginUseCase.php
    │       │   ├── LogoutUseCase.php
    │       │   ├── RequestRegistrationCodeUseCase.php
    │       │   └── ConfirmRegistrationUseCase.php
    │       └── Dto/
    │           ├── Request/
    │           └── Result/
    ├── Domain/
    │   ├── User/
    │   │   ├── Entity/
    │   │   │   ├── UserToken.php
    │   │   │   ├── UserCredentials.php
    │   │   │   └── UserRegistrationState.php
    │   │   ├── Repository/
    │   │   │   └── UserRepository.php
    │   │   └── Service/
    │   │       └── TokenGenerator.php
    │   └── Registration/
    │       ├── Entity/
    │       │   └── RegistrationConfirmation.php
    │       ├── Repository/
    │       │   └── RegistrationConfirmationRepository.php
    │       └── Service/
    │           └── RegistrationCodeGenerator.php
    ├── Infrastructure/
    │   └── Adapter/
    │       ├── BitrixMailEventRegistrationConfirmationMailer.php
    │       ├── GeeTestCaptchaVerifier.php
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
POST   /api/v1/auth/login                   → LoginUseCase
POST   /api/v1/auth/register/request-code   → RequestRegistrationCodeUseCase
POST   /api/v1/auth/register/confirm        → ConfirmRegistrationUseCase
POST   /api/v1/auth/logout                  → LogoutUseCase
```

Публичные экшены: `login`, `register/request-code`, `register/confirm`.

Защищённый экшен: `logout` через `BearerTokenFilter`.

### DI (di/auth.php)

```php
UserRepository::class
LoginUserRepositoryInterface::class → UserRepository
TokenGenerator::class
TokenGeneratorInterface::class → TokenGenerator
RegistrationConfirmationRepository::class
RegistrationCodeGenerator::class
RegistrationConfirmationMailerInterface::class → BitrixMailEventRegistrationConfirmationMailer
CaptchaVerifierInterface::class → GeeTestCaptchaVerifier
TokenResolverInterface::class → TokenResolver
LoginUseCase::class
LogoutUseCase::class
RequestRegistrationCodeUseCase::class
ConfirmRegistrationUseCase::class
AuthController::class
```

### Прикладные сценарии

- `LoginUseCase` — вход по `email` и паролю, при необходимости с GeeTest captcha.
- `RequestRegistrationCodeUseCase` — создаёт или обновляет неподтверждённую регистрацию, генерирует 6-значный код, сохраняет TTL и отправляет письмо через Bitrix mail event.
- `ConfirmRegistrationUseCase` — проверяет код, лимит попыток, активирует пользователя и сразу возвращает auth-токен.
- `LogoutUseCase` — инвалидирует текущий токен пользователя.

### Почта и локальная разработка

- Отправка подтверждения регистрации переведена на Bitrix mail event `REBIT_AUTH_REGISTRATION_CONFIRMATION`.
- Локально почта уходит через `msmtp` в `Mailpit` (`http://localhost:8025`).
- Основные env-настройки модуля: `REBIT_AUTH_MAIL_EVENT_SITE_ID`, `REBIT_AUTH_REGISTRATION_CODE_TTL_MINUTES`, `REBIT_AUTH_REGISTRATION_RESEND_COOLDOWN_SECONDS`, `REBIT_AUTH_REGISTRATION_MAX_ATTEMPTS`.
- Подробная настройка почты: [docs/how-to/email-registration-setup.md](how-to/email-registration-setup.md).
- Чек-лист проверки регистрации: [docs/instruction/email-registration-checklist.md](instruction/email-registration-checklist.md).

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

### Логирование

- Успешные HTTP-запросы и ответы Bybit логируются на уровне `debug`, чтобы не раздувать `logstash`-файлы при cron-запусках каждые 10 секунд.
- В постоянные логи остаются `warning`/`error` для нештатных ситуаций: сетевые ошибки, HTTP 4xx/5xx и ошибки `retCode` Bybit API.

---

## 7. Модуль `rebit.identity`

**Домен:** Identity — управление API-ключами Bybit, статус подключения, платёжные методы пользователя.

### Структура

```
rebit.identity/
├── .settings.php
├── include.php
├── routes.php
├── orm_annotation.php
├── di/
│   ├── connection.php
│   └── payment-method.php
└── lib/
    ├── Application/
    │   ├── ApiConnection/
    │   │   ├── UseCase/
    │   │   │   ├── ConnectApiUseCase.php
    │   │   │   ├── DisconnectApiUseCase.php
    │   │   │   ├── VerifyApiUseCase.php
    │   │   │   └── GetConnectionStatusUseCase.php
    │   │   └── Dto/
    │   │       ├── Request/
    │   │       │   └── ConnectApiRequestDto.php
    │   │       └── Result/
    │   │           └── ApiConnectionResultDto.php
    │   └── PaymentMethod/
    │       ├── UseCase/
    │       │   ├── SyncPaymentMethodsUseCase.php
    │       │   └── GetPaymentMethodsUseCase.php
    │       ├── Port/
    │       │   └── Outgoing/
    │       │       └── BybitPaymentMethodGatewayInterface.php
    │       └── Dto/
    │           └── Result/
    │               ├── PaymentMethodResultDto.php
    │               └── PaymentMethodListResultDto.php
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
    │       │   ├── ApiConnectionFailed.php
    │       │   └── PaymentMethodsSynced.php
    │       └── Service/
    │           ├── ApiKeyEncryptor.php
    │           └── ApiKeyMasker.php
    ├── Infrastructure/
    │   ├── Adapter/
    │   │   └── BybitConnectionResolver.php    # реализует BybitConnectionResolverInterface
    │   ├── Bybit/
    │   │   └── BybitPaymentMethodGateway.php  # реализует BybitPaymentMethodGatewayInterface
    │   └── Controller/
    │       └── BaseIdentityController.php
    └── Presentation/
        └── Controller/
            ├── ApiConnectionController.php
            └── PaymentMethodController.php
```

### Реализуемые контракты

| Контракт из `rebit.share` | Адаптер |
|----------------------------|---------|
| `BybitConnectionResolverInterface` | `Infrastructure/Adapter/BybitConnectionResolver` |

### Потребляемые контракты

| Контракт из `rebit.share` | Где используется |
|----------------------------|------------------|
| `BybitClientInterface` | `ConnectApiUseCase`, `VerifyApiUseCase`, `BybitPaymentMethodGateway` |

### Маршруты

```
POST   /api/v1/identity/connection          → ConnectApiUseCase
DELETE /api/v1/identity/connection          → DisconnectApiUseCase
POST   /api/v1/identity/connection/verify   → VerifyApiUseCase
GET    /api/v1/identity/connection/status   → GetConnectionStatusUseCase
GET    /api/v1/identity/payment-methods      → GetPaymentMethodsUseCase
POST   /api/v1/identity/payment-methods/sync → SyncPaymentMethodsUseCase
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

### DI (di/payment-method.php)

```php
BybitPaymentMethodGatewayInterface::class → BybitPaymentMethodGateway
SyncPaymentMethodsUseCase::class
GetPaymentMethodsUseCase::class
PaymentMethodController::class
```

### Внутренние порты

| Порт | Адаптер |
|------|---------|
| `BybitPaymentMethodGatewayInterface` | `Infrastructure/Bybit/BybitPaymentMethodGateway` |

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
    │   ├── Adapter/
    │   │   └── BalanceQueryAdapter.php         # реализует BalanceQueryInterface
    │   ├── Bybit/
    │   │   └── BybitBalanceGateway.php         # реализует BybitBalanceGatewayInterface
    │   ├── Bridge/
    │   │   └── SyncBalancesBridge.php
    │   └── Controller/
    │       └── BaseWalletController.php        # базовый контроллер с инициализацией userId
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
| `CurrencyQueryInterface` | `SyncBalancesUseCase` (сопоставление кода валюты с ID)

### Реализуемые контракты

| Контракт из `rebit.share` | Адаптер |
|----------------------------|---------|
| `BalanceQueryInterface` | `Infrastructure/Adapter/BalanceQueryAdapter` |

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
SyncBalancesUseCase::class           # потребляет CurrencyQueryInterface
SyncBalancesCommand::class
BalanceQueryInterface::class → BalanceQueryAdapter
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

## 10. API-справочник для фронтенда

Все работающие эндпоинты. Для каждого указаны: метод, путь, нужна ли авторизация, параметры запроса и форма ответа.

### 10.1. Общее

**Base URL (dev):** `https://api.rebit-p2p.loc`

**Аутентификация:** Bearer-токен в заголовке запроса:
```
Authorization: Bearer <token>
```
Токен получается через `POST /api/v1/auth/login`.

**Формат запросов и ответов:** JSON (`Content-Type: application/json`).

**Конверт ответа:**
```json
// Успешный ответ
{ "data": { ... } }

// Ошибка
{
  "data": null,
  "error": {
    "message": "Текст ошибки",
    "debug": { ... }   // только в dev-окружении
  }
}
```

**Обозначения в таблицах:**
- 🔒 — требует Bearer-токен
- 📦 — тело запроса JSON
- ❓ — query-параметр (GET)

---

### 10.2. `rebit.auth` — Аутентификация

#### `POST /api/v1/auth/login`

Вход по `email` и паролю. Возвращает токен и краткие данные пользователя.

**Body:**
```json
{
  "email": "user@example.com",
  "password": "secret"
}
```

**Response:**
```json
{
  "data": {
    "token": "eyJ...",
    "expiresAt": "2026-04-01T12:00:00+00:00",
    "user": {
      "id": 42,
      "email": "user@example.com",
      "name": "Иван"
    }
  }
}
```

---

#### `POST /api/v1/auth/register/request-code`

Первый шаг регистрации по `email`: создаёт или обновляет неподтверждённого пользователя, генерирует 6-значный код и отправляет его через Bitrix mail event.

**Body:**
```json
{
  "email": "user@example.com",
  "password": "secret123"
}
```

**Response:**
```json
{
  "data": {
    "email": "user@example.com",
    "codeExpiresAt": "2026-03-26T13:15:00+03:00",
    "resendAvailableAt": "2026-03-26T13:01:00+03:00"
  }
}
```

**Примечания:**
- код подтверждения действует ограниченное время;
- повторная отправка ограничена cooldown;
- локально письмо можно проверить в `Mailpit`.

---

#### `POST /api/v1/auth/register/confirm`

Второй шаг регистрации: проверяет 6-значный код, активирует пользователя и сразу авторизует его.

**Body:**
```json
{
  "email": "user@example.com",
  "code": "123456"
}
```

**Response:**
```json
{
  "data": {
    "token": "eyJ...",
    "expiresAt": "2026-04-01T12:00:00+00:00",
    "user": {
      "id": 42,
      "email": "user@example.com",
      "name": "Иван"
    }
  }
}
```

**Примечания:**
- код должен содержать ровно 6 цифр;
- действует лимит неудачных попыток подтверждения;
- после успешного подтверждения повторный логин не требуется.

---

#### `POST /api/v1/auth/logout` 🔒

Инвалидирует текущий токен. Тело не требуется.

**Response:**
```json
{ "data": [] }
```

---

### 10.3. `rebit.identity` — Подключение Bybit API

Управление ключами Bybit (API Key + Secret) для P2P-торговли.

#### `GET /api/v1/identity/connection/status` 🔒

Статус подключения Bybit-аккаунта.

**Response:**
```json
{
  "data": {
    "isConnected": true,
    "status": "active",
    "mode": "real",
    "maskedApiKey": "****ABCD",
    "connectedAt": "2026-03-20T10:00:00+00:00"
  }
}
```

| Поле | Тип | Значения |
|------|-----|---------|
| `status` | string | `active` / `inactive` / `error` |
| `mode` | string | `real` / `testnet` |

---

#### `POST /api/v1/identity/connection` 🔒 📦

Подключение Bybit API-ключа.

**Body:**
```json
{
  "apiKey": "your_bybit_api_key",
  "apiSecret": "your_bybit_api_secret",
  "mode": "real"
}
```

| Поле | Тип | Обязательный | Значения |
|------|-----|:---:|---------|
| `apiKey` | string | ✅ | — |
| `apiSecret` | string | ✅ | — |
| `mode` | string | ✅ | `real` / `testnet` |

---

#### `DELETE /api/v1/identity/connection` 🔒

Отключение Bybit-аккаунта.

---

#### `POST /api/v1/identity/connection/verify` 🔒

Проверка валидности текущего ключа.

---

#### `GET /api/v1/identity/payment-methods` 🔒

Список платёжных методов пользователя, синхронизированных с Bybit.

**Response:**
```json
{
  "data": {
    "items": [
      {
        "id": "7110",
        "paymentType": 14,
        "paymentName": "Bank Transfer",
        "realName": "Иван Иванов",
        "bankName": "Sberbank",
        "branchName": "",
        "accountNo": "40817810099910004312",
        "online": "0",
        "visible": 1,
        "realNameVerified": true,
        "currencyBalance": []
      },
      {
        "id": "-1",
        "paymentType": 377,
        "paymentName": "Balance",
        "realName": "",
        "bankName": "",
        "branchName": "",
        "accountNo": "",
        "online": "1",
        "visible": 0,
        "realNameVerified": false,
        "currencyBalance": ["EUR", "USD", "RUB"]
      }
    ]
  }
}
```

| Поле | Тип | Описание |
|------|-----|---------|
| `id` | string | ID метода на Bybit. `"-1"` — встроенный балансовый метод |
| `paymentType` | int | Числовой код типа |
| `paymentName` | string | Название метода (из `paymentConfigVo`) |
| `online` | string | `"0"`: офлайн (банковский перевод), `"1"`: онлайн (через баланс) |
| `currencyBalance` | string[] | Поддерживаемые валюты (только для Balance) |

> Используется при создании объявления — для выбора `paymentMethodIds`.

---

#### `POST /api/v1/identity/payment-methods/sync` 🔒

Принудительная синхронизация платёжных методов с Bybit. Тело не требуется.

---

### 10.4. `rebit.wallet` — Балансы и транзакции

#### `GET /api/v1/wallet/balances` 🔒

Список балансов пользователя по всем валютам.

**Response:**
```json
{
  "data": {
    "items": [
      {
        "id": 1,
        "userId": 42,
        "currencyId": 1,
        "available": 250.50,
        "locked": 50.00,
        "total": 300.50,
        "syncedAt": "2026-03-24T12:00:00+00:00"
      }
    ]
  }
}
```

> `currencyId` — ID из справочника `/api/v1/exchange/currencies`.
> `locked` — средства, зарезервированные под активные объявления (замораживает Bybit).

---

#### `POST /api/v1/wallet/balances/sync` 🔒

Принудительная синхронизация балансов с Bybit. Тело не требуется.

---

#### `GET /api/v1/wallet/transactions` 🔒

История транзакций пользователя с фильтрацией и пагинацией.

**Query-параметры:**

| Параметр | Тип | По умолчанию | Описание |
|----------|-----|:---:|---------|
| `type` | string | — | Тип транзакции (см. ниже) |
| `currencyId` | int | — | Фильтр по валюте |
| `dateFrom` | string | — | Формат `YYYY-MM-DD` |
| `dateTo` | string | — | Формат `YYYY-MM-DD` |
| `limit` | int | 50 | Кол-во записей |
| `offset` | int | 0 | Смещение (пагинация) |

**Значения `type`:**

| Значение | Описание |
|----------|---------|
| `deposit` | Пополнение |
| `withdrawal` | Вывод |
| `trade_buy` | Покупка по сделке |
| `trade_sell` | Продажа по сделке |
| `lock` | Заморозка средств |
| `unlock` | Разморозка средств |
| `fee` | Комиссия |

**Response:**
```json
{
  "data": {
    "items": [
      {
        "id": 101,
        "userId": 42,
        "currencyId": 1,
        "type": "trade_sell",
        "amount": 100.00,
        "balanceAfter": 350.50,
        "tradeId": 7,
        "description": "Продажа USDT по сделке #7",
        "bybitTxId": null,
        "createdAt": "2026-03-24T11:00:00+00:00"
      }
    ],
    "total": 42
  }
}
```

---

### 10.5. `rebit.exchange` — P2P-торговля

#### Справочники (публичные, без авторизации)

##### `GET /api/v1/exchange/currencies` 🔒

Список поддерживаемых валют.

**Response:**
```json
{
  "data": {
    "items": [
      { "id": 1, "code": "USDT", "name": "Tether USD", "type": "crypto", "decimals": 2, "sort": 10 },
      { "id": 2, "code": "USDC", "name": "USD Coin",   "type": "crypto", "decimals": 2, "sort": 20 },
      { "id": 3, "code": "BTC",  "name": "Bitcoin",    "type": "crypto", "decimals": 8, "sort": 30 },
      { "id": 4, "code": "RUB",  "name": "Российский рубль", "type": "fiat", "decimals": 2, "sort": 100 }
    ]
  }
}
```

| Поле `type` | Описание |
|------------|---------|
| `crypto` | Криптовалюта |
| `fiat` | Фиатная валюта |

---

##### `GET /api/v1/exchange/currency-pairs` 🔒

Список торговых пар.

**Response:**
```json
{
  "data": {
    "items": [
      { "id": 1, "code": "USDT_RUB", "tokenCurrencyId": 1, "fiatCurrencyId": 4, "isDefault": true,  "sort": 10 },
      { "id": 2, "code": "BTC_RUB",  "tokenCurrencyId": 3, "fiatCurrencyId": 4, "isDefault": false, "sort": 20 },
      { "id": 3, "code": "USDC_RUB", "tokenCurrencyId": 2, "fiatCurrencyId": 4, "isDefault": false, "sort": 30 }
    ]
  }
}
```

> `isDefault: true` — пара, выбранная по умолчанию на главной странице.

---

##### `GET /api/v1/exchange/payment-methods` 🔒

Справочник способов оплаты.

**Response:**
```json
{
  "data": {
    "items": [
      { "id": 19, "code": "SBP",       "name": "СБП",          "sort": 10 },
      { "id": 20, "code": "TINKOFF",   "name": "Tinkoff",       "sort": 20 },
      { "id": 21, "code": "SBERBANK",  "name": "Сбербанк",      "sort": 30 },
      { "id": 22, "code": "RAIFFEISEN","name": "Райффайзен",    "sort": 40 },
      { "id": 23, "code": "YUMONEY",   "name": "ЮMoney",        "sort": 50 },
      { "id": 24, "code": "GAZPROM",   "name": "Газпромбанк",   "sort": 60 },
      { "id": 25, "code": "VTB",       "name": "ВТБ",           "sort": 70 },
      { "id": 26, "code": "ALFA",      "name": "Альфа-Банк",    "sort": 80 },
      { "id": 27, "code": "CASH",      "name": "Наличные",      "sort": 100 }
    ]
  }
}
```

---

#### Стакан ордеров

##### `GET /api/v1/exchange/orderbook` 🔒

Стакан P2P-ордеров с Bybit (закэшированный).

**Query-параметры:**

| Параметр | Тип | Обязательный | Описание |
|----------|-----|:---:|---------|
| `tokenCode` | string | ✅ | Код криптовалюты (`USDT`, `BTC`, `USDC`) |
| `fiatCode` | string | ✅ | Код фиата (`RUB`) |
| `side` | string | — | `buy` / `sell` — если не указан, возвращаются обе стороны |

**Response:**
```json
{
  "data": {
    "buy": [
      {
        "id": 1,
        "bybitOrderId": "1234567890",
        "side": "buy",
        "price": 91.50,
        "amount": 500.00,
        "minLimit": 1000.00,
        "maxLimit": 50000.00,
        "username": "trader_user",
        "counterpartyRating": 98.5,
        "completedTrades": 342,
        "completionRate": 99.1,
        "paymentMethods": ["SBP", "TINKOFF"],
        "paymentTimeLimit": 15
      }
    ],
    "sell": [ ... ]
  }
}
```

> ⚠️ Стакан обновляется фоновым процессом каждые 10 секунд. Данные могут быть не актуальнее 10 сек.

---

#### Объявления

##### `GET /api/v1/exchange/advertisements` 🔒

Список объявлений текущего пользователя (синхронизируются с Bybit).

**Response:**
```json
{
  "data": {
    "items": [
      {
        "id": 5,
        "bybitAdId": "1765432100000",
        "currencyPairId": 1,
        "side": "sell",
        "priceType": "fixed",
        "price": 91.50,
        "premium": 0.00,
        "quantity": 1000.00,
        "quantityRemaining": 750.00,
        "minAmount": 1000.00,
        "maxAmount": 50000.00,
        "paymentMethodIds": ["SBP", "TINKOFF"],
        "paymentPeriod": 15,
        "feeRate": 0.002,
        "conditions": "Только верифицированные пользователи",
        "chatScriptId": 2,
        "status": "active",
        "createdAt": "2026-03-20T10:00:00+00:00",
        "updatedAt": "2026-03-24T09:00:00+00:00"
      }
    ]
  }
}
```

| Поле `side` | Описание |
|------------|---------|
| `buy` | Объявление на покупку (я покупаю крипту за фиат) |
| `sell` | Объявление на продажу (я продаю крипту за фиат) |

| Поле `priceType` | Описание |
|-----------------|---------|
| `fixed` | Фиксированная цена |
| `floating` | Плавающая (привязана к рынку через `premium`) |

| Поле `status` | Описание |
|--------------|---------|
| `active` | Активно, принимает заявки |
| `paused` | На паузе |
| `completed` | Полностью выполнено (кол-во = 0) |
| `cancelled` | Отменено |

---

##### `POST /api/v1/exchange/advertisements` 🔒 📦

Создание нового объявления через Bybit API.

**Body:**
```json
{
  "currencyPairId": 1,
  "side": "sell",
  "priceType": "fixed",
  "price": "91.50",
  "premium": null,
  "quantity": "1000",
  "minAmount": "1000",
  "maxAmount": "50000",
  "paymentMethodIds": ["SBP", "TINKOFF"],
  "paymentPeriod": 15,
  "conditions": "Только верифицированные пользователи",
  "chatScriptId": 2
}
```

| Поле | Тип | Обязательный | Описание |
|------|-----|:---:|---------|
| `currencyPairId` | int | ✅ | ID пары из `/exchange/currency-pairs` |
| `side` | string | ✅ | `buy` / `sell` |
| `priceType` | string | ✅ | `fixed` / `floating` |
| `price` | string | ✅ | Цена (строка, т.к. Bybit принимает строку) |
| `premium` | string\|null | — | Наценка в % для `floating` типа |
| `quantity` | string | ✅ | Объём в крипте |
| `minAmount` | string | ✅ | Мин. сумма сделки в фиате |
| `maxAmount` | string | ✅ | Макс. сумма сделки в фиате |
| `paymentMethodIds` | string[] | ✅ | Коды способов оплаты |
| `paymentPeriod` | int | ✅ | Время оплаты в минутах (1–1440) |
| `conditions` | string | — | Условия для контрагента |
| `chatScriptId` | int\|null | — | ID скрипта автосообщений |

---

##### `DELETE /api/v1/exchange/advertisements/{id}` 🔒

Деактивация объявления. `{id}` — локальный ID.

---

#### Сделки

##### `GET /api/v1/exchange/trades` 🔒

Список сделок пользователя (buyer или seller).

**Response:**
```json
{
  "data": {
    "items": [
      {
        "id": 7,
        "bybitOrderId": "1765000000001",
        "bybitStatus": 10,
        "side": "sell",
        "price": 91.50,
        "quantity": 100.00,
        "fiatAmount": 9150.00,
        "fee": 0.20,
        "status": "pending_payment",
        "counterpartyName": "buyer_nick",
        "counterpartyUserId": 105,
        "counterparty": {
          "bybitUserId": "290118",
          "nickname": "buyer_nick",
          "kycLevel": 2,
          "totalTrades": 342,
          "recentRate": "98.5",
          "goodAppraiseRate": "97.2",
          "goodAppraiseCount": 335,
          "badAppraiseCount": 7,
          "avgReleaseTime": "3.5",
          "avgTransferTime": "5.2",
          "accountDays": 698,
          "vipLevel": 1,
          "authStatus": 2,
          "userType": "PERSONAL",
          "isOnline": true
        },
        "currencyPairId": 1,
        "advertisementId": 5,
        "paymentDeadline": "2026-03-24T12:15:00+00:00",
        "paidAt": null,
        "completedAt": null,
        "cancelledAt": null,
        "cancelReason": null,
        "createdAt": "2026-03-24T12:00:00+00:00",
        "updatedAt": "2026-03-24T12:00:00+00:00"
      }
    ]
  }
}
```

> Поле `counterparty` содержит данные из `b_user` (группа «Контрагенты»), синхронизированные через `POST /v5/p2p/user/order/personal/info`.
> `counterpartyUserId` — ID контрагента в `b_user`.

**Статусы сделки (`status`):**

| Значение | Описание | Действие для пользователя |
|----------|---------|--------------------------|
| `pending_payment` | Ожидает оплаты от покупателя | Покупатель: перевести деньги и нажать «Оплатил» |
| `payment_sent` | Покупатель отметил оплату | Продавец: подтвердить получение |
| `payment_confirmed` | Продавец подтвердил | Ожидание завершения на Bybit |
| `completed` | Сделка завершена | — |
| `cancelled` | Отменена | — |
| `disputed` | Открыт арбитраж | Ожидание решения Bybit |

> **Важно:** `side` — это сторона **текущего пользователя** в сделке.
> `buy` = я покупаю крипту, `sell` = я продаю крипту.

---

##### `GET /api/v1/exchange/trades/{id}` 🔒

Детали одной сделки. Ответ той же формы, что элемент в списке.

---

##### `POST /api/v1/exchange/trades/{id}/pay` 🔒

Подтвердить оплату (действие покупателя). Переводит сделку в статус `payment_sent`.
Тело не требуется.

---

##### `POST /api/v1/exchange/trades/{id}/release` 🔒

Подтвердить получение оплаты и освободить крипту (действие продавца).
Переводит сделку в статус `completed`. Тело не требуется.

---

#### Чат сделки

##### `GET /api/v1/exchange/trades/{tradeId}/chat` 🔒

История сообщений в чате сделки (только локально сохранённые).

**Response:**
```json
{
  "data": {
    "items": [
      {
        "id": 15,
        "tradeId": 7,
        "userId": 42,
        "message": "Оплатил, ждите зачисления",
        "messageType": "user",
        "contentType": "str",
        "fileName": null,
        "createdAt": "2026-03-24T12:05:00+00:00"
      }
    ]
  }
}
```

| Поле `messageType` | Описание |
|-------------------|---------|
| `user` | Сообщение пользователя |
| `system` | Системное (смена статуса) |
| `script` | Автоматическое от скрипта |

| Поле `contentType` | Описание |
|-------------------|---------|
| `str` | Текст |
| `pic` | Изображение |
| `pdf` | PDF-документ |
| `video` | Видео |

> ⚠️ **Ограничение:** сообщения контрагента из UI Bybit **не отображаются** — Bybit API не предоставляет эндпоинт истории чата.

---

##### `POST /api/v1/exchange/trades/{tradeId}/chat` 🔒 📦

Отправить сообщение в чат сделки.

**Body:**
```json
{
  "tradeId": 7,
  "message": "Перевод выполнен",
  "contentType": "str",
  "fileName": null
}
```

| Поле | Тип | По умолчанию | Описание |
|------|-----|:---:|---------|
| `tradeId` | int | — | ID сделки |
| `message` | string | — | Текст сообщения или URL файла |
| `contentType` | string | `str` | `str` / `pic` / `pdf` / `video` |
| `fileName` | string\|null | null | Имя файла (для `pic`, `pdf`, `video`) |

---

#### Скрипты автосообщений

Позволяют настроить авто-отправку сообщений по расписанию при открытии сделки.

##### `GET /api/v1/exchange/chat-scripts` 🔒

Список скриптов пользователя.

**Response:**
```json
{
  "data": {
    "items": [
      {
        "id": 2,
        "name": "Приветствие покупателя",
        "isActive": true,
        "createdAt": "2026-03-20T10:00:00+00:00",
        "updatedAt": "2026-03-20T10:00:00+00:00",
        "steps": [
          { "id": 1, "sort": 10, "message": "Здравствуйте! Переводите на карту Tinkoff.", "delaySeconds": 0 },
          { "id": 2, "sort": 20, "message": "Как только переведёте — нажмите «Оплатил».", "delaySeconds": 30 }
        ]
      }
    ]
  }
}
```

---

##### `POST /api/v1/exchange/chat-scripts` 🔒 📦

Создать новый скрипт.

В production backend скрипт чата сейчас поддерживает только текстовые шаги.

В mock-режиме фронтенда для локальной демонстрации UI дополнительно допускаются:

- QR / изображение
- PDF-файл
- видео

**Body:**
```json
{
  "name": "Приветствие покупателя",
  "isActive": true,
  "steps": [
    {
      "sort": 10,
      "message": "Здравствуйте! Переводите по реквизитам ниже.",
      "delaySeconds": 0
    }
  ]
}
```

| Поле | Тип | Обязательное | Описание |
|------|-----|:-----------:|----------|
| `name` | string | ✅ | Название сценария |
| `isActive` | bool | ✅ | Активность сценария |
| `steps[].sort` | int | ✅ | Порядок шага |
| `steps[].message` | string | ✅ | Текст сообщения |
| `steps[].delaySeconds` | int | ✅ | Задержка перед отправкой |

> Примечание: поля `steps[].contentType`, `steps[].fileName`, `steps[].fileUrl` используются только во frontend mock-режиме и не сохраняются текущим production backend контрактом.

---

##### `PATCH /api/v1/exchange/chat-scripts/{id}` 🔒 📦

Обновить скрипт. Body аналогичен `POST`, плюс `"id"` в теле.

---

##### `DELETE /api/v1/exchange/chat-scripts/{id}` 🔒

Удалить скрипт.

---

#### Минимальный сценарий фронтовых моков

Для локальной проверки фронтенда без реального API предусмотрен stateful mock-режим.

Покрываемый сценарий:

1. Пользователь авторизуется и подключает Bybit API ключи.
2. После подключения фронтенд получает платежные методы, балансы и исторические данные.
3. Пользователь создаёт одношаговый сценарий с текстом или mock-вложением.
4. Пользователь создаёт объявление и может сразу включить или выключить его.
5. Для активного объявления появляется новая сделка.
6. Первый шаг сценария автоматически отправляется в чат сделки.
7. Сделка отображается в списке как новая, со статусом `pending_payment`.
8. На странице сделки пользователь видит детали и чат в одном окне.
9. После успешной оплаты пользователь выполняет действие `release` / «Отпустить средства».

Этот сценарий нужен именно для фронтенд-разработки и демонстрации UI, не заменяет реальную интеграцию с backend и Bybit.

---

## 11. Модуль `rebit.exchange` — Техническое описание

**Статус: ✅ реализован**

**Домен:** Exchange — стаканы ордеров, объявления, сделки, чат сделки, скрипты автосообщений, контрагенты.

### Структура

```
rebit.exchange/
├── .settings.php
├── include.php
├── routes.php
├── orm_annotation.php
├── di/
│   ├── currency.php
│   ├── payment-method.php
│   ├── order-book.php
│   ├── advertisement.php
│   ├── trade.php
│   ├── counterparty.php
│   ├── trade-chat.php
│   └── chat-script.php
└── lib/
    ├── Application/
    │   ├── Currency/
    │   │   └── UseCase/ → GetCurrenciesUseCase, GetCurrencyPairsUseCase
    │   ├── PaymentMethod/
    │   │   └── UseCase/ → GetPaymentMethodsUseCase
    │   ├── OrderBook/
    │   │   ├── Port/
    │   │   │   └── BybitOrderBookGatewayInterface.php
    │   │   └── UseCase/ → GetOrderBookUseCase, SyncOrderBookUseCase, CleanStaleOrdersUseCase
    │   ├── Advertisement/
    │   │   ├── Port/
    │   │   │   └── BybitAdvertisementGatewayInterface.php
    │   │   └── UseCase/ → ListAdvertisementsUseCase, CreateAdvertisementUseCase, DeactivateAdvertisementUseCase
    │   ├── Trade/
    │   │   ├── Port/
    │   │   │   └── BybitTradeGatewayInterface.php
    │   │   └── UseCase/ → ListTradesUseCase, GetTradeUseCase, ConfirmPaymentUseCase, ConfirmReceiptUseCase, SyncTradeHistoryUseCase
    │   ├── Counterparty/
    │   │   ├── Port/
    │   │   │   └── Outgoing/
    │   │   │       └── BybitCounterpartyGatewayInterface.php
    │   │   ├── UseCase/
    │   │   │   └── SyncCounterpartyUseCase.php
    │   │   └── Dto/
    │   │       └── Result/
    │   │           └── CounterpartyResultDto.php
    │   ├── TradeChat/
    │   │   ├── Port/
    │   │   │   └── BybitChatGatewayInterface.php
    │   │   └── UseCase/ → GetChatHistoryUseCase, SendMessageUseCase, ExecuteChatScriptUseCase, ProcessPendingChatScriptsUseCase
    │   └── ChatScript/
    │       └── UseCase/ → ListChatScriptsUseCase, CreateChatScriptUseCase, UpdateChatScriptUseCase, DeleteChatScriptUseCase
    ├── Domain/
    │   ├── Currency/Entity, Repository, Enum (CurrencyTypeEnum)
    │   ├── PaymentMethod/Entity, Repository
    │   ├── OrderBook/Entity, Repository
    │   ├── Advertisement/Entity, Repository, Enum (AdvertisementStatusEnum, PriceTypeEnum)
    │   ├── Trade/Entity, Repository, Enum (TradeStatusEnum, CancelReasonEnum)
    │   ├── Counterparty/Repository (CounterpartyRepository — работает с b_user через Bitrix CUser API), Event (CounterpartySynced)
    │   ├── TradeChat/Entity, Repository, Enum (ContentTypeEnum, MessageTypeEnum)
    │   ├── ChatScript/Entity (ChatScript, ChatScriptStep, ChatScriptExecution + коллекции), Repository (x3), Enum (ExecutionStatusEnum)
    │   └── Shared/Enum (SideEnum: buy/sell)
    ├── Infrastructure/
    │   ├── Adapter/
    │   │   └── CurrencyQueryAdapter.php        # реализует CurrencyQueryInterface
    │   ├── Bybit/
    │   │   ├── BybitAdvertisementGateway.php   # реализует BybitAdvertisementGatewayInterface
    │   │   ├── BybitChatGateway.php            # реализует BybitChatGatewayInterface
    │   │   ├── BybitCounterpartyGateway.php    # реализует BybitCounterpartyGatewayInterface
    │   │   ├── BybitOrderBookGateway.php       # реализует BybitOrderBookGatewayInterface
    │   │   └── BybitTradeGateway.php           # реализует BybitTradeGatewayInterface
    │   └── Controller/
    │       └── BaseExchangeController.php      # базовый контроллер с инициализацией userId
    └── Presentation/
        ├── Controller/ → CurrencyController, PaymentMethodController, OrderBookController,
        │                 AdvertisementController, TradeController, TradeChatController, ChatScriptController
        └── Command/
            ├── SyncOrderBookCommand.php        # polling стакана ордеров (каждые 10 сек)
            ├── CleanStaleOrdersCommand.php     # очистка устаревших записей стакана (каждую 1 мин)
            ├── SyncTradesCommand.php           # polling новых/изменённых сделок + синхронизация контрагентов в b_user (каждые 10 сек)
            ├── SyncTradeHistoryCommand.php     # дозагрузка истории сделок (каждые 10 мин)
            └── ExecuteChatScriptsCommand.php   # выполнение отложенных шагов скриптов (каждые 5 сек)
```

### Реализуемые контракты

| Контракт из `rebit.share` | Адаптер |
|----------------------------|---------|
| `CurrencyQueryInterface` | `Infrastructure/Adapter/CurrencyQueryAdapter` |

### Потребляемые контракты

| Контракт из `rebit.share` | Где используется |
|----------------------------|------------------|
| `BybitClientInterface` | Все Bybit-шлюзы в `Infrastructure/Bybit/` |
| `BybitConnectionResolverInterface` | Все Bybit-шлюзы в `Infrastructure/Bybit/` |
| `BalanceQueryInterface` | `CreateAdvertisementUseCase` (проверка баланса перед созданием объявления) |

### HL-блоки

| HL-блок | Таблица | Назначение |
|---------|---------|-----------|
| `RebitCurrency` | `rebit_currency` | Справочник валют |
| `RebitCurrencyPair` | `rebit_currency_pair` | Торговые пары |
| `RebitPaymentMethod` | `rebit_payment_method` | Способы оплаты |
| `RebitOrderBook` | `rebit_order_book` | Кэш стакана P2P-ордеров |
| `RebitAdvertisement` | `rebit_advertisement` | Объявления пользователей |
| `RebitTrade` | `rebit_trade` | Сделки |
| `RebitTradeMessage` | `rebit_trade_message` | Сообщения чата сделки |
| `RebitTradeChatScript` | `rebit_trade_chat_script` | Скрипты автосообщений |
| `RebitTradeChatScriptStep` | `rebit_trade_chat_script_step` | Шаги скрипта |
| `RebitChatScriptExecution` | `rebit_chat_script_execution` | Очередь исполнения скриптов |

### Внутренние порты

| Порт | Адаптер | Описание |
|------|---------|----------|
| `BybitOrderBookGatewayInterface` | `Infrastructure/Bybit/BybitOrderBookGateway` | Polling стакана Bybit |
| `BybitAdvertisementGatewayInterface` | `Infrastructure/Bybit/BybitAdvertisementGateway` | CRUD объявлений через Bybit API |
| `BybitTradeGatewayInterface` | `Infrastructure/Bybit/BybitTradeGateway` | Операции со сделками через Bybit API |
| `BybitChatGatewayInterface` | `Infrastructure/Bybit/BybitChatGateway` | Отправка сообщений в чат Bybit |
| `BybitCounterpartyGatewayInterface` | `Infrastructure/Bybit/BybitCounterpartyGateway` | Получение профиля контрагента через `user/order/personal/info` |

### Работа с контрагентами

> Подробнее: [scenario.md § 13](scenario.md#сценарий-13-сбор-и-сохранение-данных-о-контрагенте), [database.md § 1.2–1.3](database.md#12-группа-пользователей-контрагенты-b_group).

- `SyncCounterpartyUseCase` вызывается из `SyncTradesCommand` при обнаружении нового ордера.
- Данные контрагента из `POST /v5/p2p/user/order/personal/info` сохраняются в `b_user` с UF-полями `UF_BYBIT_*`.
- Контрагенты помещаются в группу пользователей **«Контрагенты»** (`COUNTERPARTIES`), `ACTIVE` = `N`.
- `CounterpartyRepository` работает с `b_user` через Bitrix CUser API (не Highload-блок).
- При повторной сделке — обновление UF-полей (статистика, рейтинг, онлайн-статус).

### Ограничения Bybit P2P API

| Операция | Bybit API | Решение |
|----------|:---------:|--------|
| Создание сделки | ❌ | Сделки создаются на UI Bybit. Rebit обнаруживает их через polling `order/pending/simplifyList` |
| Отмена сделки | ❌ | Отмена по таймеру Bybit. Статус обновляется через polling `order/info` |
| Арбитраж | ❌ | Редирект в UI Bybit. Статус `disputed` — через polling |
| История чата | ❌ | Только исходящие сообщения сохраняются локально в `rebit_trade_message` |
| Способы оплаты пользователя | ✅ | `POST /v5/p2p/user/payment/list` → `rebit.identity/SyncPaymentMethodsUseCase` |

---

## 12. Запланированные модули

Следующие модули описаны в [domain.md](domain.md) и будут реализованы по мере развития платформы.

### 12.2. `rebit.notification` — Уведомления

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

### 12.3. `rebit.security` — Безопасность

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

## 13. Фоновые процессы

Фоновые процессы размещаются в `Presentation/Command/` модуля, к чьему домену они относятся.

### Существующие

| Команда | Модуль | Класс | Описание | Интервал |
|---------|--------|-------|----------|----------|
| Синхронизация балансов | `rebit.wallet` | `Presentation/Command/SyncBalancesCommand` | Синхронизация балансов активных пользователей с Bybit | — |
| Синхронизация стакана | `rebit.exchange` | `Presentation/Command/SyncOrderBookCommand` | Polling `POST /v5/p2p/item/online` → обновление `rebit_order_book` | 10 сек |
| Очистка стакана | `rebit.exchange` | `Presentation/Command/CleanStaleOrdersCommand` | Удаление записей `rebit_order_book` старше 5 мин | 1 мин |
| Синхронизация сделок | `rebit.exchange` | `Presentation/Command/SyncTradesCommand` | Polling `order/pending/simplifyList` → обнаружение новых ордеров, синхронизация контрагентов в `b_user` (группа «Контрагенты») и обновление статусов | 10 сек |
| Синхронизация истории сделок | `rebit.exchange` | `Presentation/Command/SyncTradeHistoryCommand` | Polling `order/simplifyList` → дозагрузка завершённых ордеров в `rebit_trade` | 10 мин |
| Выполнение шагов скриптов | `rebit.exchange` | `Presentation/Command/ExecuteChatScriptsCommand` | Отправка отложенных сообщений из `rebit_chat_script_execution` через `order/message/send` | 5 сек |

### Запланированные

| Команда | Модуль | Описание | Интервал |
|---------|--------|----------|----------|
| Синхронизация платёжных методов | `rebit.identity` | `POST /v5/p2p/user/payment/list` → актуализация платёжных методов активных пользователей | 30 мин |
| Мониторинг активности | `rebit.security` | Анализ паттернов подозрительной активности | 5 мин |

---

## 14. Шаблоны файлов

### 14.1. `include.php`

```php
<?php

declare(strict_types=1);

use Bitrix\Main\Loader;
use Rebit\Share\Infrastructure\Bitrix\Module\ModuleHelper;

Loader::includeModule('highloadblock');

ModuleHelper::validateModuleInstalled('rebit.share');

ModuleHelper::compileHLEntities(['RebitEntityName']);
```

### 14.2. `.settings.php`

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

### 14.3. `install/index.php`

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

### 14.4. `routes.php`

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

### 14.5. DI-файл домена

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

### 14.6. UseCase

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

### 14.7. Controller

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

## 15. Сводная статистика

### Реализованные модули

| Модуль | Контроллеры | UseCases | Entities | Repositories | Enums | Events | Services | Ports |
|--------|:-----------:|:--------:|:--------:|:------------:|:-----:|:------:|:--------:|:-----:|
| `rebit.share` | 1 | 1 | — | — | 2 | — | — | — |
| `rebit.auth` | 1 | 4 | 4 | 2 | — | — | 4 | 4 |
| `rebit.bybit` | — | — | — | — | — | — | — | — |
| `rebit.identity` | 2 | 6 | 1+coll | 1 | 2 | 4 | 2 | 1 |
| `rebit.wallet` | 2 | 5 | 2+coll | 2 | 1 | 5 | 1 | 1 |
| `rebit.exchange` | 7 | 20 | 7+coll | 9 | 6 | 1 | — | 5 |
| **Итого** | **13** | **36** | **14** | **14** | **11** | **10** | **7** | **11** |

### Межмодульные контракты

| Контракт | Поставщик | Потребители |
|----------|-----------|-------------|
| `TokenResolverInterface` | `rebit.auth` | Middleware |
| `BybitClientInterface` | `rebit.bybit` | `rebit.identity`, `rebit.wallet`, `rebit.exchange` |
| `BybitConnectionResolverInterface` | `rebit.identity` | `rebit.wallet`, `rebit.exchange` |
| `CacheCleanerInterface` | `rebit.share` | Все модули |
| `BalanceQueryInterface` | `rebit.wallet` | `rebit.exchange` |
| `CurrencyQueryInterface` | `rebit.exchange` | `rebit.wallet` |
| `FileServiceInterface` | ⚠️ не реализован | — |
| `NotificationDispatcherInterface` | 🔜 `rebit.notification` | `rebit.exchange` (планируется) |
