<?php

declare(strict_types=1);

namespace Rebit\Exchange\Presentation\Command;

use Rebit\Exchange\Application\OrderBook\UseCase\SyncOrderBookUseCase;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Presentation\Command\Attribute\WithLock;
use Rebit\Share\Presentation\Command\RebitCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Синхронизация стакана ордеров с Bybit для всех активных подключений.
 */
#[AsCommand(
    name: 'app:exchange:sync-order-book',
    description: 'Синхронизация стакана P2P-ордеров с Bybit',
)]
#[WithLock]
final class SyncOrderBookCommand extends RebitCommand
{
    public function __construct(
        private readonly SyncOrderBookUseCase $syncOrderBookUseCase,
        private readonly BybitConnectionResolverInterface $connectionResolver,
    ) {
        parent::__construct();
    }

    protected function handle(SymfonyStyle $io, InputInterface $input): int
    {
        $io->title('Синхронизация стакана P2P с Bybit');

        $activeUserIds = $this->connectionResolver->getActiveUserIds();

        if ([] === $activeUserIds) {
            $io->warning('Нет активных подключений');

            return Command::SUCCESS;
        }

        $io->text(sprintf('Найдено подключений: %d', count($activeUserIds)));

        $successCount = 0;
        $errorCount = 0;

        foreach ($activeUserIds as $userId) {
            try {
                $this->syncOrderBookUseCase->execute($userId);
                ++$successCount;
            } catch (\Throwable $e) {
                ++$errorCount;
                $io->warning(sprintf('userId=%d: %s', $userId, $e->getMessage()));
            }
        }

        $io->table(
            ['', 'Значение'],
            [
                ['Успешно', (string)$successCount],
                ['Ошибок', (string)$errorCount],
            ],
        );

        return 0 === $errorCount ? Command::SUCCESS : Command::FAILURE;
    }
}
