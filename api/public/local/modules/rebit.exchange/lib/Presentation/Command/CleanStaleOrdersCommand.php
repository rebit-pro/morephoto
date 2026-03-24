<?php

declare(strict_types=1);

namespace Rebit\Exchange\Presentation\Command;

use Rebit\Exchange\Application\OrderBook\UseCase\CleanStaleOrdersUseCase;
use Rebit\Share\Presentation\Command\Attribute\WithLock;
use Rebit\Share\Presentation\Command\RebitCommand;
use Rebit\Share\Shared\Exception\RepositoryException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Удаление записей стакана старше 5 минут из локальной БД.
 *
 * Запуск: php bitrix/bitrix.php app:exchange:clean-stale-orders
 */
#[AsCommand(
    name: 'app:exchange:clean-stale-orders',
    description: 'Удаление устаревших записей стакана (старше 5 мин)',
)]
#[WithLock]
final class CleanStaleOrdersCommand extends RebitCommand
{
    public function __construct(
        private readonly CleanStaleOrdersUseCase $cleanStaleOrdersUseCase,
    ) {
        parent::__construct();
    }

    /**
     * @throws RepositoryException
     */
    protected function handle(SymfonyStyle $io, InputInterface $input): int
    {
        $deleted = $this->cleanStaleOrdersUseCase->execute();

        $io->success(sprintf('Удалено устаревших записей: %d', $deleted));

        return Command::SUCCESS;
    }
}
