<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\ChatScript\UseCase;

use Rebit\Exchange\Application\ChatScript\Dto\Result\ChatScriptListResultDto;
use Rebit\Exchange\Application\ChatScript\Dto\Result\ChatScriptResultDto;
use Rebit\Exchange\Application\ChatScript\Dto\Result\ChatScriptStepResultDto;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class ListChatScriptsUseCase
{
    public function __construct(
        private ChatScriptRepository $scriptRepository,
        private ChatScriptStepRepository $stepRepository,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function execute(int $userId): ChatScriptListResultDto
    {
        $scripts = $this->scriptRepository->findByUserId($userId);

        $items = [];
        foreach ($scripts as $script) {
            $steps = $this->stepRepository->findByScriptId($script->getId());

            $stepDtos = [];
            foreach ($steps as $step) {
                $stepDtos[] = new ChatScriptStepResultDto(
                    id: $step->getId(),
                    sort: $step->getUfSort(),
                    message: $step->getUfMessage(),
                    delaySeconds: $step->getUfDelaySeconds(),
                );
            }

            $items[] = new ChatScriptResultDto(
                id: $script->getId(),
                name: $script->getUfName(),
                isActive: (bool)$script->getUfIsActive(),
                createdAt: $script->getUfCreatedAt()?->format('c'),
                updatedAt: $script->getUfUpdatedAt()?->format('c'),
                steps: $stepDtos,
            );
        }

        return new ChatScriptListResultDto($items);
    }
}
