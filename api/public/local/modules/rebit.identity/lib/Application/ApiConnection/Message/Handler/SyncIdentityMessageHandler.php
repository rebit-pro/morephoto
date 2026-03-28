<?php

declare(strict_types=1);

namespace Rebit\Identity\Application\ApiConnection\Message\Handler;

use Psr\Log\LoggerInterface;
use Rebit\Identity\Application\ApiConnection\Message\SyncIdentityMessage;
use Rebit\Identity\Application\ApiConnection\UseCase\VerifyApiUseCase;
use Rebit\Share\Shared\Exception\HttpException;

final readonly class SyncIdentityMessageHandler
{
    public function __construct(
        private VerifyApiUseCase $verifyApiUseCase,
        private LoggerInterface $logger,
    ) {}

    public function __invoke(SyncIdentityMessage $message): void
    {
        try {
            $result = $this->verifyApiUseCase->execute($message->userId);
        } catch (HttpException $exception) {
            if (404 === $exception->getCode()) {
                $this->logger->warning('SyncIdentityMessage пропущено: подключение не найдено', [
                    'userId' => $message->userId,
                ]);

                return;
            }

            throw $exception;
        }

        $this->logger->info('SyncIdentityMessage получено', [
            'userId' => $message->userId,
            'status' => $result->status?->value,
            'verifiedAt' => $result->verifiedAt,
        ]);
    }
}
