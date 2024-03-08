<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Broker\Infrastructure;

use PicPay\FraudCheckCommons\Broker\Domain\BrokerProducerInterface;
use PicPay\FraudCheckCommons\Broker\Domain\ValueObject\Uuid;
use PicPay\Hyperf\Commons\Broker\BrokerFactory;
use PicPay\Contracts\Broker\BrokerInterface;
use Psr\Container\ContainerInterface;

class BrokerProducer implements BrokerProducerInterface
{
    private BrokerInterface $broker;

    public function __construct(private ContainerInterface $container)
    {
    }

    public function connect(string $queue): self
    {
        $this->broker = ($this->container->get(BrokerFactory::class))->get($queue);

        return $this;
    }

    public function produce(array $payload): void
    {
        $message = $this->broker->createMessage($payload);
        $message->setKey(Uuid::generate());

        $this->broker->push($message);
    }
}
