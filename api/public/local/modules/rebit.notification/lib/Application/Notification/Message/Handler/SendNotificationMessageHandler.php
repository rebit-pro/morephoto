<?php

declare(strict_types=1);

namespace Rebit\Notification\Application\Notification\Message\Handler;

use Psr\Log\LoggerInterface;
use Rebit\Notification\Application\Notification\Message\SendNotificationMessage;
use Rebit\Notification\Application\Notification\Port\NotificationChannelInterface;
use Rebit\Notification\Application\Notification\Port\UserEmailResolverInterface;
use Rebit\Notification\Domain\Notification\Enum\NotificationTypeEnum;

/**
 * Handler очереди notification.
 *
 * Получает SendNotificationMessage, определяет тип, обогащает payload (email)
 * и рассылает по всем каналам, которые поддерживают данный тип уведомления.
 */
final readonly class SendNotificationMessageHandler
{
    /**
     * @param list<NotificationChannelInterface> $channels
     */
    public function __construct(
        private array $channels,
        private UserEmailResolverInterface $userEmailResolver,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(SendNotificationMessage $message): void
    {
        $type = NotificationTypeEnum::tryFrom($message->type);

        if (null === $type) {
            $this->logger->warning('Неизвестный тип уведомления', [
                'type' => $message->type,
                'userId' => $message->userId,
            ]);

            return;
        }

        $payload = $this->enrichPayload($message->userId, $message->payload);
        $sent = false;

        foreach ($this->channels as $channel) {
            if (!$channel->supports($type)) {
                continue;
            }

            try {
                $channel->send($type, $message->userId, $payload);
                $sent = true;

                $this->logger->info('Уведомление отправлено', [
                    'type' => $type->value,
                    'userId' => $message->userId,
                    'channel' => $channel::class,
                ]);
            } catch (\Throwable $e) {
                $this->logger->error('Ошибка отправки уведомления', [
                    'type' => $type->value,
                    'userId' => $message->userId,
                    'channel' => $channel::class,
                    'error' => $e->getMessage(),
                ]);

                throw $e;
            }
        }

        if (!$sent) {
            $this->logger->warning('Нет каналов для типа уведомления', [
                'type' => $type->value,
                'userId' => $message->userId,
            ]);
        }
    }

    /**
     * Обогащает payload email'ом пользователя, если он не передан в сообщении.
     *
     * @param array<string, null|scalar> $payload
     *
     * @return array<string, null|scalar>
     */
    private function enrichPayload(int $userId, array $payload): array
    {
        if (!isset($payload['email']) || '' === (string)$payload['email']) {
            $payload['email'] = $this->userEmailResolver->resolve($userId);
        }

        return $payload;
    }
}
