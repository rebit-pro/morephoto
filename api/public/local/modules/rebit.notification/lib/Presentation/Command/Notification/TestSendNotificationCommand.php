<?php

declare(strict_types=1);

namespace Rebit\Notification\Presentation\Command\Notification;

use Rebit\Share\Application\Contract\Notification\Dto\SendNotificationDto;
use Rebit\Share\Application\Contract\Notification\NotificationPublisherInterface;
use Rebit\Share\Presentation\Command\RebitCommand;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Dev-команда: публикует тестовое уведомление в очередь.
 *
 * Письмо попадёт в Mailpit (http://localhost:8025).
 */
#[AsCommand(
    name: 'app:notification:test-send',
    description: 'Отправить тестовое уведомление (dev)',
)]
final class TestSendNotificationCommand extends RebitCommand
{
    public function __construct(
        private readonly NotificationPublisherInterface $publisher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('user-id', 'u', InputOption::VALUE_REQUIRED, 'ID пользователя Bitrix', '1')
            ->addOption('email', null, InputOption::VALUE_REQUIRED, 'Email (если не указан — резолвится из user-id)')
        ;
    }

    protected function handle(SymfonyStyle $io, InputInterface $input): int
    {
        $userId = (int)$input->getOption('user-id');
        $email = $input->getOption('email');

        /** @var array<string, null|scalar> $payload */
        $payload = [
            'tradeId' => '999',
            'side' => 'buy',
            'fiatAmount' => '50000.00',
            'counterpartyName' => 'TestUser',
        ];

        if (null !== $email && '' !== $email) {
            $payload['email'] = $email;
        }

        $this->publisher->publish(
            new SendNotificationDto(
                type: 'tradeDiscovered',
                userId: $userId,
                payload: $payload,
            ),
        );

        $io->success(sprintf(
            'Уведомление tradeDiscovered опубликовано в очередь (userId=%d%s)',
            $userId,
            null !== $email ? ', email=' . $email : '',
        ));
        $io->note('Проверьте Mailpit: http://localhost:8025');

        return Command::SUCCESS;
    }
}
