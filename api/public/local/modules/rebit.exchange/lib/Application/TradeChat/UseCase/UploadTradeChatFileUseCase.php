<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\TradeChat\UseCase;

use Rebit\Exchange\Application\TradeChat\Dto\Request\UploadTradeChatFileRequestDto;
use Rebit\Exchange\Application\TradeChat\Dto\Result\UploadTradeChatFileResultDto;
use Rebit\Exchange\Application\TradeChat\Port\BybitChatGatewayInterface;
use Rebit\Exchange\Domain\Trade\Repository\TradeRepository;
use Rebit\Exchange\Infrastructure\Bitrix\TradeChatUploadFileLocator;
use Rebit\Share\Infrastructure\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class UploadTradeChatFileUseCase
{
    public function __construct(
        private TradeRepository $tradeRepository,
        private BybitChatGatewayInterface $chatGateway,
        private TradeChatUploadFileLocator $fileLocator,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(UploadTradeChatFileRequestDto $dto, int $userId): UploadTradeChatFileResultDto
    {
        $trade = $this->tradeRepository->findById($dto->tradeId);

        if (null === $trade) {
            throw new EntityNotFoundException('Сделка не найдена');
        }

        if ($trade->getUfBuyerUserId() !== $userId && $trade->getUfSellerUserId() !== $userId) {
            throw new HttpException('Нет доступа к чату этой сделки', 403);
        }

        $file = $this->fileLocator->getById($dto->fileId);
        $uploadResult = $this->chatGateway->uploadFile(
            $userId,
            $file['path'],
            $file['name'],
            $file['mimeType'],
        );
        $fileUrl = $uploadResult['url'];

        if ('' === $fileUrl) {
            throw new HttpException('Bybit не вернул URL загруженного файла', 502);
        }

        $providerType = $uploadResult['type'];

        return new UploadTradeChatFileResultDto(
            fileName: $file['name'],
            fileUrl: $fileUrl,
            contentType: $this->resolveContentType($providerType, $file['mimeType']),
            providerType: '' !== $providerType ? $providerType : null,
        );
    }

    private function resolveContentType(string $providerType, string $mimeType): string
    {
        return match (strtoupper($providerType)) {
            'IMAGE' => 'pic',
            'PDF' => 'pdf',
            'VIDEO' => 'video',
            default => $this->resolveContentTypeByMimeType($mimeType),
        };
    }

    private function resolveContentTypeByMimeType(string $mimeType): string
    {
        return match (true) {
            str_starts_with($mimeType, 'image/') => 'pic',
            'application/pdf' === $mimeType => 'pdf',
            str_starts_with($mimeType, 'video/') => 'video',
            default => 'str',
        };
    }
}
