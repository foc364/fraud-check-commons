<?php

declare(strict_types=1);

namespace Test\Unit\EventSourcing\Infrastructure\Message;

use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\DomainEvent;
use PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message\MessageDispatcher;
use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageConsumer;
use Hyperf\Contract\ContainerInterface;
use PHPUnit\Framework\TestCase;

class MessageDispatcherTest extends TestCase
{
    public function testMessageDispatch(): void
    {
        $container = parent::createMock(ContainerInterface::class);
        $consumer = parent::createMock(MessageConsumer::class);
        $domainEvent = $this->createMock(DomainEvent::class);

        $container
            ->expects(parent::once())
            ->method('make')
            ->willReturn($consumer);

        $consumer
            ->expects(parent::once())
            ->method('handle');

        $consumers = MessageConsumer::class;
        $message = new Message($domainEvent);

        (new MessageDispatcher($container, $consumers))->dispatch($message);
    }
}
