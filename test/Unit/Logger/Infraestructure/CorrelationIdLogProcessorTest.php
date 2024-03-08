<?php

declare(strict_types=1);

namespace Test\Unit\Logger\Infraestructure;

use PHPUnit\Framework\TestCase;
use PicPay\FraudCheckCommons\Http\Infrastructure\CorrelationIdService;
use PicPay\FraudCheckCommons\Logger\Infrastructure\CorrelationIdLogProcessor;

class CorrelationIdLogProcessorTest extends TestCase
{
    public function testAddCorrelationIdIntoRecord(): void
    {
        $service = $this->createMock(CorrelationIdService::class);
        $service->expects($this->once())
            ->method('get')
            ->willReturn('ebab90ea-60be-40e7-9588-0036bc8183d1');

        $processor = new CorrelationIdLogProcessor($service);

        $record = $processor([
            'message' => 'Fake Log',
            'context' => []
        ]);

        $this->assertArrayHasKey('X-Request-ID', $record);
        $this->assertEquals('ebab90ea-60be-40e7-9588-0036bc8183d1', $record['X-Request-ID']);
    }
}
