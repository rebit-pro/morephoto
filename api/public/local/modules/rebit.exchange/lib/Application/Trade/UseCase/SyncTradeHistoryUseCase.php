<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\UseCase;

use Rebit\Exchange\Application\Trade\Dto\Bybit\BybitTradeOrderSummaryDto;
use Psr\Log\LoggerInterface;
use Rebit\Exchange\Application\Trade\Port\BybitTradeGatewayInterface;
use Rebit\Exchange\Domain\Trade\Enum\TradeStatusEnum;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Дозагрузка завершённых сделок из Bybit (POST /v5/p2p/order/simplifyList).
 *
 * Пагинирует все ордера пользователя, создаёт отсутствующие в локальной БД
 * и обновляет статусы существующих.
 */
final readonly class SyncTradeHistoryUseCase
{
    public function __construct(
        private TradeRepository $tradeRepository,
        private BybitTradeGatewayInterface $bybitGateway,
        private LoggerInterface $logger,
    ) {}

    /**
     * @return array{int, int} [newCount, updatedCount]
     *
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(int $userId): array
    {
        $newCount = 0;
        $updatedCount = 0;
        $page = 1;

        do {
            $result = $this->bybitGateway->fetchAllOrders($userId, $page);
            $items = $result->items;

            foreach ($items as $item) {
                $bybitOrderId = $item->id;
                if ('' === $bybitOrderId) {
                    continue;
                }

                $existing = $this->tradeRepository->findByBybitOrderId($bybitOrderId);

                if (null === $existing) {
                    $this->createTrade($item, $userId);
                    ++$newCount;
                } else {
                    $bybitStatus = $item->status;
                    if ($bybitStatus !== $existing->getUfBybitStatus()) {
                        $existing->setUfBybitStatus($bybitStatus);
                        $existing->setUfStatus(TradeStatusEnum::fromBybit($bybitStatus)->value);
                        $this->tradeRepository->save($existing);
                        ++$updatedCount;
                    }
                }
            }

            ++$page;
        } while ([] !== $items);

        if ($newCount > 0 || $updatedCount > 0) {
            $this->logger->info('Trade history synced', [
                'userId' => $userId,
                'new' => $newCount,
                'updated' => $updatedCount,
            ]);
        }

        return [$newCount, $updatedCount];
    }

    /**
     * @throws RepositoryException
     */
    private function createTrade(BybitTradeOrderSummaryDto $item, int $userId): void
    {
        $bybitStatus = $item->status;
        $side = (0 === $item->side) ? 'buy' : 'sell';

        $this->tradeRepository->createFromBybit([
            'bybitOrderId' => $item->id,
            'bybitStatus' => $bybitStatus,
            'buyerUserId' => 'buy' === $side ? $userId : 0,
            'sellerUserId' => 'sell' === $side ? $userId : 0,
            'side' => $side,
            'price' => (float)$item->price,
            'quantity' => 0.0,
            'fiatAmount' => (float)$item->amount,
            'fee' => (float)$item->fee,
            'status' => TradeStatusEnum::fromBybit($bybitStatus)->value,
            'counterpartyName' => $item->targetNickName,
        ]);
    }
}
