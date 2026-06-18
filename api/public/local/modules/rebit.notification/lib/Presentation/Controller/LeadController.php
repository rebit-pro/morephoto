<?php

declare(strict_types=1);

namespace Rebit\Notification\Presentation\Controller;

use Rebit\Notification\Application\Lead\Dto\Request\SubmitLeadRequestDto;
use Rebit\Notification\Application\Lead\UseCase\SubmitLeadUseCase;
use Rebit\Share\Infrastructure\Bitrix\ControllerJson;
use Rebit\Share\Infrastructure\Controller\BaseJsonController;
use Rebit\Share\Shared\Exception\HttpException;

/**
 * Приём заявок с формы сайта и доставка в Telegram.
 *
 * Эндпоинт публичный (без авторизации). Сайт обращается с другого домена,
 * поэтому добавляем CORS-заголовки и обрабатываем preflight (OPTIONS).
 */
final class LeadController extends BaseJsonController
{
    public function __construct(
        private readonly SubmitLeadUseCase $submitLeadUseCase,
        private readonly string $allowedOrigin,
    ) {
        parent::__construct();
    }

    /**
     * POST /api/v1/lead
     *
     * @throws HttpException
     */
    public function submitAction(SubmitLeadRequestDto $dto): ControllerJson
    {
        return $this->withCors(
            $this->json($this->submitLeadUseCase->execute($dto)),
        );
    }

    /**
     * OPTIONS /api/v1/lead — preflight-запрос браузера.
     */
    public function preflightAction(): ControllerJson
    {
        return $this->withCors($this->json([]));
    }

    public function configureActions(): array
    {
        return [
            'preflight' => [
                'prefilters' => [],
            ],
        ];
    }

    private function withCors(ControllerJson $response): ControllerJson
    {
        $response->addHeader('Access-Control-Allow-Origin', $this->allowedOrigin);
        $response->addHeader('Vary', 'Origin');
        $response->addHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
        $response->addHeader('Access-Control-Allow-Headers', 'Content-Type');
        $response->addHeader('Access-Control-Max-Age', '86400');

        return $response;
    }
}
