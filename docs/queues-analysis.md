# Анализ применения очередей в Rebit P2P

> Документ для ознакомления и принятия решений перед внедрением очередей (Symfony Messenger + RabbitMQ).

---

## 1. Текущая ситуация

### Фоновые процессы

Сейчас проект использует **смешанный режим**: часть фоновых процессов остаётся на cron через supercronic, а асинхронные реакции вынесены в RabbitMQ + Symfony Messenger consumer'ы.

| Команда | Интервал | Тип нагрузки |
|---------|----------|--------------|
| `sync-order-book` | 10 сек | Polling Bybit API → запись в БД |
| `sync-trades` | 10 сек | Polling Bybit API → запись в БД + синхронизация контрагентов |
| `execute-chat-scripts` | 5 сек | Чтение БД → вызов Bybit API |
| `clean-stale-orders` | 1 мин | DELETE из БД |
| `sync-balances` | 5 мин | Polling Bybit API → запись в БД |
| `sync-trade-history` | 10 мин | Polling Bybit API → запись в БД |

### Проблемы legacy-подхода

1. **Cron с эмуляцией секундных интервалов** — hack через `for`-циклы и `sleep`.
2. **Нет разделения ответственности** — polling, обработка и реакция на события в одном потоке.
3. **Нет retry/failure-обработки** — если команда упала, она повторится только при следующем cron-тике.
4. **Нет масштабирования** — при росте числа пользователей нельзя добавить consumer'ов.
5. **Связанность синхронных операций** — при обнаружении сделки cron синхронно выполняет: запись сделки → получение контрагента → сохранение контрагента → запуск чат-скрипта.

### Инфраструктура

| Компонент | Статус |
|-----------|--------|
| RabbitMQ | ✅ Развёрнут в `docker-compose.yml` и `docker-compose-production.yml` |
| Redis | ✅ Есть (env `REDIS_HOST`, пакет `predis/predis`) |
| Symfony Messenger | ✅ Установлен (`symfony/messenger`) |
| Symfony AMQP Transport | ✅ Установлен (`symfony/amqp-messenger`) |
| `ext-amqp` | ✅ Собирается в dev/prod Docker-образах PHP |
| `MESSENGER_TRANSPORT_DSN` | ✅ Прокинут в `api-php-fpm`, `api-php-cli`, `api-cron` и consumer-контейнеры |
| Consumer-контейнеры | ✅ Есть отдельные сервисы под очереди |

### 1.1. Production-аудит от 2026-03-29 (`rebit-pro`)

Проверка выполнена напрямую на production-хосте `rebit-pro` (Docker Swarm stack `site`).

#### Актуальное состояние сервисов

| Сервис | Факт на production | Вывод |
|--------|---------------------|-------|
| `site_api` | `2/2`, образ `ghcr.io/rebit-pro/rebit-p2p-api:96c2ffbb04a80162e2451a8d21955737eba42642` | HTTP-слой обновился до текущего release `site_198` |
| `site_frontend` | `2/2`, образ `ghcr.io/rebit-pro/rebit-p2p-frontend:96c2ffbb04a80162e2451a8d21955737eba42642` | Frontend обновился |
| `site_api-php-fpm` | `2/2`, но фактически остался на старом образе `ghcr.io/rebit-pro/rebit-p2p-api-php-fpm:fe29b17d4b171d0efef2b0b7f0c2905c5ae23310` | rollout нового php-fpm не состоялся, Swarm откатил сервис |
| `site_api-cron` | `0/1`, образ `ghcr.io/rebit-pro/rebit-p2p-api-php-cli:96c2ffbb04a80162e2451a8d21955737eba42642` | cron не работает |
| `site_api-*-consumer` | все `0/1`, тот же образ `rebit-p2p-api-php-cli:96c2ffbb04a80162e2451a8d21955737eba42642` | ни один consumer не работает |
| `site_rabbitmq` | `1/1`, `rabbitmq:3.13-management-alpine` | брокер поднят, но без рабочих consumer'ов |

#### Корневая причина падения php-cli / php-fpm новых образов

Во всех новых `php-cli`/`php-fpm` контейнерах воспроизводится один и тот же фатальный стартовый сбой:

```text
/usr/local/bin/docker-entrypoint.sh: 23: Bad substitution
```

Причина — в `api/docker/common/php/docker-entrypoint.sh` использована bash-подстановка:

```sh
${MESSENGER_TRANSPORT_DSN//__RABBITMQ_PASSWORD__/$RABBITMQ_SECRET_PASSWORD_ENCODED}
```

Но entrypoint запускается под `#!/bin/sh`, а в production-образах это `dash`, который такую подстановку не поддерживает.

Следствия:

1. новый `site_api-php-fpm` не стартует и откатывается на старый образ;
2. `site_api-cron` не стартует совсем;
3. все queue-consumer сервисы падают с exit code `2`;
4. очередь сообщений на production фактически не обслуживается.

#### Фактическое состояние очередной инфраструктуры

- `rabbitmqctl list_queues ...` на production не показал рабочих очередей с consumer'ами на момент проверки;
- через рабочий контейнер старого `site_api-php-fpm` команда `php /app/public/local/bin/bitrix-console list` показывает только legacy cron-команды (`sync-order-book`, `sync-trades`, `execute-chat-scripts`, `sync-balances`, `sync-trade-history`) и общий `messenger:consume`;
- специализированные команды consumer'ов и test-команды из нового кода на production сейчас недоступны, потому что рабочий runtime остаётся на старом `php-fpm`, а новый `php-cli` не может стартовать.

#### Что есть в коде, но не работает в текущем production runtime

В репозитории уже присутствуют отдельные consumer/test-команды:

- `app:notification:consume`
- `app:exchange:trade-event:consume`
- `app:exchange:chat-script:consume`
- `app:wallet:balance-sync:consume`
- `app:identity:sync:consume`
- `app:audit:consume`
- `app:notification:test-send`
- `app:exchange:trade-event:test`
- `app:exchange:chat-script:test`
- `app:wallet:balance-sync:test`
- `app:identity:sync:test`
- `app:audit:test`

На production их нельзя считать работоспособными до исправления entrypoint и успешного выката актуальных `php-cli`/`php-fpm` образов.

#### Безопасные команды диагностики для production

```bash
ssh rebit-pro 'docker stack services site'
ssh rebit-pro 'docker service ps site_api-cron --no-trunc'
ssh rebit-pro 'docker service ps site_api-notification-consumer --no-trunc'
ssh rebit-pro 'docker logs $(docker ps -a --filter name=site_api-notification-consumer --format "{{.Names}}" | head -n 1) 2>&1 | tail -n 20'
ssh rebit-pro 'docker exec site_rabbitmq.1.p5hcdy6yjo9x7g4y20tgo2svl rabbitmqctl list_queues name messages consumers'
```

#### Итог по production на 2026-03-29

Инфраструктура очередей **задекларирована и частично развернута**, но **не функционирует в runtime**:

- RabbitMQ поднят;
- release `site_198` переключил `frontend` и `api`;
- `php-fpm` не обновился на новый образ;
- все `php-cli` consumer'ы и cron упали на entrypoint ещё до старта приложения;
- следовательно, очереди и новые test/consumer команды сейчас не обслуживаются.

---

## 2. Где очереди полезны

### 2.1. ✅ Рекомендовано — высокий приоритет

#### A. Уведомления (`rebit.notification`)

**Статус:** Реализовано базовое сообщение `SendNotificationMessage`, consumer и тестовая команда публикации.

**Сценарий:** Любой модуль публикует сообщение `SendNotificationMessage` → очередь → consumer разруливает по каналам.

**Выигрыш:**
- Асинхронная отправка — HTTP-ответ не ждёт Telegram API или SMTP
- Retry при ошибках email/telegram
- Разные приоритеты для торговых и маркетинговых уведомлений
- Лёгкое масштабирование канальных consumer'ов

**Примеры сообщений:**
- `TradeDiscoveredNotification` — новая сделка обнаружена
- `TradeStatusChangedNotification` — статус сделки изменился
- `PaymentDeadlineWarningNotification` — осталось 5 мин до дедлайна
- `SecurityAlertNotification` — подозрительная активность

---

#### B. Шаги чат-скриптов (`rebit.exchange`)

**Текущая реализация:** есть очередь `chatScriptStep`, message/handler/factory/use case, consumer и тестовая команда. Прикладной handler пока остаётся заглушкой с логированием. Legacy-команда `ExecuteChatScriptsCommand` пока сохранена как fallback.

**Почему очередь лучше:**
- Шаги скрипта имеют `delay_seconds` — идеально ложатся на delayed message (TTL в AMQP или `delay` в Messenger stamp)
- При ошибке Bybit API → автоматический retry с backoff
- Не нужен polling таблицы — шаги планируются при обнаружении сделки
- Дедупликация: один шаг не будет отправлен дважды

**Очередь:** `chatScriptStep`

**Сообщение:**
```
ExecuteChatScriptStepMessage {
    executionId: int     // ID записи rebit_chat_script_execution
    tradeId: int
    stepId: int
}
```

---

#### C. Реакции на обнаружение сделки (`rebit.exchange`)

**Текущая реализация:** `SyncTradesCommand` остаётся polling-командой, но при обнаружении новой сделки и смене статуса уже публикует события в очередь `tradeEvent`.
1. Сохраняет сделку в БД
2. Вызывает `SyncCounterpartyUseCase` → запрос к Bybit `user/order/personal/info` → сохранение в `b_user`
3. Запускает чат-скрипт (если привязан)
4. (Планируется) Отправляет уведомление

**Проблема:** Если запрос контрагента упал — вся итерация sync прерывается. Чем больше действий по цепочке — тем хрупче cron.

**Решение с очередью:** `SyncTradesCommand` обнаруживает сделку, сохраняет в БД, публикует `TradeDiscoveredMessage` → consumer'ы:
- Синхронизация контрагента
- Запуск чат-скрипта
- Отправка уведомления

**Очередь:** `tradeEvent`

**Сообщения:**
```
TradeDiscoveredMessage { tradeId: int, bybitOrderId: string }
TradeStatusChangedMessage { tradeId: int, oldStatus: string, newStatus: string }
```

---

### 2.2. ⚠️ Рекомендовано — средний приоритет

#### D. Синхронизация балансов по событию

**Текущая реализация:** cron каждые 5 мин для всех активных пользователей.

**Улучшение:** При завершении сделки (`TradeCompleted`) → публикуем `SyncBalanceMessage` в очередь → целевая синхронизация баланса конкретного пользователя.

Cron раз в 5 минут остаётся как fallback, но основная синхронизация — event-driven.

**Очередь:** `balanceSync`

---

#### E. Синхронизация платёжных методов по событию

**Текущая реализация:** Запланирован cron каждые 30 мин.

**Улучшение:** Публикация `SyncPaymentMethodsMessage` при подключении API-ключей или при создании объявления. Cron — только fallback.

**Очередь:** `identitySync`

---

#### F. Логирование и аудит (`rebit.security`)

**Статус:** Модуль запланирован.

**Почему очередь:** AuditLog — append-only журнал, запись не должна замедлять основной HTTP-запрос. Можно собирать в очередь и писать пачками.

**Очередь:** `audit`

---

### 2.3. ❌ НЕ рекомендовано для очередей

| Процесс | Почему НЕ очередь |
|---------|-------------------|
| `sync-order-book` (10 сек) | Регулярный polling — не event-driven. Стакан обновляется целиком, нет отдельных сообщений. Cron — правильный инструмент. |
| `sync-trades` (10 сек, polling) | Сам **polling** остаётся cron. Очередь — для реакций на найденные изменения. |
| `sync-trade-history` (10 мин) | Массовая дозагрузка — проще через cron. |
| `clean-stale-orders` (1 мин) | Простой `DELETE WHERE date < ...` — не имеет смысла переносить в очередь. |

---

## 3. Предлагаемые очереди

| Очередь | Enum-значение | Назначение | Модуль-publisher | Модуль-consumer |
|---------|---------------|------------|------------------|-----------------|
| `tradeEvent` | `TRADE_EVENT` | Реакции на события сделок | `rebit.exchange` | `rebit.exchange`, `rebit.notification` |
| `chatScriptStep` | `CHAT_SCRIPT_STEP` | Отложенные шаги скриптов | `rebit.exchange` | `rebit.exchange` |
| `notification` | `NOTIFICATION` | Уведомления всех каналов | Все модули | `rebit.notification` |
| `balanceSync` | `BALANCE_SYNC` | Синхронизация балансов по событию | `rebit.exchange` | `rebit.wallet` |
| `identitySync` | `IDENTITY_SYNC` | Синхронизация identity по событию | `rebit.identity`, `rebit.exchange` | `rebit.identity` |
| `audit` | `AUDIT` | Аудит действий пользователя | Все модули | `rebit.share` (временно, до появления `rebit.security`) |
| `messengerFailed` | `FAILED` | Failed transport (общий DLQ) | Messenger retry | — (ручной разбор) |

> На текущем этапе инфраструктура уже введена, а для всех очередей из реестра добавлены базовые message/handler/factory/use case/consumer/test-command.
> Следующий шаг — заменять заглушки handler'ов на реальную бизнес-логику и постепенно убирать cron-fallback там, где это безопасно.

---

## 4. Архитектура (из orteka.share, адаптированная)

### Слои

```
rebit.share/
├── Application/
│   └── Contract/
│       └── Messenger/
│           ├── AbstractMessage.php              # Базовый класс всех сообщений
│           └── MessagePublisherInterface.php     # Порт: dispatch(message, deduplicateTime)
├── Infrastructure/
│   └── Messenger/
│       ├── AbstractMessengerFactory.php         # Абстрактная фабрика Bus + Publisher
│       ├── AmqpConnectionFactory.php            # AMQP DSN → TransportInterface
│       ├── BitrixDedupCache.php                 # Дедупликация через Bitrix cache
│       ├── ConsumerRunner.php                   # Обёртка Worker с retry/failed
│       ├── ConsumerRunnerInterface.php
│       ├── DedupCacheInterface.php
│       ├── MessengerBusConfigBuilder.php        # Маршрутизация message → handler + transport
│       ├── MessengerBusConfigDto.php
│       ├── MessengerBusFactory.php              # Сборка MessageBus без Symfony Framework
│       ├── MessengerMessagePublisher.php        # Реализация MessagePublisherInterface
│       ├── MessengerRouteDto.php                # value object: message ↔ handler ↔ queue
│       └── SimpleServiceContainer.php           # PSR-11 контейнер для Messenger
└── Shared/
    └── Enum/
        └── MessengerQueueEnum.php               # Реестр всех очередей
```

### Как работает

1. **Модуль-publisher** получает через DI `MessagePublisherInterface` и вызывает `dispatch()`.
2. `MessengerMessagePublisher` проверяет дедупликацию (если `deduplicateTime > 0`) и отправляет в Symfony Messenger `MessageBus`.
3. `MessageBus` через `SendMessageMiddleware` направляет сообщение в нужный AMQP-транспорт по роутингу.
4. RabbitMQ хранит сообщение в очереди.
5. **Consumer-команда** запускает `ConsumerRunner::run()` → `Worker` слушает очередь и вызывает handler.
6. При ошибке — retry с backoff (MultiplierRetryStrategy), после исчерпания попыток — в `FAILED` очередь.

### Пример потока: обнаружение сделки

```
SyncTradesCommand (cron 10 сек)
    │
    ├─ Polling Bybit → находит новую сделку
    ├─ Сохраняет в rebit_trade
    └─ $publisher->dispatch(new TradeDiscoveredMessage(tradeId: 42))
                        │
                  ┌─────┴──────────────────────────┐
                  │        RabbitMQ                 │
                  │  queue: tradeEvent              │
                  └─────┬──────────────────────────┘
                        │
              ┌─────────▼──────────┐
              │  Consumer Worker   │
              │  (отдельный процесс)│
              └──┬─────────────────┘
                 │
                 ├─ SyncCounterpartyHandler → Bybit API → b_user
                 ├─ StartChatScriptHandler → publish ExecuteChatScriptStepMessage
                 └─ SendTradeNotificationHandler → publish SendNotificationMessage
```

---

## 5. Необходимые инфраструктурные изменения

### 5.1. Composer-пакеты

```bash
composer require symfony/messenger symfony/amqp-messenger
```

> В проекте уже установлены `symfony/messenger`, `symfony/amqp-messenger`, `php-amqplib/php-amqplib`, а `ext-amqp` собирается в Docker-образах PHP.

### 5.2. Docker: RabbitMQ и consumer'ы

```yaml
# docker-compose.yml
rabbitmq:
    image: rabbitmq:3.13-management-alpine
    restart: unless-stopped
    ports:
        - "5672:5672"    # AMQP
        - "15672:15672"  # Management UI
    environment:
        RABBITMQ_DEFAULT_USER: ${RABBITMQ_USER:-rebit}
        RABBITMQ_DEFAULT_PASS: ${RABBITMQ_PASSWORD:-rebit}
        RABBITMQ_DEFAULT_VHOST: ${RABBITMQ_VHOST:-rebit}
    volumes:
        - rabbitmq-data:/var/lib/rabbitmq
```

### 5.3. ENV-переменные

```env
MESSENGER_TRANSPORT_DSN=amqp://rebit:rebit@rabbitmq:5672/rebit
```

### 5.4. Docker: consumer-процессы

```yaml
api-notification-consumer:
    command: ["php", "public/local/bin/bitrix-console", "app:notification:consume"]

api-trade-event-consumer:
    command: ["php", "public/local/bin/bitrix-console", "app:exchange:trade-event:consume"]

api-chat-script-consumer:
    command: ["php", "public/local/bin/bitrix-console", "app:exchange:chat-script:consume"]

api-balance-sync-consumer:
    command: ["php", "public/local/bin/bitrix-console", "app:wallet:balance-sync:consume"]

api-identity-sync-consumer:
    command: ["php", "public/local/bin/bitrix-console", "app:identity:sync:consume"]

api-audit-consumer:
    command: ["php", "public/local/bin/bitrix-console", "app:audit:consume"]
```

> Текущая конфигурация — один контейнер на каждую очередь.
> `--limit=100 --time-limit=300` — worker обработает до 100 сообщений или 5 минут, затем перезапустится (предотвращает утечки памяти).

### 5.5. Альтернатива: Redis транспорт

Если RabbitMQ избыточен на текущем этапе, Symfony Messenger поддерживает **Redis транспорт** (Redis Streams). Redis уже есть в проекте.

```bash
composer require symfony/redis-messenger
```

```env
MESSENGER_TRANSPORT_DSN=redis://redis:6379/messages
```

**Плюсы Redis:** не нужен дополнительный сервис, уже развёрнут.
**Минусы Redis:** нет dead-letter queue из коробки, менее надёжен при перезапусках, нет Management UI.

**Рекомендация:** Начать с RabbitMQ — он стандарт для Messenger и уже указан в стеке проекта (CLAUDE.md).

---

## 6. Этапность внедрения

### Этап 1 — Инфраструктура
- [x] Интеграция Messenger-слоя в `rebit.share`
- [x] Добавление RabbitMQ в docker-compose
- [x] Добавление `symfony/messenger` + `symfony/amqp-messenger` в composer
- [x] ENV + проверка `ext-amqp`
- [x] Базовые consumer/test-команды для отладки очередей

### Этап 2 — Первые очереди
- [x] `tradeEvent` — при обнаружении сделки публиковать `TradeDiscoveredMessage`
- [x] `chatScriptStep` — есть consumer, тестовая команда и DI-конфигурация
- [x] Consumer-контейнеры в docker-compose

### Этап 3 — Уведомления
- [x] Реализация `rebit.notification` с `notification` очередью
- [x] Publisher уведомлений подключён в `rebit.exchange`

### Этап 4 — Каркас остальных очередей
- [x] `balanceSync` — message/handler/factory/use case/consumer/test-command
- [x] `identitySync` — message/handler/factory/use case/consumer/test-command
- [x] `audit` — временная реализация в `rebit.share`
- [ ] Наполнить все handler'ы прикладной бизнес-логикой вместо заглушек

### Этап 5 — Вывод из fallback-режима
- [ ] Перенести аудит из `rebit.share` в выделенный модуль безопасности
- [ ] Оценить отказ от cron-fallback для `execute-chat-scripts` и event-driven сценариев

---

## 7. Зафиксированные решения

1. **RabbitMQ выбран** как основной транспорт Messenger.
2. **Consumer-контейнеры** идут по одному на очередь.
3. **`MessengerQueueEnum` заполнен** для `tradeEvent`, `chatScriptStep`, `notification`, `balanceSync`, `identitySync`, `audit`, `messengerFailed`.
4. **`ext-amqp` присутствует** в dev/prod Docker-образах PHP.
5. **Cron остаётся fallback-механизмом** для legacy-сценариев, пока handler'ы очередей не наполнены полноценной бизнес-логикой.

---

## 8. Резюме

| Аспект | Текущее состояние | С очередями |
|--------|-------------------|-------------|
| Реакция на события | Синхронная цепочка в cron | Асинхронная, развязанная |
| Retry при ошибках | Нет (повтор на следующем тике) | Автоматический с backoff |
| Масштабирование | 1 cron-процесс | N consumer'ов на очередь |
| Уведомления | Не реализованы | Готовая инфраструктура |
| Чат-скрипты | Polling таблицы каждые 5 сек | Event-driven + delayed |
| Мониторинг ошибок | Логи | Failed queue + логи |
| Сложность инфраструктуры | Простая | +RabbitMQ (+management UI) |
