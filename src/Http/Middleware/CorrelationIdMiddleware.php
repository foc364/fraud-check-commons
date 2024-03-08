<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Http\Middleware;

use PicPay\FraudCheckCommons\Http\Infrastructure\CorrelationIdService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CorrelationIdMiddleware implements MiddlewareInterface
{
    private const X_REQUEST_ID = 'X-Request-ID';

    public function __construct(
        private CorrelationIdService   $correlationIdService
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uuid = $request->getHeaderLine(self::X_REQUEST_ID);

        $this->correlationIdService->set($uuid);

        return $handler->handle($request);
    }
}