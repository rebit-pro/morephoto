<?php

declare(strict_types=1);

namespace Rebit\Identity\Tests\Domain\ApiConnection\Service;

use PHPUnit\Framework\TestCase;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyMasker;

/**
 * @internal
 */
final class ApiKeyMaskerTest extends TestCase
{
    private ApiKeyMasker $masker;

    protected function setUp(): void
    {
        $this->masker = new ApiKeyMasker();
    }

    public function testMaskLongKey(): void
    {
        $result = $this->masker->mask('EO0gCaxwD79OuvUqxT');

        self::assertSame('EO0g**********UqxT', $result);
    }

    public function testMaskExactlyEightChars(): void
    {
        $result = $this->masker->mask('12345678');

        self::assertSame('********', $result);
    }

    public function testMaskShortKey(): void
    {
        $result = $this->masker->mask('abc');

        self::assertSame('***', $result);
    }

    public function testMaskNineChars(): void
    {
        $result = $this->masker->mask('123456789');

        self::assertSame('1234*6789', $result);
    }

    public function testMaskPreservesFirstAndLastFourChars(): void
    {
        $result = $this->masker->mask('ABCDEFGHIJKLMNOP');

        self::assertStringStartsWith('ABCD', $result);
        self::assertStringEndsWith('MNOP', $result);
        self::assertSame(16, mb_strlen($result));
    }
}
