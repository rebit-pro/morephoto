<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\ChatScript\Repository;

use Bitrix\Main\Type\DateTime;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScript;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptCollection;
use Rebit\Exchange\Domain\ChatScript\Entity\Table\ChatScriptTable;
use Rebit\Share\Shared\Repository\RepositoryExceptionTrait;
use Rebit\Share\Shared\Exception\RepositoryException;

final class ChatScriptRepository
{
    use RepositoryExceptionTrait;

    /**
     * @throws RepositoryException
     */
    public function findByUserId(int $userId): ChatScriptCollection
    {
        return $this->query(
            fn(): ChatScriptCollection => ChatScriptTable::query()
                ->setSelect(['*'])
                ->where('UF_USER_ID', $userId)
                ->setOrder(['ID' => 'DESC'])
                ->exec()
                ->fetchCollection(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function findById(int $id): ?ChatScript
    {
        return $this->query(
            fn(): ?ChatScript => ChatScriptTable::query()
                ->setSelect(['*'])
                ->where('ID', $id)
                ->exec()
                ->fetchObject(),
        );
    }

    /**
     * @throws RepositoryException
     */
    public function create(
        int $userId,
        string $name,
        bool $isActive,
    ): ChatScript {
        $now = new DateTime();

        /** @var ChatScript $script */
        $script = ChatScriptTable::createObject()
            ->setUfUserId($userId)
            ->setUfName($name)
            ->setUfIsActive($isActive ? 1 : 0)
            ->setUfCreatedAt($now)
            ->setUfUpdatedAt($now)
        ;

        $this->persist($script);

        return $script;
    }

    /**
     * @throws RepositoryException
     */
    public function save(ChatScript $script): void
    {
        $script->setUfUpdatedAt(new DateTime());
        $this->persist($script);
    }

    /**
     * @throws RepositoryException
     */
    public function delete(int $id): void
    {
        $this->query(
            static function() use ($id): void {
                ChatScriptTable::delete($id);
            },
        );
    }
}
