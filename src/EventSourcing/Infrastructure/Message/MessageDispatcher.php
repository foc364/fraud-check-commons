<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Infrastructure\Message;

use EventSauce\EventSourcing\Message;
use EventSauce\EventSourcing\MessageDispatcher as EventSauceMessageDispatcher;
use Hyperf\Contract\ContainerInterface;

final class MessageDispatcher implements EventSauceMessageDispatcher
{
    /**
     * @var string[]
     */
    private array $consumers;

    public function __construct(
        private ContainerInterface $container,
        string ...$consumers
    ) {
        $this->consumers = $consumers;
    }

    public function dispatch(Message ...$messages): void
    {
        foreach ($this->consumers as $consumer) {
            $consumer = $this->container->make($consumer);
            foreach ($messages as $message) {
                $consumer->handle($message);
            }
        }
    }
}
