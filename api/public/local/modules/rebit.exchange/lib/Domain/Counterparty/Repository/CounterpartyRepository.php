<?php

declare(strict_types=1);

namespace Rebit\Exchange\Domain\Counterparty\Repository;

use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
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
     * @param array{
     *     userId: string,
     *     nickName: string,
     *     realName?: string,
     *     realNameEn?: string,
     *     kycLevel?: int|string,
     *     kycCountryCode?: string,
     *     isOnline?: bool,
     *     totalFinishCount?: int|string,
     *     totalFinishBuyCount?: int|string,
     *     totalFinishSellCount?: int|string,
     *     recentRate?: int|string,
     *     recentFinishCount?: int|string,
     *     averageReleaseTime?: string,
     *     averageTransferTime?: string,
     *     accountCreateDays?: int|string,
     *     firstTradeDays?: int|string,
     *     totalTradeAmount?: string,
     *     recentTradeAmount?: string,
     *     goodAppraiseRate?: string,
     *     goodAppraiseCount?: int|string,
     *     badAppraiseCount?: int|string,
     *     authStatus?: int|string,
     *     vipLevel?: int|string,
     *     userType?: string,
     *     blocked?: string,
     *     registerTime?: string,
     * } $profile
     *
     * @throws RepositoryException
     */
    public function upsert(array $profile): int
    {
        $existingUserId = $this->findIdByBybitUserId((string)$profile['userId']);
        $fields = $this->buildFields($profile);

        if (null !== $existingUserId) {
            $this->update($existingUserId, $fields);

            return $existingUserId;
        }

        return $this->create($fields, (string)$profile['userId']);
    }

    /**
     * @param array<string, int|string> $fields
     *
     * @throws RepositoryException
     */
    private function create(array $fields, string $bybitUserId): int
    {
        $user = new \CUser();
        $password = bin2hex(random_bytes(16));
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
        $groupIds = \CUser::GetUserGroup($userId);
        $counterpartyGroupId = $this->getCounterpartiesGroupId();

        if (false === in_array($counterpartyGroupId, $groupIds, true)) {
            $groupIds[] = $counterpartyGroupId;
        }

        if (false === $user->Update($userId, $fields + ['GROUP_ID' => $groupIds])) {
            throw new RepositoryException((string)$user->LAST_ERROR);
        }
    }

    /**
     * @param array<string, mixed> $profile
     *
     * @return array<string, int|string>
     */
    private function buildFields(array $profile): array
    {
        $now = new DateTime();

        return [
            'NAME' => (string)($profile['nickName'] ?? ('bybit_' . (string)$profile['userId'])),
            'UF_BYBIT_USER_ID' => (string)$profile['userId'],
            'UF_BYBIT_NICKNAME' => (string)($profile['nickName'] ?? ''),
            'UF_BYBIT_REAL_NAME' => (string)($profile['realName'] ?? ''),
            'UF_BYBIT_REAL_NAME_EN' => (string)($profile['realNameEn'] ?? ''),
            'UF_BYBIT_KYC_LEVEL' => (int)($profile['kycLevel'] ?? 0),
            'UF_BYBIT_KYC_COUNTRY' => (string)($profile['kycCountryCode'] ?? ''),
            'UF_BYBIT_IS_ONLINE' => (isset($profile['isOnline']) && true === (bool)$profile['isOnline']) ? 1 : 0,
            'UF_BYBIT_TOTAL_TRADES' => (int)($profile['totalFinishCount'] ?? 0),
            'UF_BYBIT_TOTAL_BUY_TRADES' => (int)($profile['totalFinishBuyCount'] ?? 0),
            'UF_BYBIT_TOTAL_SELL_TRADES' => (int)($profile['totalFinishSellCount'] ?? 0),
            'UF_BYBIT_RECENT_RATE' => (string)($profile['recentRate'] ?? ''),
            'UF_BYBIT_RECENT_TRADES' => (int)($profile['recentFinishCount'] ?? 0),
            'UF_BYBIT_AVG_RELEASE_TIME' => (string)($profile['averageReleaseTime'] ?? ''),
            'UF_BYBIT_AVG_TRANSFER_TIME' => (string)($profile['averageTransferTime'] ?? ''),
            'UF_BYBIT_ACCOUNT_DAYS' => (int)($profile['accountCreateDays'] ?? 0),
            'UF_BYBIT_FIRST_TRADE_DAYS' => (int)($profile['firstTradeDays'] ?? 0),
            'UF_BYBIT_TRADE_AMOUNT' => (string)($profile['totalTradeAmount'] ?? ''),
            'UF_BYBIT_RECENT_TRADE_AMOUNT' => (string)($profile['recentTradeAmount'] ?? ''),
            'UF_BYBIT_GOOD_RATE' => (string)($profile['goodAppraiseRate'] ?? ''),
            'UF_BYBIT_GOOD_COUNT' => (int)($profile['goodAppraiseCount'] ?? 0),
            'UF_BYBIT_BAD_COUNT' => (int)($profile['badAppraiseCount'] ?? 0),
            'UF_BYBIT_AUTH_STATUS' => (int)($profile['authStatus'] ?? 0),
            'UF_BYBIT_VIP_LEVEL' => (int)($profile['vipLevel'] ?? 0),
            'UF_BYBIT_USER_TYPE' => (string)($profile['userType'] ?? ''),
            'UF_BYBIT_BLOCKED' => (string)($profile['blocked'] ?? ''),
            'UF_BYBIT_REGISTER_TIME' => (string)($profile['registerTime'] ?? ''),
            'UF_BYBIT_LAST_SYNCED_AT' => $now->toString(),
        ];
    }

    /**
     * @throws RepositoryException
     */
    private function getCounterpartiesGroupId(): int
    {
        $groupId = \CGroup::GetIDByCode(self::COUNTERPARTIES_GROUP_CODE);
        if (false === $groupId || 0 >= (int)$groupId) {
            throw new RepositoryException('Группа пользователей COUNTERPARTIES не найдена');
        }

        return (int)$groupId;
    }
}
