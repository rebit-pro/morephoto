# Rebit — Coding Context

Отвечать всегда на русском языке.
Писать код как Senior PHP. SOLID, KISS, DRY, без оверкодинга. Согласуй изменения с разработчиком. Баги/опечатки не по теме — тоже сообщай.

## Stack & Namespaces

DevOps: Docker, Docker Compose, Docker Swarm, MySQL, Redis, RabbitMQ.
Programmer: PHP 8.4, Bitrix D7, Vue 3 (Vite), Vuetify, Makefile.

## PHP Style

- `declare(strict_types=1)` везде, строгая типизация (аргументы, return, свойства)
- Yoda style: `null === $value`, `'' !== $string`
- `final readonly` классы где возможно; `match` вместо `switch`
- Enum case: `case FIRST_ELEMENT = 'firstElement'`
- Явные проверки вместо `empty()`: `[] === $array`, `null === $value`. Исключение: `!$bool`, конкатенация
- Cast без пробела: `(int)$value`; PSR-12 скобки метода на новой строке
- Замыкания/стрелки: типы аргументов, возврата, `static` если применимо
- Форматирование: `local/php-cs-fixer.php`
- phpDoc массивов в стиле phpStan с переносом:

```php
/** @var array<int, array{
 *     id: int,
 *     name: string,
 * }> */
```

## Архитектура

Слои: `Domain`, `Application`, `Infrastructure`, `Presentation`, `Shared`.

- `ServiceLocator` — только в DI-конфигах и bootstrap
- Зависимости через конструктор (constructor property promotion)
- DTO/VO — `final readonly`, named arguments для сложных вызовов
- Hot path: без лишних циклов, `array_merge`, spread, промежуточных массивов
- Инфра-исключения → предметные исключения Application/Domain
- Legacy/Bitrix-код → `Infrastructure` или bootstrap
- Сложные массивы: обязателен phpDoc с shape-типами

**Выбор слоя:**
1. Bitrix (кроме ORM) / Elastic / HTTP / `Infrastructure`
2. API-ответ → `Presentation`
3. Предметное правило → `Domain`
4. Сценарий через порты → `Application` (UseCase)

Антипаттерны: SQL в UseCase, бизнес-логика в Builder, форматирование в UseCase.

## Структура модуля

```
local/modules/rebit.<name>/
  di/Layers/{Infrastructure,Presentation,Shared}.php   # слоевые DI
  di/<Domain>.php                                       # предметные DI
  events/events.php                                     # События
  install/components/
  lib/{Application,Domain,Infrastructure,Presentation,Shared}/
  include.php       # bootstrap
  routes.php        # HTTP-маршруты
  .settings.php     # подключает di/, отдаёт Bitrix
```

## DI-конфигурация

Все сервисы — Singleton. Без изменяемого состояния — данные через аргументы методов.
`.settings.php` подключает `di/` файлы через `array_merge`. Эталон: `кebit.auth/.settings.php`

```php
// di/Foo.php — concrete class
GetFooUseCase::class => [
    'className' => GetFooUseCase::class,
    'constructorParams' => static fn(): array => [
        ServiceLocator::getInstance()->get(FooGatewayInterface::class),
    ],
],
```

**Interface-key с ручной сборкой** — использовать `constructor`, не `className + constructorParams` (Bitrix игнорирует `constructorParams` для interface-key):

```php
FooGatewayInterface::class => [
    'constructor' => static fn(): FooGatewayInterface => new ElasticFooGateway(
        ServiceLocator::getInstance()->get(ElasticClientFactory::class)->create(),
    ),
],
```

`className` для interface-key допустим только при автопроводке без спец. аргументов.

## Именование DTO

| Контекст | Суффикс                                                      |
|----------|--------------------------------------------------------------|
| Application вход/выход | `*InputDto` / `*OutputDto`                                   |
| Controller / API | `*RequestDto` (implements `RequestDtoInterface`)             |
| Межмодульный | `rebit.share/lib/Application/Contracts/<Domain>/Dto/`        |
| Cache | `Application/<Domain>/Enum/*CacheEnum` (value=ключ, `ttl()`) |
