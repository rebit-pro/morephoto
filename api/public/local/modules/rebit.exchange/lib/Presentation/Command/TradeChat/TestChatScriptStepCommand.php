<?php

declare(strict_types=1);

namespace Rebit\Exchange\Presentation\Command\TradeChat;

use Rebit\Exchange\Application\TradeChat\Message\ExecuteChatScriptStepMessage;
use Rebit\Exchange\Application\TradeChat\Port\ChatScriptStepPublisherInterface;
use Rebit\Share\Presentation\Command\RebitCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:exchange:chat-script:test',
    description: 'Опубликовать тестовое сообщение в очередь chatScriptStep',
)]
final class TestChatScriptStepCommand extends RebitCommand
{
    public function __construct(
        private readonly ChatScriptStepPublisherInterface $publisher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('execution-id', null, InputOption::VALUE_REQUIRED, 'ID исполнения сценария', '1')
            ->addOption('trade-id', null, InputOption::VALUE_REQUIRED, 'ID сделки', '1')
            ->addOption('step-id', null, InputOption::VALUE_REQUIRED, 'ID шага', '1')
        ;
    }

    protected function handle(SymfonyStyle $io, InputInterface $input): int
    {
        $executionId = (int)$input->getOption('execution-id');
        $tradeId = (int)$input->getOption('trade-id');
        $stepId = (int)$input->getOption('step-id');

        $this->publisher->dispatch(
            new ExecuteChatScriptStepMessage(
                executionId: $executionId,
                tradeId: $tradeId,
                stepId: $stepId,
            ),
        );

        $io->success(sprintf(
            'Сообщение ExecuteChatScriptStepMessage опубликовано в очередь chatScriptStep (executionId=%d, tradeId=%d, stepId=%d)',
            $executionId,
            $tradeId,
            $stepId,
        ));

        return Command::SUCCESS;
    }
}
