<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\UseCase;

use Psr\Log\LoggerInterface;
use Rebit\Exchange\Application\TradeChat\Message\ExecuteChatScriptStepMessage;
use Rebit\Exchange\Domain\Advertisement\Repository\AdvertisementRepository;
use Rebit\Exchange\Domain\ChatScript\Entity\ChatScriptStep;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptExecutionRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Exchange\Domain\Trade\Entity\Trade;
use Rebit\Share\Application\Contract\Messenger\MessagePublisherInterface;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class StartTradeChatScriptUseCase
{
    public function __construct(
        private AdvertisementRepository $advertisementRepository,
        private ChatScriptRepository $chatScriptRepository,
        private ChatScriptStepRepository $chatScriptStepRepository,
        private ChatScriptExecutionRepository $chatScriptExecutionRepository,
        private MessagePublisherInterface $chatScriptStepPublisher,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws RepositoryException
     */
    public function execute(Trade $trade): void
    {
        if (0 >= $trade->getUfAdvertisementId()) {
            $this->logger->info('Пропущен запуск чат-скрипта: advertisementId не задан', [
                'tradeId' => $trade->getId(),
            ]);

            return;
        }

        $advertisement = $this->advertisementRepository->findById($trade->getUfAdvertisementId());
        if (null === $advertisement) {
            $this->logger->warning('Пропущен запуск чат-скрипта: объявление не найдено', [
                'tradeId' => $trade->getId(),
                'advertisementId' => $trade->getUfAdvertisementId(),
            ]);

            return;
        }

        if (0 >= $advertisement->getUfChatScriptId()) {
            $this->logger->info('Пропущен запуск чат-скрипта: для объявления не привязан скрипт', [
                'tradeId' => $trade->getId(),
                'advertisementId' => $advertisement->getId(),
            ]);

            return;
        }

        $script = $this->chatScriptRepository->findById($advertisement->getUfChatScriptId());
        if (null === $script || 1 !== $script->getUfIsActive()) {
            $this->logger->warning('Пропущен запуск чат-скрипта: скрипт не найден или неактивен', [
                'tradeId' => $trade->getId(),
                'advertisementId' => $advertisement->getId(),
                'chatScriptId' => $advertisement->getUfChatScriptId(),
            ]);

            return;
        }

        if ($this->chatScriptExecutionRepository->existsPendingForTrade($trade->getId())) {
            $this->logger->info('Пропущен запуск чат-скрипта: для сделки уже есть pending-исполнение', [
                'tradeId' => $trade->getId(),
            ]);

            return;
        }

        $steps = $this->chatScriptStepRepository->findByScriptId($script->getId());
        /** @var array<int, ChatScriptStep> $stepList */
        $stepList = $steps->getAll();
        $firstStep = $stepList[0] ?? null;

        if (null === $firstStep) {
            $this->logger->warning('Пропущен запуск чат-скрипта: у скрипта нет шагов', [
                'tradeId' => $trade->getId(),
                'chatScriptId' => $script->getId(),
            ]);

            return;
        }

        $execution = $this->chatScriptExecutionRepository->enqueue(
            tradeId: $trade->getId(),
            scriptId: $script->getId(),
            userId: $advertisement->getUfUserId(),
            firstStepDelaySeconds: $firstStep->getUfDelaySeconds(),
        );

        $this->chatScriptStepPublisher->dispatch(
            new ExecuteChatScriptStepMessage(
                executionId: $execution->getId(),
                tradeId: $trade->getId(),
                stepId: $firstStep->getId(),
                delaySeconds: $firstStep->getUfDelaySeconds(),
            ),
        );

        $this->logger->info('Чат-скрипт поставлен в очередь для сделки', [
            'tradeId' => $trade->getId(),
            'advertisementId' => $advertisement->getId(),
            'chatScriptId' => $script->getId(),
            'executionId' => $execution->getId(),
            'firstStepId' => $firstStep->getId(),
            'delaySeconds' => $firstStep->getUfDelaySeconds(),
        ]);
    }
}
