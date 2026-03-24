# RequestDto

DTO (объекты передачи данных), которые автоматически мапятся на параметры и тело запроса в экшене контроллера. Он может быть только один на Action.
Обязательно обозначение маркерным `RequestDtoInterface`!

Кол-во и названия параметров должны точно соответствовать параметрам\телу запроса.
Внутри DTO необходимо описать валидацию по правилам[ валидатора Symfony](https://symfony.ru/doc/current/validation.html).
Обязательно описание всех массивов через `@var` над самим свойством.

Если в маршруте есть параметры, то они добавляются в DTO как свойства.

## Пример

```php
<?php

declare(strict_types=1);

namespace Rebit\Bybit\Presentation\Controller\Review\Dto;

use Rebit\Share\Application\Interface\RequestDtoInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ReviewListRequestDto implements RequestDtoInterface
{
    public function __construct(
        #[Assert\GreaterThan(0)]
        public int $id,

        /**
         * @var PropertyDto[]
         */
        #[Assert\Valid]
        public array $properties,
    ) {}
}
```

## Использование в контроллере

```php
public function getListAction(ReviewListRequestDto $data): ControllerJson
{
    return $this->json($this->reviewGetListUseCase->execute($data));
}
```

---
<- [09. PhpDoc](09_phpdoc.md) | [11. Exceptions - работа с исключениями](11_исключения.md) ->

[^ К оглавлению](README.md)
