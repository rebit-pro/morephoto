<?php

declare(strict_types=1);

namespace Rebit\Notification\Application\Notification\UseCase;

use Rebit\Share\Infrastructure\Messenger\AmqpConnectionFactory;
use Rebit\Share\Infrastructure\Messenger\ConsumerRunnerInterface;
use Rebit\Share\Shared\Enum\MessengerQueueEnum;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * UseCase запуска consumer'а очереди notification.
 */
final readonly class ConsumeNotificationsUseCase
{
    public function __construct(
        private ConsumerRunnerInterface $consumerRunner,
        private AmqpConnectionFactory $amqpConnectionFactory,
        private MessageBusInterface $bus,
    ) {}

    public function execute(int $limit, int $timeLimit): void
    {
        $queue = MessengerQueueEnum::NOTIFICATION;
        $transport = $this->amqpConnectionFactory->create($queue);

        $this->consumerRunner->run(
            transport: $transport,
            queueName: $queue->value,
            bus: $this->bus,
            limit: $limit,
            timeLimit: $timeLimit,
        );
    }
}
