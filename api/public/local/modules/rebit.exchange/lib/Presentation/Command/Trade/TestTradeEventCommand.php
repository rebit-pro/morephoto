<?php

declare(strict_types=1);

namespace Rebit\Exchange\Presentation\Command\Trade;

use Rebit\Exchange\Application\Trade\Message\TradeDiscoveredMessage;
use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;
use Rebit\Share\Presentation\Command\RebitCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:exchange:trade-event:test',
    description: 'Опубликовать тестовое сообщение в очередь tradeEvent',
)]
final class TestTradeEventCommand extends RebitCommand
{
    public function __construct(
        private readonly MessagePublisherInterface $publisher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('trade-id', null, InputOption::VALUE_REQUIRED, 'ID сделки', '1')
            ->addOption('bybit-order-id', null, InputOption::VALUE_REQUIRED, 'Bybit order ID', 'debug-trade-order-1')
            ->addOption('fiat-amount', null, InputOption::VALUE_REQUIRED, 'Сумма сделки в фиате', '0')
        ;
    }

    protected function handle(SymfonyStyle $io, InputInterface $input): int
    {
        $tradeId = (int)$input->getOption('trade-id');
        $bybitOrderId = (string)$input->getOption('bybit-order-id');
        $fiatAmount = (string)$input->getOption('fiat-amount');

        $this->publisher->dispatch(
            new TradeDiscoveredMessage(
                tradeId: $tradeId,
                bybitOrderId: $bybitOrderId,
                fiatAmount: $fiatAmount,
            ),
        );

        $io->success(sprintf(
            'Сообщение TradeDiscoveredMessage опубликовано в очередь tradeEvent (tradeId=%d, bybitOrderId=%s)',
            $tradeId,
            $bybitOrderId,
        ));

        return Command::SUCCESS;
    }
}
