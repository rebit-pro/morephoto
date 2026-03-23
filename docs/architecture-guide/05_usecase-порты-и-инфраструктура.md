# UseCase, порты и инфраструктура

Эта глава объясняет основной рабочий паттерн новой архитектуры.

## Зачем нужен этот паттерн

Без него приложение очень быстро начинает зависеть от деталей:

- `UseCase` знает про Elastic;
- builder (сборщик компонента) знает про legacy API;
- доменная логика знает про Bitrix request (запрос);
- сценарий нельзя переиспользовать без полного копирования.

Паттерн `UseCase -> Port -> Infrastructure Adapter` нужен, чтобы отделить:

- сценарий;
- контракт потребности;
- техническую реализацию.

## Общая схема

```text
Presentation
    |
    v
UseCase
    |
    v
Outgoing Port
    |
    v
Infrastructure Adapter
    |
    v
Elastic / HTTP / legacy / Bitrix API
```

## 1. `UseCase` (сценарий)

### Что это

`UseCase` (сценарий) это сценарий применения системы.
Простыми словами: один законченный шаг, который имеет понятный смысл для бизнеса или интерфейса.

Примеры:

- получить список товаров по фильтру;
- построить SEO-данные раздела;
- отправить запись на приём;
- получить список акций.

### Почему `UseCase` (сценарий) нужен отдельно

Если сценарий размазан по контроллеру, builder (сборщику компонента) и инфраструктуре, то нельзя ответить на вопрос:
«Где вообще живёт логика этого действия?»

`UseCase` (сценарий) нужен, чтобы у сценария был один вход.

### Целевой стандарт

- `final readonly`;
- один публичный метод `execute()`;
- зависимости только через конструктор;
- orchestration (оркестрация) без технических деталей.

### Что допустимо внутри

- вызов одного или нескольких портов;
- вызов application service (прикладного сервиса);
- кэширование;
- сборка выходного DTO;
- простая orchestration-логика (логика оркестрации).

### Что недопустимо

- `ServiceLocator`;
- прямой `Elastic\Client`;
- `\CIBlockElement`;
- логика шаблона;
- ручной парсинг HTTP.

### Пример `UseCase` (сценария)

```php
final readonly class GetFooUseCase
{
    public function __construct(
        private FooGatewayInterface $fooGateway,
        private FooAppService $fooAppService,
    ) {}

    public function execute(GetFooInputDto $input): GetFooOutputDto
    {
        $preparedInput = $this->fooAppService->prepare($input);

        return $this->fooGateway->query($preparedInput);
    }
}
```

## 2. `Port` (порт)

### Что это

`Port` (порт) это контракт потребности приложения.
Он описывает, что нужно use case (сценарию) от внешнего мира, но не описывает конкретную технологию.

### Почему порт важен

Без порта use case (сценарий) начинает знать детали реализации.
С портом use case (сценарий) знает только предметное действие:

- `query(...)`
- `listItems(...)`
- `countByFilter(...)`
- `createAppointment(...)`

### Пример порта

```php
interface FooGatewayInterface
{
    public function query(GetFooInputDto $input): GetFooOutputDto;
}
```

### Как называть методы порта

Правильно:

- `query`
- `listItems`
- `countByFilter`
- `createAppointment`

Неправильно:

- `execute`
- `run`
- `handle`

Потому что порт описывает не «команду абстрактного объекта», а предметное действие.

## 3. `Infrastructure adapter` (адаптер инфраструктуры)

### Что это

`Adapter` (адаптер) это конкретная техническая реализация порта.

Он знает:

- как сходить в Elastic;
- как обратиться к legacy-классу;
- как вызвать HTTP API;
- как обработать техническое исключение.

### Почему `adapter` (адаптер) нужен отдельно

Если техническая реализация живёт прямо в use case (сценарии), то его становится трудно менять и тестировать.

### Пример адаптера

```php
final readonly class ElasticFooGateway implements FooGatewayInterface
{
    public function __construct(
        private Client $client,
    ) {}

    public function query(GetFooInputDto $input): GetFooOutputDto
    {
        try {
            $response = $this->client->search([
                'index' => 'foo',
                'body' => [
                    'query' => [
                        'term' => [
                            'id' => $input->id,
                        ],
                    ],
                ],
            ]);

            return new GetFooOutputDto(
                items: [],
            );
        } catch (\Throwable $e) {
            throw new FooQueryException($e->getMessage(), previous: $e);
        }
    }
}
```

## 4. Как это связывается в DI

Если зависимость является портом, DI должен связывать именно интерфейс с реализацией.
Это делает контракт явным и не даёт concrete-классу просочиться в `UseCase`.

```php
return [
    GetFooUseCase::class => [
        'className' => GetFooUseCase::class,
        'constructorParams' => static function(): array {
            $locator = ServiceLocator::getInstance();

            return [
                $locator->get(FooGatewayInterface::class),
                $locator->get(FooAppService::class),
            ];
        },
    ],
    FooGatewayInterface::class => [
        'constructor' => static function(): FooGatewayInterface {
            return new ElasticFooGateway(
                ServiceLocator::getInstance()->get(ElasticClientFactory::class)->create(),
            );
        },
    ],
    FooAppService::class => [
        'className' => FooAppService::class,
    ],
];
```

`FooGatewayInterface::class => ElasticFooGateway::class` это нормальный способ выразить порт в Bitrix DI, если реализация собирается автопроводкой без специальных аргументов.
Если реализации нужны scalar-аргументы, объекты из factory или другая ручная сборка, для interface-key нужно использовать `constructor`, а не `className + constructorParams`.
`FooAppService::class => FooAppService::class` тоже нормален, потому что это внутренний сервис без отдельного порта.

## 5. Реальный пример из `rebit.wallet`

| Роль | Пример |
|---|---|
| UseCase | `FacetProductListQueryUseCase` |
| Port | `FacetProductListQueryInterface` |
| Adapter (адаптер) | `ElasticFacetProductListQuery` |

Это хороший эталон для нового кода, потому что сценарий отделён от Elastic-реализации.

## 6. `Legacy adapter` (адаптер legacy) как частный случай

Иногда источник данных пока только старый.
Тогда строим временную схему:

```text
UseCase -> Port -> LegacyAdapter -> старый код
```

Пример:

- `ProductsPromotionsInterface`
- `PromotionListAdapter`

Это нужно, чтобы перевести код на новую архитектуру до полной замены старого источника.

## 7. Исключения

Техническое исключение не должно протекать наружу как есть.

Неправильно:

```php
public function query(GetFooInputDto $input): GetFooOutputDto
{
    return $this->client->search(...);
}
```

Правильно:

```php
public function query(GetFooInputDto $input): GetFooOutputDto
{
    try {
        return $this->doQuery($input);
    } catch (\Throwable $e) {
        throw new FooQueryException('Ошибка получения данных Foo', previous: $e);
    }
}
```

Почему:

- наружу выходит предметный смысл ошибки;
- технический `previous` сохраняется для логирования и диагностики.

## 8. Incoming Port: не используется

В проекте входящие порты (Incoming Port / Driving Port) **не применяются**.
UseCase вызывается напрямую из Presentation-слоя (контроллер, компонент, CLI-команда).
Дополнительная абстракция между вызывающим кодом и UseCase не нужна.

## 9. Кросс-доменное взаимодействие

Внутри одного домена UseCase может вызывать другой UseCase.
Между доменами прямой вызов UseCase → UseCase **запрещён**.

Для связи между доменами используется тот же паттерн Outgoing Port + Adapter:

- порт описывается в домене-потребителе;
- адаптер в Infrastructure делегирует в инфраструктуру или AppService поставщика, минуя его UseCase;
- в DI порт связывается с адаптером через интерфейс, а не через concrete-класс адаптера.

Подробно с примерами: [06. Кросс-доменное взаимодействие](./06_кросс-доменное-взаимодействие.md).

---

<- [04. Слои и правило выбора](04_слои-и-правило-выбора.md) | [06. Кросс-доменное взаимодействие](06_кросс-доменное-взаимодействие.md) ->

[^ К оглавлению](README.md)
