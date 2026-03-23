<?php

declare(strict_types=1);

namespace Rebit\Wallet\Infrastructure\Bridge;

use Bitrix\Main\DI\ServiceLocator;
use Rebit\Wallet\Presentation\Command\SyncBalancesCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

/**
 * Мост для запуска синхронизации балансов из Bitrix-окружения (cron, агент и т.д.).
 *
 * Делегирует выполнение в SyncBalancesCommand, разрешая зависимости через ServiceLocator.
 */
final class SyncBalancesBridge
{
    public static function run(): void
    {
        /** @var SyncBalancesCommand $command */
        $command = ServiceLocator::getInstance()->get(SyncBalancesCommand::class);
        $command->run(new ArrayInput([]), new NullOutput());
    }
}
