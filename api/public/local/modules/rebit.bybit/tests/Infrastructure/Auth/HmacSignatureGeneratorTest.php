<?php

declare(strict_types=1);

namespace Rebit\Bybit\Tests\Infrastructure\Auth;

use PHPUnit\Framework\TestCase;
use Rebit\Bybit\Infrastructure\Auth\HmacSignatureGenerator;

/**
 * @internal
 */
final class HmacSignatureGeneratorTest extends TestCase
{
    private HmacSignatureGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new HmacSignatureGenerator();
    }

    public function testGeneratesValidHmacSha256(): void
    {
        $signature = $this->generator->generate(
            apiSecret: 'my-secret',
            timestamp: '1700000000000',
            apiKey: 'my-api-key',
            recvWindow: '5000',
            payload: 'param=value',
        );

        // HMAC-SHA256 всегда 64 символа hex
        self::assertSame(64, strlen($signature));
        self::assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $signature);
    }

    public function testDeterministicOutput(): void
    {
        $params = [
            'apiSecret' => 'secret',
            'timestamp' => '1700000000000',
            'apiKey' => 'APIKEY',
            'recvWindow' => '5000',
            'payload' => '',
        ];

        $a = $this->generator->generate(...$params);
        $b = $this->generator->generate(...$params);

        self::assertSame($a, $b);
    }

    public function testDifferentSecretsProduceDifferentSignatures(): void
    {
        $sig1 = $this->generator->generate(
            apiSecret: 'secret-a',
            timestamp: '1700000000000',
            apiKey: 'key',
            recvWindow: '5000',
            payload: 'test',
        );

        $sig2 = $this->generator->generate(
            apiSecret: 'secret-b',
            timestamp: '1700000000000',
            apiKey: 'key',
            recvWindow: '5000',
            payload: 'test',
        );

        self::assertNotSame($sig1, $sig2);
    }

    public function testPayloadAffectsSignature(): void
    {
        $sig1 = $this->generator->generate(
            apiSecret: 'secret',
            timestamp: '1700000000000',
            apiKey: 'key',
            recvWindow: '5000',
            payload: '{"coin":"BTC"}',
        );

        $sig2 = $this->generator->generate(
            apiSecret: 'secret',
            timestamp: '1700000000000',
            apiKey: 'key',
            recvWindow: '5000',
            payload: '{"coin":"ETH"}',
        );

        self::assertNotSame($sig1, $sig2);
    }

    public function testMatchesKnownHmac(): void
    {
        // Manually computed: hash_hmac('sha256', '1700000000000APIKEY5000queryString', 'SECRET')
        $expected = hash_hmac(
            'sha256',
            '1700000000000APIKEY5000queryString',
            'SECRET',
        );

        $result = $this->generator->generate(
            apiSecret: 'SECRET',
            timestamp: '1700000000000',
            apiKey: 'APIKEY',
            recvWindow: '5000',
            payload: 'queryString',
        );

        self::assertSame($expected, $result);
    }
}
