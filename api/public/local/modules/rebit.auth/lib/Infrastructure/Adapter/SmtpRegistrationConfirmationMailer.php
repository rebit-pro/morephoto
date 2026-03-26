<?php

declare(strict_types=1);

namespace Rebit\Auth\Infrastructure\Adapter;

use Bitrix\Main\Type\DateTime;
use Rebit\Auth\Application\Auth\Contract\RegistrationConfirmationMailerInterface;
use Rebit\Share\Shared\Exception\HttpException;

final readonly class SmtpRegistrationConfirmationMailer implements RegistrationConfirmationMailerInterface
{
    private const string ENCRYPTION_NONE = 'none';
    private const string ENCRYPTION_SSL = 'ssl';
    private const string ENCRYPTION_TLS = 'tls';

    public function __construct(
        private string $host,
        private int $port,
        private string $encryption,
        private string $username,
        private string $password,
        private string $fromEmail,
        private string $fromName,
        private int $timeoutSeconds,
    ) {}

    /**
     * @throws HttpException
     */
    public function sendConfirmationCode(string $email, string $code, DateTime $expiresAt): void
    {
        if ('' === $this->host || '' === $this->fromEmail) {
            throw new HttpException('Почтовый сервер не настроен.', 500);
        }

        $socket = null;

        try {
            $socket = $this->connect();
            $helloDomain = $this->resolveHelloDomain();

            $this->expectResponse($socket, [220]);
            $this->sendCommand($socket, 'EHLO ' . $helloDomain, [250]);

            if (self::ENCRYPTION_TLS === $this->encryption) {
                $this->sendCommand($socket, 'STARTTLS', [220]);

                if (true !== stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new \RuntimeException('Не удалось включить TLS для SMTP-соединения.');
                }

                $this->sendCommand($socket, 'EHLO ' . $helloDomain, [250]);
            }

            if ('' !== $this->username) {
                $this->sendCommand($socket, 'AUTH LOGIN', [334]);
                $this->sendCommand($socket, base64_encode($this->username), [334]);
                $this->sendCommand($socket, base64_encode($this->password), [235]);
            }

            $this->sendCommand($socket, 'MAIL FROM:<' . $this->fromEmail . '>', [250]);
            $this->sendCommand($socket, 'RCPT TO:<' . $email . '>', [250, 251]);
            $this->sendCommand($socket, 'DATA', [354]);

            $message = $this->buildMessage($email, $code, $expiresAt);
            fwrite($socket, $this->escapeMessageBody($message) . "\r\n.\r\n");
            $this->expectResponse($socket, [250]);
            $this->sendCommand($socket, 'QUIT', [221]);
        } catch (\Throwable $exception) {
            throw new HttpException('Не удалось отправить письмо с кодом подтверждения.', 502, previous: $exception instanceof \Exception ? $exception : null);
        } finally {
            if (is_resource($socket)) {
                fclose($socket);
            }
        }
    }

    /**
     * @return resource
     */
    private function connect()
    {
        $transport = match ($this->encryption) {
            self::ENCRYPTION_SSL => 'ssl://' . $this->host . ':' . $this->port,
            self::ENCRYPTION_NONE, self::ENCRYPTION_TLS => 'tcp://' . $this->host . ':' . $this->port,
            default => throw new \RuntimeException('Неподдерживаемый тип SMTP-шифрования.'),
        };

        $socket = @stream_socket_client(
            $transport,
            $errorCode,
            $errorMessage,
            $this->timeoutSeconds,
            STREAM_CLIENT_CONNECT,
        );

        if (false === $socket) {
            throw new \RuntimeException(
                sprintf('Не удалось подключиться к SMTP-серверу: %s (%d)', $errorMessage, $errorCode),
            );
        }

        stream_set_timeout($socket, $this->timeoutSeconds);

        return $socket;
    }

    /**
     * @param resource $socket
     *
     * @throws \RuntimeException
     */
    private function sendCommand($socket, string $command, array $expectedCodes): void
    {
        fwrite($socket, $command . "\r\n");
        $this->expectResponse($socket, $expectedCodes);
    }

    /**
     * @param resource        $socket
     * @param array<int, int> $expectedCodes
     *
     * @throws \RuntimeException
     */
    private function expectResponse($socket, array $expectedCodes): void
    {
        $response = $this->readResponse($socket);
        $code = (int)substr($response, 0, 3);

        if (!in_array($code, $expectedCodes, true)) {
            throw new \RuntimeException('SMTP-сервер вернул ошибку: ' . trim($response));
        }
    }

    /**
     * @param resource $socket
     */
    private function readResponse($socket): string
    {
        $response = '';

        while (!feof($socket)) {
            $line = fgets($socket);

            if (false === $line) {
                break;
            }

            $response .= $line;

            if (strlen($line) < 4 || ' ' === $line[3]) {
                break;
            }
        }

        if ('' === $response) {
            throw new \RuntimeException('SMTP-сервер не вернул ответ.');
        }

        return $response;
    }

    private function buildMessage(string $email, string $code, DateTime $expiresAt): string
    {
        $subject = $this->encodeHeader('Код подтверждения регистрации в Rebit P2P');
        $from = $this->formatFromHeader();
        $expiresAtText = $expiresAt->format('d.m.Y H:i');

        $body = implode("\r\n", [
            'Здравствуйте!',
            '',
            'Ваш код подтверждения для регистрации в Rebit P2P: ' . $code,
            'Код действует до ' . $expiresAtText . '.',
            '',
            'Если вы не запрашивали регистрацию, просто проигнорируйте это письмо.',
        ]);

        return implode("\r\n", [
            'From: ' . $from,
            'To: <' . $email . '>',
            'Subject: ' . $subject,
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
            'Date: ' . date(DATE_RFC2822),
            '',
            $body,
        ]);
    }

    private function escapeMessageBody(string $message): string
    {
        return preg_replace('/(^|\r\n)\./', '$1..', $message) ?? $message;
    }

    private function formatFromHeader(): string
    {
        if ('' === $this->fromName) {
            return '<' . $this->fromEmail . '>';
        }

        return $this->encodeHeader($this->fromName) . ' <' . $this->fromEmail . '>';
    }

    private function encodeHeader(string $value): string
    {
        return sprintf('=?UTF-8?B?%s?=', base64_encode($value));
    }

    private function resolveHelloDomain(): string
    {
        $hostName = gethostname();

        if (false === $hostName || '' === $hostName) {
            return 'localhost';
        }

        return $hostName;
    }
}
