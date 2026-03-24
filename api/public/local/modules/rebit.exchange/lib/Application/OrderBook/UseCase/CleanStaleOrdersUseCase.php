<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\OrderBook\UseCase;

use Psr\Log\LoggerInterface;
use Rebit\Exchange\Domain\OrderBook\Repository\OrderBookRepository;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Удаление устаревших записей стакана (старше N минут).
 */
final readonly class CleanStaleOrdersUseCase
{
    private const int DEFAULT_STALE_MINUTES = 5;

    public function __construct(
        private OrderBookRepository $orderBookRepository,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function execute(int $staleMinutes = self::DEFAULT_STALE_MINUTES): int
    {
        $deleted = $this->orderBookRepository->deleteStale($staleMinutes);

        if ($deleted > 0) {
            $this->logger->info('Cleaned stale order book entries', [
                'deleted' => $deleted,
                'staleMinutes' => $staleMinutes,
            ]);
        }

        return $deleted;
    }
}
