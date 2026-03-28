<?php

declare(strict_types=1);

namespace Rebit\Wallet\Tests\Application\Balance\Message\Handler;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Wallet\Application\Balance\Dto\Result\BalanceListResultDto;
use Rebit\Wallet\Application\Balance\Dto\Result\BalanceResultDto;
use Rebit\Wallet\Application\Balance\Message\Handler\SyncBalanceMessageHandler;
use Rebit\Wallet\Application\Balance\Message\SyncBalanceMessage;
use Rebit\Wallet\Application\Balance\UseCase\SyncBalancesUseCase;

/**
 * @internal
 */
final class SyncBalanceMessageHandlerTest extends TestCase
{
    public function testHandlesSuccessfulSync(): void
    {
        $result = new BalanceListResultDto([
            new BalanceResultDto(
                id: 1,
                userId: 10,
                currencyId: 20,
                currency: 'USDT',
                available: 100.0,
                locked: 0.0,
                total: 100.0,
            ),
        ]);

        $useCase = $this->createStub(SyncBalancesUseCase::class);
        $useCase->method('execute')->willReturn($result);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->exactly(2))->method('info');

        $handler = new SyncBalanceMessageHandler($useCase, $logger);
        $handler(new SyncBalanceMessage(userId: 10, currency: 'USDT'));
    }

    public function testLogsWarningAndReturnsOn400(): void
    {
        $useCase = $this->createStub(SyncBalancesUseCase::class);
        $useCase->method('execute')->willThrowException(new HttpException('No API', 400));

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning');

        $handler = new SyncBalanceMessageHandler($useCase, $logger);
        $handler(new SyncBalanceMessage(userId: 10, currency: 'USDT'));
    }

    public function testRethrowsNon400HttpException(): void
    {
        $useCase = $this->createStub(SyncBalancesUseCase::class);
        $useCase->method('execute')->willThrowException(new HttpException('Boom', 500));

        $logger = $this->createStub(LoggerInterface::class);
        $handler = new SyncBalanceMessageHandler($useCase, $logger);

        $this->expectException(HttpException::class);
        $this->expectExceptionCode(500);

        $handler(new SyncBalanceMessage(userId: 10));
    }
}
