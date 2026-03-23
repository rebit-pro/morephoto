# UseCase и Service

В архитектуре используются три типа сервисных классов: **UseCase** и **Application Service** (слой Application), **Domain Service** (слой Domain). Каждый решает свою задачу.

## UseCase (Сценарий использования)

**UseCase** — это **конкретное действие**, которое может выполнить пользователь (или система).

- UseCase — это **отдельная бизнес-операция**, например:
  - "Зарегистрировать пользователя" (`RegisterUserUseCase`)
  - "Оформить заказ" (`CreateOrderUseCase`)
  - "Отправить уведомление" (`SendNotificationUseCase`)

- UseCase **не знает**, **как** именно делается операция (это работа репозиториев, сервисов и т.д.), он только **координирует** её выполнение, являясь по сути оркестратором. Может содержать простую логику оркестрации.

## Application Service

**Application Service** — сервис уровня Application, который **координирует работу с данными**: загрузка, маппинг, обогащение, кеширование, логирование...

- Может зависеть от репозиториев, портов, логгера, других Application/Domain сервисов.
- Содержит несколько методов, сгруппированных по ответственности.
- Используется внутри UseCase или других Application Service.

**Путь:** `Application/<Domain>/Service/`

**Суффикс:** `AppService`

### Пример Application Service

```php
<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Product\Service;

use Rebit\Wallet\Application\Product\Dto\ProductOutputDto;
use Rebit\Wallet\Application\Product\Port\Outgoing\ProductPropertyQueryInterface;
use Rebit\Wallet\Domain\Product\Repository\ProductPropertyRepository;

final readonly class ProductEnrichmentAppService
{
    public function __construct(
        private ProductPropertyRepository $propertyRepository,   // ← репозиторий 
        private ProductPropertyQueryInterface $propertyProvider,  // ← порт из другого домена или Elastic (инфраструктура)
    ) {}

    /**
     * Обогащает товар свойствами из нескольких источников.
     */
    public function enrichWithProperties(ProductOutputDto $product): ProductOutputDto
    {
        $properties = $this->propertyRepository->getByProductId($product->id);
        $extraData = $this->propertyProvider->query($product->id);

        return $product->withProperties($properties, $extraData);
    }

    /**
     * @return array<int, string>
     */
    public function getPropertyNamesForSection(int $sectionId): array
    {
        return $this->propertyRepository->getNamesBySectionId($sectionId);
    }
}
```

**Ключевое:** зависит от инфраструктуры (репозитории, порты), отвечает за **как загрузить и подготовить данные**.

## Domain Service

**Domain Service** — сервис уровня Domain, содержащий **чистую бизнес-логику**: вычисления, правила, алгоритмы.

- **Не зависит** от инфраструктуры: ни портов, ни логгера, ни Bitrix.
- Чистые функции: вход → вычисление → выход. Легко тестировать.
- Используется из Application-слоя (UseCase, AppService) и других Domain-сервисов **того же домена**. Между доменами — только через порты. Presentation обращается к Domain только через Application.

**Путь:** `Domain/<Domain>/Service/`

### Пример Domain Service

```php
<?php

declare(strict_types=1);

namespace Rebit\Wallet\Domain\Product\Service;

final readonly class ProductPopularityCalculator
{
    private const int HIGH_PRIORITY_THRESHOLD = 100;
    private const int SALES_WEIGHT = 10;

    /**
     * Рассчитывает рейтинг популярности товара.
     * Чистая бизнес-логика, не зависит от инфраструктуры.
     */
    public function calculate(int $sort, int $sales30Days): int
    {
        if ($sort <= self::HIGH_PRIORITY_THRESHOLD) {
            return $sort;
        }

        return $sort + ($sales30Days * self::SALES_WEIGHT);
    }
}
```

**Ключевое:** нет зависимостей от инфраструктуры, отвечает за **что является правилом бизнеса**.

## Сравнение: UseCase vs Application Service vs Domain Service

| Характеристика | UseCase | Application Service | Domain Service |
|---|---|---|---|
| **Назначение** | Бизнес-сценарий (одна операция) | Координация данных (загрузка, маппинг) | Бизнес-правила и алгоритмы |
| **Методы** | Один метод `execute()` | Несколько методов | Один или несколько методов |
| **Зависимости** | Порты, App/Domain сервисы | Репозитории, порты, Domain-сервисы | Нет (или только Domain-сервисы того же домена) |
| **Инфраструктура** | Через порты | Через порты | Не зависит |
| **Тестируемость** | Требует моков портов | Требует моков портов/репозиториев | Чистые unit-тесты, если нет зависимости от репозитория (Bitrix ORM) |
| **Переиспользование** | Нет (один сценарий) | Внутри Application-слоя | Application и Domain того же домена |
| **Пример** | `CreateOrderUseCase` → `execute()` | `OrderEnrichmentAppService` → `enrich()`, `mapToOutput()` | `OrderPriceCalculator` → `calculate()` |

## Когда что создавать?

- **UseCase** — точка входа для бизнес-сценария. Один сценарий = один UseCase.
- **Application Service** — логика загрузки/трансформации данных, которая переиспользуется в нескольких UseCase или слишком объёмна для размещения внутри UseCase.
- **Domain Service** — чистое бизнес-правило или алгоритм, не зависящий от способа хранения/доставки данных.

## Структура UseCase

```php
<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Product\UseCase;

use Rebit\Wallet\Application\Product\Dto\ProductListInputDto;
use Rebit\Wallet\Application\Product\Dto\ProductListOutputDto;
use Rebit\Wallet\Application\Product\Exception\ProductQueryException;
use Rebit\Wallet\Application\Product\Port\Outgoing\ProductListQueryInterface;
use Rebit\Wallet\Application\Product\Service\ProductFilterService;
use Rebit\Wallet\Application\Product\Service\ProductOutputMapper;
use Rebit\Wallet\Infrastructure\Product\Exception\ElasticProviderException;
use Rebit\Wallet\Infrastructure\Product\Exception\FilterValidationException;

final readonly class ProductListQueryUseCase
{
    public function __construct(
        private ProductListQueryInterface $productListProvider,      // ← Port (инфраструктура)
        private ProductOutputMapper $mapper,                         // ← Service (преобразование)
        private ProductFilterService $filterService,                // ← Service (логика оркестрации)
    ) {}

    /**
     * @throws ProductQueryException
     */
    public function execute(ProductListInputDto $inputDto): ProductListOutputDto
    {
        try {
            // Шаг 1: оркестрируем подготовку фильтров
            $filters = $this->filterService->prepareFilters($inputDto);

            // Шаг 2: получаем товары через порт инфраструктуры
            $products = $this->productListProvider->query($filters);

            // Шаг 3: трансформируем в выходной DTO через mapper
            return $this->mapper->mapCollection($products);
        } catch (ElasticProviderException $e) {
            // Заворачиваем инфраструктурное исключение в предметное
            throw new ProductQueryException('Ошибка при получении товаров', previous: $e);
        } catch (FilterValidationException $e) {
            // То же самое для исключений сервиса подготовки
            throw new ProductQueryException('Ошибка при подготовке фильтров', previous: $e);
        }
    }
}
```

## Правила UseCase

1. **Один метод** `execute()` — не создавайте другие public-методы.
2. **Зависимости через конструктор** — только DI.
   - **Port (исходящий)** — интерфейс для инфраструктуры: Elastic, HTTP и т.д. (например, `ProductListQueryInterface`)
   - **Service (Application)** — сервисы логики оркестрации и трансформации данных (например, `ProductFilterService`, `ProductOutputMapper`)
3. **Работает только с портами (интерфейсами)** для инфраструктуры (Outgoing Port), конкретные сервисы можно инжектировать как есть.
4. **Input Dto и Output Dto** — строгая типизация на входе и выходе.
5. **Оркестрирует шаги сценария** — координирует вызовы портов и сервисов, содержит простую логику оркестрации, но не сложные алгоритмы.
6. **Перехватывает исключения инфраструктуры** (в try-catch) **и выбрасывает предметные исключения** Application/Domain уровня.
   - Инфраструктурные исключения (Elastic, БД, HTTP, валидация и т.д.) не должны пробиваться в контроллер/компонент.
   - Передавайте оригинальное исключение через параметр `previous` для логирования и отладки.
7. Осуществляем тут логирование и кеширование при необходимости.

---

<- [11. Exceptions - работа с исключениями](11_исключения.md) | [13. Репозитории](13_репозитории.md) ->

[^ К оглавлению](README.md)
