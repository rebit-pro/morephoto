<?php

declare(strict_types=1);

namespace Rebit\Wallet\Presentation\Command\Balance;

use Rebit\Share\Application\Contract\Wallet\BalanceSyncPublisherInterface;
use Rebit\Share\Application\Contract\Wallet\Message\SyncBalanceMessage;
use Rebit\Share\Presentation\Command\RebitCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:wallet:balance-sync:test',
    description: 'Опубликовать тестовое сообщение в очередь balanceSync',
)]
final class TestBalanceSyncCommand extends RebitCommand
{
    public function __construct(
        private readonly BalanceSyncPublisherInterface $publisher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('user-id', 'u', InputOption::VALUE_REQUIRED, 'ID пользователя', '1')
            ->addOption('currency', 'c', InputOption::VALUE_REQUIRED, 'Код валюты', '')
        ;
    }

    protected function handle(SymfonyStyle $io, InputInterface $input): int
    {
        $userId = (int)$input->getOption('user-id');
        $currency = (string)$input->getOption('currency');

        $this->publisher->dispatch(
            new SyncBalanceMessage(
                userId: $userId,
                currency: '' === $currency ? null : $currency,
            ),
        );

        $io->success(sprintf(
            'Сообщение SyncBalanceMessage опубликовано в очередь balanceSync (userId=%d%s)',
            $userId,
            '' === $currency ? '' : ', currency=' . $currency,
        ));

        return Command::SUCCESS;
    }
}
