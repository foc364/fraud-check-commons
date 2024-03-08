<?php

declare(strict_types=1);

namespace Test\Unit\EventSourcing\Infrastructure;

use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\EventTypeFormatted;
use PHPUnit\Framework\TestCase;
use EventSauce\EventSourcing\ClassNameInflector;

class EventTypeFormattedTest extends TestCase
{
    private const EVENT_TYPE = 'test.namespace.domain_event';

    public function testMustFormatClassType(): void
    {
        $classNameInflector = $this->createMock(ClassNameInflector::class);

        $classNameInflector
            ->expects($this->once())
            ->method('typeToClassName')
            ->willReturn('Tess\\Namespace\\DomainEvent');

        $className = (new EventTypeFormatted($classNameInflector))->format(self::EVENT_TYPE);

        $this->assertEquals('DomainEvent', $className);
    }
}
