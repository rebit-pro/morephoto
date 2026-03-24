<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\ChatScript\UseCase;

use Rebit\Exchange\Application\ChatScript\Dto\Request\UpdateChatScriptRequestDto;
use Rebit\Exchange\Application\ChatScript\Dto\Result\ChatScriptResultDto;
use Rebit\Exchange\Application\ChatScript\Dto\Result\ChatScriptStepResultDto;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Infrastructure\Exception\ValidationHttpException;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class UpdateChatScriptUseCase
{
    public function __construct(
        private ChatScriptRepository $scriptRepository,
        private ChatScriptStepRepository $stepRepository,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(UpdateChatScriptRequestDto $dto, int $userId): ChatScriptResultDto
    {
        $script = $this->scriptRepository->findById($dto->id);

        if (null === $script) {
            throw new EntityNotFoundException('Скрипт не найден');
        }

        if ($script->getUfUserId() !== $userId) {
            throw new HttpException('Нет доступа к этому скрипту', 403);
        }

        if ('' === trim($dto->name)) {
            throw new ValidationHttpException('Название скрипта не может быть пустым');
        }

        if ([] === $dto->steps) {
            throw new ValidationHttpException('Скрипт должен содержать хотя бы один шаг');
        }

        $script
            ->setUfName($dto->name)
            ->setUfIsActive($dto->isActive ? 1 : 0)
        ;

        $this->scriptRepository->save($script);

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
