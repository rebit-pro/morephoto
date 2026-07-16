<?php

declare(strict_types=1);

namespace Rebit\Leadhunter\Infrastructure\LeadHunt\Notifier;

use Psr\Log\LoggerInterface;
use Rebit\Leadhunter\Application\LeadHunt\Dto\PendingLeadDto;
use Rebit\Leadhunter\Application\LeadHunt\Port\HuntNotifierInterface;

/**
 * Композит каналов доставки: основной + резервный.
 *
 * Резервный канал включается только на последней попытке — короткий сбой
 * основного канала отрабатывается штатными ретраями (следующими прогонами
 * команды), и лишь когда попытки исчерпаны, заявка уходит резервом,
 * а не застревает в failed навсегда.
 */
final readonly class FallbackHuntNotifier implements HuntNotifierInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private HuntNotifierInterface $primary,
        private HuntNotifierInterface $fallback,
        private int $maxAttempts,
    ) {}

    public function notify(PendingLeadDto $lead): bool
    {
        if ($this->primary->notify($lead)) {
            return true;
        }

        if ($lead->attempts + 1 < $this->maxAttempts) {
            return false;
        }

        $this->logger->warning('Основной канал доставки исчерпал попытки, уходим на резервный', [
            'leadId' => $lead->id,
            'attempts' => $lead->attempts + 1,
        ]);

        return $this->fallback->notify($lead);
    }
}
