<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\Broker\UI\Console;

use Hyperf\Command\Command;
use Hyperf\GoTask\WithGoTask;
use Interop\Queue\Message;
use PicPay\Contracts\Broker\BrokerFactoryInterface;
use PicPay\Contracts\Broker\BrokerInterface;
use PicPay\Contracts\Broker\BrokerProcessorInterface;
use PicPay\FraudCheckCommons\Broker\Domain\BrokerConsumerException;
use PicPay\FraudCheckCommons\Broker\Domain\BrokerConsumerInterface;
use PicPay\FraudCheckCommons\Bus\Domain\CommandBusInterface;
use PicPay\FraudCheckCommons\Validator\Domain\ValidationException;
use PicPay\FraudCheckCommons\Validator\Domain\ValidatorInterface;
use PicPay\Hyperf\Commons\Observability\Otel\WithOtelAgent;
use Psr\Log\LoggerInterface;

abstract class BrokerCommand extends Command implements BrokerProcessorInterface, WithOtelAgent, WithGoTask
{
    private BrokerInterface $broker;

    public function __construct(
        BrokerFactoryInterface $brokerFactory,
        protected LoggerInterface $logger,
        protected CommandBusInterface $commandBus
    ) {
        $this->broker = $brokerFactory->get($this->getTopicConfig());

        parent::__construct($this->getCommandName());
    }

    public function handle(): void
    {
        $this->broker->consume($this);
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws BrokerConsumerException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws ValidationException
     */
    public function process(Message $message): void
    {
        $body = (array) json_decode($message->getBody(), true);

        $this->getValidator()->validate($body);

        $consumerClassName = $this->getConsumer();
        $consumerClassInstance = new $consumerClassName($this->logger, $this->commandBus);

        if (!$consumerClassInstance instanceof BrokerConsumerInterface) {
            throw new BrokerConsumerException('Invalid consumer processor');
        }

        $consumerClassInstance->handle($body);
    }

    abstract protected function getCommandName(): string;

    abstract protected function getTopicConfig(): string;

    abstract protected function getConsumer(): string;

    abstract protected function getValidator(): ValidatorInterface;
}
