<?php

declare(strict_types=1);

namespace Rebit\Leadhunter\Tests\Infrastructure\LeadHunt\Notifier;

use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Rebit\Leadhunter\Application\LeadHunt\Dto\PendingLeadDto;
use Rebit\Leadhunter\Application\LeadHunt\Port\HuntNotifierInterface;
use Rebit\Leadhunter\Domain\LeadHunt\Enum\LeadSourceEnum;
use Rebit\Leadhunter\Infrastructure\LeadHunt\Notifier\FallbackHuntNotifier;

/**
 * @internal
 */
final class FallbackHuntNotifierTest extends TestCase
{
    private const int MAX_ATTEMPTS = 5;

    public function testPrimarySuccessSkipsFallback(): void
    {
        $primary = $this->notifier(true);
        $fallback = $this->createMock(HuntNotifierInterface::class);
        $fallback->expects(self::never())->method('notify');

        self::assertTrue($this->composite($primary, $fallback)->notify($this->lead(0)));
    }

    public function testPrimaryFailureBeforeLastAttemptDoesNotUseFallback(): void
    {
        $primary = $this->notifier(false);
        $fallback = $this->createMock(HuntNotifierInterface::class);
        $fallback->expects(self::never())->method('notify');

        self::assertFalse($this->composite($primary, $fallback)->notify($this->lead(self::MAX_ATTEMPTS - 2)));
    }

    public function testPrimaryFailureOnLastAttemptDeliversViaFallback(): void
    {
        $primary = $this->notifier(false);
        $fallback = $this->createMock(HuntNotifierInterface::class);
        $fallback->expects(self::once())->method('notify')->willReturn(true);

        self::assertTrue($this->composite($primary, $fallback)->notify($this->lead(self::MAX_ATTEMPTS - 1)));
    }

    public function testBothChannelsFailedReturnsFalse(): void
    {
        $primary = $this->notifier(false);
        $fallback = $this->notifier(false);

        self::assertFalse($this->composite($primary, $fallback)->notify($this->lead(self::MAX_ATTEMPTS - 1)));
    }

    private function composite(HuntNotifierInterface $primary, HuntNotifierInterface $fallback): FallbackHuntNotifier
    {
        return new FallbackHuntNotifier(new NullLogger(), $primary, $fallback, self::MAX_ATTEMPTS);
    }

    private function notifier(bool $result): HuntNotifierInterface
    {
        $notifier = $this->createStub(HuntNotifierInterface::class);
        $notifier->method('notify')->willReturn($result);

        return $notifier;
    }

    private function lead(int $attempts): PendingLeadDto
    {
        return new PendingLeadDto(
            id: 1,
            source: LeadSourceEnum::FL_RU,
            title: 'Правки Битрикс',
            description: 'Мелкие правки',
            url: 'https://www.fl.ru/projects/1/pravki.html',
            matchedKeywords: ['битрикс'],
            attempts: $attempts,
        );
    }
}
