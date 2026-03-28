<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Trade\UseCase;

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
            $items = $result['items'] ?? [];

            foreach ($items as $item) {
                $bybitOrderId = (string)($item['id'] ?? '');
                if ('' === $bybitOrderId) {
                    continue;
                }

                $existing = $this->tradeRepository->findByBybitOrderId($bybitOrderId);

                if (null === $existing) {
                    $this->createTrade($item, $userId);
                    ++$newCount;
                } else {
                    $bybitStatus = (int)($item['status'] ?? 0);
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
     * @param array<string, mixed> $item
     *
     * @throws RepositoryException
     */
    private function createTrade(array $item, int $userId): void
    {
        $bybitStatus = (int)($item['status'] ?? 0);
        $side = (0 === (int)($item['side'] ?? 0)) ? 'buy' : 'sell';

        $this->tradeRepository->createFromBybit([
            'bybitOrderId' => (string)($item['id'] ?? ''),
            'bybitStatus' => $bybitStatus,
            'buyerUserId' => 'buy' === $side ? $userId : 0,
            'sellerUserId' => 'sell' === $side ? $userId : 0,
            'side' => $side,
            'price' => (float)($item['price'] ?? 0),
            'quantity' => 0.0,
            'fiatAmount' => (float)($item['amount'] ?? 0),
            'fee' => (float)($item['fee'] ?? 0),
            'status' => TradeStatusEnum::fromBybit($bybitStatus)->value,
            'counterpartyName' => (string)($item['targetNickName'] ?? ''),
        ]);
    }
}
