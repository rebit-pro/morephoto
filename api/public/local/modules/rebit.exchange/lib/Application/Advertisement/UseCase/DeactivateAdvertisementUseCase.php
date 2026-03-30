<?php

declare(strict_types=1);

namespace Rebit\Exchange\Application\Advertisement\UseCase;

use Rebit\Exchange\Application\Advertisement\Port\BybitAdvertisementGatewayInterface;
use Rebit\Exchange\Domain\Advertisement\Enum\AdvertisementStatusEnum;
use Rebit\Exchange\Domain\Advertisement\Repository\AdvertisementRepository;
use Rebit\Share\Shared\Exception\EntityNotFoundException;
use Rebit\Share\Shared\Exception\HttpException;
use Rebit\Share\Shared\Exception\RepositoryException;

/**
 * Деактивация (удаление) объявления через Bybit API.
 */
final readonly class DeactivateAdvertisementUseCase
{
    public function __construct(
        private AdvertisementRepository $advertisementRepository,
        private BybitAdvertisementGatewayInterface $bybitGateway,
    ) {}

    /**
     * @throws HttpException
     * @throws RepositoryException
     */
    public function execute(int $advertisementId, int $userId): void
    {
        $ad = $this->advertisementRepository->findById($advertisementId);

        if (null === $ad) {
            throw new EntityNotFoundException('Объявление не найдено');
        }

        if ($ad->getUfUserId() !== $userId) {
            throw new HttpException('Нет доступа к этому объявлению', 403);
        }

        $bybitAdId = $ad->getUfBybitAdId();
        if ('' !== $bybitAdId) {
            $this->bybitGateway->cancel($userId, $bybitAdId);
        }

        $ad->setUfStatus(AdvertisementStatusEnum::Cancelled->value);
        $this->advertisementRepository->save($ad);
    }
}
