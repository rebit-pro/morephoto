<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\ChatScript\UseCase;

use Rebit\Exchange\Domain\Advertisement\Repository\AdvertisementRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class DeleteChatScriptUseCase
{
    public function __construct(
        private ChatScriptRepository $scriptRepository,
        private ChatScriptStepRepository $stepRepository,
        private AdvertisementRepository $advertisementRepository,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(int $scriptId, int $userId): void
    {
        $script = $this->scriptRepository->findById($scriptId);

        if (null === $script) {
            throw new EntityNotFoundException('Скрипт не найден');
        }

        if ($script->getUfUserId() !== $userId) {
            throw new HttpException('Нет доступа к этому скрипту', 403);
        }

        $this->advertisementRepository->clearChatScriptId($scriptId);

        $this->stepRepository->deleteByScriptId($scriptId);
        $this->scriptRepository->delete($scriptId);
    }
}
