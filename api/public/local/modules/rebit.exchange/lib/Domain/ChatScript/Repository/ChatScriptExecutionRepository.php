<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\ChatScript\Repository;

use Bitrix\Main\Type\DateTime;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptExecution;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptExecutionCollection;
use Rebit\Exchange\Domain\ChatScript\Entity\Table\ChatScriptExecutionTable;
use Rebit\Exchange\Domain\ChatScript\Enum\ExecutionStatusEnum;
use Rebit\Share\Infrastructure\Repository\RepositoryExceptionTrait;
use Rebit\Share\Shared\Exception\RepositoryException;

final class ChatScriptExecutionRepository
{
    use RepositoryExceptionTrait;

    /**
     * Получить все pending-исполнения, у которых наступило время следующего шага.
     *
     * @throws RepositoryException
     */
    public function findReadyToProcess(): ChatScriptExecutionCollection
    {
        $now = new DateTime();

        return $this->query(
            fn(): ChatScriptExecutionCollection => ChatScriptExecutionTable::query()
                ->setSelect(['*'])
                ->where('UF_STATUS', ExecutionStatusEnum::Pending->value)
                ->where('UF_NEXT_RUN_AT', '<=', $now)
                ->setOrder(['UF_NEXT_RUN_AT' => 'ASC'])
                ->exec()
                ->fetchCollection(),
        );
    }

    /**
     * Проверяет, есть ли уже pending-исполнение для данной сделки.
     *
     * @throws RepositoryException
     */
    public function existsPendingForTrade(int $tradeId): bool
    {
        return $this->query(
            fn(): bool => 0 < ChatScriptExecutionTable::query()
                ->where('UF_TRADE_ID', $tradeId)
                ->where('UF_STATUS', ExecutionStatusEnum::Pending->value)
                ->queryCountTotal(),
        );
    }

    /**
     * Ставит скрипт в очередь на исполнение для сделки.
     *
     * @throws RepositoryException
     */
    public function enqueue(
        int $tradeId,
        int $scriptId,
        int $userId,
        int $firstStepDelaySeconds = 0,
    ): ChatScriptExecution {
        $now = new DateTime();
        $nextRunAt = (new DateTime())->add("+{$firstStepDelaySeconds} seconds");

        /** @var ChatScriptExecution $execution */
        $execution = ChatScriptExecutionTable::createObject()
            ->setUfTradeId($tradeId)
            ->setUfScriptId($scriptId)
            ->setUfUserId($userId)
            ->setUfLastStepSort(0)
            ->setUfStatus(ExecutionStatusEnum::Pending->value)
            ->setUfNextRunAt($nextRunAt)
            ->setUfCreatedAt($now)
        ;

        $this->persist($execution);

        return $execution;
    }

    /**
     * @throws RepositoryException
     */
    public function save(ChatScriptExecution $execution): void
    {
        $this->persist($execution);
    }

    /**
     * Отменяет все pending-исполнения для сделки (при отмене сделки).
     *
     * @throws RepositoryException
     */
    public function cancelByTradeId(int $tradeId): void
    {
        $executions = $this->query(
            fn(): ChatScriptExecutionCollection => ChatScriptExecutionTable::query()
                ->setSelect(['*'])
                ->where('UF_TRADE_ID', $tradeId)
                ->where('UF_STATUS', ExecutionStatusEnum::Pending->value)
                ->exec()
                ->fetchCollection(),
        );

        foreach ($executions as $execution) {
            $execution->setUfStatus(ExecutionStatusEnum::Cancelled->value);
            $this->persist($execution);
        }
    }
}
