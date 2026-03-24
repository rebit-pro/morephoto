# Домен, ORM и репозиторий

## Что такое `Domain`

`Domain` это предметное ядро.
Здесь живёт код, который отвечает на вопрос:

«Как устроена предметная область и какие у неё правила?»

В контексте проекта это могут быть:

- сущности каталога;
- правила SEO;
- правила умного фильтра;
- репозитории предметных данных;
- объекты-значения (value object);
- доменные исключения.

## Почему `Domain` важен

Если предметные правила живут не в домене, а в случайных местах, они становятся невидимыми.
Тогда разработчик не понимает:

- где искать инвариант;
- где менять правило;
- где правильно строить новую логику.

## Практика проекта: DDD с Bitrix-упрощением

В классическом DDD код домена полностью отвязан от ORM: сущности — обычные PHP-классы, а доступ к данным идёт через интерфейсы.
Мы так не делаем, потому что Bitrix ORM генерирует Entity-классы автоматически, и обёртка поверх них — это лишний код без пользы.

Вместо этого мы используем ORM-классы Bitrix напрямую как доменные объекты.

### Что где лежит

Все ORM-классы одного домена живут вместе:

```
lib/Domain/<Domain>/
├── Entity/
│   ├── Table/
│   │   └── ProductTable.php        # описание таблицы (поля, связи)
│   ├── Product.php                 # сущность — extends EO_Product
│   └── ProductCollection.php       # коллекция — extends EO_Product_Collection
└── Repository/
    └── ProductRepository.php       # точка доступа к данным
```

### Что делает каждый файл

**`ProductTable`** — описывает структуру таблицы: поля, типы, связи. Bitrix по нему генерирует SQL и базовые классы `EO_Product` / `EO_Product_Collection`.

```php
final class ProductTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'product';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))->configurePrimary()->configureAutocomplete(),
            new StringField('NAME'),
            new IntegerField('PRICE'),
        ];
    }

    public static function getObjectClass(): string
    {
        return Product::class;
    }

    public static function getCollectionClass(): string
    {
        return ProductCollection::class;
    }
}
```

**`Product`** — сущность. Наследует автогенерированный `EO_Product`, может содержать доменные методы.

```php
final class Product extends EO_Product
{
    public function isActive(): bool
    {
        return 'Y' === $this->getActive();
    }
}
```

**`ProductCollection`** — типизированная коллекция сущностей.

```php
final class ProductCollection extends EO_Product_Collection
{
}
```

**`ProductRepository`** — единственное место, где пишутся запросы к этой таблице. UseCase и builder обращаются только к репозиторию, а не строят запросы сами.

### Откуда берутся `EO_*`

`EO_Product` и `EO_Product_Collection` — базовые классы, которые Bitrix генерирует по описанию `ProductTable`. Они содержат геттеры, сеттеры и query-методы. Генерация запускается командой `make annotate MODULE_NAME=rebit.xxx`.

Наши классы `Product` и `ProductCollection` наследуют их и могут добавлять предметные методы.

## Что относится к `Domain`

- сущности Bitrix ORM;
- коллекции;
- объекты-значения (value object);
- предметные исключения;
- доменные сервисы;
- репозитории;
- предметные enum.

## DTO и `ValueObject` (объект-значение)

Эти понятия из [Правила написания кода](../code-writing-rules/README.md) тоже важны для архитектуры.

### DTO

DTO нужен для передачи данных между слоями.
Он не должен становиться носителем предметного поведения.

Признаки DTO:

- переносит данные;
- обычно `final readonly`;
- может лежать в `Application`, `Presentation`, `Contracts`.

### `ValueObject` (объект-значение)

`ValueObject` (объект-значение) описывает единый концепт предметной области.
Он не идентифицируется по ID, а сравнивается по значению.

Признаки ValueObject:

- immutable;
- содержит инвариант;
- может иметь методы и проверки;
- выражает предметный смысл.

Пример общего объекта:

```php
final readonly class Money
{
    public function __construct(
        private int $amount,
    ) {
        if (0 > $amount) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount;
    }
}
```

## Репозиторий

### Что это

Репозиторий (`Repository`) это точка доступа к предметным данным.
Он нужен, чтобы запросы к хранилищу не расползались по сценариям (use case), builder и компонентам.

### Почему репозиторий у нас живёт в `Domain`

Потому что Bitrix ORM в проекте считается частью предметного доступа к данным, а не отдельной тяжёлой внешней интеграцией.
Это важное проектное правило.

## Что возвращает репозиторий

- `Entity`;
- `Collection`;
- `Result` Bitrix ORM;
- скаляры;
- массивы скаляров.

- не возвращаем «сырые» ассоциативные структуры;

### Почему не DTO

Потому что за парсинг и маппинг данных отвечают сервисы и UseCase и могут это делать по-разному с одним и тем же набором данных.

## Пример репозитория

```php
final readonly class ProductRepository
{
    public function getById(int $id): ?Product
    {
        return ProductTable::query()
            ->setSelect(['ID', 'NAME'])
            ->where('ID', $id)
            ->fetchObject();
    }

    /**
     * @param array<int, int> $ids
     */
    public function getByIds(array $ids): ProductCollection
    {
        return ProductTable::query()
            ->setSelect(['ID', 'NAME'])
            ->whereIn('ID', $ids)
            ->fetchCollection();
    }
}
```

## Конвенция `get*` и `find*`

Это важное правило из [Правил написания кода](../code-writing-rules/README.md).

| Префикс | Смысл |
|---|---|
| `get*` | ожидаем наличие, при отсутствии бросаем исключение |
| `find*` | допускаем отсутствие и возвращаем `null` |

Пример:

```php
public function findById(int $id): ?Product
{
    // ...
}

public function getById(int $id): Product
{
    $product = $this->findById($id);

    if (!$product instanceof Product) {
        throw new ProductNotFoundException();
    }

    return $product;
}
```

## Что ещё важно для ORM-репозиториев

- не используем ORM кэш через `setCacheTtl()` как основной проектный подход, кеширует уровень Application;
- держим запросы и выборки в репозитории, а не размазываем по сценариям (use case);

## Что не должно жить в репозитории

- оркестрация нескольких сценариев;
- работа с HTTP;
- подготовка данных под шаблон;
- логика внешнего API;
- случайная бизнес-логика, не связанная с доступом к данным.

## Где проходит граница с `Infrastructure`

| Ситуация | Куда класть |
|---|---|
| Bitrix ORM и raw SQL предметного доступа | `Domain` |
| Elastic, HTTP API, файловая система, legacy-классы | `Infrastructure` |

## Частые ошибки

### Ошибка 1. `Use case` (сценарий) сам делает ORM-запрос

Это ломает разделение ответственности.
`UseCase` должен вызывать репозиторий или порт, а не строить запрос сам.

### Ошибка 2. Репозиторий начинает форматировать данные под UI

Репозиторий работает с предметными данными, а не с отображением.

### Ошибка 3. Внешний HTTP-клиент кладётся в `Domain`

Любая внешняя интеграция, не относящаяся к предметному ORM-доступу, это уже `Infrastructure`.

---

<- [06. Кросс-доменное взаимодействие](06_кросс-доменное-взаимодействие.md) | [08. Bitrix-компоненты и MVVM](08_bitrix-компоненты.md) ->

[^ К оглавлению](README.md)
