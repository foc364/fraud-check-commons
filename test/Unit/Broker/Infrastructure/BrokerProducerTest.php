<?php

declare(strict_types=1);

namespace Test\Unit\Broker\Infrastructure;

use PicPay\FraudCheckCommons\Broker\Infrastructure\BrokerProducer;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use PicPay\Hyperf\Commons\Broker\BrokerFactory;
use PicPay\Contracts\Broker\BrokerInterface;
use PicPay\Contracts\Broker\BrokerFactoryInterface;
use Enqueue\RdKafka\RdKafkaMessage as Message;

class BrokerProducerTest extends TestCase
{
    public function testShouldProduceMessage(): void
    {
        $body = [];
        $message = $this->createMock(Message::class);
        $message->expects($this->once())
            ->method('setKey');

        $broker = $this->createMock(BrokerInterface::class);
        $broker
            ->expects($this->once())
            ->method('createMessage')
            ->with($body)
            ->willReturn($message);

        $broker
            ->expects($this->once())
            ->method('push')
            ->with($message);

        $brokerFactory = $this->createMock(BrokerFactoryInterface::class);
        $brokerFactory
            ->expects($this->once())
            ->method('get')
            ->with('test_queue_producer')
            ->willReturn($broker);

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects($this->once())
            ->method('get')
            ->with(BrokerFactory::class)
            ->willReturn($brokerFactory);

        $producer = new BrokerProducer($container);
        $producer->connect('test_queue_producer')
            ->produce(
                payload: []
            );
    }
}
