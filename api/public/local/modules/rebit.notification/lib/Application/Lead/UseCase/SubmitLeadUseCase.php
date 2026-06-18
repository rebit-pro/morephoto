<?php

declare(strict_types=1);

namespace Rebit\Notification\Application\Lead\UseCase;

use Rebit\Notification\Application\Lead\Dto\LeadMessageDto;
use Rebit\Notification\Application\Lead\Dto\Request\SubmitLeadRequestDto;
use Rebit\Notification\Application\Lead\Dto\Result\SubmitLeadResultDto;
use Rebit\Notification\Application\Lead\Port\LeadNotifierInterface;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * Принимает заявку с сайта и отправляет её ответственному получателю.
 */
final readonly class SubmitLeadUseCase
{
    public function __construct(
        private LeadNotifierInterface $notifier,
    ) {}

    /**
     * @throws HttpException
     */
    public function execute(SubmitLeadRequestDto $dto): SubmitLeadResultDto
    {
        // honeypot: поле заполнено только ботом — тихо принимаем, не доставляя.
        if ('' !== $dto->company) {
            return new SubmitLeadResultDto();
        }

        $this->notifier->notify(
            new LeadMessageDto(
                name: trim($dto->name),
                phone: trim($dto->phone),
                description: trim($dto->description),
                page: trim($dto->page),
            ),
        );

        return new SubmitLeadResultDto();
    }
}
