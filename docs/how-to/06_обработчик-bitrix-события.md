# Как добавить обработчик Bitrix-события

Короткая инструкция. Подробности и обоснования: [../architecture-guide/14_bitrix-события.md](../architecture-guide/14_bitrix-события.md).

## Что получится

```text
include.php → events/events.php → EventBridge → DI-handler → UseCase
```

## Шаги

### 1. Подключите файл событий в `include.php`

```php
// include.php
include_once __DIR__ . '/events/events.php';
```

Если файл `events/events.php` уже подключён — пропустите этот шаг.

### 2. Зарегистрируйте событие в `events/events.php`

```php
use Bitrix\Main\EventManager;
use Rebit\YourModule\Infrastructure\Event\YourModuleEventBridge;

$eventManager = EventManager::getInstance();
$eventManager->addEventHandler(
    'iblock',
    'OnAfterIBlockElementUpdate',
    [YourModuleEventBridge::class, 'onSomethingHappened'],
);
```

### 3. Создайте или дополните EventBridge

Bridge наследует `AbstractEventBridge` из `rebit.share`.
Один bridge — один модуль. Если bridge для модуля уже существует, добавьте в него новый метод.

```php
// lib/Infrastructure/Event/YourModuleEventBridge.php

final class YourModuleEventBridge extends AbstractEventBridge
{
    /**
     * @param array<string, mixed> $fields
     */
    public static function onSomethingHappened(array &$fields): void
    {
        /** @var SomethingHappenedHandler $handler */
        $handler = self::getHandler(SomethingHappenedHandler::class);
        $handler->handle($fields);
    }
}
```

Bridge не должен содержать проверки, ветвление или логирование — только `getHandler()` и вызов.

### 4. Создайте handler

Handler — обычный DI-сервис. Реализует `BitrixEventHandlerInterface`.

```php
// lib/Infrastructure/<Domain>/Event/SomethingHappenedHandler.php

final readonly class SomethingHappenedHandler implements BitrixEventHandlerInterface
{
    public function __construct(
        private DoSomethingUseCase $doSomething,
    ) {}

    /**
     * @param array<string, mixed> $fields
     */
    public function handle(array $fields): void
    {
        // валидация и фильтрация payload Bitrix
        if (!isset($fields['ID'])) {
            return;
        }

        // вызов use case с нормализованными данными
        $this->doSomething->execute((int)$fields['ID']);
    }
}
```

Payload Bitrix не должен уходить глубже handler — дальше только DTO или скаляры.

### 5. Зарегистрируйте handler в DI

```php
// di/Events.php или di/Layers/Infrastructure.php

return [
    SomethingHappenedHandler::class => [
        'className' => SomethingHappenedHandler::class,
        'constructorParams' => static fn(): array => [
            ServiceLocator::getInstance()->get(DoSomethingUseCase::class),
        ],
    ],
];
```

## Где лежат файлы

| Что | Путь |
|-----|------|
| Регистрация событий | `events/events.php` |
| Bridge | `lib/Infrastructure/Event/<Module>EventBridge.php` |
| Handler | `lib/Infrastructure/<Domain>/Event/*Handler.php` |
| DI | `di/Events.php` или `di/Layers/Infrastructure.php` |

## Чеклист

- [ ] `include.php` подключает `events/events.php`
- [ ] Событие зарегистрировано через `addEventHandler`
- [ ] Bridge наследует `AbstractEventBridge`, метод только делегирует
- [ ] Handler реализует `BitrixEventHandlerInterface`
- [ ] Handler не пробрасывает сырой Bitrix payload в UseCase
- [ ] Handler зарегистрирован в DI

## Эталон

`rebit.wallet`:

- `events/events.php`
- `lib/Infrastructure/Event/CatalogModuleEventBridge.php`
- `lib/Infrastructure/Facet/Event/FacetUpdateOnElementChangeHandler.php`
- `di/Events.php`
