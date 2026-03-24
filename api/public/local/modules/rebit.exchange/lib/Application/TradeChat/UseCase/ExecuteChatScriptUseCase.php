<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\UseCase;

use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptRepository;
use Rebit\Exchange\Domain\ChatScript\Repository\ChatScriptStepRepository;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Domain\TradeChat\Enum\ContentTypeEnum;
use Rebit\Exchange\Domain\TradeChat\Enum\MessageTypeEnum;
use Rebit\Exchange\Domain\TradeChat\Repository\TradeMessageRepository;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Исполнение скрипта автосообщений для сделки.
 * Отправляет шаги скрипта с задержками в Bybit + сохраняет локально.
 */
final readonly class ExecuteChatScriptUseCase
{
    public function __construct(
        private ChatScriptRepository $scriptRepository,
        private ChatScriptStepRepository $stepRepository,
        private TradeRepository $tradeRepository,
        private TradeMessageRepository $messageRepository,
        private BybitChatGatewayInterface $chatGateway,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(int $tradeId, int $scriptId, int $userId): int
    {
        $trade = $this->tradeRepository->findById($tradeId);
        if (null === $trade) {
            throw new EntityNotFoundException('Сделка не найдена');
        }

        $script = $this->scriptRepository->findById($scriptId);
        if (null === $script) {
            throw new EntityNotFoundException('Скрипт не найден');
        }

        if ($script->getUfUserId() !== $userId) {
            throw new HttpException('Нет доступа к этому скрипту', 403);
        }

        $steps = $this->stepRepository->findByScriptId($scriptId);
        $sentCount = 0;

        foreach ($steps as $step) {
            if ($step->getUfDelaySeconds() > 0) {
                sleep($step->getUfDelaySeconds());
            }

            $msgUuid = Uuid::uuid4()->toString();

            try {
                $this->chatGateway->sendMessage(
                    $userId,
                    $trade->getUfBybitOrderId(),
                    $step->getUfMessage(),
                    ContentTypeEnum::Str->value,
                    $msgUuid,
                );

                $this->messageRepository->create(
                    tradeId: $tradeId,
                    userId: $userId,
                    message: $step->getUfMessage(),
                    messageType: MessageTypeEnum::Script,
                    contentType: ContentTypeEnum::Str,
                    bybitMsgUuid: $msgUuid,
                    scriptStepId: $step->getId(),
                );

                ++$sentCount;
            } catch (HttpException $e) {
                $this->logger->warning('Chat script step failed', [
                    'tradeId' => $tradeId,
                    'scriptId' => $scriptId,
                    'stepId' => $step->getId(),
                    'error' => $e->getMessage(),
                ]);

                break;
            }
        }

        return $sentCount;
    }
}
