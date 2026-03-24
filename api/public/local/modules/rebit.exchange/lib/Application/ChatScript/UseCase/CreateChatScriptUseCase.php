<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\ChatScript\UseCase;

use Rebit\Exchange\Application\ChatScript\Dto\Request\CreateChatScriptRequestDto;
use Rebit\Exchange\Application\ChatScript\Dto\Result\ChatScriptResultDto;
use Rebit\Exchange\Application\ChatScript\Dto\Result\ChatScriptStepResultDto;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Share\Infrastructure\Exception\ValidationHttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class CreateChatScriptUseCase
{
    public function __construct(
        private ChatScriptRepository $scriptRepository,
        private ChatScriptStepRepository $stepRepository,
    ) {}

    /**
     * @throws ValidationHttpException
     * @throws RepositoryException
     */
    public function execute(CreateChatScriptRequestDto $dto, int $userId): ChatScriptResultDto
    {
        if ('' === trim($dto->name)) {
            throw new ValidationHttpException('Название скрипта не может быть пустым');
        }

        if ([] === $dto->steps) {
            throw new ValidationHttpException('Скрипт должен содержать хотя бы один шаг');
        }

        $script = $this->scriptRepository->create(
            userId: $userId,
            name: $dto->name,
            isActive: $dto->isActive,
        );

        $steps = $this->stepRepository->replaceSteps($script->getId(), $dto->steps);

        $stepDtos = [];
        foreach ($steps as $step) {
            $stepDtos[] = new ChatScriptStepResultDto(
                id: $step->getId(),
                sort: $step->getUfSort(),
                message: $step->getUfMessage(),
                delaySeconds: $step->getUfDelaySeconds(),
            );
        }

        return new ChatScriptResultDto(
            id: $script->getId(),
            name: $script->getUfName(),
            isActive: (bool)$script->getUfIsActive(),
            createdAt: $script->getUfCreatedAt()?->format('c'),
            updatedAt: $script->getUfUpdatedAt()?->format('c'),
            steps: $stepDtos,
        );
    }
}
