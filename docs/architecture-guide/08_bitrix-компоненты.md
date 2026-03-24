# Bitrix-компоненты и MVVM

> ⚠️ **В текущей версии проекта Rebit P2P Bitrix-компоненты с шаблонами не используются.**
> Фронтенд реализован на Vue 3 + TypeScript + Vite (SPA).
> Этот раздел сохранён как справочный на случай, если компоненты понадобятся в будущем.

Эта глава объясняет, как писать компоненты в новой архитектуре проекта.

## Почему компонент нельзя оставлять «толстым»

В legacy Bitrix-компонент часто превращается в место, где одновременно живут:

- чтение `$_REQUEST`;
- запросы к БД;
- бизнес-логика;
- подготовка шаблона;
- кэширование;
- рендер.

Такой компонент почти невозможно безопасно поддерживать.

Новая схема нужна, чтобы разделить эти ответственности.

## Целевая схема компонента

```text
install/components/<name>/class.php
    -> ParamsDto
    -> Builder
    -> ResultDto
    -> ViewModel
    -> шаблон (template)
```

## Что означает каждая часть

| Часть | Что это | Зачем нужна |
|---|---|---|
| `class.php` | тонкий вход в компонент | не держит бизнес-логику |
| `ParamsDto` | строгий контракт входных параметров | убирает массивную магию |
| `Builder` | orchestration (оркестрация) для компонента | собирает данные через сценарии (use case) |
| `ExceptionResolver` | policy обработки непойманного исключения компонента | задаёт fallback / skip render / 404 / redirect |
| `ResultDto` | строгий результат сборки | отделяет сборку от шаблона |
| `ViewModel` | удобное API для template (шаблона) | шаблон становится простым |
| `template` | только отображение | без предметных решений |

## Поток выполнения

```text
Страница
  -> IncludeComponent(...)
  -> class.php
  -> Builder::build(...)
  -> UseCase::execute(...)
  -> ResultDto
  -> ViewModel
  -> шаблон (template)
```

## Эталон в проекте

Смотрите `catalog.section.fast`:

- `api/public/local/modules/rebit.wallet/install/components/catalog.section.fast/class.php`
- `api/public/local/modules/rebit.wallet/lib/Presentation/Component/CatalogSection/CatalogSectionBuilder.php`
- `api/public/local/modules/rebit.wallet/lib/Presentation/Component/CatalogSection/ViewModel/CatalogSectionViewModel.php`

## 1. `class.php`

### Что он должен делать

- выбрать builder;
- выбрать DTO параметров;
- при необходимости вернуть view model (модель представления).

### Чего он не должен делать

- ходить в Bitrix ORM;
- вызывать Elastic;
- содержать бизнес-логику;
- решать SEO-задачи;
- собирать итоговый список товаров.

### Пример

```php
class CatalogSectionFast extends AbstractDtoComponent implements HasViewModelInterface
{
    protected function getBuilderClass(): string
    {
        return CatalogSectionBuilder::class;
    }

    protected function getParamsDtoClass(): string
    {
        return CatalogSectionComponentParamsDto::class;
    }

    protected function getExceptionResolverClass(): string
    {
        return CatalogSectionExceptionResolver::class;
    }

    public function getViewModel(): CatalogSectionViewModel
    {
        return new CatalogSectionViewModel($this);
    }
}
```

Для новых компонентов `getExceptionResolverClass()` почти всегда нужно переопределять.
Не переопределять его можно только как редкое осознанное исключение, когда компонент обязан пробрасывать ошибку выше без собственной деградации.
Тогда сработает `DefaultComponentExceptionResolver`, и исключение будет отдано Битрикс и скорее всего выведено на странице.

## 2. `ParamsDto`

### Что это

Строго типизированный контракт входа в компонент.

### Почему это важно

Стандартный `$arParams` слишком легко превращается в неуправляемый массив.
DTO делает вход:

- проверяемым;
- читаемым;
- переиспользуемым.

### Именование

- `*ComponentParamsDto`
- `*ComponentResultDto`

## 3. `Builder` (сборщик)

### Что это

`Builder` это orchestration-слой (слой оркестрации) компонента.
Он не выражает предметную бизнес-логику, а собирает данные, нужные конкретному представлению.

### Что `builder` может делать

- вызвать несколько сценариев (use case);
- собрать итоговый результат;
- выбрать нужную форму данных для view model (модели представления).

### Что `builder` не должен делать

- напрямую ходить в Elastic или legacy;
- содержать тяжёлые предметные правила;
- работать как репозиторий (repository) или сценарий (use case).
- глотать ошибки через `return null`, если это не валидный бизнес-результат.

### Пример схемы `builder`

```php
final readonly class FooBuilder implements ComponentBuilderInterface
{
    public function __construct(
        private GetFooListUseCase $fooList,
        private GetFooSeoUseCase $fooSeo,
    ) {}

    public function build(FooComponentParamsDto $params): ?FooComponentResultDto
    {
        $items = $this->fooList->execute($params->sectionId);
        $seo = $this->fooSeo->execute($params->sectionId);

        return new FooComponentResultDto(
            items: $items,
            seo: $seo,
        );
    }
}
```

## 3.1. Обработка непойманных исключений компонента

В проекте непойманные исключения компонентов обрабатываются в `AbstractDtoComponent` по заданным вами правилам.

Схема:

```text
Builder
  -> throw
  -> AbstractDtoComponent catch (\Throwable)
  -> ComponentExceptionResolverInterface
  -> ComponentExceptionResolutionDto
  -> render fallback / skip render / 404 / redirect
```

Это нужно, чтобы:

- не размазывать деградацию по builder-ам;
- централизовать поведение компонента на ошибке;
- сохранить `class.php` тонким;
- не смешивать orchestration и Bitrix-side effects.

### Почему `ExceptionResolver` считается стандартом

Для нового компонента `ExceptionResolver` обычно обязателен.
Через него компонент явно описывает своё поведение на непойманной ошибке.

Обычно resolver нужен, если компонент должен:

- показать заглушку;
- скрыться;
- завершить запрос `404`;
- сделать redirect.

### Когда допустимо не задавать resolver

Только в редких случаях, когда компонент должен работать в fail-fast режиме и обязан пробрасывать исключение выше без собственной деградации.
Это должно быть осознанным решением, а не поведением по умолчанию.

### Какие действия поддерживаются

`ComponentExceptionActionEnum`:

- RENDER_FALLBACK: компонент не рендерится штатно, вместо него выводится шаблон заглушки;
- SKIP_RENDER: компонент молча не выводит никакой HTML;
- PROCESS_404: компонент завершает запрос через стандартный показ 404;
- REDIRECT: компонент завершает запрос HTTP-редиректом на другой URL, который передается в исключении ComponentRedirectException.

### Эталонные примеры

`catalog.section.fast`:

- любой непойманный `Throwable` логируется;
- компонент рендерит заглушку;
- HTTP-статус `503`.

`catalog.smart.filter.fast`:

- `SmartFilterParsePathException` -> `404`;
- `ComponentRedirectException` -> redirect;
- прочие ошибки -> `skip render`.

### Что можно ловить локально

Локальный `catch` допустим в двух случаях:

1. Это отдельный продуктовый сценарий, а не деградация компонента.
2. Нужно преобразовать низкоуровневое исключение в более понятный верхний сценарий.

Пример продуктового сценария:

- `SmartFilterUnknownPropertyException` в `SmartFilterBuilder` локально превращается в `SmartFilterIncomingFilterDto::createEmpty()`.

Пример преобразования:

- builder ловит низкоуровневую ошибку и бросает `ComponentRedirectException` с runtime URL.

### Что нельзя делать

- `catch (\Throwable) { return null; }`
- `catch (\Throwable) { log; return null; }`
- `LocalRedirect()` прямо в builder, если это policy компонента;
- `Tools::process404()` прямо в builder, если это policy компонента.

Подробная инструкция с примерами:

- [../how-to/03_обработка-исключений-в-компоненте.md](../how-to/03_обработка-исключений-в-компоненте.md)

## 4. `ViewModel` (модель представления)

### Что это

`ViewModel` (модель представления) это объект, который делает шаблон удобным.

### Почему нужен отдельный слой для шаблона

Шаблон не должен:

- знать устройство use case (сценария);
- собирать заголовки и подписи;
- вычислять UI-состояния;
- разбирать большой DTO вручную.

`ViewModel` (модель представления) превращает результат в понятный API для template (шаблона).

## 5. `SetViewTarget` и AJAX

Это важное проектное ограничение.

`SetViewTarget` использует буферизацию вывода, поэтому нельзя бездумно оборачивать в него тяжёлые компоненты.

Особенно опасны случаи:

- компонент сам использует `SetViewTarget`;
- компонент живёт вместе с AJAX-потоком;

### Для каталога это означает

- `catalog.smart.filter.fast` вызываем inline;
- `catalog.section.fast` вызываем inline;
- не используем `SetViewTarget` для этой пары при AJAX-сценарии.

## Что нужно запомнить

1. Компонент не должен быть местом для бизнес-логики и инфраструктуры.
2. `class.php` тонкий, `Builder` собирает, `ExceptionResolver` задаёт деградацию, `ViewModel` подаёт, шаблон рендерит.
3. DTO нужны не ради формальности, а ради управляемого контракта.
4. Непойманные ошибки компонента не обрабатываются хаотично в builder: общая policy живёт в `ExceptionResolver`.
5. Для компонентов важно соблюдать ограничения `SetViewTarget` и AJAX.

---

<- [07. Домен, ORM и репозиторий](07_домен-orm-и-репозиторий.md) | [09. HTTP API: контроллеры и маршруты](09_http-api-контроллеры-и-маршруты.md) ->

[^ К оглавлению](README.md)
