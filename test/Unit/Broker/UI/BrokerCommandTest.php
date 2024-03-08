<?php

declare(strict_types=1);

namespace Test\Unit\Broker\UI;

use PHPUnit\Framework\TestCase;
use Interop\Queue\Message;
use PicPay\Contracts\Broker\BrokerFactoryInterface;
use PicPay\Contracts\Broker\BrokerInterface;
use PicPay\FraudCheckCommons\Broker\Domain\BrokerConsumerException;
use PicPay\FraudCheckCommons\Broker\UI\Console\BrokerCommand;
use PicPay\FraudCheckCommons\Bus\Domain\CommandBusInterface;
use PicPay\FraudCheckCommons\Validator\Application\Validator;
use PicPay\FraudCheckCommons\Validator\Domain\ValidationException;
use PicPay\FraudCheckCommons\Validator\Domain\ValidatorInterface;
use Psr\Log\LoggerInterface;
use Test\Unit\Broker\UI\Fixtures\BrokerConsumer;
use Test\Unit\Broker\UI\Fixtures\InvalidBrokerConsumer;

class BrokerCommandTest extends TestCase
{
    /**
     * @var BrokerFactoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private BrokerFactoryInterface $brokerFactory;

    /**
     * @var BrokerInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private BrokerInterface $broker;

    /**
     * @var Message|\PHPUnit\Framework\MockObject\MockObject
     */
    private Message $message;

    private LoggerInterface $logger;

    private CommandBusInterface $commandBus;

    protected function setUp(): void
    {
        $this->brokerFactory = $this->createMock(BrokerFactoryInterface::class);
        $this->broker = $this->createMock(BrokerInterface::class);
        $this->message = $this->createMock(Message::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->commandBus = $this->createMock(CommandBusInterface::class);

        parent::setUp();
    }

    public function testShouldProcessMessage(): void
    {
        $this->broker->expects($this->once())
            ->method('consume');

        $this->brokerFactory->expects($this->once())
            ->method('get')
            ->with('test_queue_consumer')
            ->willReturn($this->broker);

        $validator = new class () extends Validator implements ValidatorInterface {
            public function rules(): array
            {
                return [];
            }
        };

        $class = new class ($this->brokerFactory, $this->logger, $this->commandBus, $validator) extends BrokerCommand {
            private ValidatorInterface $validator;

            public function __construct(
                BrokerFactoryInterface $brokerFactory,
                protected LoggerInterface $logger,
                protected CommandBusInterface $commandBus,
                ValidatorInterface $validator
            ) {
                $this->validator = $validator;

                parent::__construct($brokerFactory, $logger, $commandBus);
            }

            public function getCommandName(): string
            {
                return 'test:example';
            }

            public function getTopicConfig(): string
            {
                return 'test_queue_consumer';
            }

            public function getConsumer(): string
            {
                return BrokerConsumer::class;
            }

            public function getValidator(): ValidatorInterface
            {
                return $this->validator;
            }
        };

        $this->message->expects($this->once())
            ->method('getBody')
            ->willReturn('[]');

        $class->handle();
        $class->process($this->message);
    }

    public function testShouldBrokerConsumerException(): void
    {
        $this->expectException(BrokerConsumerException::class);

        $this->broker->expects($this->once())
            ->method('consume');

        $this->brokerFactory->expects($this->once())
            ->method('get')
            ->with('test_queue_consumer')
            ->willReturn($this->broker);

        $validator = new class () extends Validator implements ValidatorInterface {
            public function rules(): array
            {
                return [];
            }
        };

        $class = new class ($this->brokerFactory, $this->logger, $this->commandBus, $validator) extends BrokerCommand {
            private ValidatorInterface $validator;

            public function __construct(
                BrokerFactoryInterface $brokerFactory,
                protected LoggerInterface $logger,
                protected CommandBusInterface $commandBus,
                ValidatorInterface $validator
            ) {
                $this->validator = $validator;

                parent::__construct($brokerFactory, $logger, $commandBus);
            }

            public function getCommandName(): string
            {
                return 'test:example';
            }

            public function getTopicConfig(): string
            {
                return 'test_queue_consumer';
            }

            public function getConsumer(): string
            {
                return InvalidBrokerConsumer::class;
            }

            public function getValidator(): ValidatorInterface
            {
                return $this->validator;
            }
        };

        $this->message->expects($this->once())
            ->method('getBody')
            ->willReturn('[]');

        $class->handle();
        $class->process($this->message);
    }

    public function testShouldBrokerConsumerExceptionValidator(): void
    {
        $this->expectException(ValidationException::class);

        $this->broker->expects($this->once())
            ->method('consume');

        $this->brokerFactory->expects($this->once())
            ->method('get')
            ->with('test_queue_consumer')
            ->willReturn($this->broker);


        $validator = new class () extends Validator implements ValidatorInterface {
            public function rules(): array
            {
                return [ 'id' => ['required']];
            }
        };

        $class = new class ($this->brokerFactory, $this->logger, $this->commandBus, $validator) extends BrokerCommand {
            private ValidatorInterface $validator;

            public function __construct(
                BrokerFactoryInterface $brokerFactory,
                protected LoggerInterface $logger,
                protected CommandBusInterface $commandBus,
                ValidatorInterface $validator
            ) {
                $this->validator = $validator;

                parent::__construct($brokerFactory, $logger, $commandBus);
            }

            public function getCommandName(): string
            {
                return 'test:example';
            }

            public function getTopicConfig(): string
            {
                return 'test_queue_consumer';
            }

            public function getConsumer(): string
            {
                return BrokerConsumer::class;
            }

            public function getValidator(): ValidatorInterface
            {
                return $this->validator;
            }
        };

        $this->message->expects($this->once())
            ->method('getBody')
            ->willReturn('[]');

        $class->handle();
        $class->process($this->message);
    }
}
