<?php

declare(strict_types=1);

namespace Rebit\Identity\Tests\Domain\ApiConnection\Service;

use PHPUnit\Framework\TestCase;
use Rebit\Identity\Domain\ApiConnection\Service\ApiKeyEncryptor;

/**
 * @internal
 */
final class ApiKeyEncryptorTest extends TestCase
{
    private ApiKeyEncryptor $encryptor;

    protected function setUp(): void
    {
        $this->encryptor = new ApiKeyEncryptor(bin2hex(random_bytes(16)));
    }

    public function testEncryptReturnsBase64String(): void
    {
        $encrypted = $this->encryptor->encrypt('my-secret-key');

        self::assertNotEmpty($encrypted);
        self::assertNotFalse(base64_decode($encrypted, true));
    }

    public function testDecryptReturnsOriginalValue(): void
    {
        $original = 'EO0gCaxwD79OuvUqxT';

        $encrypted = $this->encryptor->encrypt($original);
        $decrypted = $this->encryptor->decrypt($encrypted);

        self::assertSame($original, $decrypted);
    }

    public function testEncryptProducesDifferentCiphertextEachTime(): void
    {
        $plainText = 'same-value';

        $a = $this->encryptor->encrypt($plainText);
        $b = $this->encryptor->encrypt($plainText);

        self::assertNotSame($a, $b);
    }

    public function testDecryptWithDifferentKeyFails(): void
    {
        $encrypted = $this->encryptor->encrypt('secret');
        $otherEncryptor = new ApiKeyEncryptor(bin2hex(random_bytes(16)));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Decryption failed');

        $otherEncryptor->decrypt($encrypted);
    }

    public function testDecryptInvalidBase64Throws(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid base64 data');

        $this->encryptor->decrypt('not-valid-base64!!!');
    }

    public function testEncryptDecryptEmptyString(): void
    {
        $encrypted = $this->encryptor->encrypt('');
        $decrypted = $this->encryptor->decrypt($encrypted);

        self::assertSame('', $decrypted);
    }

    public function testEncryptDecryptLongString(): void
    {
        $original = str_repeat('a', 10000);

        $encrypted = $this->encryptor->encrypt($original);
        $decrypted = $this->encryptor->decrypt($encrypted);

        self::assertSame($original, $decrypted);
    }
}
