<?php
declare(strict_types=1);
namespace Rebit\Wallet\Tests\Domain\Balance\Service;
use PHPUnit\Framework\TestCase;
use Rebit\Wallet\Domain\Balance\Exception\InsufficientFundsException;
use Rebit\Wallet\Domain\Balance\Exception\InsufficientLockedFundsException;
use Rebit\Wallet\Domain\Balance\Service\BalanceCalculator;
/**
 * @internal
 */
final class BalanceCalculatorTest extends TestCase
{
    private BalanceCalculator $calculator;
    protected function setUp(): void
    {
        $this->calculator = new BalanceCalculator();
    }
    // --- assertCanLock ---
    public function testAssertCanLockPassesWhenSufficientFunds(): void
    {
        $this->calculator->assertCanLock(10.0, 5.0);
        self::assertTrue(true); // no exception
    }
    public function testAssertCanLockPassesWhenExactAmount(): void
    {
        $this->calculator->assertCanLock(5.0, 5.0);
        self::assertTrue(true);
    }
    public function testAssertCanLockThrowsWhenInsufficientFunds(): void
    {
        $this->expectException(InsufficientFundsException::class);
        $this->calculator->assertCanLock(3.0, 5.0);
    }
    public function testAssertCanLockThrowsWhenZeroAvailable(): void
    {
        $this->expectException(InsufficientFundsException::class);
        $this->calculator->assertCanLock(0.0, 0.001);
    }
    // --- assertCanUnlock ---
    public function testAssertCanUnlockPassesWhenSufficientLocked(): void
    {
        $this->calculator->assertCanUnlock(10.0, 5.0);
        self::assertTrue(true);
    }
    public function testAssertCanUnlockPassesWhenExactAmount(): void
    {
        $this->calculator->assertCanUnlock(5.0, 5.0);
        self::assertTrue(true);
    }
    public function testAssertCanUnlockThrowsWhenInsufficientLocked(): void
    {
        $this->expectException(InsufficientLockedFundsException::class);
        $this->calculator->assertCanUnlock(2.0, 5.0);
    }
    // --- calculateTotal ---
    public function testCalculateTotal(): void
    {
        self::assertSame(15.0, $this->calculator->calculateTotal(10.0, 5.0));
    }
    public function testCalculateTotalWithZeros(): void
    {
        self::assertSame(0.0, $this->calculator->calculateTotal(0.0, 0.0));
    }
    // --- detectDiscrepancy ---
    public function testDetectDiscrepancyReturnsTrueWhenAboveThreshold(): void
    {
        self::assertTrue($this->calculator->detectDiscrepancy(100.0, 100.001));
    }
    public function testDetectDiscrepancyReturnsFalseWhenBelowThreshold(): void
    {
        self::assertFalse($this->calculator->detectDiscrepancy(100.0, 100.000000001));
    }
    public function testDetectDiscrepancyReturnsFalseWhenEqual(): void
    {
        self::assertFalse($this->calculator->detectDiscrepancy(100.0, 100.0));
    }
    public function testDetectDiscrepancyWithCustomThreshold(): void
    {
        self::assertFalse($this->calculator->detectDiscrepancy(100.0, 100.5, 1.0));
        self::assertTrue($this->calculator->detectDiscrepancy(100.0, 101.5, 1.0));
    }
}
