<?php

declare(strict_types=1);

namespace PicPay\FraudCheckCommons\EventSourcing\Infrastructure;

use PicPay\FraudCheckCommons\EventSourcing\Domain\EventTypeFormatted as EventTypeFormattedInterface;
use EventSauce\EventSourcing\ClassNameInflector;

final class EventTypeFormatted implements EventTypeFormattedInterface
{
    public function __construct(private ClassNameInflector $classNameInflector)
    {
    }

    public function format(string $eventType): string
    {
        $className = $this->classNameInflector->typeToClassName($eventType);
        $parts = explode("\\", $className);

        return end($parts);
    }
}
