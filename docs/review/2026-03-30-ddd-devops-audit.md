# Аудит DDD и DevOps — 2026-03-30

## Что проверено
- модули в `api/public/local/modules`
- dev/prod цепочка загрузки переменных окружения для API
- prod-логи в `/srv/rebit-p2p/logs/logstash`

## Результат по слоям DDD

Автоматическая проверка импортов показала **42 потенциальных нарушения**.
Ниже — подтверждённые проблемные категории.

### Статус remediation

На текущий момент runtime-код в `api/public/local/modules/*/lib/**` дополнительно перепроверен автоматическим проходом по импортам.

Итог после правок:
- **0 оставшихся запретных импортов** между слоями в `lib/**`
- **0 прямых межмодульных импортов** вне допустимого использования `rebit.share` в `lib/**`

Важно: в `tests/**` и части `di/*.php` могут оставаться совместимые legacy-алиасы и инфраструктурные сборки. Это не runtime-протечки слоёв из `lib/**`.

### 1. `Domain -> Infrastructure`
Подтверждено использование `Rebit\Share\Infrastructure\Repository\RepositoryExceptionTrait` в доменных репозиториях:
- `rebit.identity/lib/Domain/ApiConnection/Repository/ApiConnectionRepository.php`
- `rebit.wallet/lib/Domain/Transaction/Repository/TransactionRepository.php`
- `rebit.wallet/lib/Domain/Balance/Repository/BalanceRepository.php`
- `rebit.exchange/lib/Domain/*/Repository/*.php`
- `rebit.auth/lib/Domain/User/Repository/UserRepository.php`
- `rebit.auth/lib/Domain/Registration/Repository/RegistrationConfirmationRepository.php`
- `rebit.share/lib/Domain/Audit/Repository/AuditLogRepository.php`

Это реальная протечка слоя: доменные классы зависят от типа из `Infrastructure`.
Целевое исправление: вынести trait/обёртку в `Shared` или дублировать минимальную обёртку в домене.

**Статус:** исправлено.

Что сделано:
- добавлен `rebit.share/lib/Shared/Repository/RepositoryExceptionTrait.php`
- legacy `Infrastructure/Repository/RepositoryExceptionTrait.php` оставлен как совместимый алиас
- доменные репозитории переведены на shared-trait

### 2. `Domain -> Application`
Подтверждены прямые зависимости домена от application-контрактов/DTO:
- `rebit.exchange/lib/Domain/Counterparty/Repository/CounterpartyRepository.php`
  зависит от `Rebit\Exchange\Application\Trade\Dto\Bybit\BybitCounterpartyProfileDto`
- `rebit.auth/lib/Domain/User/Repository/UserRepository.php`
  реализует `Rebit\Auth\Application\Auth\Contract\LoginUserRepositoryInterface`
- `rebit.auth/lib/Domain/User/Service/TokenGenerator.php`
  реализует `Rebit\Auth\Application\Auth\Contract\TokenGeneratorInterface`
- `rebit.share/lib/Domain/File/Dto/Result/UploadFileResultDto.php`
  реализует `Rebit\Share\Application\Interface\ResultDtoInterface`

Это также реальная протечка слоя. По правилам проекта `Domain` должен знать только `Shared`.

**Статус:** исправлено в runtime-коде.

Что сделано:
- `rebit.share/lib/Domain/File/Dto/Result/UploadFileResultDto.php`
  переведён с `Application\Interface\ResultDtoInterface` на shared-маркер `ResponseDtoInterface`
- в `rebit.auth` доменные контракты вынесены в `Domain`, а application-контракты оставлены как совместимые алиасы
- `rebit.exchange/lib/Domain/Counterparty/Repository/CounterpartyRepository.php`
  переведён на доменный `CounterpartyProfileDto`

### 3. `Application -> Infrastructure`
Подтверждены прямые зависимости application-слоя от инфраструктурных типов:
- `rebit.notification/lib/Application/Notification/UseCase/ConsumeNotificationsUseCase.php`
  использует `AmqpConnectionFactory`, `ConsumerRunnerInterface`
- `rebit.exchange/lib/Application/*/UseCase/*.php`
  используют `Rebit\Share\Infrastructure\Exception\*`
- `rebit.exchange/lib/Application/TradeChat/UseCase/UploadTradeChatFileUseCase.php`
  зависит от `Rebit\Exchange\Infrastructure\Bitrix\TradeChatUploadFileLocator`

Часть из них — чистые архитектурные нарушения, часть — переходный технический долг.

**Статус:** исправлено в runtime-коде.

Что сделано:
- `rebit.notification/lib/Application/Notification/UseCase/ConsumeNotificationsUseCase.php`
  переведён на `MessageConsumerRunnerInterface` и `MessageTransportFactoryInterface`
- `rebit.exchange/lib/Application/**`
  переведён с `Rebit\Share\Infrastructure\Exception\*` на `Rebit\Share\Shared\Exception\*`
- `rebit.exchange/lib/Application/TradeChat/UseCase/UploadTradeChatFileUseCase.php`
  переведён с `TradeChatUploadFileLocator` на application-порт `TradeChatUploadFileLocatorInterface`

### 4. Межмодульные зависимости вне `rebit.share`
Подтверждён минимум один прямой межмодульный импорт, который не должен жить в контракте `share`:
- `rebit.share/lib/Application/Contract/Notification/Dto/SendNotificationDto.php`
  содержит `use Rebit\Notification\Application\Notification\Message\SendNotificationMessage`

Для `share` это обратная зависимость на конкретный модуль.

**Статус:** исправлено.

Что сделано:
- из `rebit.share/lib/Application/Contract/Notification/Dto/SendNotificationDto.php`
  удалена обратная ссылка на `rebit.notification`

## Результат по env dev/prod

### Что найдено
В dev `BYBIT_TESTNET_BASE_URL` и `BYBIT_MAINNET_BASE_URL` приходят из `docker-compose.yml`.

В prod они должны приходить из Docker Swarm config `rebit_backend_env`, который монтируется в `/app/public/.env`.

На сервере:
- файл `/srv/rebit-p2p/swarm/backend.env` уже содержит обе переменные;
- но активный immutable config `rebit_backend_env_208` **их не содержит**;
- поэтому внутри контейнера `getenv('BYBIT_TESTNET_BASE_URL')` и `getenv('BYBIT_MAINNET_BASE_URL')` возвращали `false`.

### Следствие
`BybitEnvironmentEnum::baseUrl()` возвращал пустую строку, и запрос превращался в путь вида:
- `'/v5/user/query-api'`

Из-за этого HTTP-клиент падал с ошибкой:
- `Only http and https shemes are supported.`

### Что исправлено
1. В `rebit.share/lib/Application/Contract/Bybit/BybitEnvironmentEnum.php` добавлен безопасный fallback:
   - testnet → `https://api-testnet.bybit.com`
   - mainnet → `https://api.bybit.com`
2. Добавлена валидация схемы URL (`http` / `https`).
3. На prod нужно перевыпустить `rebit_backend_env` и задеплоить stack с новой ссылкой на config.

## Результат по логам

### Что найдено
В `settings_extra.php` было:
- `RotatingFileHandler(..., maxFiles: 30)`

На prod уже есть файл:
- `bybit-2026-03-25.log` размером `61956897` байт (> 50M)

### Что исправлено
1. В `api/public/local/php_interface/settings_extra.php` уменьшено `maxFiles` с `30` до `5`.
2. В репозиторий добавлен host-конфиг `deploy/logrotate/rebit-p2p-logstash`:
   - `daily`
   - `maxage 5`
   - `maxsize 50M`
   - `copytruncate`
   - `compress`
   - без `delaycompress`, чтобы oversized-файлы сжимались сразу

## Что внедрять следующим шагом
1. Досинхронизировать `tests/**` с новыми shared/domain-типами, чтобы убрать legacy-импорты в тестах.
2. Досинхронизировать `di/*.php`, где use case уже работают через application-порты, но сборка ещё местами использует старые concrete/legacy-ключи.
3. Запустить регулярную проверку архитектуры в CI отдельным скриптом по `lib/**`.
4. При желании удалить compatibility-алиасы после стабилизации и отдельного прохода по тестам.
