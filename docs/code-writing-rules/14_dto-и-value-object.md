# DTO и Value Object

## DTO (Data Transfer Object)

**DTO** — это простой объект для переноса данных между слоями приложения. Он не содержит логики, только свойства и аксессоры. По умолчанию DTO readonly.
Для уменьшения оверкода могут быть открыты поля на запись (например, для счетчиков).
Могут иметь кастомные конструкторы.

### Используется для:

- Передачи данных в контроллер
- Сериализации/десериализации
- Защиты доменной логики от внешнего ввода

### Пример DTO

```php
<?php

declare(strict_types=1);

namespace Rebit\Wallet\Application\Product\Dto;

final readonly class ProductListOutputDto
{
    public function __construct(
        /** @var array<int, ProductItemDto> $items ключ - ID товара */
        public array $items,
        public int $total,
        public int $page,
        public int $pageSize,
    ) {}
}
```

## Value Object (VO)

**Value Object** — объект, представляющий данные, а не сущность. Он не изменяется после создания (immutable) и сравнивается по значению, а не по ID.

### Используется для:

- Инкапсуляции инвариантов (например, `DateRange`, `Money`, `Email`)
- Самодокументирования кода
- Упрощения валидации

### Пример Value Object

```php
<?php

declare(strict_types=1);

namespace Rebit\Share\Domain\Value;

final readonly class Money
{
    private const CURRENCY = 'RUB';

    /**
     * @param int|float $amount Сумма в копейках
     */
    public function __construct(
        private int|float $amount,
    ) {
        if (0 > $amount) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
    }

    public function getAmount(): int|float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return self::CURRENCY;
    }

    /**
     * Сравнение двух объектов Money по значению
     */
    public function equals(self $other): bool
    {
        return $this->amount === $other->amount
            && $this->getCurrency() === $other->getCurrency();
    }

    public function add(self $other): self
    {
        return new self($this->amount + $other->amount);
    }

    /**
     * @return array{
     *     amount: int|float,
     *     currency: string,
     * }
     */
    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'currency' => self::CURRENCY,
        ];
    }
}
```

## Отличие DTO и Value Object

| Характеристика | DTO                                                         | Value Object |
|---|-------------------------------------------------------------|---|
| **Назначение** | Передача данных между слоями                                | Представление единого концепта |
| **Логика** | Нет, только данные                                          | Может быть валидация и методы |
| **Immutability** | Обычно readonly                                             | Обязательно readonly |
| **Сравнение** | По полям (если нужно)                                       | По значению (equals) |
| **Примеры** | `ProductListOutputDto`                                      | `Money`, `DateRange`, `Email` |
| **Создание** | Часто через конструктор/фабрику/кастомный конструктор в DTO | Через конструктор с валидацией |

---

<- [13. Репозитории](13_репозитории.md) |

[^ К оглавлению](README.md)
