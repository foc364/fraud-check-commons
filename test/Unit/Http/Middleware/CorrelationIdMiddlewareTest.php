<?php

declare(strict_types=1);

namespace Test\Unit\Http\Middleware;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use PicPay\FraudCheckCommons\Http\Infrastructure\CorrelationIdService;
use PicPay\FraudCheckCommons\Http\Middleware\CorrelationIdMiddleware;
use Psr\Http\Server\RequestHandlerInterface;

class CorrelationIdMiddlewareTest extends TestCase
{
    public function testCaptureCorrelationId(): void
    {
        $request = new ServerRequest(
            'POST',
            '/payment',
            ['X-Request-ID' => '123456789'],
            json_encode([
                'value' => 123
            ])
        );

        $response = new Response(
            201,
            ['X-Request-ID' => '123456789'],
            json_encode([
                'id' => 2,
                'value' => 123
            ])
        );

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $correlationIdService = $this->createMock(CorrelationIdService::class);
        $correlationIdService->expects($this->once())
            ->method('set')
            ->with('123456789');

        $middleware = new CorrelationIdMiddleware($correlationIdService);

        $actual = $middleware->process($request, $handler);

        $this->assertEquals($response, $actual);
    }

    public function testEmptyCorrelationId(): void
    {
        $request = new ServerRequest(
            'POST',
            '/payment',
            [],
            json_encode([
                'value' => 123
            ])
        );

        $response = new Response(
            201,
            [],
            json_encode([
                'id' => 2,
                'value' => 123
            ])
        );

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $correlationIdService = $this->createMock(CorrelationIdService::class);
        $correlationIdService->expects($this->once())
            ->method('set')
            ->with(null);

        $middleware = new CorrelationIdMiddleware($correlationIdService);

        $actual = $middleware->process($request, $handler);

        $this->assertEquals($response, $actual);
    }
}