# Прототип модулей Bitrix: P2P-платформа Rebit

> Модули лежат в `local/modules/rebit.<name>/`.
> Каждый модуль соответствует одному Bounded Context из [domain.md](domain.md).
> Общая инфраструктура — в `rebit.share` (уже существует).

---

## 1. Карта модулей

| # | Модуль | Домен | Namespace | Зависимости |
|---|--------|-------|-----------|-------------|
| 1 | `rebit.share` | — (инфраструктура) | `Rebit\Share` | — |
| 2 | `rebit.identity` | Identity | `Rebit\Identity` | `rebit.share` |
| 3 | `rebit.exchange` | Exchange | `Rebit\Exchange` | `rebit.share`, `rebit.identity`, `rebit.wallet` |
| 4 | `rebit.wallet` | Wallet | `Rebit\Wallet` | `rebit.share`, `rebit.identity` |
| 5 | `rebit.notification` | Notification | `Rebit\Notification` | `rebit.share` |
| 6 | `rebit.security` | Security | `Rebit\Security` | `rebit.share`, `rebit.identity` |

```
rebit.share ◄─── rebit.identity ◄─── rebit.exchange
     ▲                 ▲                    │
     │                 │                    │
     ├──── rebit.wallet ◄───────────────────┘
     │
     ├──── rebit.notification (слушает события всех модулей)
     │
     └──── rebit.security
```

---

## 2. Соглашения по структуре модуля

Каждый модуль следует единой структуре, проверенной в `rebit.photoorder`:

```
rebit.<name>/
├── .settings.php          # DI-контейнер (ServiceLocator)
├── include.php            # Загрузка зависимостей, class_alias
├── routes.php             # Маршруты (Bitrix RoutingConfigurator)
├── install/
│   └── index.php          # Установщик модуля
└── lib/
    ├── Controller/        # JSON-контроллеры (наследуют BaseJsonController)
    ├── Application/       # Use Cases — оркестрация доменной логики
    │   └── <Feature>/
    │       └── UseCase/
    └── Domain/            # Доменный слой
        └── <Feature>/
            ├── Entity/
            │   ├── Table/         # Bitrix ORM DataManager (getObjectClass, getCollectionClass)
            │   ├── <Name>.php     # Entity-объект
            │   └── <Name>Collection.php  # Коллекция сущностей
            ├── Repository/# Репозитории (raw SQL / Bitrix ORM / HL-блоки)
            ├── Dto/
            │   ├── Request/   # Входные DTO
            │   └── Result/    # Выходные DTO
            ├── Enum/      # Доменные enum-ы
            ├── Event/     # Доменные события
            ├── Service/   # Доменные сервисы (если нужна логика вне UseCase)
            └── ValueObject/ # Value Objects
```

**Правила:**
- Контроллеры — только в `Controller/`, наследуют `Rebit\Share\Infrastructure\Controller\BaseJsonController`.
- UseCase — `final readonly class`, принимает RequestDto, возвращает ResultDto.
- Репозитории HL-блоков наследуют `Rebit\Share\Infrastructure\Repository\AbstractHLBlockRepository`.
- Entity-объект — наследует скомпилированный `EO_*` класс HL-блока.
- Collection — наследует `EO_*_Collection`.
- Table — `final class`, наследует скомпилированный DataManager HL-блока, переопределяет `getObjectClass()` и `getCollectionClass()`.
- Все enum-ы — `enum` PHP 8.1+ с `string` backed type.
- Все классы `final readonly` где возможно (Entity и Collection — `final`, но не `readonly`, т.к. наследуют Bitrix ORM).
- `declare(strict_types=1)` во всех файлах.

---

## 3. Модуль `rebit.identity`

**Домен:** Identity — аутентификация, API-ключи Bybit.

### Структура

```
rebit.identity/
├── .settings.php
├── include.php
├── routes.php
├── install/
│   └── index.php
└── lib/
    ├── Controller/
    │   └── ApiConnectionController.php
    ├── Application/
    │   └── ApiConnection/
    │       └── UseCase/
    │           ├── ConnectApiUseCase.php
    │           ├── DisconnectApiUseCase.php
    │           └── VerifyApiUseCase.php
    └── Domain/
        └── ApiConnection/
            ├── Entity/
            │   ├── Table/
            │   │   └── ApiConnectionTable.php
            │   ├── ApiConnection.php
            │   └── ApiConnectionCollection.php
            ├── Repository/
            │   └── ApiConnectionRepository.php
            ├── Dto/
            │   ├── Request/
            │   │   └── ConnectApiRequestDto.php
            │   └── Result/
            │       └── ApiConnectionResultDto.php
            ├── Enum/
            │   ├── ConnectionModeEnum.php
            │   └── ConnectionStatusEnum.php
            ├── Event/
            │   ├── ApiConnectionCreated.php
            │   ├── ApiConnectionRevoked.php
            │   └── ApiConnectionFailed.php
            └── Service/
                └── ApiKeyEncryptor.php
```

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

### DI (.settings.php) — ключевые сервисы

```php
ApiKeyEncryptor::class
ApiConnectionRepository::class
ConnectApiUseCase::class
DisconnectApiUseCase::class
VerifyApiUseCase::class
```

---

## 4. Модуль `rebit.exchange`

**Домен:** Exchange — стаканы, объявления, сделки, чат сделки, скрипты автосообщений.

> Самый крупный модуль. Фичи сгруппированы по поддоменам внутри `Domain/`.

### Структура

```
rebit.exchange/
├── .settings.php
├── include.php
├── routes.php
├── install/
│   └── index.php
└── lib/
    ├── Controller/
    │   ├── OrderBookController.php
    │   ├── AdvertisementController.php
    │   ├── TradeController.php
    │   ├── TradeChatController.php
    │   └── ChatScriptController.php
    ├── Application/
    │   ├── OrderBook/
    │   │   └── UseCase/
    │   │       └── GetOrderBookUseCase.php
    │   ├── Advertisement/
    │   │   └── UseCase/
    │   │       ├── CreateAdvertisementUseCase.php
    │   │       ├── UpdateAdvertisementUseCase.php
    │   │       ├── DeactivateAdvertisementUseCase.php
    │   │       └── ListAdvertisementsUseCase.php
    │   ├── Trade/
    │   │   └── UseCase/
    │   │       ├── CreateTradeUseCase.php
    │   │       ├── ConfirmPaymentUseCase.php
    │   │       ├── ConfirmReceiptUseCase.php
    │   │       ├── CancelTradeUseCase.php
    │   │       ├── OpenDisputeUseCase.php
    │   │       ├── GetTradeUseCase.php
    │   │       └── ListTradesUseCase.php
    │   ├── TradeChat/
    │   │   └── UseCase/
    │   │       ├── SendMessageUseCase.php
    │   │       ├── GetChatHistoryUseCase.php
    │   │       └── MarkMessagesReadUseCase.php
    │   └── ChatScript/
    │       └── UseCase/
    │           ├── CreateChatScriptUseCase.php
    │           ├── UpdateChatScriptUseCase.php
    │           ├── DeleteChatScriptUseCase.php
    │           ├── ListChatScriptsUseCase.php
    │           └── ExecuteChatScriptUseCase.php
    └── Domain/
        ├── Currency/
        │   ├── Entity/
        │   │   ├── Table/
        │   │   │   ├── CurrencyTable.php
        │   │   │   └── CurrencyPairTable.php
        │   │   ├── Currency.php
        │   │   ├── CurrencyCollection.php
        │   │   ├── CurrencyPair.php
        │   │   └── CurrencyPairCollection.php
        │   ├── Repository/
        │   │   ├── CurrencyRepository.php
        │   │   └── CurrencyPairRepository.php
        │   └── Enum/
        │       └── CurrencyTypeEnum.php       # crypto | fiat
        ├── PaymentMethod/
        │   ├── Entity/
        │   │   ├── Table/
        │   │   │   └── PaymentMethodTable.php
        │   │   ├── PaymentMethod.php
        │   │   └── PaymentMethodCollection.php
        │   └── Repository/
        │       └── PaymentMethodRepository.php
        ├── OrderBook/
        │   ├── Entity/
        │   │   ├── Table/
        │   │   │   └── OrderBookEntryTable.php
        │   │   ├── OrderBookEntry.php
        │   │   └── OrderBookEntryCollection.php
        │   ├── Repository/
        │   │   └── OrderBookRepository.php
        │   ├── Dto/
        │   │   ├── Request/
        │   │   │   └── OrderBookFilterDto.php
        │   │   └── Result/
        │   │       ├── OrderBookResultDto.php
        │   │       └── OrderBookEntryDto.php
        │   └── Enum/
        │       └── SideEnum.php               # buy | sell
        ├── Advertisement/
        │   ├── Entity/
        │   │   ├── Table/
        │   │   │   └── AdvertisementTable.php
        │   │   ├── Advertisement.php
        │   │   └── AdvertisementCollection.php
        │   ├── Repository/
        │   │   └── AdvertisementRepository.php
        │   ├── Dto/
        │   │   ├── Request/
        │   │   │   ├── CreateAdvertisementDto.php
        │   │   │   └── UpdateAdvertisementDto.php
        │   │   └── Result/
        │   │       └── AdvertisementResultDto.php
        │   └── Enum/
        │       ├── PriceTypeEnum.php           # fixed | floating
        │       └── AdvertisementStatusEnum.php # active | paused | completed | cancelled
        ├── Trade/
        │   ├── Entity/
        │   │   ├── Table/
        │   │   │   └── TradeTable.php
        │   │   ├── Trade.php
        │   │   └── TradeCollection.php
        │   ├── Repository/
        │   │   └── TradeRepository.php
        │   ├── Dto/
        │   │   ├── Request/
        │   │   │   ├── CreateTradeDto.php
        │   │   │   └── TradeFilterDto.php
        │   │   └── Result/
        │   │       ├── TradeResultDto.php
        │   │       └── TradeListResultDto.php
        │   ├── Enum/
        │   │   ├── TradeStatusEnum.php         # created | pending_payment | payment_sent | ...
        │   │   └── CancelReasonEnum.php        # timeout | user | insufficient_funds | dispute
        │   ├── Event/
        │   │   ├── TradeCreated.php
        │   │   ├── TradePaymentSent.php
        │   │   ├── TradePaymentConfirmed.php
        │   │   ├── TradeCompleted.php
        │   │   ├── TradeCancelled.php
        │   │   ├── TradeDisputed.php
        │   │   └── TradeTimerExpired.php
        │   └── Service/
        │       └── TradeStateMachine.php
        ├── TradeChat/
        │   ├── Entity/
        │   │   ├── Table/
        │   │   │   └── TradeMessageTable.php
        │   │   ├── TradeMessage.php
        │   │   └── TradeMessageCollection.php
        │   ├── Repository/
        │   │   └── TradeMessageRepository.php
        │   ├── Dto/
        │   │   ├── Request/
        │   │   │   └── SendMessageDto.php
        │   │   └── Result/
        │   │       ├── ChatHistoryResultDto.php
        │   │       └── TradeMessageDto.php
        │   ├── Enum/
        │   │   └── MessageTypeEnum.php         # user | system | script
        │   ├── Event/
        │   │   └── TradeMessageSent.php
        │   └── Service/
        │       └── AntiSpamService.php
        └── ChatScript/
            ├── Entity/
            │   ├── Table/
            │   │   ├── TradeChatScriptTable.php
            │   │   └── TradeChatScriptStepTable.php
            │   ├── TradeChatScript.php
            │   ├── TradeChatScriptCollection.php
            │   ├── TradeChatScriptStep.php
            │   └── TradeChatScriptStepCollection.php
            ├── Repository/
            │   ├── ChatScriptRepository.php
            │   └── ChatScriptStepRepository.php
            ├── Dto/
            │   ├── Request/
            │   │   ├── CreateChatScriptDto.php
            │   │   └── UpdateChatScriptDto.php
            │   └── Result/
            │       ├── ChatScriptResultDto.php
            │       └── ChatScriptStepDto.php
            ├── Event/
            │   ├── TradeChatScriptStarted.php
            │   └── TradeChatScriptDeleted.php
            └── Service/
                └── PlaceholderResolver.php
```

### Маршруты

```
# Стакан
GET    /api/v1/exchange/orderbook                        → GetOrderBookUseCase

# Справочники
GET    /api/v1/exchange/currencies                       → ListCurrenciesUseCase
GET    /api/v1/exchange/currency-pairs                   → ListCurrencyPairsUseCase
GET    /api/v1/exchange/payment-methods                  → ListPaymentMethodsUseCase

# Объявления
GET    /api/v1/exchange/advertisements                   → ListAdvertisementsUseCase
POST   /api/v1/exchange/advertisements                   → CreateAdvertisementUseCase
PATCH  /api/v1/exchange/advertisements/{id}              → UpdateAdvertisementUseCase
DELETE /api/v1/exchange/advertisements/{id}              → DeactivateAdvertisementUseCase

# Сделки
GET    /api/v1/exchange/trades                           → ListTradesUseCase
POST   /api/v1/exchange/trades                           → CreateTradeUseCase
GET    /api/v1/exchange/trades/{id}                      → GetTradeUseCase
POST   /api/v1/exchange/trades/{id}/confirm-payment      → ConfirmPaymentUseCase
POST   /api/v1/exchange/trades/{id}/confirm-receipt      → ConfirmReceiptUseCase
POST   /api/v1/exchange/trades/{id}/cancel               → CancelTradeUseCase
POST   /api/v1/exchange/trades/{id}/dispute              → OpenDisputeUseCase

# Чат сделки
GET    /api/v1/exchange/trades/{id}/chat                 → GetChatHistoryUseCase
POST   /api/v1/exchange/trades/{id}/chat                 → SendMessageUseCase
POST   /api/v1/exchange/trades/{id}/chat/read            → MarkMessagesReadUseCase

# Скрипты автосообщений
GET    /api/v1/exchange/chat-scripts                     → ListChatScriptsUseCase
POST   /api/v1/exchange/chat-scripts                     → CreateChatScriptUseCase
PATCH  /api/v1/exchange/chat-scripts/{id}                → UpdateChatScriptUseCase
DELETE /api/v1/exchange/chat-scripts/{id}                → DeleteChatScriptUseCase
```

### HL-блоки

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

### DI (.settings.php) — ключевые сервисы

```php
// Репозитории
CurrencyRepository::class
CurrencyPairRepository::class
PaymentMethodRepository::class
OrderBookRepository::class
AdvertisementRepository::class
TradeRepository::class
TradeMessageRepository::class
ChatScriptRepository::class
ChatScriptStepRepository::class

// Сервисы
TradeStateMachine::class
AntiSpamService::class
PlaceholderResolver::class

// UseCases (все)
GetOrderBookUseCase::class
CreateAdvertisementUseCase::class
// ... и т.д.
```

---

## 5. Модуль `rebit.wallet`

**Домен:** Wallet — балансы, транзакции, блокировка средств.

### Структура

```
rebit.wallet/
├── .settings.php
├── include.php
├── routes.php
├── install/
│   └── index.php
└── lib/
    ├── Controller/
    │   ├── BalanceController.php
    │   └── TransactionController.php
    ├── Application/
    │   ├── Balance/
    │   │   └── UseCase/
    │   │       ├── GetBalancesUseCase.php
    │   │       ├── LockFundsUseCase.php
    │   │       ├── UnlockFundsUseCase.php
    │   │       └── SyncBalancesUseCase.php
    │   └── Transaction/
    │       └── UseCase/
    │           ├── ListTransactionsUseCase.php
    │           └── ExportTransactionsUseCase.php
    └── Domain/
        ├── Balance/
        │   ├── Entity/
        │   │   ├── Table/
        │   │   │   └── BalanceTable.php
        │   │   ├── Balance.php
        │   │   └── BalanceCollection.php
        │   ├── Repository/
        │   │   └── BalanceRepository.php
        │   ├── Dto/
        │   │   ├── Request/
        │   │   │   └── LockFundsDto.php
        │   │   └── Result/
        │   │       └── BalanceResultDto.php
        │   ├── Event/
        │   │   ├── BalanceSynced.php
        │   │   ├── FundsLocked.php
        │   │   ├── FundsUnlocked.php
        │   │   ├── FundsTransferred.php
        │   │   └── BalanceDiscrepancyDetected.php
        │   └── Service/
        │       └── BalanceCalculator.php
        └── Transaction/
            ├── Entity/
            │   ├── Table/
            │   │   └── TransactionTable.php
            │   ├── Transaction.php
            │   └── TransactionCollection.php
            ├── Repository/
            │   └── TransactionRepository.php
            ├── Dto/
            │   ├── Request/
            │   │   └── TransactionFilterDto.php
            │   └── Result/
            │       ├── TransactionResultDto.php
            │       └── TransactionListResultDto.php
            └── Enum/
                └── TransactionTypeEnum.php   # deposit | withdrawal | trade_buy | ...
```

### Маршруты

```
GET    /api/v1/wallet/balances                    → GetBalancesUseCase
POST   /api/v1/wallet/balances/sync               → SyncBalancesUseCase
GET    /api/v1/wallet/transactions                 → ListTransactionsUseCase
GET    /api/v1/wallet/transactions/export          → ExportTransactionsUseCase
```

### HL-блоки

| HL-блок | Таблица |
|---------|---------|
| `RebitBalance` | `rebit_balance` |
| `RebitTransaction` | `rebit_transaction` |

---

## 6. Модуль `rebit.notification`

**Домен:** Notification — уведомления, каналы доставки, настройки.

### Структура

```
rebit.notification/
├── .settings.php
├── include.php
├── routes.php
├── install/
│   └── index.php
└── lib/
    ├── Controller/
    │   ├── NotificationController.php
    │   └── NotificationPreferenceController.php
    ├── Application/
    │   ├── Notification/
    │   │   └── UseCase/
    │   │       ├── ListNotificationsUseCase.php
    │   │       ├── MarkReadUseCase.php
    │   │       ├── MarkAllReadUseCase.php
    │   │       └── GetUnreadCountUseCase.php
    │   ├── Preference/
    │   │   └── UseCase/
    │   │       ├── GetPreferencesUseCase.php
    │   │       └── UpdatePreferencesUseCase.php
    │   └── Sender/
    │       └── UseCase/
    │           └── SendNotificationUseCase.php
    └── Domain/
        ├── Notification/
        │   ├── Entity/
        │   │   ├── Table/
        │   │   │   └── NotificationTable.php
        │   │   ├── Notification.php
        │   │   └── NotificationCollection.php
        │   ├── Repository/
        │   │   └── NotificationRepository.php
        │   ├── Dto/
        │   │   ├── Request/
        │   │   │   └── NotificationFilterDto.php
        │   │   └── Result/
        │   │       └── NotificationResultDto.php
        │   └── Enum/
        │       ├── NotificationCategoryEnum.php  # trade | system | security
        │       └── NotificationTypeEnum.php      # trade_created | payment_received | ...
        ├── Preference/
        │   ├── Entity/
        │   │   ├── Table/
        │   │   │   └── NotificationPreferenceTable.php
        │   │   ├── NotificationPreference.php
        │   │   └── NotificationPreferenceCollection.php
        │   └── Repository/
        │       └── NotificationPreferenceRepository.php
        └── Channel/
            ├── Enum/
            │   └── ChannelEnum.php               # in_app | push | email | telegram
            └── Service/
                ├── ChannelDispatcher.php
                ├── InAppChannel.php
                ├── PushChannel.php
                ├── EmailChannel.php
                └── TelegramChannel.php
```

### Маршруты

```
GET    /api/v1/notifications                         → ListNotificationsUseCase
GET    /api/v1/notifications/unread-count             → GetUnreadCountUseCase
POST   /api/v1/notifications/{id}/read                → MarkReadUseCase
POST   /api/v1/notifications/read-all                 → MarkAllReadUseCase
GET    /api/v1/notifications/preferences              → GetPreferencesUseCase
PATCH  /api/v1/notifications/preferences              → UpdatePreferencesUseCase
```

### HL-блоки

| HL-блок | Таблица |
|---------|---------|
| `RebitNotification` | `rebit_notification` |
| `RebitNotificationPreference` | `rebit_notification_preference` |

---

## 7. Модуль `rebit.security`

**Домен:** Security — сессии, 2FA, аудит, мониторинг.

### Структура

```
rebit.security/
├── .settings.php
├── include.php
├── routes.php
├── install/
│   └── index.php
└── lib/
    ├── Controller/
    │   ├── SessionController.php
    │   ├── TwoFactorController.php
    │   └── AuditController.php
    ├── Application/
    │   ├── Session/
    │   │   └── UseCase/
    │   │       ├── ListSessionsUseCase.php
    │   │       ├── TerminateSessionUseCase.php
    │   │       └── TerminateAllSessionsUseCase.php
    │   ├── TwoFactor/
    │   │   └── UseCase/
    │   │       ├── EnableTwoFactorUseCase.php
    │   │       ├── DisableTwoFactorUseCase.php
    │   │       └── VerifyTwoFactorUseCase.php
    │   ├── Audit/
    │   │   └── UseCase/
    │   │       └── ListAuditLogUseCase.php
    │   └── Alert/
    │       └── UseCase/
    │           ├── ListAlertsUseCase.php
    │           └── ResolveAlertUseCase.php
    └── Domain/
        ├── Session/
        │   ├── Entity/
        │   │   ├── Table/
        │   │   │   └── UserSessionTable.php
        │   │   ├── UserSession.php
        │   │   └── UserSessionCollection.php
        │   ├── Repository/
        │   │   └── UserSessionRepository.php
        │   └── Dto/
        │       └── Result/
        │           └── SessionResultDto.php
        ├── TwoFactor/
        │   ├── Entity/
        │   │   ├── Table/
        │   │   │   └── TwoFactorAuthTable.php
        │   │   ├── TwoFactorAuth.php
        │   │   └── TwoFactorAuthCollection.php
        │   ├── Repository/
        │   │   └── TwoFactorAuthRepository.php
        │   ├── Enum/
        │   │   └── TwoFactorMethodEnum.php     # totp | sms | email
        │   └── Service/
        │       └── TotpService.php
        ├── Audit/
        │   ├── Entity/
        │   │   ├── Table/
        │   │   │   └── AuditLogTable.php
        │   │   ├── AuditLog.php
        │   │   └── AuditLogCollection.php
        │   ├── Repository/
        │   │   └── AuditLogRepository.php
        │   ├── Dto/
        │   │   ├── Request/
        │   │   │   └── AuditFilterDto.php
        │   │   └── Result/
        │   │       └── AuditLogResultDto.php
        │   └── Service/
        │       └── AuditLogger.php
        └── Alert/
            ├── Entity/
            │   ├── Table/
            │   │   └── SecurityAlertTable.php
            │   ├── SecurityAlert.php
            │   └── SecurityAlertCollection.php
            ├── Repository/
            │   └── SecurityAlertRepository.php
            ├── Enum/
            │   ├── AlertTypeEnum.php           # frequent_cancellations | large_trade | ...
            │   └── AlertSeverityEnum.php       # low | medium | high | critical
            └── Service/
                └── SuspiciousActivityDetector.php
```

### Маршруты

```
# Сессии
GET    /api/v1/security/sessions                     → ListSessionsUseCase
DELETE /api/v1/security/sessions/{id}                 → TerminateSessionUseCase
DELETE /api/v1/security/sessions                      → TerminateAllSessionsUseCase

# 2FA
POST   /api/v1/security/2fa/enable                    → EnableTwoFactorUseCase
POST   /api/v1/security/2fa/disable                   → DisableTwoFactorUseCase
POST   /api/v1/security/2fa/verify                    → VerifyTwoFactorUseCase

# Аудит
GET    /api/v1/security/audit-log                     → ListAuditLogUseCase

# Алерты
GET    /api/v1/security/alerts                        → ListAlertsUseCase
POST   /api/v1/security/alerts/{id}/resolve           → ResolveAlertUseCase
```

### HL-блоки

| HL-блок | Таблица |
|---------|---------|
| `RebitUserSession` | `rebit_user_session` |
| `RebitAuditLog` | `rebit_audit_log` |
| `RebitSecurityAlert` | `rebit_security_alert` |
| `RebitTwoFactorAuth` | `rebit_two_factor_auth` |

---

## 8. Фоновые агенты (Cron / Bitrix Agents)

Фоновые процессы размещаются в модуле, к чьему домену они относятся.

| Агент | Модуль | Класс | Интервал |
|-------|--------|-------|----------|
| Синхронизация стакана | `rebit.exchange` | `Agent\SyncOrderBookAgent` | 10 сек |
| Очистка стакана | `rebit.exchange` | `Agent\CleanStaleOrdersAgent` | 1 мин |
| Проверка истекающих сделок | `rebit.exchange` | `Agent\CheckExpiredTradesAgent` | 1 мин |
| Выполнение шагов скриптов | `rebit.exchange` | `Agent\ExecuteScriptStepsAgent` | 5 сек |
| Синхронизация истории сделок | `rebit.exchange` | `Agent\SyncTradeHistoryAgent` | 10 мин |
| Синхронизация балансов | `rebit.wallet` | `Agent\SyncBalancesAgent` | 5 мин |
| Мониторинг активности | `rebit.security` | `Agent\MonitorSuspiciousActivityAgent` | 5 мин |

Агенты размещаются в `lib/Agent/` внутри соответствующего модуля.

---

## 9. Шаблоны файлов модуля

### 9.1. `include.php` (пример для `rebit.exchange`)

```php
<?php

declare(strict_types=1);

use Bitrix\Main\Application;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');

if (
    !Loader::includeModule('rebit.share')
    && !Application::getInstance()->getContext()->getRequest()->isAdminSection()
) {
    throw new RuntimeException('Module "rebit.share" is not installed!');
}

if (
    !Loader::includeModule('rebit.identity')
    && !Application::getInstance()->getContext()->getRequest()->isAdminSection()
) {
    throw new RuntimeException('Module "rebit.identity" is not installed!');
}

if (
    !Loader::includeModule('rebit.wallet')
    && !Application::getInstance()->getContext()->getRequest()->isAdminSection()
) {
    throw new RuntimeException('Module "rebit.wallet" is not installed!');
}
```

### 9.2. `.settings.php` (пример для `rebit.exchange`, фрагмент)

```php
<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Domain\Trade\Service\TradeStateMachine;
use Rebit\Exchange\Domain\TradeChat\Service\AntiSpamService;
use Rebit\Exchange\Application\Trade\UseCase\CreateTradeUseCase;

return [
    'services' => [
        'value' => [
            TradeRepository::class => [
                'className' => TradeRepository::class,
            ],
            TradeStateMachine::class => [
                'className' => TradeStateMachine::class,
            ],
            AntiSpamService::class => [
                'className' => AntiSpamService::class,
            ],
            CreateTradeUseCase::class => [
                'className' => CreateTradeUseCase::class,
                'constructorParams' => static function () {
                    $sl = ServiceLocator::getInstance();

                    return [
                        $sl->get(TradeRepository::class),
                        $sl->get(TradeStateMachine::class),
                    ];
                },
            ],
        ],
        'readonly' => true,
    ],
];
```

### 9.3. `routes.php` (пример для `rebit.exchange`, фрагмент)

```php
<?php

declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;
use Rebit\Exchange\Controller\OrderBookController;
use Rebit\Exchange\Controller\TradeController;
use Rebit\Exchange\Controller\TradeChatController;
use Rebit\Exchange\Controller\ChatScriptController;
use Rebit\Exchange\Controller\AdvertisementController;

return static function (RoutingConfigurator $routes) {
    // Стакан
    $routes->get('/api/v1/exchange/orderbook', [OrderBookController::class, 'listAction']);

    // Объявления
    $routes->get('/api/v1/exchange/advertisements', [AdvertisementController::class, 'listAction']);
    $routes->post('/api/v1/exchange/advertisements', [AdvertisementController::class, 'createAction']);

    // Сделки
    $routes->get('/api/v1/exchange/trades', [TradeController::class, 'listAction']);
    $routes->post('/api/v1/exchange/trades', [TradeController::class, 'createAction']);
    $routes->get('/api/v1/exchange/trades/{id}', [TradeController::class, 'getAction']);
    $routes->post('/api/v1/exchange/trades/{id}/confirm-payment', [TradeController::class, 'confirmPaymentAction']);
    $routes->post('/api/v1/exchange/trades/{id}/cancel', [TradeController::class, 'cancelAction']);

    // Чат сделки
    $routes->get('/api/v1/exchange/trades/{id}/chat', [TradeChatController::class, 'historyAction']);
    $routes->post('/api/v1/exchange/trades/{id}/chat', [TradeChatController::class, 'sendAction']);
    $routes->post('/api/v1/exchange/trades/{id}/chat/read', [TradeChatController::class, 'markReadAction']);

    // Скрипты автосообщений
    $routes->get('/api/v1/exchange/chat-scripts', [ChatScriptController::class, 'listAction']);
    $routes->post('/api/v1/exchange/chat-scripts', [ChatScriptController::class, 'createAction']);
    $routes->patch('/api/v1/exchange/chat-scripts/{id}', [ChatScriptController::class, 'updateAction']);
    $routes->delete('/api/v1/exchange/chat-scripts/{id}', [ChatScriptController::class, 'deleteAction']);
};
```

### 9.4. Пример Enum

```php
<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Trade\Enum;

enum TradeStatusEnum: string
{
    case Created = 'created';
    case PendingPayment = 'pending_payment';
    case PaymentSent = 'payment_sent';
    case PaymentConfirmed = 'payment_confirmed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Disputed = 'disputed';
}
```

### 9.5. Пример Entity/Table (HL-блок)

**Table — DataManager (`Domain/Trade/Entity/Table/TradeTable.php`):**

```php
<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Trade\Entity\Table;

use Bitrix\Highloadblock\HighloadBlockTable;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Entity\TradeCollection;

/**
 * DataManager для HL-блока RebitTrade.
 *
 * Регистрируется через compileEntity при первом обращении.
 * Переопределяет getObjectClass / getCollectionClass для
 * подмены стандартных EO_ на доменные Entity / Collection.
 */
final class TradeTable extends /* compiled HL DataManager */
{
    public static function getObjectClass(): string
    {
        return Trade::class;
    }

    public static function getCollectionClass(): string
    {
        return TradeCollection::class;
    }
}
```

**Entity — объект (`Domain/Trade/Entity/Trade.php`):**

```php
<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Trade\Entity;

/**
 * Entity-объект сделки.
 * Наследует скомпилированный EO_* класс HL-блока RebitTrade.
 */
final class Trade extends /* EO_RebitTrade */ {}
```

**Collection (`Domain/Trade/Entity/TradeCollection.php`):**

```php
<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Trade\Entity;

/**
 * Коллекция сделок.
 * Наследует скомпилированный EO_*_Collection класс HL-блока.
 */
final class TradeCollection extends /* EO_RebitTrade_Collection */ {}
```

> **Примечание:** конкретные родительские классы `EO_*` генерируются Bitrix при компиляции HL-блока.
> Для автокомплита и статического анализа используется файл `orm_annotations.php`.

### 9.6. Пример Repository (HL-блок)

```php
<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Trade\Repository;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Exchange\Domain\Trade\Entity\TradeCollection;
use Rebit\Exchange\Domain\Trade\Entity\Table\TradeTable;
use Rebit\Exchange\Domain\Trade\Enum\TradeStatusEnum;

final class TradeRepository
{
    private const int TTL = 60;

    /**
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function findById(int $id): ?Trade
    {
        return TradeTable::query()
            ->setSelect(['*'])
            ->where('ID', $id)
            ->setCacheTtl(self::TTL)
            ->exec()
            ->fetchObject();
    }

    /**
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function findActiveByBuyerUserId(int $userId): TradeCollection
    {
        return TradeTable::query()
            ->setSelect(['*'])
            ->where('UF_BUYER_USER_ID', $userId)
            ->whereIn('UF_STATUS', [
                TradeStatusEnum::PendingPayment->value,
                TradeStatusEnum::PaymentSent->value,
                TradeStatusEnum::PaymentConfirmed->value,
            ])
            ->setOrder(['UF_CREATED_AT' => 'DESC'])
            ->exec()
            ->fetchCollection();
    }
}
```

### 9.7. Пример UseCase

```php
<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\UseCase;

use Rebit\Exchange\Domain\Trade\Dto\Request\CreateTradeDto;
use Rebit\Exchange\Domain\Trade\Dto\Result\TradeResultDto;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Domain\Trade\Service\TradeStateMachine;

final readonly class CreateTradeUseCase
{
    public function __construct(
        private TradeRepository $tradeRepository,
        private TradeStateMachine $stateMachine,
    ) {}

    public function execute(CreateTradeDto $dto): TradeResultDto
    {
        // 1. Проверить баланс (через rebit.wallet)
        // 2. Заблокировать средства
        // 3. Создать ордер в Bybit API
        // 4. Сохранить сделку в БД
        // 5. Запустить скрипт чата (если есть)
        // 6. Вернуть результат
    }
}
```

### 9.8. Пример Controller

```php
<?php

declare(strict_types=1);

namespace Rebit\Exchange\Controller;

use Rebit\Exchange\Application\Trade\UseCase\CreateTradeUseCase;
use Rebit\Exchange\Application\Trade\UseCase\GetTradeUseCase;
use Rebit\Exchange\Application\Trade\UseCase\ListTradesUseCase;
use Rebit\Exchange\Domain\Trade\Dto\Request\CreateTradeDto;
use Rebit\Exchange\Domain\Trade\Dto\Request\TradeFilterDto;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;
use Rebit\Share\Infrastructure\Controller\BaseJsonController;

final class TradeController extends BaseJsonController
{
    public function __construct(
        private readonly CreateTradeUseCase $createUseCase,
        private readonly GetTradeUseCase $getUseCase,
        private readonly ListTradesUseCase $listUseCase,
    ) {
        parent::__construct();
    }

    public function createAction(CreateTradeDto $dto): ControllerJson
    {
        return $this->json($this->createUseCase->execute($dto));
    }

    public function getAction(int $id): ControllerJson
    {
        return $this->json($this->getUseCase->execute($id));
    }

    public function listAction(TradeFilterDto $dto): ControllerJson
    {
        return $this->json($this->listUseCase->execute($dto));
    }
}
```

---

## 10. Сводная статистика

| Модуль | Контроллеры | UseCases | Entity/Table/Collection | Repositories | Enums | Events | Services |
|--------|:-----------:|:--------:|:-----------------------:|:------------:|:-----:|:------:|:--------:|
| `rebit.identity` | 1 | 4 | 1 / 1 / 1 | 1 | 2 | 3 | 1 |
| `rebit.exchange` | 5 | 16 | 9 / 9 / 9 | 9 | 6 | 10 | 3 |
| `rebit.wallet` | 2 | 5 | 2 / 2 / 2 | 2 | 1 | 5 | 1 |
| `rebit.notification` | 2 | 7 | 2 / 2 / 2 | 2 | 3 | — | 5 |
| `rebit.security` | 3 | 8 | 4 / 4 / 4 | 4 | 3 | — | 3 |
| **Итого** | **13** | **40** | **18 / 18 / 18** | **18** | **15** | **18** | **13** |
