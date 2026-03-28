<?php

declare(strict_types=1);

namespace Rebit\Exchange\Presentation\Command;

use Psr\Log\LoggerInterface;
use Rebit\Exchange\Application\Trade\Message\TradeDiscoveredMessage;
use Rebit\Exchange\Application\Trade\Message\TradeStatusChangedMessage;
use Rebit\Exchange\Application\Trade\Port\BybitTradeGatewayInterface;
use Rebit\Exchange\Domain\Trade\Enum\TradeStatusEnum;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;
use Rebit\Share\Presentation\Command\Attribute\WithLock;
use Rebit\Share\Presentation\Command\RebitCommand;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;
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
        private readonly MessagePublisherInterface $tradeEventPublisher,
        private readonly LoggerInterface $logger,
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
     *
     * @throws HttpException|RepositoryException
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
                $side = (0 === (int)($item['side'] ?? 0)) ? 'buy' : 'sell';
                $buyerUserId = 'buy' === $side ? $userId : 0;
                $sellerUserId = 'sell' === $side ? $userId : 0;
                $fiatAmount = (float)($item['amount'] ?? 0);
                $counterpartyName = (string)($item['targetNickName'] ?? '');

                $trade = $this->tradeRepository->createFromBybit([
                    'bybitOrderId' => $bybitOrderId,
                    'bybitStatus' => $bybitStatus,
                    'buyerUserId' => $buyerUserId,
                    'sellerUserId' => $sellerUserId,
                    'side' => $side,
                    'price' => (float)($item['price'] ?? 0),
                    'quantity' => 0.0,
                    'fiatAmount' => $fiatAmount,
                    'fee' => (float)($item['fee'] ?? 0),
                    'status' => TradeStatusEnum::fromBybit($bybitStatus)->value,
                    'counterpartyName' => $counterpartyName,
                ]);

                try {
                    $this->tradeEventPublisher->dispatch(
                        new TradeDiscoveredMessage(
                            tradeId: $trade->getId(),
                            bybitOrderId: $bybitOrderId,
                        ),
                    );
                } catch (\Throwable $exception) {
                    $this->logger->error(
                        'Не удалось опубликовать событие новой сделки',
                        [
                            'userId' => $userId,
                            'tradeId' => $trade->getId(),
                            'bybitOrderId' => $bybitOrderId,
                            'error' => $exception->getMessage(),
                            'exceptionClass' => $exception::class,
                        ],
                    );
                }

                ++$newCount;
            } else {
                $bybitStatus = (int)($item['status'] ?? 0);
                if ($bybitStatus !== $existing->getUfBybitStatus()) {
                    $oldStatus = (string)$existing->getUfStatus();
                    $newStatus = TradeStatusEnum::fromBybit($bybitStatus)->value;

                    $existing->setUfBybitStatus($bybitStatus);
                    $existing->setUfStatus($newStatus);
                    $this->tradeRepository->save($existing);

                    try {
                        $this->tradeEventPublisher->dispatch(
                            new TradeStatusChangedMessage(
                                tradeId: $existing->getId(),
                                oldStatus: $oldStatus,
                                newStatus: $newStatus,
                            ),
                        );
                    } catch (\Throwable $exception) {
                        $this->logger->error(
                            'Не удалось опубликовать событие смены статуса сделки',
                            [
                                'userId' => $userId,
                                'tradeId' => $existing->getId(),
                                'bybitOrderId' => $bybitOrderId,
                                'oldStatus' => $oldStatus,
                                'newStatus' => $newStatus,
                                'error' => $exception->getMessage(),
                                'exceptionClass' => $exception::class,
                            ],
                        );
                    }

                    ++$updatedCount;
                }
            }
        }

        return [$newCount, $updatedCount];
    }
}
