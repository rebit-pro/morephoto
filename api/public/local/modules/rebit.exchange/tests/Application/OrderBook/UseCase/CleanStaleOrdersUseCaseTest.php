<?php

declare(strict_types=1);

namespace Rebit\Exchange\Tests\Application\OrderBook\UseCase;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Rebit\Exchange\Application\OrderBook\UseCase\CleanStaleOrdersUseCase;
use Rebit\Exchange\Domain\OrderBook\Repository\OrderBookRepository;

/**
 * @internal
 */
final class CleanStaleOrdersUseCaseTest extends TestCase
{
    public function testDeletesStaleEntriesAndReturnsCount(): void
    {
        $repo = $this->createMock(OrderBookRepository::class);
        $repo
            ->expects($this->once())
            ->method('deleteStale')
            ->with(5)
            ->willReturn(12)
        ;

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('info')
            ->with('Cleaned stale order book entries', ['deleted' => 12, 'staleMinutes' => 5])
        ;

        $useCase = new CleanStaleOrdersUseCase($repo, $logger);

        self::assertSame(12, $useCase->execute());
    }

    public function testNoLogWhenNothingDeleted(): void
    {
        $repo = $this->createStub(OrderBookRepository::class);
        $repo->method('deleteStale')->willReturn(0);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('info');

        $useCase = new CleanStaleOrdersUseCase($repo, $logger);

        self::assertSame(0, $useCase->execute());
    }

    public function testCustomStaleMinutes(): void
    {
        $repo = $this->createMock(OrderBookRepository::class);
        $repo
            ->expects($this->once())
            ->method('deleteStale')
            ->with(10)
            ->willReturn(3)
        ;

        $logger = $this->createStub(LoggerInterface::class);

        $useCase = new CleanStaleOrdersUseCase($repo, $logger);

        self::assertSame(3, $useCase->execute(10));
    }
}
