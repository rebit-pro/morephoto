# DevOps-требования для P2P-чата сделок и обмена файлами

> Дата фиксации: 2026-03-27
> Scope: `rebit-p2p`, backend `api/`, frontend `frontend/`, production на Docker Swarm.

## 1. Цель документа

Документ фиксирует:

- что уже подтверждено в проекте по DevOps-контуру;
- какие пробелы остаются до production-ready эксплуатации;
- какие инструменты и практики обязательны для чатов сделок с сообщениями и файлами;
- какие критерии считать минимально достаточными для запуска и сопровождения.

## 2. Что подтверждено на текущий момент

### 2.1. Уже есть

- локальное окружение в `docker-compose.yml`;
- production-описание сервисов в `docker-compose-production.yml`;
- деплой в Docker Swarm через `Makefile`;
- GitHub Actions pipeline в `.github/workflows/makefile.yml`;
- versioned Swarm configs/secrets через `deploy/swarm-publish-runtime.sh`;
- production-сервисы `frontend`, `api`, `api-php-fpm`, `api-cron`, `api-mysql`, `api-memcached` на сервере `rebit-pro`;
- cron/supercronic для polling-процессов: стакан, активные сделки, история, чат-скрипты, балансы;
- healthcheck на уровне nginx-конфига контейнера (`api/docker/common/nginx/conf.d/default.conf`);
- backend quality gates: `lint`, `php-cs-fixer`, `phpstan`, `phpunit`.

### 2.2. Важные ограничения текущей реализации

- чат сделки сейчас опирается на polling, а не на realtime transport;
- история чата синхронизируется через API Bybit при чтении (`SyncChatMessagesUseCase` вызывается из `GetChatHistoryUseCase`), но остаётся polling-only и без фоновой очереди;
- отправка файлов в чат Bybit реализована через backend upload endpoint `/api/v1/exchange/trades/{tradeId}/chat/upload` и последующую отправку `fileUrl`, описанную в `docs/api.md`;
- во frontend прикрепление файлов доступно как в реальном API, так и в `mock api`, но обработка ошибок upload-а пока остаётся минимальной;
- `SendMessageRequestDto` синхронизирован с текущим flow Bybit: текст и `fileUrl` отправляются отдельными сообщениями после backend upload-а;
- в runtime фактически используется cron, а очередь сообщений/консьюмеры из архитектурной документации не подтверждены кодом и прод-инфраструктурой;
- `README.md` декларирует `Redis` и `RabbitMQ`, но в production compose RabbitMQ отсутствует, а Redis не поднят отдельным сервисом.

### 2.3. Таблица "что уже есть / чего нет / критичность"

| Область | Что уже есть | Чего нет / пробел | Критичность |
|---|---|---|---|
| Контейнеризация | `docker-compose.yml`, `docker-compose-production.yml` | — | Низкая |
| Deploy | `Makefile`, Swarm deploy, rollback через release dir | Нет `post-deploy smoke gate` | Высокая |
| CI backend | `lint`, `cs-check`, `phpstan`, `phpunit` в `.github/workflows/makefile.yml` | — | Низкая |
| CI frontend | job есть, но фактически `Skip (no frontend checks yet)` | Нет реальных frontend checks | Высокая |
| Secrets/configs | versioned Swarm configs/secrets через `deploy/swarm-publish-runtime.sh` | Нет формализованной rotation policy | Средняя |
| Production runtime | на `rebit-pro` подняты `frontend`, `api`, `api-php-fpm`, `api-cron`, `api-mysql`, `api-memcached` | Нет подтверждённого Redis-сервиса; нет RabbitMQ runtime | Высокая |
| Healthcheck | health endpoint описан в nginx-конфиге | На проде проверка через `localhost` вернула `404` | Критическая |
| Background jobs | `api-cron` + supercronic + polling trade/order/chat scripts | Нет явного мониторинга выполнения cron | Высокая |
| Text chat | Роуты, контроллер, use case, локальная история, polling-синхронизация входящих сообщений из Bybit | Нет realtime-транспорта и фонового sync-контура | Средняя |
| File chat flow | End-to-end upload в Bybit реализован через backend endpoint и отправку `fileUrl` | Нужны антиспам, мониторинг ошибок и постобработка файлов | Высокая |
| Backend files | Есть upload flow для trade-chat и серверная валидация базовых MIME/размера | Нет AV scan / object storage / очереди постобработки | Высокая |
| Frontend files | UI выбора файла есть и работает с реальным API и mock | Нет продвинутого recovery/retry UX при частичных ошибках upload-а | Средняя |
| Observability | Частично есть лог-каталог `/srv/rebit-p2p/logs` | Нет Prometheus/Grafana/Loki/Sentry/alerts | Критическая |
| Backup/restore | По документации это требуется | Нет подтверждённого backup/restore процесса | Критическая |
| Security scans | `roave/security-advisories` в dev-зависимостях | Нет Trivy / Gitleaks / registry scan / secret scan pipeline | Высокая |
| Rate limit / anti-spam | В доменной документации антиспам описан | Нет подтверждённого runtime-механизма | Высокая |
| Object storage | Нет | Нет S3/MinIO стратегии для файлов | Высокая |
| AV scan | Нет | Нет антивирусной проверки файлов | Высокая |
| Runbook'и | Документ требований создан | Нет полноценных эксплуатационных runbook'ов | Средняя |
| Notification/Security домены | Запланированы в `docs/modules.md` | Ещё не реализованы | Средняя |
| Async architecture | В архитектурной документации есть глава про очереди | В текущем коде/проде не подтверждено реальное использование broker/consumer | Высокая |

## 3. Обязательные DevOps-требования для проекта

## 3.1. Delivery / CI-CD

### Требуется сохранить и усилить

1. Build, lint, static analysis и tests должны оставаться обязательными перед merge в `main`.
2. Для frontend нужно включить реальные проверки, а не `Skip (no frontend checks yet)`.
3. После deploy обязательны smoke checks:
   - доступность `frontend`;
   - доступность `api`;
   - успешный ответ health endpoint через ingress;
   - базовая авторизация;
   - чтение списка сделок;
   - чтение истории чата.
4. Миграции должны быть встроены в безопасный release-процесс:
   - либо отдельный stage до переключения трафика;
   - либо post-deploy job с контролируемым rollback plan.
5. Rollback должен быть не только на уровне symlink/stack, но и с check-list после отката.

### Необходимые инструменты

- GitHub Actions — уже используется;
- reusable smoke tests через Postman/Newman или k6 smoke suite;
- Trivy для container scan;
- Gitleaks для поиска секретов;
- Dependabot или Renovate для зависимостей;
- SBOM generation (`syft`) — желательно.

## 3.2. Observability

### Требования

1. Все API-запросы, cron-задачи и ошибки интеграций должны иметь correlation id.
2. Нужны отдельные дашборды по:
   - API latency/error rate;
   - Bybit integration errors;
   - cron execution duration/failures;
   - trade sync lag;
   - chat message send failures;
   - file upload failures.
3. Нужны алерты на:
   - падение сервиса;
   - рост 5xx;
   - неуспешный cron;
   - backlog/lag синхронизации сделок;
   - ошибки отправки сообщений/вложений;
   - нехватку места на диске.

### Необходимые инструменты

- Prometheus;
- Grafana;
- Loki + Promtail или Graylog/ELK;
- Sentry для backend/frontend ошибок;
- Uptime Kuma / Better Stack / внешний uptime-monitor.

## 3.3. Runtime и асинхронность

### Требования

1. Нужно явно зафиксировать, что используется для:
   - кеша;
   - rate limit;
   - блокировок;
   - фоновой асинхронной обработки.
2. Для P2P-чата с файлами и постобработкой нужен выделенный async-контур.
3. Polling допускается для синхронизации сделок с Bybit, но не должен быть единственным механизмом для тяжёлых внутренних задач.

### Необходимые инструменты

- Redis — как минимум для rate limit, locks, ephemeral state;
- RabbitMQ — если проект идёт по документации с очередями/консьюмерами;
- либо документированная альтернатива очередям, если команда сознательно отказывается от RabbitMQ.

### 3.3.1. Где RabbitMQ будет полезен именно в этом проекте

RabbitMQ не заменяет polling Bybit API и не обязателен для каждого фонового процесса.
Его основная польза здесь — надёжная асинхронная обработка внутренних задач Rebit, где важны retry, буферизация нагрузки и разделение producer / consumer.

#### Приоритетные сценарии

1. **Обработка файлов чата после загрузки**
   - антивирусная проверка;
   - извлечение метаданных;
   - генерация preview / thumbnail;
   - перенос из временного storage в постоянное;
   - cleanup временных файлов.

2. **Надёжная отправка сообщений и вложений в чат**
   - временная недоступность Bybit API не теряет сообщение;
   - можно строить retry / backoff;
   - можно вводить dead-letter очередь для неуспешных задач.

3. **Обработка событий сделки**
   - `TradeDiscovered`;
   - `TradeCompleted`;
   - `TradeCancelled`;
   - запуск уведомлений, аудита и пост-обработки без перегрузки `sync-trades`.

4. **Исполнение чат-скриптов и отложенных сообщений**
   - сейчас это решается через частый cron;
   - с RabbitMQ проще изолировать выполнение, видеть backlog и масштабировать consumers.

5. **Уведомления и fan-out события**
   - email;
   - push;
   - telegram;
   - in-app уведомления.

6. **Аудит и аналитика**
   - запись технических событий;
   - аудит критических действий;
   - выгрузка событий в аналитический контур без замедления API.

#### Что RabbitMQ даст по сравнению с одним cron

| Сценарий | Cron-only | RabbitMQ |
|---|---|---|
| Retry при ошибке Bybit | вручную или через повторный цикл | штатный retry/backoff |
| Пиковая нагрузка | все задачи выполняются в одном цикле | задачи буферизуются в очереди |
| Тяжёлые post-upload операции | нагружают HTTP/cron | выносятся в отдельные consumers |
| Наблюдение backlog | почти отсутствует | можно видеть глубину очереди и lag |
| Изоляция ошибок | ошибка может ломать общий цикл | ошибка локализуется на consumer/queue |

#### Рекомендуемые очереди для первого внедрения

1. `chat.file.process`
2. `chat.message.send`
3. `chat.script.execute`
4. `trade.event.process`
5. `notification.dispatch`

#### Практическая рекомендация

- `cron` оставить для polling Bybit;
- RabbitMQ использовать для внутренних асинхронных действий Rebit;
- тяжёлые, повторяемые и нестабильные интеграционные шаги не выполнять прямо внутри HTTP-контроллера.

## 3.4. Файлы и вложения в чатах

### Требования

1. Файлы должны проходить двухшаговый flow:
   - upload;
   - отправка сообщения с URL/метаданными.
2. Для каждого файла должны быть ограничения:
   - mime-type;
   - размер;
   - расширение;
   - антивирусная проверка;
   - срок хранения;
   - аудит загрузки.
3. Нужна стратегия хранения:
   - локальный bind mount допустим только как временное решение;
   - для production предпочтительно объектное хранилище.
4. Нужен процесс очистки устаревших файлов и orphaned uploads.

### Необходимые инструменты

- S3-совместимое хранилище: MinIO / Yandex Object Storage / AWS S3;
- ClamAV для AV scan;
- signed URLs для скачивания приватных файлов;
- image/video metadata validation.

## 3.5. Security

### Требования

1. Секреты должны оставаться вне git и публиковаться versioned-объектами.
2. Нужна ротация секретов и документированный порядок ротации.
3. Для API нужны rate limits минимум на:
   - login;
   - отправку сообщений;
   - загрузку файлов;
   - destructive trade actions.
4. Нужен audit trail по операциям:
   - подтверждение оплаты;
   - release активов;
   - отправка сообщений;
   - загрузка файлов;
   - изменения API-ключей.
5. Для production нужны регулярные security scans образов и зависимостей.

### Необходимые инструменты

- Swarm secrets — уже используются;
- Vault / SOPS + age — желательно для зрелого secret management;
- Traefik middlewares или внешний WAF;
- Trivy / Grype;
- Gitleaks;
- Fail2ban или аналог на периметре — опционально.

## 3.6. Надёжность и backup / restore

### Требования

1. Должны быть оформлены backup policy и restore test policy.
2. Бэкапить нужно:
   - MySQL;
   - `upload/`;
   - `bitrix/` при необходимости для окружения;
   - runtime env/config inventory.
3. Нужно подтвердить целевые показатели:
   - RPO;
   - RTO;
   - периодичность проверки восстановления.
4. Rollback релиза и restore данных — разные процедуры и должны быть задокументированы отдельно.

### Необходимые инструменты

- Percona XtraBackup или регулярный dump + проверка восстановления;
- Restic / Borg / rclone для offsite backup;
- object storage для хранения backup-артефактов.

## 3.7. Эксплуатация и runbook'и

### Обязательные runbook'и

1. Деплой новой версии.
2. Rollback версии.
3. Падение `api-cron`.
4. Рост ошибок Bybit API.
5. Не отправляются сообщения в чат.
6. Не проходят загрузки файлов.
7. Переполнение диска.
8. Восстановление после сбоя MySQL.

## 4. Приоритет недостающих инструментов

## P0 — нужно до production-ready чата с файлами

1. Prometheus + Grafana.
2. Централизованные логи: Loki/Promtail или Graylog.
3. Sentry.
4. Реальные frontend checks в CI.
5. Smoke tests после deploy.
6. Backup + restore regламент.
7. Redis как подтверждённый runtime-компонент.
8. Объектное хранилище для вложений.
9. Антивирусная проверка вложений.

## P1 — желательно в ближайшую итерацию

1. RabbitMQ или другой формализованный async-broker.
2. Trivy / Gitleaks / Dependabot.
3. Load testing через k6.
4. Внешний uptime-monitor.
5. Документированная ротация секретов.

## P2 — следующий этап зрелости

1. Vault / SOPS.
2. SBOM и supply-chain metadata.
3. Отдельные SLO/SLI для sync lag и chat delivery.
4. Capacity planning и cost observability.

## 5. Acceptance criteria для статуса «готово к эксплуатации»

Проект можно считать production-ready для P2P-чата и файлов только если:

- деплой полностью воспроизводим и проходит без ручных правок на сервере;
- есть автоматические проверки backend и frontend;
- есть smoke tests после deploy;
- есть метрики, логи и алерты;
- подтверждён backup/restore;
- файлы проходят валидацию и AV scan;
- есть rate limiting и аудит критических действий;
- есть понятный rollback и runbook по инцидентам;
- async-контур для тяжёлых задач документирован и реально поднят в runtime.

## 6. Краткий вывод по текущему состоянию

На 2026-03-27 у проекта есть хороший базовый фундамент для DevOps:

- контейнеризация;
- Swarm deploy;
- versioned secrets/configs;
- backend quality gates;
- рабочий production stack.

Но до полноценной эксплуатационной зрелости не хватает ключевых вещей:

- наблюдаемости;
- подтверждённой backup/restore-практики;
- завершённого file pipeline для trade chat;
- формализованной async-инфраструктуры;
- полноценных post-deploy проверок;
- закрытия security-gaps supply chain и secrets governance.

Поэтому текущий DevOps-контур можно считать достаточным для controlled development / early production, но не для зрелой безопасной эксплуатации чатов сделок с вложениями без дополнительного усиления.
