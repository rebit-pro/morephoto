<?php

declare(strict_types=1);

namespace Rebit\Wallet\Presentation\Command;

use Psr\Log\LoggerInterface;
use Rebit\Share\Application\Contract\Bybit\BybitConnectionResolverInterface;
use Rebit\Wallet\Application\Balance\UseCase\SyncBalancesUseCase;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
final class SyncBalancesCommand extends Command
{
    public function __construct(
        private readonly SyncBalancesUseCase $syncBalancesUseCase,
        private readonly BybitConnectionResolverInterface $connectionResolver,
        private readonly LoggerInterface $logger,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Синхронизация балансов с Bybit');

        $this->logger->info('Запуск синхронизации балансов через SyncBalancesCommand');

        try {
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
                    $this->logger->warning('SyncBalancesCommand: ошибка для userId=' . $userId, [
                        'error' => $e->getMessage(),
                    ]);
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

            $this->logger->info(sprintf(
                'Синхронизация балансов завершена за %.1f сек. Успешно: %d, ошибок: %d',
                $elapsed,
                $successCount,
                $errorCount,
            ));

            return 0 === $errorCount ? Command::SUCCESS : Command::FAILURE;
        } catch (\Throwable $e) {
            $this->logger->error('SyncBalancesCommand: критическая ошибка', [
                'error' => $e->getMessage(),
            ]);
            $io->error('Критическая ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
