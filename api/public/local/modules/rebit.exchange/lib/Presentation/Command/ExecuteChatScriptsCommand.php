<?php

declare(strict_types=1);

namespace Rebit\Exchange\Presentation\Command;

use Rebit\Exchange\Application\TradeChat\UseCase\ProcessPendingChatScriptsUseCase;
use Rebit\Share\Presentation\Command\Attribute\WithLock;
use Rebit\Share\Presentation\Command\RebitCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Отправка отложенных шагов чат-скриптов через Bybit API.
 *
 * Запуск: php bitrix/bitrix.php app:exchange:execute-chat-scripts
 */
#[AsCommand(
    name: 'app:exchange:execute-chat-scripts',
    description: 'Отправка отложенных шагов чат-скриптов',
)]
#[WithLock]
final class ExecuteChatScriptsCommand extends RebitCommand
{
    public function __construct(
        private readonly ProcessPendingChatScriptsUseCase $processPendingChatScriptsUseCase,
    ) {
        parent::__construct();
    }

    protected function handle(SymfonyStyle $io, InputInterface $input): int
    {
        [$sent, $completed, $errors] = $this->processPendingChatScriptsUseCase->execute();

        if (0 === $sent && 0 === $completed && 0 === $errors) {
            return Command::SUCCESS;
        }

        $io->table(
            ['', 'Значение'],
            [
                ['Отправлено шагов', (string)$sent],
                ['Завершено скриптов', (string)$completed],
                ['Ошибок', (string)$errors],
            ],
        );

        return 0 === $errors ? Command::SUCCESS : Command::FAILURE;
    }
}
