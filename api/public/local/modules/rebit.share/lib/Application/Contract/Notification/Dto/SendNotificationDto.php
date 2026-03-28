<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Notification\Dto;

use Rebit\Notification\Application\Notification\Message\SendNotificationMessage;

/**
 * Межмодульный DTO для публикации уведомлений.
 *
 * @see SendNotificationMessage
 */
final readonly class SendNotificationDto
{
    /**
     * @param string                     $type    тип уведомления (значение NotificationTypeEnum)
     * @param int                        $userId  ID пользователя-получателя
     * @param array<string, null|scalar> $payload данные, специфичные для типа уведомления
     */
    public function __construct(
        public string $type,
        public int $userId,
        public array $payload = [],
    ) {}
}
