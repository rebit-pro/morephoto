# HOW TO: добавить маршрут в модуль с API

## 1. Целевое дерево папок

Для нового модуля с API ориентир такой:

```text
local/modules/
`-- rebit.<module>/
    |-- di/
    |   |-- <Domain>.php
    |   `-- Layers/
    |       `-- Presentation.php
    |-- lib/
    |   |-- Application/
    |   |   `-- <Domain>/
    |   |       |-- Dto/
    |   |       |   `-- Request/
    |   |       |       `-- <Action>RequestDto.php
    |   |       `-- UseCase/
    |   |           `-- <Action>UseCase.php
    |   `-- Presentation/
    |       `-- Controller/
    |           `-- <Domain>/
    |               `-- <Entity>Controller.php
    |-- install/
    |   `-- index.php
    |-- routes.php
    `-- .settings.php
```

Пример реальных имен:

```text
local/modules/
`-- rebit.example/
    |-- di/
    |   `-- User.php
    |-- lib/
    |   |-- Application/
    |   |   `-- User/
    |   |       |-- Dto/Request/BanUserRequestDto.php
    |   |       `-- UseCase/BanUserUseCase.php
    |   `-- Presentation/
    |       `-- Controller/
    |           `-- User/
    |               `-- UserController.php
    |-- install/index.php
    |-- routes.php
    `-- .settings.php
```

## 2. Если API в модуле появляется впервые

Создайте `routes.php` в корне модуля и подключите роутинг в `install/index.php`:

- `use ModuleRoutingTrait`
- `initModuleRouting()` в `__construct()`
- `installModuleRouting()` в `DoInstall()`
- `uninstallModuleRouting()` в `DoUninstall()`

Важно:

- `routes.php` обязателен, иначе `initModuleRouting()` упадет
- если модуль уже установлен и вы только что добавили роутинг впервые, модуль нужно переустановить

Пример `install/index.php`:

```php
<?php

declare(strict_types=1);

use Bitrix\Main\InvalidOperationException;
use Bitrix\Main\SystemException;
use Rebit\Share\Infrastructure\Bitrix\Module\ModuleRoutingTrait;

class Rebit_Example extends CModule
{
    use ModuleRoutingTrait;

    public $MODULE_ID = 'rebit.example';

    /**
     * @throws SystemException
     */
    public function __construct()
    {
        $this->initModuleRouting();
    }

    /**
     * @throws InvalidOperationException
     * @throws SystemException
     */
    public function DoInstall(): bool
    {
        RegisterModule($this->MODULE_ID);
        $this->installModuleRouting();

        return true;
    }

    /**
     * @throws InvalidOperationException
     * @throws SystemException
     */
    public function DoUninstall(): bool
    {
        $this->uninstallModuleRouting();
        UnRegisterModule($this->MODULE_ID);

        return true;
    }
}
```

## 3. Создайте входной DTO/Collection/Entity

Правило для action:

- один action -> один typed DTO
- имя DTO -> `*RequestDto`
- обычный запрос -> `RequestDtoInterface`
- загрузка файлов -> `RequestFileDtoInterface`
- валидация -> `#[Assert\...]` в DTO
- переименование входных полей -> `#[SerializedName(...)]` в DTO, если внешний ключ запроса не совпадает с именем свойства

Если в route есть path-параметр, имя placeholder должно совпадать с полем DTO:

- route: `/api/v1/users/{id}/ban/`
- DTO: `public int $id`

Техническая реализация под капотом:

- Контроллер сам собирает данные запроса и вызывает `ArrayToDtoMapper`
- `ArrayToDtoMapper` сам переименовывает поля по `#[SerializedName(...)]`
- после гидрации DTO автоматически валидируется через Symfony Validator
- лишние поля отбрасываются; в лог пишется warning, отсутствующие поля выбросят исключение
- для path-параметров `{id}` переименование не используйте, имя в route и имя поля DTO должны совпадать
- массивы обязательно должны быть описаны phpDoc через `@var`
- не дублируйте scalar-тип через `#[Assert\Type('integer')]`, `#[Assert\Type('string')]` и т.п., типы валидируются автоматически

Пример `RequestDto`:

```php
<?php

declare(strict_types=1);

namespace Rebit\Example\Application\User\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class BanUserRequestDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\GreaterThan(0)]
        public int $id,
    ) {}
}
```

Пример DTO с валидацией и переименованием поля:

```php
<?php

declare(strict_types=1);

namespace Rebit\Example\Application\User\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateUserRequestDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $name,
        #[Assert\NotBlank]
        #[Assert\Email]
        #[SerializedName('user_email')]
        public string $email,
        #[Assert\GreaterThan(0)]
        public int $cityId,
    ) {}
}
```

Что это дает:

- запрос может прислать поле `user_email`
- в DTO оно попадет в свойство `$email`
- если `name`, `user_email` или `cityId` невалидны, action не выполнится

Пример DTO с массивом:

```php
<?php

declare(strict_types=1);

namespace Rebit\Example\Application\User\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateUserTagsRequestDto implements RequestDtoInterface
{
    /**
     * @param list<string> $tags
     */
    public function __construct(
        #[Assert\GreaterThan(0)]
        public int $id,
        /** @var list<string> */
        #[Assert\NotNull]
        #[Assert\Count(min: 1, max: 20)]
        #[Assert\All([
            new Assert\Length(min: 1, max: 50),
        ])]
        public array $tags,
    ) {}
}
```

Важно для массивов:

- для `array` обязателен `phpDoc` над свойством или параметром: `@var list<string>`, `@var ItemDto[]`, `@var array<int, ItemDto>`
- если массив содержит DTO, добавляйте `#[Assert\Valid]`
- если нужен внешний ключ запроса, можно сочетать массив с `#[SerializedName(...)]`
- не дублируйте `Assert\Type` для элементов, если тип уже выражен в `@var list<string>` или `@var ItemDto[]`

Пример массива вложенных DTO:

```php
<?php

declare(strict_types=1);

namespace Rebit\Example\Application\Order\Dto\Request;

use Rebit\Share\Application\Interface\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class OrderItemRequestDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank]
        public string $sku,
        #[Assert\GreaterThan(0)]
        public int $quantity,
    ) {}
}

final readonly class CreateOrderRequestDto implements RequestDtoInterface
{
    /**
     * @param OrderItemRequestDto[] $items
     */
    public function __construct(
        #[Assert\GreaterThan(0)]
        public int $userId,
        /** @var OrderItemRequestDto[] */
        #[Assert\NotNull]
        #[Assert\Count(min: 1)]
        #[Assert\Valid]
        public array $items,
    ) {}
}
```

Если тело запроса это корневой JSON-массив `[{...}, {...}]`, используйте не DTO с `array`, а collection:

```php
<?php

declare(strict_types=1);

namespace Rebit\Example\Application\Product\Dto\Request;

use Rebit\Share\Application\Collection\AbstractRequestCollection;
use Rebit\Share\Application\Interface\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProductImportItemRequestDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\NotBlank]
        public string $name,
        #[Assert\GreaterThan(0)]
        public int $price,
    ) {}
}

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

Пример action для collection:

```php
public function importAction(ProductImportCollection $collection): HttpResponse
{
    $this->importProductsUseCase->execute($collection);

    return $this->created();
}
```

## 4. Создайте UseCase и контроллер

Целевой паттерн для нового кода:

- контроллер лежит в `Presentation`-слое модуля
- класс `final`
- наследование от `BaseJsonController`
- зависимости только через конструктор
- action вызывает UseCase и возвращает `$this->json()`, `$this->created()` или `$this->noContent()`

Если модуль legacy и уже использует `lib/Controller`, сохраняйте текущую структуру модуля.

Пример `UseCase`:

```php
<?php

declare(strict_types=1);

namespace Rebit\Example\Application\User\UseCase;

use Rebit\Example\Application\User\Dto\Request\BanUserRequestDto;
use Rebit\Example\Application\User\Port\Outgoing\UserWriterInterface;

final readonly class BanUserUseCase
{
    public function __construct(
        private UserWriterInterface $userWriter,
    ) {}

    public function execute(BanUserRequestDto $inputDto): void
    {
        $this->userWriter->ban($inputDto->id);
    }
}
```

Пример `Controller`:

```php
<?php

declare(strict_types=1);

namespace Rebit\Example\Presentation\Controller\User;

use Bitrix\Main\HttpResponse;
use Rebit\Example\Application\User\Dto\Request\BanUserRequestDto;
use Rebit\Example\Application\User\UseCase\BanUserUseCase;
use Rebit\Share\Infrastructure\Controller\BaseJsonController;

final class UserController extends BaseJsonController
{
    public function __construct(
        private readonly BanUserUseCase $banUserUseCase,
    ) {
        parent::__construct();
    }

    public function banAction(BanUserRequestDto $dto): HttpResponse
    {
        $this->banUserUseCase->execute($dto);

        return $this->noContent();
    }
}
```

## 5. Зарегистрируйте зависимости в DI

Минимум:

- контроллер
- UseCase
- порты и их реализации

Правила:

- регистрации храните в `di/`
- `.settings.php` должен подключать нужный DI-файл
- если интерфейсная реализация собирается вручную, используйте `constructor`

Пример `di/User.php`:

```php
<?php

declare(strict_types=1);

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Example\Application\User\Port\Outgoing\UserWriterInterface;
use Rebit\Example\Application\User\UseCase\BanUserUseCase;
use Rebit\Example\Infrastructure\User\Persistence\BitrixUserWriter;
use Rebit\Example\Presentation\Controller\User\UserController;

return [
    BanUserUseCase::class => [
        'className' => BanUserUseCase::class,
        'constructorParams' => static function(): array {
            return [
                ServiceLocator::getInstance()->get(UserWriterInterface::class),
            ];
        },
    ],
    UserWriterInterface::class => [
        'className' => BitrixUserWriter::class,
    ],
    UserController::class => [
        'className' => UserController::class,
        'constructorParams' => static function(): array {
            return [
                ServiceLocator::getInstance()->get(BanUserUseCase::class),
            ];
        },
    ],
];
```

## 6. Добавьте маршрут в `routes.php`

Пример:

```php
<?php

declare(strict_types=1);

use Bitrix\Main\Routing\RoutingConfigurator;
use Rebit\Example\Presentation\Controller\User\UserController;

return static function(RoutingConfigurator $routes): void {
    $routes->post('/api/v1/users/{id}/ban/', [UserController::class, 'banAction']);
};
```

## 7. Проверьте перед сдачей

- route объявлен в `routes.php`
- placeholder в route совпадает с полем DTO
- action принимает один DTO
- контроллер и UseCase резолвятся из DI
- модульный роутинг установлен


## Подробно

- [../architecture-guide/09_http-api-контроллеры-и-маршруты.md](../architecture-guide/09_http-api-контроллеры-и-маршруты.md)
- [../architecture-guide/03_структура-модуля-и-di.md](../architecture-guide/03_структура-модуля-и-di.md)
