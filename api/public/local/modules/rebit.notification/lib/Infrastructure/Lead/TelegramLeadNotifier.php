<?php

declare(strict_types=1);

namespace Rebit\Notification\Infrastructure\Lead;

use Psr\Log\LoggerInterface;
use Rebit\Notification\Application\Lead\Dto\LeadAttachmentDto;
use Rebit\Notification\Application\Lead\Dto\LeadMessageDto;
use Rebit\Notification\Application\Lead\Port\LeadNotifierInterface;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * Доставка заявки в Telegram через Bot API (sendMessage + sendDocument).
 *
 * Используем cURL напрямую (а не RebitHttpClient/Bitrix HttpClient), потому что
 * с сервера api.telegram.org заблокирован и запрос идёт через прокси, в т.ч.
 * SOCKS5 — это умеет cURL, но не Bitrix HttpClient. Тип прокси определяется
 * по схеме в $proxy (например socks5h://user:pass@host:port или http://...).
 *
 * Порядок доставки: сначала текст заявки (быстро, критично), затем — файл ТЗ
 * (медленнее). Если текст ушёл, а файл нет, запрос не валим: заявка уже принята,
 * ошибку вложения логируем и отдельным сообщением предупреждаем получателя.
 *
 * Токен бота, chat_id и прокси берутся из окружения сервера.
 */
final readonly class TelegramLeadNotifier implements LeadNotifierInterface
{
    private const int TIMEOUT_SECONDS = 12;

    private const int DOCUMENT_TIMEOUT_SECONDS = 60;

    public function __construct(
        private LoggerInterface $logger,
        private string $botToken,
        private string $chatId,
        private string $apiBaseUrl,
        private string $proxy,
    ) {}

    /**
     * @throws HttpException
     */
    public function notify(LeadMessageDto $lead, ?LeadAttachmentDto $attachment = null): void
    {
        if ('' === $this->botToken || '' === $this->chatId) {
            $this->logger->error('Telegram-получатель заявок не настроен: пустой токен или chat_id');

            throw new HttpException('Сервис заявок временно недоступен', 503);
        }

        // 1. Текст заявки — критичная часть: при провале возвращаем ошибку.
        $textSent = $this->request(
            'sendMessage',
            http_build_query([
                'chat_id' => $this->chatId,
                'text' => $this->buildText($lead),
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => 'true',
            ]),
            self::TIMEOUT_SECONDS,
        );

        if (!$textSent) {
            throw new HttpException('Не удалось отправить заявку', 502);
        }

        if (null === $attachment) {
            return;
        }

        // 2. Файл ТЗ — best-effort: заявка уже доставлена, провал файла не валит запрос.
        $documentSent = $this->request(
            'sendDocument',
            [
                'chat_id' => $this->chatId,
                'caption' => $this->buildCaption($lead),
                'parse_mode' => 'HTML',
                'document' => new \CURLFile($attachment->path, $attachment->mimeType, $attachment->name),
            ],
            self::DOCUMENT_TIMEOUT_SECONDS,
        );

        if (!$documentSent) {
            // Текст уже у получателя — предупреждаем его, что файл не дошёл (тоже best-effort).
            $this->request(
                'sendMessage',
                http_build_query([
                    'chat_id' => $this->chatId,
                    'text' => '⚠️ Файл ТЗ к предыдущей заявке доставить не удалось — попросите клиента прислать его ещё раз.',
                ]),
                self::TIMEOUT_SECONDS,
            );
        }
    }

    /**
     * Низкоуровневая отправка метода Bot API через прокси.
     *
     * @param array<string, mixed>|string $postFields http_build_query-строка (обычный POST)
     *                                                или массив (multipart с CURLFile)
     *
     * @return bool успешно ли Telegram принял запрос (ok=true)
     */
    private function request(string $method, array|string $postFields, int $timeout): bool
    {
        $handle = curl_init($this->buildMethodUrl($method));

        if (false === $handle) {
            $this->logger->error('Не удалось инициализировать запрос в Telegram', ['method' => $method]);

            return false;
        }

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => self::TIMEOUT_SECONDS,
            CURLOPT_POSTFIELDS => $postFields,
        ];

        if ('' !== $this->proxy) {
            $options[CURLOPT_PROXY] = $this->proxy;
        }

        curl_setopt_array($handle, $options);

        $response = curl_exec($handle);
        $status = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);
        $error = curl_error($handle);
        curl_close($handle);

        if (!is_string($response) || 200 !== $status) {
            $this->logger->error('Не удалось отправить запрос в Telegram', [
                'method' => $method,
                'status' => $status,
                'error' => $error,
                'response' => is_string($response) ? mb_substr($response, 0, 500) : '',
            ]);

            return false;
        }

        $decoded = json_decode($response, true);

        if (!is_array($decoded) || true !== ($decoded['ok'] ?? false)) {
            $this->logger->error('Telegram отклонил запрос', [
                'method' => $method,
                'response' => mb_substr($response, 0, 500),
            ]);

            return false;
        }

        return true;
    }

    private function buildMethodUrl(string $method): string
    {
        return sprintf('%s/bot%s/%s', rtrim($this->apiBaseUrl, '/'), $this->botToken, $method);
    }

    private function buildText(LeadMessageDto $lead): string
    {
        $lines = [
            '🆕 <b>Новая заявка с сайта</b>',
            '',
            '👤 <b>Имя:</b> ' . $this->escape($lead->name),
            '📞 <b>Телефон:</b> ' . $this->escape($lead->phone),
        ];

        if ('' !== $lead->email) {
            $lines[] = '✉️ <b>Email:</b> ' . $this->escape($lead->email);
        }

        $lines[] = '';
        $lines[] = '📝 <b>Описание:</b>';
        $lines[] = $this->escape($lead->description);

        if ('' !== $lead->page) {
            $lines[] = '';
            $lines[] = '🔗 ' . $this->escape($lead->page);
        }

        return implode("\n", $lines);
    }

    private function buildCaption(LeadMessageDto $lead): string
    {
        return '📎 <b>ТЗ к заявке от ' . $this->escape($lead->name) . '</b>';
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
