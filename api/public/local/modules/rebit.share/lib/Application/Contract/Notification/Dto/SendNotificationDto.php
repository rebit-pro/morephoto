<?php

declare(strict_types=1);

namespace Rebit\Share\Application\Contract\Notification\Dto;

/**
 * Межмодульный DTO для публикации уведомлений.
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
