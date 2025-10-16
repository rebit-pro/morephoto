<?php

namespace api\src\Http\Action;

use api\src\Http\JsonResponse;
use Psr\Http\Message\{
    RequestInterface as Request,
    ResponseFactoryInterface,
    ResponseInterface as Response,
    ServerRequestInterface};
use Psr\Http\Server\RequestHandlerInterface;
use stdClass;

final class HomeAction implements RequestHandlerInterface
{
    #[\Override]
    public function handle(ServerRequestInterface $request): Response
    {
        return new JsonResponse(new stdClass());
    }
}
