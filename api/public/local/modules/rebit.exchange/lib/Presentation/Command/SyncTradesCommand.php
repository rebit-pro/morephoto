<?php

declare(strict_types=1);

namespace Rebit\Exchange\Presentation\Command;

use Rebit\Exchange\Application\Trade\Port\BybitTradeGatewayInterface;
use Rebit\Exchange\Domain\Trade\Enum\TradeStatusEnum;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Presentation\Command\Attribute\WithLock;
use Rebit\Share\Presentation\Command\RebitCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Polling активных ордеров из Bybit. Обнаруживает новые сделки и обновляет статусы.
 */
#[AsCommand(
    name: 'app:exchange:sync-trades',
    description: 'Синхронизация активных P2P-сделок с Bybit',
)]
#[WithLock]
final class SyncTradesCommand extends RebitCommand
{
    public function __construct(
        private readonly TradeRepository $tradeRepository,
        private readonly BybitTradeGatewayInterface $bybitGateway,
        private readonly BybitConnectionResolverInterface $connectionResolver,
    ) {
        parent::__construct();
    }

    protected function handle(SymfonyStyle $io, InputInterface $input): int
    {
        $io->title('Синхронизация P2P-сделок с Bybit');

        $activeUserIds = $this->connectionResolver->getActiveUserIds();

        if ([] === $activeUserIds) {
            $io->warning('Нет активных подключений');

            return Command::SUCCESS;
        }

        $newCount = 0;
        $updatedCount = 0;
        $errorCount = 0;

        foreach ($activeUserIds as $userId) {
            try {
                [$new, $updated] = $this->syncUserTrades($userId);
                $newCount += $new;
                $updatedCount += $updated;
            } catch (\Throwable $e) {
                ++$errorCount;
                $io->warning(sprintf('userId=%d: %s', $userId, $e->getMessage()));
            }
        }

        $io->table(
            ['', 'Значение'],
            [
                ['Новых сделок', (string)$newCount],
                ['Обновлено статусов', (string)$updatedCount],
                ['Ошибок', (string)$errorCount],
            ],
        );

        return 0 === $errorCount ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * @return array{int, int} [newCount, updatedCount]
     */
    private function syncUserTrades(int $userId): array
    {
        $result = $this->bybitGateway->fetchActiveOrders($userId);
        $items = $result['items'] ?? [];

        $newCount = 0;
        $updatedCount = 0;

        foreach ($items as $item) {
            $bybitOrderId = (string)($item['id'] ?? '');
            if ('' === $bybitOrderId) {
                continue;
            }

            $existing = $this->tradeRepository->findByBybitOrderId($bybitOrderId);

            if (null === $existing) {
                $bybitStatus = (int)($item['status'] ?? 0);
                $this->tradeRepository->createFromBybit([
                    'bybitOrderId' => $bybitOrderId,
                    'bybitStatus' => $bybitStatus,
                    'buyerUserId' => $userId,
                    'sellerUserId' => 0,
                    'side' => ((int)($item['side'] ?? 0) === 0) ? 'buy' : 'sell',
                    'price' => (float)($item['price'] ?? 0),
                    'quantity' => 0.0,
                    'fiatAmount' => (float)($item['amount'] ?? 0),
                    'fee' => (float)($item['fee'] ?? 0),
                    'status' => TradeStatusEnum::fromBybit($bybitStatus)->value,
                    'counterpartyName' => (string)($item['targetNickName'] ?? ''),
                ]);
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

        return [$newCount, $updatedCount];
    }
}
