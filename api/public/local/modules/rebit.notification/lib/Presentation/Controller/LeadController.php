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
 * Эндпоинт публичный (без авторизации). CORS и preflight (OPTIONS)
 * обрабатывает nginx (см. docker/common/nginx/conf.d), поэтому контроллер
 * заголовки CORS не добавляет — иначе они продублируются.
 */
final class LeadController extends BaseJsonController
{
    public function __construct(
        private readonly SubmitLeadUseCase $submitLeadUseCase,
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
        return $this->json($this->submitLeadUseCase->execute($dto));
    }
}
