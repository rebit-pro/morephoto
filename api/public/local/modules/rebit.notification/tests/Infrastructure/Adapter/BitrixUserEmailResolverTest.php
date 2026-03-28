<?php

declare(strict_types=1);

namespace Rebit\Notification\Tests\Infrastructure\Adapter;

use PHPUnit\Framework\TestCase;
use Rebit\Notification\Infrastructure\Adapter\BitrixUserEmailResolver;

/**
 * @internal
 */
final class BitrixUserEmailResolverTest extends TestCase
{
    protected function setUp(): void
    {
        \CUser::resetMockUsers();
    }

    public function testResolvesEmailFromBitrixUser(): void
    {
        \CUser::setMockUser(42, ['EMAIL' => 'trader@example.com']);

        $resolver = new BitrixUserEmailResolver();

        self::assertSame('trader@example.com', $resolver->resolve(42));
    }

    public function testReturnsNullWhenUserNotFound(): void
    {
        $resolver = new BitrixUserEmailResolver();

        self::assertNull($resolver->resolve(999));
    }

    public function testReturnsNullForZeroUserId(): void
    {
        $resolver = new BitrixUserEmailResolver();

        self::assertNull($resolver->resolve(0));
    }

    public function testReturnsNullWhenEmailEmpty(): void
    {
        \CUser::setMockUser(42, ['EMAIL' => '']);

        $resolver = new BitrixUserEmailResolver();

        self::assertNull($resolver->resolve(42));
    }
}
