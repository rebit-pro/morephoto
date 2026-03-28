<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Counterparty\Repository;

use Bitrix\Main\GroupTable;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserGroupTable;
use Bitrix\Main\UserTable;
use Rebit\Exchange\Application\Trade\Dto\Bybit\BybitCounterpartyProfileDto;
use Rebit\Share\Infrastructure\Repository\RepositoryExceptionTrait;
use Rebit\Share\Shared\Exception\RepositoryException;

final readonly class CounterpartyRepository
{
    use RepositoryExceptionTrait;

    private const string COUNTERPARTIES_GROUP_CODE = 'COUNTERPARTIES';

    /**
     * @throws RepositoryException
     */
    public function findIdByBybitUserId(string $bybitUserId): ?int
    {
        return $this->query(function() use ($bybitUserId): ?int {
            $row = UserTable::query()
                ->setSelect(['ID'])
                ->where('UF_BYBIT_USER_ID', $bybitUserId)
                ->setLimit(1)
                ->exec()
                ->fetch()
            ;

            if (false === $row) {
                return null;
            }

            return (int)$row['ID'];
        });
    }

    /**
     * @throws RepositoryException
     */
    public function upsert(BybitCounterpartyProfileDto $profile): int
    {
        $existingUserId = $this->findIdByBybitUserId($profile->userId);
        $fields = $this->buildFields($profile);

        if (null !== $existingUserId) {
            $this->update($existingUserId, $fields);

            return $existingUserId;
        }

        return $this->create($fields, $profile->userId);
    }

    /**
     * @param array<string, int|string> $fields
     *
     * @throws RepositoryException
     */
    private function create(array $fields, string $bybitUserId): int
    {
        $user = new \CUser();

        try {
            $password = bin2hex(random_bytes(16));
        } catch (\Throwable $exception) {
            throw new RepositoryException(
                'Не удалось сгенерировать пароль контрагента',
                0,
                $exception,
            );
        }

        $counterpartyGroupId = $this->getCounterpartiesGroupId();

        $userId = $user->Add($fields + [
            'LOGIN' => 'bybit_' . $bybitUserId,
            'EMAIL' => 'bybit_' . $bybitUserId . '@counterparty.local',
            'ACTIVE' => 'N',
            'PASSWORD' => $password,
            'CONFIRM_PASSWORD' => $password,
            'GROUP_ID' => [$counterpartyGroupId],
        ]);

        if (false === $userId) {
            throw new RepositoryException((string)$user->LAST_ERROR);
        }

        return (int)$userId;
    }

    /**
     * @param array<string, int|string> $fields
     *
     * @throws RepositoryException
     */
    private function update(int $userId, array $fields): void
    {
        $user = new \CUser();
        $groupIds = $this->getUserGroupIds($userId);
        $counterpartyGroupId = $this->getCounterpartiesGroupId();

        if (false === in_array($counterpartyGroupId, $groupIds, true)) {
            $groupIds[] = $counterpartyGroupId;
        }

        if (false === $user->Update($userId, $fields + ['GROUP_ID' => $groupIds])) {
            throw new RepositoryException((string)$user->LAST_ERROR);
        }
    }

    /**
     * @return array<string, int|string>
     */
    private function buildFields(BybitCounterpartyProfileDto $profile): array
    {
        $now = new DateTime();

        return [
            'NAME' => '' !== $profile->nickName ? $profile->nickName : 'bybit_' . $profile->userId,
            'UF_BYBIT_USER_ID' => $profile->userId,
            'UF_BYBIT_NICKNAME' => $profile->nickName,
            'UF_BYBIT_REAL_NAME' => $profile->realName,
            'UF_BYBIT_REAL_NAME_EN' => $profile->realNameEn,
            'UF_BYBIT_KYC_LEVEL' => $profile->kycLevel,
            'UF_BYBIT_KYC_COUNTRY' => $profile->kycCountryCode,
            'UF_BYBIT_IS_ONLINE' => $profile->isOnline ? 1 : 0,
            'UF_BYBIT_TOTAL_TRADES' => $profile->totalFinishCount,
            'UF_BYBIT_TOTAL_BUY_TRADES' => $profile->totalFinishBuyCount,
            'UF_BYBIT_TOTAL_SELL_TRADES' => $profile->totalFinishSellCount,
            'UF_BYBIT_RECENT_RATE' => $profile->recentRate,
            'UF_BYBIT_RECENT_TRADES' => $profile->recentFinishCount,
            'UF_BYBIT_AVG_RELEASE_TIME' => $profile->averageReleaseTime,
            'UF_BYBIT_AVG_TRANSFER_TIME' => $profile->averageTransferTime,
            'UF_BYBIT_ACCOUNT_DAYS' => $profile->accountCreateDays,
            'UF_BYBIT_FIRST_TRADE_DAYS' => $profile->firstTradeDays,
            'UF_BYBIT_TRADE_AMOUNT' => $profile->totalTradeAmount,
            'UF_BYBIT_RECENT_TRADE_AMOUNT' => $profile->recentTradeAmount,
            'UF_BYBIT_GOOD_RATE' => $profile->goodAppraiseRate,
            'UF_BYBIT_GOOD_COUNT' => $profile->goodAppraiseCount,
            'UF_BYBIT_BAD_COUNT' => $profile->badAppraiseCount,
            'UF_BYBIT_AUTH_STATUS' => $profile->authStatus,
            'UF_BYBIT_VIP_LEVEL' => $profile->vipLevel,
            'UF_BYBIT_USER_TYPE' => $profile->userType,
            'UF_BYBIT_BLOCKED' => $profile->blocked,
            'UF_BYBIT_REGISTER_TIME' => $profile->registerTime,
            'UF_BYBIT_LAST_SYNCED_AT' => $now->toString(),
        ];
    }

    /**
     * @throws RepositoryException
     */
    private function getCounterpartiesGroupId(): int
    {
        $groupId = $this->query(
            static function(): ?int {
                $row = GroupTable::query()
                    ->setSelect(['ID'])
                    ->where('STRING_ID', self::COUNTERPARTIES_GROUP_CODE)
                    ->setLimit(1)
                    ->exec()
                    ->fetch()
                ;

                if (false === $row) {
                    return null;
                }

                return (int)$row['ID'];
            },
        );

        if (null === $groupId || 0 >= $groupId) {
            throw new RepositoryException('Группа пользователей COUNTERPARTIES не найдена');
        }

        return $groupId;
    }

    /**
     * @return array<int, int>
     *
     * @throws RepositoryException
     */
    private function getUserGroupIds(int $userId): array
    {
        return $this->query(
            static function() use ($userId): array {
                $rows = UserGroupTable::query()
                    ->setSelect(['GROUP_ID'])
                    ->where('USER_ID', $userId)
                    ->exec()
                    ->fetchAll()
                ;

                return array_map(
                    static fn(array $row): int => (int)$row['GROUP_ID'],
                    $rows,
                );
            },
        );
    }
}
