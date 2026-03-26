# Ревью модулей rebit.wallet и rebit.exchange

> Дата: 2026-03-26
> Автор: Code Review Agent

---

## Общая оценка

| Модуль | Архитектура | PHP-стиль | DI | Тесты | Роуты | Итого |
|---|---|---|---|---|---|---|
| **rebit.wallet** | ⚠️ Есть нарушения | ✅ Хорошо | ✅ Корректно | ❌ Отсутствуют | ✅ Работают | ⚠️ |
| **rebit.exchange** | ⚠️ Есть замечания | ⚠️ Мелкие отклонения | ✅ Корректно | ✅ 86/86 pass | ✅ Работают | ✅ |

---

## 1. Архитектурные нарушения

### 🔴 CRITICAL: `BalanceCalculator` бросает `HttpException` из Domain

**Файл:** `rebit.wallet/lib/Domain/Balance/Service/BalanceCalculator.php:26-34`

Domain-сервис импортирует `Rebit\Share\Shared\Exception\HttpException` и кидает его напрямую. Это нарушает DDD — доменный слой не должен знать о HTTP-транспорте.

**Рекомендация:** Создать доменное исключение `InsufficientFundsException` и `InsufficientLockedFundsException`, маппить на HTTP 422 в Application или Presentation.

```php
// Domain/Balance/Exception/InsufficientFundsException.php
final class InsufficientFundsException extends \DomainException { ... }

// Application или Presentation — перехват и маппинг на HttpException(422)
```

**Примечание:** Документация (`07_коды-возврата.md`) допускает `HttpException` в Application для чисто API-модулей, но **Domain — это запрещено в любом случае**.

---

### 🔴 CRITICAL: `SyncBalancesUseCase` создаёт `new GetBalancesUseCase()` внутри execute()

**Файл:** `rebit.wallet/lib/Application/Balance/UseCase/SyncBalancesUseCase.php:40`

```php
return new GetBalancesUseCase($this->balanceRepository)->execute($userId);
```

Нарушение DI — UseCase создаёт другой UseCase вручную. Зависимость должна инъектироваться через конструктор.

**Рекомендация:** Добавить `GetBalancesUseCase` в конструктор `SyncBalancesUseCase` и в DI-конфигурацию.

---

### 🟡 WARNING: `ListTradesUseCase::toResultDto()` — public static метод, используемый другими UseCase

**Файлы:**
- `rebit.exchange/lib/Application/Trade/UseCase/ListTradesUseCase.php:37`
- `ConfirmPaymentUseCase.php:52` — `ListTradesUseCase::toResultDto($trade)`
- `ConfirmReceiptUseCase.php:50` — `ListTradesUseCase::toResultDto($trade)`
- `GetTradeUseCase.php:60` — `ListTradesUseCase::toResultDto($trade)`

UseCase предоставляет публичную статическую утилиту для других UseCase, что создаёт скрытую связность.

**Рекомендация:** Вынести маппинг в отдельный `TradeResultDtoMapper` (в `Application/Trade/Mapper/`) или дублировать как приватный метод в каждом UseCase.

---

### 🟡 WARNING: `LockFundsUseCase` и `UnlockFundsUseCase` бросают `HttpException` напрямую

**Файлы:** `rebit.wallet/lib/Application/Balance/UseCase/LockFundsUseCase.php:39-42`, `UnlockFundsUseCase.php:39-42`

```php
throw new HttpException(
    sprintf('Баланс не найден: userId=%d, currencyId=%d', $dto->userId, $dto->currencyId),
    404,
);
```

Эти UseCase вызываются из модуля exchange (не из HTTP). Если допускается только API-использование — ОК по документации, но стоит зафиксировать.

---

### 🟡 WARNING: Domain Events в wallet объявлены, но не используются

**Файлы:**
- `Domain/Balance/Event/FundsLocked.php`
- `Domain/Balance/Event/FundsUnlocked.php`
- `Domain/Balance/Event/FundsTransferred.php`
- `Domain/Balance/Event/BalanceSynced.php`
- `Domain/Balance/Event/BalanceDiscrepancyDetected.php`

Классы событий объявлены, но нигде не диспатчатся. Отсутствует `events/events.php` в обоих модулях.

**Рекомендация:** Либо подключить механизм публикации событий, либо удалить неиспользуемые классы.

---

## 2. PHP-стиль и Code Standards

### 🟡 Enum case naming — `PascalCase` вместо `UPPER_SNAKE_CASE`

По CLAUDE.md стандарт: `case FIRST_ELEMENT = 'firstElement'`

Текущее состояние:
- `TransactionTypeEnum`: `case Deposit = 'deposit'` ❌
- `SideEnum`: `case Buy = 'buy'` ❌
- `PriceTypeEnum`: `case Fixed = 'fixed'` ❌
- `TradeStatusEnum`: `case PendingPayment = 'pending_payment'` ❌
- `AdvertisementStatusEnum`: `case Active = 'active'` ❌

**Рекомендация:** Переименовать на `case DEPOSIT = 'deposit'`, `case BUY = 'buy'` и т.д. Или зафиксировать текущий стиль как допустимое отклонение для проекта.

---

### 🟡 `TransactionFilterDto` — `final class` вместо `final readonly class`

**Файл:** `rebit.wallet/lib/Application/Transaction/Dto/Request/TransactionFilterDto.php:12`

```php
final class TransactionFilterDto implements RequestDtoInterface
```

По стандарту DTO должны быть `final readonly class`.

Аналогично:
- `CreateAdvertisementRequestDto` — `final class` ❌
- `SendMessageRequestDto` — `final class` ❌
- `CreateChatScriptRequestDto` — `final class` ❌
- `UpdateChatScriptRequestDto` — `final class` ❌

**Примечание:** Свойства объявлены `readonly`, но `readonly` на уровне класса даёт дополнительную гарантию.

---

### 🟡 Отсутствие валидации `#[Assert\...]` на RequestDto

В `rebit.auth` эталонный `LoginRequestDto` использует Symfony Assert:
```php
#[Assert\NotBlank(message: 'Email обязателен.')]
#[Assert\Email(message: 'Некорректный email.')]
```

В `rebit.exchange` и `rebit.wallet` все RequestDto не имеют атрибутов валидации:
- `CreateAdvertisementRequestDto` — нет валидации полей
- `SendMessageRequestDto` — нет `#[Assert\NotBlank]` для message
- `TransactionFilterDto` — нет валидации limit/offset

**Рекомендация:** Добавить Assert-атрибуты или ручную валидацию в UseCase.

---

### ✅ Что соблюдается корректно

- `declare(strict_types=1)` — во всех файлах ✅
- Yoda style: `null === $balance`, `[] !== $coins`, `'' !== $bybitOrderId` ✅
- `final readonly class` на UseCase и ResultDto ✅
- `match` вместо `switch` ✅
- Явные проверки вместо `empty()` ✅
- phpDoc для массивов в стиле phpStan с переносом ✅
- Cast без пробела: `(int)$value`, `(float)$dto->price` ✅

---

## 3. DI-конфигурация

### ✅ Корректно

- Interface-key используют `constructor` (а не `className + constructorParams`) ✅
- Concrete class без зависимостей используют `className` ✅
- `.settings.php` — `array_merge` из `di/` файлов ✅
- Console commands зарегистрированы ✅
- `ServiceLocator` только в DI-конфигах и Infrastructure ✅

### 🟢 INFO: Нет `di/Layers/` — контроллеры в доменных файлах

По стандарту контроллеры должны быть в `di/Layers/Presentation.php`. Сейчас `BalanceController` зарегистрирован в `di/balance.php`, `TradeController` в `di/trade.php`.

**Рекомендация:** Вынести контроллеры и команды в `di/Layers/Presentation.php` при рефакторинге. На текущем этапе не критично.

---

## 4. Именование DTO

| DTO | Текущее имя | Стандарт | Статус |
|---|---|---|---|
| `LockFundsDto` | `*Dto` | `*InputDto` (Application вход) | ⚠️ Переименовать в `LockFundsInputDto` |
| `TransactionFilterDto` | `*FilterDto` | `*RequestDto` (Controller) | ⚠️ Переименовать в `TransactionFilterRequestDto` |
| `BalanceResultDto` | `*ResultDto` | `*ResultDto` | ✅ |
| `CreateAdvertisementRequestDto` | `*RequestDto` | `*RequestDto` | ✅ |
| `SendMessageRequestDto` | `*RequestDto` | `*RequestDto` | ✅ |
| `TradeResultDto` | `*ResultDto` | `*ResultDto` | ✅ |

---

## 5. Тесты

### 🔴 CRITICAL: rebit.wallet — тесты полностью отсутствуют

Модуль с финансовой логикой (блокировка/разблокировка средств, синхронизация балансов, расчёт дискрепанций) не имеет ни одного теста.

**Необходимые тесты:**
- `BalanceCalculator` — `assertCanLock`, `assertCanUnlock`, `detectDiscrepancy`
- `LockFundsUseCase` / `UnlockFundsUseCase` — happy path + edge cases
- `SyncBalancesUseCase` — синхронизация, создание новых, расхождения
- `GetBalancesUseCase` — пустой/непустой список
- `ListTransactionsUseCase` — фильтрация, пагинация
- `ExportTransactionsUseCase` — корректный лимит

### ✅ rebit.exchange — 86 тестов, все проходят

Покрытие:
- ✅ 22 UseCase покрыты тестами (по 2-5 сценариев каждый)
- ✅ Happy path + error scenarios (404, 403, валидация)
- ✅ `declare(strict_types=1)` и `final class` на тестах
- ⚠️ PHPUnit Deprecations: 2, PHPUnit Notices: 2 — стоит исправить

---

## 6. Роуты — результаты тестирования

| # | Метод | URL | Статус | Результат |
|---|---|---|---|---|
| 1 | GET | `/api/v1/wallet/balances` | 200 ✅ | 3 баланса |
| 2 | POST | `/api/v1/wallet/balances/sync` | 200 ✅ | Синхронизация с Bybit |
| 3 | GET | `/api/v1/wallet/transactions` | 200 ✅ | Пустой список |
| 4 | GET | `/api/v1/wallet/transactions/export` | 200 ✅ | Пустой список (TODO: CSV) |
| 5 | GET | `/api/v1/exchange/currencies` | 200 ✅ | 4 валюты |
| 6 | GET | `/api/v1/exchange/currency-pairs` | 200 ✅ | 5 пар |
| 7 | GET | `/api/v1/exchange/payment-methods` | 200 ✅ | 9 методов |
| 8 | GET | `/api/v1/exchange/orderbook?token=USDT&fiat=RUB` | 200 ✅ | Стакан ордеров |
| 9 | GET | `/api/v1/exchange/advertisements` | 200 ✅ | Пустой список |
| 10 | GET | `/api/v1/exchange/trades` | 200 ✅ | 13 сделок |
| 11 | GET | `/api/v1/exchange/trades/1` | 200 ✅ | Детали сделки |
| 12 | GET | `/api/v1/exchange/chat-scripts` | 200 ✅ | Пустой список |
| 13 | GET | `/api/v1/exchange/trades/1/chat` | 200 ✅ | Пустой список |

**POST/DELETE/PATCH маршруты** (создание объявлений, подтверждение оплаты, отправка сообщений) требуют реальных данных Bybit и не тестировались destructive-запросами.

### 🟡 Замечание по роутам

1. **Отсутствует trailing slash** — по документации (`06_роуты-и-контроллеры.md`) URL должны заканчиваться `/`. Все маршруты без trailing slash.
2. **DELETE возвращает 200 с `json([])`** вместо `204 No Content` (`$this->noContent()`):
   - `AdvertisementController::deleteAction` → `return $this->json([]);`
   - `ChatScriptController::deleteAction` → `return $this->json([]);`
3. **POST /balances/sync** возвращает 200 вместо 201 или 202. Семантически это command/action, а не создание ресурса — допустимо.
4. **`TradeController::payAction`** принимает скалярные аргументы вместо RequestDto:
   ```php
   public function payAction(int $id, string $paymentType, string $paymentId): ControllerJson
   ```
   По стандарту POST-action должен принимать `RequestDto` с `#[Assert\...]`.

---

## 7. Дополнительные замечания

### 🟡 Дублирующиеся валютные пары в API ответе

GET `/api/v1/exchange/currency-pairs` возвращает дубли:
- id=1 и id=3 оба `USDT_RUB`
- id=2 и id=4 оба `BTC_RUB`

Вероятно ошибка в данных БД, но стоит проверить.

### 🟡 `BaseWalletController` и `BaseExchangeController` не `final`

Оба базовых контроллера объявлены как `class` без `final`, хотя от них наследуются только `final` контроллеры. По стандарту стоит сделать `abstract class`.

### 🟢 Хорошие практики в коде

- Чёткое разделение на слои Domain/Application/Infrastructure/Presentation ✅
- Порты (interfaces) в Application, реализация в Infrastructure ✅
- Кросс-доменное взаимодействие через контракты (`BalanceQueryInterface`, `CurrencyQueryInterface`) ✅
- Collection-классы для ORM-сущностей ✅
- Логирование через PSR LoggerInterface ✅
- Аккуратный маппинг Bybit API → Domain Enum ✅

---

## Приоритеты исправлений

1. 🔴 **Написать тесты для rebit.wallet** — критично для финансовой логики
2. 🔴 **Убрать `HttpException` из `BalanceCalculator`** — нарушение Domain isolation
3. 🔴 **Инъекция `GetBalancesUseCase` через DI** в `SyncBalancesUseCase`
4. 🟡 **Вынести `toResultDto` из `ListTradesUseCase`** в маппер
5. 🟡 **Сделать RequestDto `final readonly class`** + добавить `#[Assert\...]`
6. 🟡 **DELETE → `$this->noContent()`** вместо `$this->json([])`
7. 🟡 **Enum case naming** → согласовать UPPER_SNAKE_CASE или зафиксировать PascalCase
8. 🟢 Trailing slash в роутах
9. 🟢 `LockFundsDto` → `LockFundsInputDto`
