<?php

declare(strict_types=1);

namespace Rebit\Wallet\Presentation\Command;

use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Share\Presentation\Command\RebitCommand;
use Rebit\Wallet\Application\Balance\UseCase\SyncBalancesUseCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Синхронизация балансов всех пользователей с активным подключением к Bybit.
 *
 * Замена устаревшего SyncBalancesAgent.
 * Запуск: php bitrix/bitrix.php app:wallet:sync-balances
 */
#[AsCommand(
    name: 'app:wallet:sync-balances',
    description: 'Синхронизация балансов пользователей с Bybit',
)]
final class SyncBalancesCommand extends RebitCommand
{
    public function __construct(
        private readonly SyncBalancesUseCase $syncBalancesUseCase,
        private readonly BybitConnectionResolverInterface $connectionResolver,
    ) {
        parent::__construct();
    }

    protected function handle(SymfonyStyle $io, InputInterface $input): int
    {
        $io->title('Синхронизация балансов с Bybit');

        $start = microtime(true);

        $activeUserIds = $this->connectionResolver->getActiveUserIds();

        if ([] === $activeUserIds) {
            $io->warning('Нет активных подключений для синхронизации');

            return Command::SUCCESS;
        }

        $io->text(sprintf('Найдено подключений: %d', count($activeUserIds)));

        $successCount = 0;
        $errorCount = 0;

        foreach ($activeUserIds as $userId) {
            try {
                $this->syncBalancesUseCase->execute($userId);
                ++$successCount;
            } catch (\Throwable $e) {
                ++$errorCount;
                $io->warning(sprintf('userId=%d: %s', $userId, $e->getMessage()));
            }
        }

        $elapsed = microtime(true) - $start;

        $io->newLine();
        $io->table(
            ['', 'Значение'],
            [
                ['Всего подключений', (string)count($activeUserIds)],
                ['Успешно', (string)$successCount],
                ['Ошибок', (string)$errorCount],
                ['Время, сек', sprintf('%.1f', $elapsed)],
            ],
        );

        return 0 === $errorCount ? Command::SUCCESS : Command::FAILURE;
    }
}
