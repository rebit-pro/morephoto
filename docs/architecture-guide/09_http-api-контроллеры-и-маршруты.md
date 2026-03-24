# HTTP API: контроллеры и маршруты

Эта глава описывает HTTP API-слой.

## Что делает `controller` (контроллер) в новой архитектуре

`Controller` (контроллер) это входная точка HTTP-запроса. Его задача минимальна:

1. принять уже сматченный маршрут (route);
2. получить `RequestDto`;
3. вызвать `UseCase`;
4. вернуть корректный HTTP-ответ.

Контроллер не должен превращаться в «мини-сервис».

## Схема потока

```text
HTTP-запрос (request)
  -> routes.php
  -> action контроллера (Controller action)
  -> RequestDto
  -> UseCase
  -> json()/created()/noContent()
```

## Где что лежит

| Что | Где |
|---|---|
| контроллеры | `api/public/local/modules/rebit.<module>/lib/Controller` |
| маршруты | `api/public/local/modules/rebit.<module>/routes.php` |
| DI для контроллеров | `api/public/local/modules/rebit.<module>/di/` |
| входная точка Bitrix для DI | `api/public/local/modules/rebit.<module>/.settings.php`, который подключает `di/` |

## Целевой стандарт для нового кода

Для controller DI действует тот же паттерн, что и для остального графа зависимостей:

- регистрации контроллеров лежат в `di/`, обычно в `di/Layers/Presentation.php`;
- `.settings.php` модуля подключает эти DI-файлы и возвращает `services` для Bitrix.

```php
final class FooController extends BaseJsonController
{
    public function __construct(
        private readonly GetFooUseCase $getFooUseCase,
    ) {
        parent::__construct();
    }

    public function getAction(GetFooRequestDto $dto): ControllerJson
    {
        return $this->json($this->getFooUseCase->execute($dto));
    }
}
```

### Что здесь важно

- контроллер `final`, если нет причины для наследования;
- зависимости приходят через конструктор;
- в базовом сценарии action принимает один `RequestDto`;
- action не занимается бизнес-логикой;
- ответ возвращается через базовые helper-методы контроллера.

## RequestDto

### Что это

`RequestDto` это typed-контракт (типизированный контракт) входа в action.

### Зачем он нужен

Без DTO controller (контроллер) быстро скатывается в ручной разбор:

- `$_REQUEST`
- `Context::getCurrent()->getRequest()`;
- неявные приведения типов;
- размазанную валидацию.

DTO делает вход:

- строгим;
- валидируемым;
- читаемым;
- повторяемым.

### Базовые правила

- имя: `*RequestDto`;
- для обычного запроса реализует `RequestDtoInterface`;
- для файлов реализует `RequestFileDtoInterface`;
- валидация задаётся на самом DTO через `#[Assert\...]`.
- DTO ответа контроллера оформляем как `*ResultDto`, если возвращаем структурированный ответ.

Пример:

```php
final readonly class GetUserRequestDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        public int $id,
    ) {}
}
```

## Дополнительный автомаппинг: `Entity` и request-коллекции

Базовый сценарий для нового кода в этой главе это `RequestDto`.
Но в проекте также есть автомаппинг `Entity` из path-параметра и автомаппинг коллекции DTO из JSON-массива.

Это полезно знать, чтобы читать существующий код и не ломать уже работающие action.

### Автомаппинг `Entity` из route-параметра

Если action принимает доменную `Entity`, платформа может загрузить её автоматически по `{id}` из route (маршрута).

Пример:

```php
$routes->get('/api/v1/products/{id}/', [ProductController::class, 'getAction']);
```

```php
final class ProductController extends BaseJsonController
{
    public function __construct(
        private readonly ProductGetQueryUseCase $productGetQueryUseCase,
    ) {
        parent::__construct();
    }

    public function getAction(Product $product): ControllerJson
    {
        return $this->json($this->productGetQueryUseCase->execute($product));
    }
}
```

Что происходит:

- из `{id}` берётся идентификатор;
- ORM загружает `Product`;
- если сущность не найдена, выбрасывается ошибка уровня HTTP.

Использовать это стоит осторожно.
Для нового кода и сложных action понятнее и предсказуемее `RequestDto`.

### Автомаппинг request-коллекции

Если тело запроса это корневой JSON-массив, его можно замаппить не в один DTO, а в typed-коллекцию DTO.

Пример DTO элемента:

```php
final readonly class ProductImportItemRequestDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Type('string')]
        public string $name,
        #[Assert\NotNull]
        #[Assert\Type('integer')]
        #[Assert\GreaterThan(0)]
        public int $price,
    ) {}
}
```

Пример коллекции:

```php
/**
 * @extends AbstractRequestCollection<ProductImportItemRequestDto>
 */
final readonly class ProductImportCollection extends AbstractRequestCollection
{
    public static function getItemClass(): string
    {
        return ProductImportItemRequestDto::class;
    }
}
```

Использование в action:

```php
public function importAction(ProductImportCollection $collection): HttpResponse
{
    $this->importProductsUseCase->execute($collection);

    return $this->created();
}
```

Эти сценарии подробнее разобраны в
[06. Роуты и контроллеры](../code-writing-rules/06_роуты-и-контроллеры.md).

## `routes.php`

### Что это

Файл декларации HTTP-маршрутов модуля.

### Пример

```php
return static function(RoutingConfigurator $routes): void {
    $routes->get('/api/v1/example/foo/', [FooController::class, 'getAction']);
    $routes->post('/api/v1/example/foo/', [FooController::class, 'createAction']);
};
```

### Параметры пути

Если в route (маршруте) есть `{id}`, поле DTO должно называться так же:

```php
$routes->post('/api/v1/users/{id}/ban/', [UserController::class, 'banAction']);
```

```php
final readonly class BanUserRequestDto implements RequestDtoInterface
{
    public function __construct(
        public int $id,
    ) {}
}
```

## Какие ответы использовать

| Сценарий | Что возвращать |
|---|---|
| обычный JSON-ответ | `$this->json(...)` |
| создан новый ресурс | `$this->created()` или `json(..., status: 201)` |
| команда выполнилась без тела | `$this->noContent()` |

### Базовые HTTP-коды

| Код | Когда использовать |
|---|---|
| `200 OK` | обычный успешный запрос |
| `201 Created` | ресурс создан |
| `204 No Content` | команда выполнена, тело не нужно |

## Маппинг `Entity` и `Collection` в HTTP-ответ

`Controller` не должен отдавать `Entity` и `Collection` напрямую в `json()`.

Почему:

- наружу начинает протекать внутренняя доменная модель;
- контракт API случайно привязывается к ORM-структуре;
- любое изменение `Entity` ломает внешний JSON-ответ.

Неправильно:

```php
final class UserController extends BaseJsonController
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct();
    }

    public function getAction(GetUserRequestDto $dto): ControllerJson
    {
        return $this->json($this->userRepository->getById($dto->id));
    }
}
```

### Пример: маппинг одной `Entity`

Правильно:

```php
final readonly class GetUserResultDto
{
    public function __construct(
        public int $id,
        public string $name,
        public bool $isActive,
    ) {}
}

final readonly class GetUserUseCase
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function execute(GetUserRequestDto $dto): GetUserResultDto
    {
        $user = $this->userRepository->getById($dto->id);

        return new GetUserResultDto(
            id: $user->getId(),
            name: $user->getName(),
            isActive: $user->isActive(),
        );
    }
}

final class UserController extends BaseJsonController
{
    public function __construct(
        private readonly GetUserUseCase $getUserUseCase,
    ) {
        parent::__construct();
    }

    public function getAction(GetUserRequestDto $dto): ControllerJson
    {
        return $this->json($this->getUserUseCase->execute($dto));
    }
}
```

### Пример: маппинг `Collection`

Не держите маппинг коллекции прямо внутри `execute()`.

- для короткого и локального преобразования достаточно приватного метода;
- если маппинг переиспользуется или растёт по правилам, его лучше вынести в отдельный mapper.

Правильно:

```php
final readonly class UserItemResultDto
{
    public function __construct(
        public int $id,
        public string $name,
    ) {}
}

final readonly class UserListResultDto
{
    /**
     * @param array<int, UserItemResultDto> $items
     */
    public function __construct(
        public array $items,
    ) {}
}

final readonly class ListUsersUseCase
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function execute(): UserListResultDto
    {
        $users = $this->userRepository->getActiveUsers();

        return new UserListResultDto(
            items: $this->mapItems($users),
        );
    }

    /**
     * @param iterable<User> $users
     *
     * @return array<int, UserItemResultDto>
     */
    private function mapItems(iterable $users): array
    {
        $items = [];

        foreach ($users as $user) {
            $items[] = new UserItemResultDto(
                id: $user->getId(),
                name: $user->getName(),
            );
        }

        return $items;
    }
}
```

Если маппинг становится общим или сложным, лучше так:

```php
final readonly class UserListResultMapper
{
    /**
     * @param iterable<User> $users
     *
     * @return array<int, UserItemResultDto>
     */
    public function mapItems(iterable $users): array
    {
        $items = [];

        foreach ($users as $user) {
            $items[] = new UserItemResultDto(
                id: $user->getId(),
                name: $user->getName(),
            );
        }

        return $items;
    }
}
```

Главная мысль:

- `Entity` и `Collection` живут внутри `Domain`;
- наружу через HTTP лучше отдавать `ResultDto`;
- не размазывайте маппинг по action и не раздувайте `execute()` длинным преобразованием;
- controller получает уже готовый результат сценария и не сериализует ORM-объекты сам.

## Антипаттерны

- SQL, Elastic и legacy-вызовы внутри action;
- ручной разбор запроса (request) при наличии DTO-мэппинга;
- несколько сценариев в одном action;
- `ServiceLocator` внутри контроллера;
- подготовка сложного UI-ответа прямо в controller (контроллере) вместо use case (сценария) и presentation-слоя (слоя представления).

## Что нужно запомнить

1. `Controller` (контроллер) это тонкая HTTP-входная точка, а не место бизнес-логики.
2. В базовом сценарии action принимает типизированный `RequestDto` и вызывает `UseCase`.
3. Роуты живут в `routes.php`, а DI контроллерного слоя описывается в `di/` модуля.

---

<- [08. Bitrix-компоненты и MVVM](08_bitrix-компоненты.md) | [10. CLI-команды](10_cli-команды.md) ->

[^ К оглавлению](README.md)
