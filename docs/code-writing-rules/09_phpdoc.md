# PhpDoc для PHPStan

PHPStan — это инструмент статического анализа кода, который помогает выявлять потенциальные ошибки на этапе разработки. Для корректной работы PHPStan важно правильно использовать phpDoc-комментарии. В этом руководстве описаны правила и примеры использования phpDoc для iterable (массивов, коллекций, генераторов), Result-объектов БД, дженериков и исключений.

## 1. Обязательный phpDoc с описанием полей всех iterable, Result-объектов БД и дженериков

### Описание

Каждый метод или функция, которые возвращают или принимают iterable (массивы, коллекции, генераторы), Result-объекты БД или дженерики, должны содержать phpDoc-комментарий с описанием их структуры. Это помогает PHPStan и разработчику понять тип данных и предотвратить ошибки, а также phpStorm выведет подсказки.

### Примеры

#### 1.1. Массивы

Если метод возвращает массив или принимает его как параметр, необходимо описать структуру массива.

```php
/**
 * @param array<int, string> $ids Список ID пользователей
 * @return array<int, array{
 *     id: int,
 *     name: string,
 *     email: string,
 * }> Список пользователей с их данными
 */
public function getUsersData(array $ids): array
{
    // ...
}
```

#### 1.2. Коллекции и генераторы

Для коллекций и генераторов также требуется указывать тип данных.

```php
/**
 * @return \Generator<int, User> Генератор пользователей
 */
public function getUsersGenerator(): \Generator
{
    // ...
}

/**
 * @param Collection<Product> $products Коллекция товаров
 *
 * @return Collection<Product>
 */
public function filterProducts(Collection $products): Collection
{
    // ...
}
```

#### 1.3. Result-объекты БД

Для Result-объектов БД необходимо описать структуру данных, которые они содержат.

```php
/**
 * @return \Bitrix\Main\DB\Result<array{
 *     ID: int,
 *     NAME: string,
 *     ACTIVE: string,
 * }>
 */
public function getActiveUsers(): \Bitrix\Main\DB\Result
{
    return UserTable::query()
        ->setSelect(['ID', 'NAME', 'ACTIVE'])
        ->where('ACTIVE', '=', 'Y')
        ->exec();
}
```

#### 1.4. Дженерики

Для дженериков необходимо указать тип данных, которые они обрабатывают.

```php
/**
 * @template T of Entity
 * @param class-string<T> $className Класс сущности
 *
 * @return Collection<T> Коллекция сущностей
 */
public function getCollection(string $className): Collection
{
    // ...
}
```

## 2. Обязательный phpDoc для всех исключений

### Описание

Для каждого метода или функции, которые могут выбрасывать исключения, необходимо указать их в phpDoc-комментарии. Это правило не распространяется на контроллеры.

### Примеры

Если метод может выбросить исключения, укажите их с помощью тега `@throws`. Так разработчик при использовании вашего метода будет знать, что в метод может вернуть исключение и сможет его корректно обработать.

```php
/**
 * @throws UserNotFoundException Если пользователь не найден
 * @throws DatabaseException Если возникла ошибка БД
 * @return User Данные пользователя
 */
public function getUserById(int $id): User
{
    $result = UserTable::getById($id);

    if (!$result instanceof User) {
        throw new UserNotFoundException('User not found');
    }

    return $result->fetchObject();
}
```

---

<- [08. Комментарии](08_комментарии.md) | [10. RequestDto](10_request-dto.md) ->

[^ К оглавлению](README.md)
