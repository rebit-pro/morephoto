<?php

declare(strict_types=1);

namespace Rebit\Exchange\Presentation\Command;

use Rebit\Exchange\Application\Trade\UseCase\SyncTradeHistoryUseCase;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Presentation\Command\Attribute\WithLock;
use Rebit\Share\Presentation\Command\RebitCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Дозагрузка завершённых P2P-сделок из Bybit.
 *
 * Запуск: php bitrix/bitrix.php app:exchange:sync-trade-history
 */
#[AsCommand(
    name: 'app:exchange:sync-trade-history',
    description: 'Дозагрузка завершённых P2P-сделок из Bybit',
)]
#[WithLock]
final class SyncTradeHistoryCommand extends RebitCommand
{
    public function __construct(
        private readonly SyncTradeHistoryUseCase $syncTradeHistoryUseCase,
        private readonly BybitConnectionResolverInterface $connectionResolver,
    ) {
        parent::__construct();
    }

    protected function handle(SymfonyStyle $io, InputInterface $input): int
    {
        $io->title('Дозагрузка истории P2P-сделок из Bybit');

        $activeUserIds = $this->connectionResolver->getActiveUserIds();

        if ([] === $activeUserIds) {
            $io->warning('Нет активных подключений');

            return Command::SUCCESS;
        }

        $io->text(sprintf('Найдено подключений: %d', count($activeUserIds)));

        $totalNew = 0;
        $totalUpdated = 0;
        $errorCount = 0;

        foreach ($activeUserIds as $userId) {
            try {
                [$new, $updated] = $this->syncTradeHistoryUseCase->execute($userId);
                $totalNew += $new;
                $totalUpdated += $updated;
            } catch (\Throwable $e) {
                ++$errorCount;
                $io->warning(sprintf('userId=%d: %s', $userId, $e->getMessage()));
            }
        }

        $io->table(
            ['', 'Значение'],
            [
                ['Новых сделок', (string)$totalNew],
                ['Обновлено статусов', (string)$totalUpdated],
                ['Ошибок', (string)$errorCount],
            ],
        );

        return 0 === $errorCount ? Command::SUCCESS : Command::FAILURE;
    }
}
