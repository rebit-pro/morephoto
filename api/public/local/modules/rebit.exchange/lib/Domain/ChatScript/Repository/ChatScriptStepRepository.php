<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\ChatScript\Repository;

use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStepCollection;
use Rebit\Exchange\Domain\ChatScript\Entity\Table\ChatScriptStepTable;
use Rebit\Share\Shared\Repository\RepositoryExceptionTrait;
use Rebit\Share\Shared\Exception\RepositoryException;

final class ChatScriptStepRepository
{
    use RepositoryExceptionTrait;

    /**
     * @throws RepositoryException
     */
    public function findById(int $id): ?ChatScriptStep
    {
        return $this->query(
            fn(): ?ChatScriptStep => ChatScriptStepTable::query()
                ->setSelect(['*'])
                ->where('ID', $id)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findByScriptId(int $scriptId): ChatScriptStepCollection
    {
        return $this->query(
            fn(): ChatScriptStepCollection => ChatScriptStepTable::query()
                ->setSelect(['*'])
                ->where('UF_SCRIPT_ID', $scriptId)
                ->setOrder(['UF_SORT' => 'ASC'])
                ->exec()
                ->fetchCollection(),
        );
    }

    /**
     * Удаляет все шаги скрипта и создаёт новые.
     *
     * @param array<int, array{
     *     sort: int,
     *     message: string,
     *     delaySeconds: int,
     * }> $steps
     *
     * @throws RepositoryException
     */
    public function replaceSteps(int $scriptId, array $steps): ChatScriptStepCollection
    {
        $this->deleteByScriptId($scriptId);

        $collection = new ChatScriptStepCollection();

        foreach ($steps as $stepData) {
            /** @var ChatScriptStep $step */
            $step = ChatScriptStepTable::createObject()
                ->setUfScriptId($scriptId)
                ->setUfSort($stepData['sort'])
                ->setUfMessage($stepData['message'])
                ->setUfDelaySeconds($stepData['delaySeconds'])
            ;

            $this->persist($step);
            $collection[] = $step;
        }

        return $collection;
    }

    /**
     * @throws RepositoryException
     */
    public function deleteByScriptId(int $scriptId): void
    {
        $existing = $this->findByScriptId($scriptId);

        foreach ($existing as $step) {
            $this->query(
                static function() use ($step): void {
                    ChatScriptStepTable::delete($step->getId());
                },
            );
        }
    }
}
