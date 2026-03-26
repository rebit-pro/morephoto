<?php

declare(strict_types=1);

use Monolog\Formatter\HtmlFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Formatter\LogstashFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Rebit\Share\Infrastructure\Logger\CommonLoggerProcessor;
use Rebit\Share\Shared\Enum\LogChannelEnum;

return [
    'monolog' => [
        'value' => [
            'logstash' => [
                'handler' => static fn(LogChannelEnum $channel) => new RotatingFileHandler(
                    dirname($_SERVER['DOCUMENT_ROOT']) . '/logs/logstash/' . $channel->value . '.log',
                    maxFiles: 30,
                    level: Logger::INFO,
                ),
                'formatter' => static fn(LogChannelEnum $channel) => new LogstashFormatter($channel->value),
                'processor' => static function(LogChannelEnum $channel, array $record): array {
                    return (new CommonLoggerProcessor($record))();
                },
            ],
            'stdout' => [
                'handler' => static fn() => new StreamHandler('php://stdout', Logger::INFO),
                'formatter' => static fn() => new LineFormatter(
                    allowInlineLineBreaks: true,
                    ignoreEmptyContextAndExtra: true,
                ),
                'processor' => static function(LogChannelEnum $channel, array $record): array {
                    return (new CommonLoggerProcessor($record))();
                },
            ],
        ],
    ],
];
