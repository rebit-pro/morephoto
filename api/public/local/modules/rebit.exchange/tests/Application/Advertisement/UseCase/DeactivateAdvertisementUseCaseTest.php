<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\Advertisement\UseCase;

use PHPUnit\Framework\TestCase;
use Rebit\Exchange\Application\Advertisement\Port\BybitAdvertisementGatewayInterface;
use Rebit\Exchange\Application\Advertisement\UseCase\DeactivateAdvertisementUseCase;
use Rebit\Exchange\Domain\Advertisement\Entity\Advertisement;
use Rebit\Exchange\Domain\Advertisement\Repository\AdvertisementRepository;
use Rebit\Share\Shared\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * @internal
 */
final class DeactivateAdvertisementUseCaseTest extends TestCase
{
    private const int USER_ID = 42;

    public function testSuccessfulDeactivation(): void
    {
        $ad = $this->createMock(Advertisement::class);
        $ad->method('getUfUserId')->willReturn(self::USER_ID);
        $ad->method('getUfBybitAdId')->willReturn('bybit-ad-100');

        $ad
            ->expects($this->once())
            ->method('setUfStatus')
            ->with('cancelled')
        ;

        $repo = $this->createMock(AdvertisementRepository::class);
        $repo
            ->expects($this->once())
            ->method('findById')
            ->with(10)
            ->willReturn($ad)
        ;
        $repo
            ->expects($this->once())
            ->method('save')
            ->with($ad)
        ;

        $gateway = $this->createMock(BybitAdvertisementGatewayInterface::class);
        $gateway
            ->expects($this->once())
            ->method('cancel')
            ->with(self::USER_ID, 'bybit-ad-100')
        ;

        (new DeactivateAdvertisementUseCase($repo, $gateway))->execute(10, self::USER_ID);
    }

    public function testDeactivationSkipsBybitCancelWhenNoBybitAdId(): void
    {
        $ad = $this->createMock(Advertisement::class);
        $ad->method('getUfUserId')->willReturn(self::USER_ID);
        $ad->method('getUfBybitAdId')->willReturn('');

        $ad->expects($this->once())->method('setUfStatus')->with('cancelled');

        $repo = $this->createStub(AdvertisementRepository::class);
        $repo->method('findById')->willReturn($ad);

        $gateway = $this->createMock(BybitAdvertisementGatewayInterface::class);
        $gateway
            ->expects($this->never())
            ->method('cancel')
        ;

        (new DeactivateAdvertisementUseCase($repo, $gateway))->execute(10, self::USER_ID);
    }

    public function testAdvertisementNotFoundThrows404(): void
    {
        $repo = $this->createStub(AdvertisementRepository::class);
        $repo->method('findById')->willReturn(null);

        $gateway = $this->createStub(BybitAdvertisementGatewayInterface::class);

        $this->expectException(EntityNotFoundException::class);
        $this->expectExceptionMessage('Объявление не найдено');

        (new DeactivateAdvertisementUseCase($repo, $gateway))->execute(10, self::USER_ID);
    }

    public function testAccessDeniedThrows403(): void
    {
        $ad = $this->createStub(Advertisement::class);
        $ad->method('getUfUserId')->willReturn(999);

        $repo = $this->createStub(AdvertisementRepository::class);
        $repo->method('findById')->willReturn($ad);

        $gateway = $this->createStub(BybitAdvertisementGatewayInterface::class);

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(403);

        (new DeactivateAdvertisementUseCase($repo, $gateway))->execute(10, self::USER_ID);
    }
}
