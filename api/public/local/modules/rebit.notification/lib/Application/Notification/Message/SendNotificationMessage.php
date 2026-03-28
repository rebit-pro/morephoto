<?php

declare(strict_types=1);

namespace Rebit\Notification\Application\Notification\Message;

use Rebit\Share\Application\Contract\Messenger\AbstractMessage;

/**
 * Сообщение очереди: отправить уведомление пользователю.
 *
 * В payload передаются только скаляры. Handler достаёт остальное из БД при необходимости.
 */
final readonly class SendNotificationMessage extends AbstractMessage
{
    /**
     * @param string                     $type    значение NotificationTypeEnum
     * @param int                        $userId  ID пользователя-получателя
     * @param array<string, null|scalar> $payload данные, специфичные для типа уведомления
     */
    public function __construct(
        public string $type,
        public int $userId,
        public array $payload = [],
    ) {
        parent::__construct();
    }

    public function getDeduplicationKey(): string
    {
        return 'notification_' . $this->type . '_' . $this->userId . '_' . md5(serialize($this->payload));
    }
}
