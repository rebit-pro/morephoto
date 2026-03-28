<?php

declare(strict_types=1);

namespace Rebit\Identity\Presentation\Command\ApiConnection;

use Rebit\Identity\Application\ApiConnection\Message\SyncIdentityMessage;
use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;
use Rebit\Share\Presentation\Command\RebitCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:identity:sync:test',
    description: 'Опубликовать тестовое сообщение в очередь identitySync',
)]
final class TestIdentitySyncCommand extends RebitCommand
{
    public function __construct(
        private readonly MessagePublisherInterface $publisher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('user-id', 'u', InputOption::VALUE_REQUIRED, 'ID пользователя', '1');
    }

    protected function handle(SymfonyStyle $io, InputInterface $input): int
    {
        $userId = (int)$input->getOption('user-id');

        $this->publisher->dispatch(
            new SyncIdentityMessage(
                userId: $userId,
            ),
        );

        $io->success(sprintf(
            'Сообщение SyncIdentityMessage опубликовано в очередь identitySync (userId=%d)',
            $userId,
        ));

        return Command::SUCCESS;
    }
}
