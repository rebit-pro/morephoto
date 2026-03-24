<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\PaymentMethod\UseCase;

use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\PaymentMethod\Dto\Result\PaymentMethodListResultDto;
use Rebit\Exchange\Application\PaymentMethod\UseCase\GetPaymentMethodsUseCase;
use Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethod;
use Rebit\Exchange\Domain\PaymentMethod\Entity\PaymentMethodCollection;
use Rebit\Exchange\Domain\PaymentMethod\Repository\PaymentMethodRepository;

/**
 * @internal
 */
final class GetPaymentMethodsUseCaseTest extends TestCase
{
    public function testReturnsEmptyListWhenNoMethods(): void
    {
        $collection = $this->createStub(PaymentMethodCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([]));
        $repo = $this->createMock(PaymentMethodRepository::class);
        $repo
            ->expects($this->once())
            ->method('findActive')
            ->willReturn($collection)
        ;
        $result = (new GetPaymentMethodsUseCase($repo))->execute();
        self::assertInstanceOf(PaymentMethodListResultDto::class, $result);
        self::assertSame([], $result->items);
    }

    public function testReturnsListWithPaymentMethods(): void
    {
        $method1 = $this->createStub(PaymentMethod::class);
        $method1->method('getId')->willReturn(1);
        $method1->method('getUfCode')->willReturn('sberbank');
        $method1->method('getUfName')->willReturn('Сбербанк');
        $method1->method('getUfSort')->willReturn(100);
        $method2 = $this->createStub(PaymentMethod::class);
        $method2->method('getId')->willReturn(2);
        $method2->method('getUfCode')->willReturn('tinkoff');
        $method2->method('getUfName')->willReturn('Тинькофф');
        $method2->method('getUfSort')->willReturn(200);
        $collection = $this->createStub(PaymentMethodCollection::class);
        $collection->method('getIterator')->willReturn(new \ArrayIterator([$method1, $method2]));
        $repo = $this->createStub(PaymentMethodRepository::class);
        $repo->method('findActive')->willReturn($collection);
        $result = (new GetPaymentMethodsUseCase($repo))->execute();
        self::assertCount(2, $result->items);
        self::assertSame(1, $result->items[0]->id);
        self::assertSame('sberbank', $result->items[0]->code);
        self::assertSame('Сбербанк', $result->items[0]->name);
        self::assertSame(100, $result->items[0]->sort);
        self::assertSame(2, $result->items[1]->id);
        self::assertSame('tinkoff', $result->items[1]->code);
        self::assertSame('Тинькофф', $result->items[1]->name);
        self::assertSame(200, $result->items[1]->sort);
    }
}
