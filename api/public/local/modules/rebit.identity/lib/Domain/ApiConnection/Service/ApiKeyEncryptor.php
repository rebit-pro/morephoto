<?php

declare(strict_types=1);

namespace Rebit\Identity\Domain\ApiConnection\Service;

use Random\RandomException;

/**
 * Шифрование/дешифрование API-ключей Bybit (AES-256-GCM).
 */
final readonly class ApiKeyEncryptor
{
    private const string CIPHER = 'aes-256-gcm';
    private const int TAG_LENGTH = 16;

    public function __construct(
        private string $encryptionKey,
    ) {}

    /**
     * @throws \RuntimeException
     * @throws RandomException
     */
    public function encrypt(string $plainText): string
    {
        $ivLength = openssl_cipher_iv_length(self::CIPHER);

        if (false === $ivLength) {
            throw new \RuntimeException('Failed to get IV length for cipher');
        }

        $iv = random_bytes($ivLength);
        $tag = '';

        $encrypted = openssl_encrypt(
            $plainText,
            self::CIPHER,
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            self::TAG_LENGTH,
        );

        if (false === $encrypted) {
            throw new \RuntimeException('Encryption failed');
        }

        return base64_encode($iv . $tag . $encrypted);
    }

    /**
     * @throws \RuntimeException
     */
    public function decrypt(string $encoded): string
    {
        $data = base64_decode($encoded, true);

        if (false === $data) {
            throw new \RuntimeException('Invalid base64 data');
        }

        $ivLength = openssl_cipher_iv_length(self::CIPHER);

        if (false === $ivLength) {
            throw new \RuntimeException('Failed to get IV length for cipher');
        }

        $iv = substr($data, 0, $ivLength);
        $tag = substr($data, $ivLength, self::TAG_LENGTH);
        $encrypted = substr($data, $ivLength + self::TAG_LENGTH);

        $decrypted = openssl_decrypt(
            $encrypted,
            self::CIPHER,
            $this->encryptionKey,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
        );

        if (false === $decrypted) {
            throw new \RuntimeException('Decryption failed');
        }

        return $decrypted;
    }
}
