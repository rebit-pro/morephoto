# Bitrix-события и тонкий мост

Эта глава описывает, как в проекте оформлять обработку событий Bitrix.

## 1. Базовое правило проекта

Правильная схема такая:

`include.php` -> `events/events.php` -> `EventBridge` -> DI-обработчик -> `UseCase`

Где:

- `include.php` или подключаемый из него `events/events.php` только регистрирует событие;
- `EventBridge` это тонкий статический мост между callback Bitrix и обычным DI-сервисом;
- DI-обработчик находится в инфраструктуре и содержит реальную обработку входных данных из события битрикс;
- `UseCase` принимает от DI-обработчика уже нормализованные данные: DTO или скаляры.

## 2. Зачем нужен тонкий мост

В Bitrix события регистрируются как статический callable.
В такой точке неудобно и неправильно размещать реальную логику:

- нельзя использовать DI;
- легко начать тащить `ServiceLocator` в runtime-код;
- bootstrap начинает жить как скрытый слой выполнения;
- обработчик становится трудно тестировать.

Поэтому в проекте используется тонкий мост:

- bridge знает только, какой DI-handler вызвать;
- bridge использует общий механизм резолва handler через `AbstractEventBridge`, а не собирает зависимости вручную;
- вся реальная логика уходит в обычный класс с конструкторным DI.

## 3. Что где должно лежать

| Что | Где хранить                                           | Что делает |
|---|-------------------------------------------------------|---|
| Регистрация событий | `events/events.php` (подключен в include.php)         | привязывает Bitrix event к bridge |
| Тонкий мост | `lib/Infrastructure/Event/<ИмяМодуля>EventBridge.php` | лениво резолвит handler и делегирует вызов |
| Реальный обработчик | `lib/Infrastructure/<Domain>/Event/*Handler.php`      | валидирует payload и вызывает `UseCase` |
| Сборка зависимостей | `di/Events.php` или `di/Layers/Infrastructure.php`    | регистрирует handler и его зависимости |

## 4. Эталонный пример из `rebit.wallet`

Где смотреть в проекте:

- `api/public/local/modules/rebit.wallet/include.php`
- `api/public/local/modules/rebit.wallet/events/events.php`
- `api/public/local/modules/rebit.wallet/lib/Infrastructure/Event/CatalogModuleEventBridge.php`
- `api/public/local/modules/rebit.wallet/lib/Infrastructure/Facet/Event/FacetUpdateOnElementChangeHandler.php`
- `api/public/local/modules/rebit.wallet/di/Events.php`

### Шаг 1. `include.php` подключает файл событий

```php
use Rebit\Share\Infrastructure\Bitrix\Module\ModuleHelper;

ModuleHelper::validateModuleInstalled('rebit.share');

include_once __DIR__ . '/events/events.php';
```

### Шаг 2. `events/events.php` регистрирует callback на bridge

```php
use Bitrix\Main\EventManager;
use Rebit\Wallet\Infrastructure\Event\CatalogModuleEventBridge;

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockElementUpdate',
    [CatalogModuleEventBridge::class, 'onFacetElementUpdate'],
);
```

### Шаг 3. Bridge только проксирует вызов в DI-handler

Bridge должен наследоваться от `AbstractEventBridge` из `rebit.share`, чтобы не дублировать резолв handler через `ServiceLocator` и проверку типа обработчика.

```php
final class CatalogModuleEventBridge extends AbstractEventBridge
{
    /**
     * @param array{ID: int|string, IBLOCK_ID: int|string} $fields
     */
    public static function onFacetElementUpdate(array &$fields): void
    {
        /** @var FacetUpdateOnElementChangeHandler $handler */
        $handler = self::getHandler(FacetUpdateOnElementChangeHandler::class);
        $handler->onUpdate($fields);
    }
}
```

### Шаг 4. Реальный handler это обычный DI-сервис

Handler должен реализовывать `BitrixEventHandlerInterface` из `rebit.share`.

```php
final readonly class FacetUpdateOnElementChangeHandler implements BitrixEventHandlerInterface
{
    public function __construct(
        private DispatchFacetUpdateMessageUseCase $dispatchFacetUpdate,
    ) {}

    /**
     * @param array{ID: int|string, IBLOCK_ID: int|string} $fields
     */
    public function onUpdate(array &$fields): void
    {
        $iblockId = $this->resolveTrackedIblockId($fields);
        if (null === $iblockId) {
            return;
        }

        $this->dispatchFacetUpdate->execute(
            (int)$fields['ID'],
            $iblockId,
            FacetUpdateSourceEnum::IBLOCK_ELEMENT_UPDATE,
        );
    }
}
```

### Шаг 5. Handler собирается через DI

```php
return [
    FacetUpdateOnElementChangeHandler::class => [
        'className' => FacetUpdateOnElementChangeHandler::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(DispatchFacetUpdateMessageUseCase::class),
        ],
    ],
];
```

## 5. Почему это считается правильным

Потому что ответственности разделены:

- bootstrap только регистрирует событие;
- bridge только связывает Bitrix callback и наш модуль;
- handler адаптирует внешний payload;
- `UseCase` получает уже нормализованные данные.

`ServiceLocator` остаётся только на границе интеграции и в DI, а не проникает в `UseCase`, runtime-handler или доменную логику.

## 6. Парные before/after события и request-local state store

Иногда одного события недостаточно.
Типичный случай: нужно понять, **изменилось ли значение по-настоящему**, а в `OnAfter...` Битрикс уже не даёт старое состояние.

Тогда в проекте допустим паттерн:

`OnBefore...` -> request-local store -> `OnAfter...`

Используем его только при всех условиях одновременно:

- оба события гарантированно происходят в рамках **одного PHP-запроса**;
- нужно сравнить старое и новое состояние;
- старое состояние нельзя безопасно получить в `OnAfter...`;
- хранить это состояние в БД, кеше или очереди было бы избыточно и дороже.

Правильная схема такая:

1. В `OnBefore...` handler читает текущее значение из источника правды.
2. Сохраняет его во временный request-local store по ключу сущности.
3. В `OnAfter...` handler читает сохранённое значение, сравнивает с новым и сразу очищает запись.

### Что такое request-local store

Это маленькое временное хранилище, живущее только внутри одного PHP-запроса.
Обычно это `final` utility-класс со `static` массивом и методами `remember()/pull()/clear()`.

Такой store:

- не является бизнес-кешом;
- не переживает конец запроса;
- не требует memcache, Redis, БД или файлов;
- не создаёт межпроцессную синхронизацию там, где она не нужна.

### Когда это правильно

- `OnBeforeIBlockSectionUpdate` + `OnAfterIBlockSectionUpdate`;
- `OnBeforeDelete` + `OnAfterDelete`, если нужно помнить старые поля;
- любые парные события одного запроса, где нужен diff old/new.

### Когда это неправильно

- сравнение состояния между **разными запросами**;
- передача данных между воркерами, cron-задачами или очередями;
- предметный кеш, который должен переживать запрос;
- попытка использовать request-local store как замену нормальному persistence.

### Чего делать не надо

- складывать old-state в `\Bitrix\Main\Data\Cache`, memcache или Redis, если это нужно только в рамках одного запроса;
- тащить request-local store в `UseCase` или domain;
- хранить там уже “бизнес-решение”, а не сырой integration-state;
- забывать очищать запись после чтения.

### Какой формат данных хранить

По умолчанию храните **сырой внешний state**, а сравнение делайте уже в `OnAfter...` после нормализации.

Это важно, потому что:

- `OnBefore...` и `OnAfter...` могут отдавать payload в разном виде;
- нормализация зависит от конкретного интеграционного сценария;
- store должен оставаться тупым и дешёвым.

### Эталонный пример

В `rebit.wallet` изменение `UF_SMART_FILTER` оформлено именно так:

- `OnBeforeIBlockSectionUpdate` читает текущее raw-значение поля;
- сохраняет его в `RequestLocalEventStateStore` со scope модуля;
- `OnAfterIBlockSectionUpdate` сравнивает old/new после нормализации;
- только при реальном изменении ставит `FacetReindexMessage`.

Смотреть:

- `api/public/local/modules/rebit.wallet/events/events.php`
- `api/public/local/modules/rebit.wallet/lib/Infrastructure/SmartFilter/Event/SmartFilterConfigUpdateEventHandler.php`
- `api/public/local/modules/rebit.share/lib/Infrastructure/Event/RequestLocalEventStateStore.php`

### Пример 1. Регистрация парных событий

```php
$eventManager->addEventHandler(
    'iblock',
    'OnBeforeIBlockSectionUpdate',
    [CatalogModuleEventBridge::class, 'onBeforeSmartFilterConfigUpdate'],
);
$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockSectionUpdate',
    [CatalogModuleEventBridge::class, 'onSmartFilterConfigUpdate'],
);
```

Смысл пары такой:

- в `OnBefore...` мы читаем старое состояние из источника правды;
- в `OnAfter...` получаем новое состояние из payload события;
- между ними переносим raw-state через request-local store.

API и ограничения `RequestLocalEventStateStore` как event utility описаны в [13_хэлперы-и-трейты.md](./13_хэлперы-и-трейты.md).

### Пример 2. Реальное использование в handler

```php
final readonly class SmartFilterConfigUpdateEventHandler implements BitrixEventHandlerInterface
{
    private const string REQUEST_LOCAL_SCOPE = 'catalog.smart_filter.section_update';

    /**
     * @param array{ID?: int|string, IBLOCK_ID?: int|string, UF_SMART_FILTER?: mixed} $fields
     */
    public function beforeUpdate(array &$fields): void
    {
        if (!$this->isSupportedSectionUpdate($fields) || !array_key_exists('UF_SMART_FILTER', $fields)) {
            return;
        }

        $sectionId = (int)$fields['ID'];
        $currentConfig = $this->loadCurrentSmartFilterConfig($sectionId);

        RequestLocalEventStateStore::remember(self::REQUEST_LOCAL_SCOPE, $sectionId, $currentConfig);
    }

    /**
     * @param array{RESULT?: bool, IBLOCK_ID?: int|string, ID?: int|string, UF_SMART_FILTER?: mixed} $fields
     */
    public function handler(array &$fields): void
    {
        if (
            !$this->isSupportedSectionUpdate($fields)
            || false === ($fields['RESULT'] ?? false)
            || !array_key_exists('UF_SMART_FILTER', $fields)
        ) {
            return;
        }

        $sectionId = (int)$fields['ID'];
        $previousConfig = RequestLocalEventStateStore::pull(self::REQUEST_LOCAL_SCOPE, $sectionId);
        $currentConfig = $this->normalizeSmartFilterConfig($fields['UF_SMART_FILTER']);

        if (null !== $previousConfig && $previousConfig === $currentConfig) {
            return;
        }

        $this->publisher->dispatch(
            new FacetReindexMessage(
                source: FacetUpdateSourceEnum::SMART_FILTER_CONFIG_UPDATE,
            ),
            deduplicateTime: self::DEDUP_TTL,
        );
    }
}
```

Здесь `beforeUpdate()` сохраняет old-state, а `handler()` забирает его через `pull()` и сравнивает с уже нормализованным новым значением. Это и есть правильный use-case для request-local storage: временный integration-state внутри одного запроса, без Redis, кеша и БД.

### Чеклист ревью

Перед тем как одобрять `request-local state store`, проверьте все пункты:

- это именно пара `OnBefore...` + `OnAfter...`, а не связь между разными запросами;
- old-state действительно нельзя надёжно получить в `OnAfter...` без дополнительного временного хранения;
- state store используется только в `Infrastructure/Event`;
- store хранит raw integration-state, а не DTO, entity или бизнес-решение;
- запись кладётся в store только в `OnBefore...`;
- чтение должно быть одноразовым через `pull()`, а `get()/peek()` вообще не должны появляться в API такого store;
- после чтения состояние не остаётся висеть в store;
- у store есть явный `scope`, чтобы разные сценарии не смешивались;
- без store этот код пришлось бы реализовывать через cache/Redis/БД, что для одного запроса избыточно;
- store не используется как предметный кеш, shared-state между воркерами или транспорт между очередями.

## 7. Как писать новый обработчик события

Пошаговая инструкция с шаблоном кода: [../how-to/06_обработчик-bitrix-события.md](../how-to/06_обработчик-bitrix-события.md).

## 8. Чего делать не надо

- писать бизнес-логику прямо в `include.php` или `events/events.php`;
- вызывать `UseCase` напрямую из bridge;
- держать в bridge проверки, ветвление и логирование;
- дублировать в bridge ручной резолв обработчика через `ServiceLocator`, потому что это уже покрыто `AbstractEventBridge`;
- тащить `ServiceLocator` в `UseCase`, application service, builder или domain-код;
- передавать сырой Bitrix payload глубже `Infrastructure`, если можно сразу преобразовать его в примитивы или DTO;
- делать bridge толстым и превращать его в ещё один runtime-сервис.

## Что нужно запомнить

1. Событие Bitrix это внешний entrypoint и место для `Infrastructure`, а не для предметной логики.
2. Новый код оформляет событие через тонкий bridge, а не через толстый static handler.
3. Bridge должен использовать `AbstractEventBridge`, а не делать ручной резолв обработчика.
4. Handler должен реализовывать `BitrixEventHandlerInterface` и быть обычным DI-классом с явными зависимостями.
5. Bitrix payload нужно оставлять в обработчике, дальше только DTO или скаляры.
6. Для парных `before/after` событий внутри одного запроса допустим request-local state store, но только как integration-техника, а не как предметный кеш.

---

<- [13. `rebit.share`: хэлперы и трейты](13_хэлперы-и-трейты.md) | [15. Очереди и консьюмеры](15_очереди-и-консьюмеры.md) ->

[^ К оглавлению](README.md)
