<?php
declare(strict_types=1);
namespace Rebit\Wallet\Tests\Application\Transaction\UseCase;
use PHPUnit\Framework\TestCase;
use Rebit\Wallet\Application\Transaction\Dto\Request\TransactionFilterRequestDto;
use Rebit\Wallet\Application\Transaction\Dto\Result\TransactionListResultDto;
use Rebit\Wallet\Application\Transaction\UseCase\ExportTransactionsUseCase;
use Rebit\Wallet\Application\Transaction\UseCase\ListTransactionsUseCase;
/**
 * @internal
 */
final class ExportTransactionsUseCaseTest extends TestCase
{
    private const int USER_ID = 1;
    public function testDelegatesWithLimit10000AndOffset0(): void
    {
        $filter = new TransactionFilterRequestDto(
            type: 'deposit',
            currencyId: 5,
            dateFrom: '2026-01-01',
            dateTo: '2026-03-01',
            limit: 50,
            offset: 10,
        );
        $expectedResult = new TransactionListResultDto([], 0);
        $listUseCase = $this->createMock(ListTransactionsUseCase::class);
        $listUseCase
            ->expects(self::once())
            ->method('execute')
            ->with(
                self::USER_ID,
                self::callback(static function (TransactionFilterRequestDto $dto): bool {
                    return 10000 === $dto->limit
                        && 0 === $dto->offset
                        && 'deposit' === $dto->type
                        && 5 === $dto->currencyId
                        && '2026-01-01' === $dto->dateFrom
                        && '2026-03-01' === $dto->dateTo;
                }),
            )
            ->willReturn($expectedResult);
        $result = (new ExportTransactionsUseCase($listUseCase))->execute(self::USER_ID, $filter);
        self::assertSame($expectedResult, $result);
    }
}
