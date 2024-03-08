<?php

declare(strict_types=1);

namespace Test\Unit\Http\Infrastructure;

use PHPUnit\Framework\TestCase;
use PicPay\FraudCheckCommons\Context\Infraestructure\ContextManager;
use PicPay\FraudCheckCommons\Http\Infrastructure\CorrelationIdService;

class CorrelationIdServiceTest extends TestCase
{
    public function testGetCorrelationId(): void
    {
        $contextManager = $this->createMock(ContextManager::class);
        $contextManager->expects($this->exactly(2))
            ->method('find')
            ->with('correlation_id')
            ->willReturn('bb8fc3eb-4125-422f-a9d6-38db0428d801');

        $service = new CorrelationIdService($contextManager);
        $this->assertEquals('bb8fc3eb-4125-422f-a9d6-38db0428d801', $service->get());
        $this->assertEquals(['headers' => ['X-Request-ID' => 'bb8fc3eb-4125-422f-a9d6-38db0428d801']], $service->getAsHeader());
    }

    public function testSetCorrelationId(): void
    {
        $contextManager = $this->createMock(ContextManager::class);
        $contextManager->expects($this->once())
            ->method('store')
            ->with('correlation_id', 'eeefe15e-7078-45ad-9fc1-a2a847488bd9');

        $service = new CorrelationIdService($contextManager);
        $service->set('eeefe15e-7078-45ad-9fc1-a2a847488bd9');
    }
}